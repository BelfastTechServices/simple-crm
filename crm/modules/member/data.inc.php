<?php

/**
 * Return data for one or more members.
 * @param $opts An associative array of options, possible keys are:
 *   'cid' If specified, return a member (or members if array) with the given id,
 *   'filter' An array mapping filter names to filter values
 * @return An array with each element representing a member.
 */
function member_data ($opts = array()) {
    global $db_connect;
    // Query database
    $sql = "
        SELECT
        `member`.`cid`, `firstName`, `middleName`, `lastName`, `email`, `phone`, `createdBy`, `createdDate`, `createdTime`,
        `address1`, `address2`, `address3`, `town_city`, `zipcode`,
        `username`, `hash`
        FROM `member`
        LEFT JOIN `contact` ON `member`.`cid`=`contact`.`cid`
        LEFT JOIN `user` ON `member`.`cid`=`user`.`cid`
        WHERE 1
    ";
    if (isset($opts['cid']) and !empty($opts['cid'])) {
        if (is_array($opts['cid'])) {
            $terms = array();
            foreach ($opts['cid'] as $cid) {
                $term = "'" . mysqli_real_escape_string($db_connect, $cid) . "'";
                $terms[] = $term;
            }
            $esc_list = "(" . implode(',', $terms) .")";
            $sql .= "
                AND `member`.`cid` IN $esc_list
            ";
        } else {
            $esc_cid = mysqli_real_escape_string($db_connect, $opts['cid']);
            $sql .= "
                AND `member`.`cid`='$esc_cid'
            ";
        }
    }
    $sql .= "
        GROUP BY `member`.`cid`
    ";
    $sql .= "
        ORDER BY `lastName`, `firstName`, `middleName` ASC
    ";
    $res = mysqli_query($db_connect, $sql);
    if (!$res) crm_error(mysqli_error($res));
    // Store data
    $members = array();
    $row = mysqli_fetch_assoc($res);
    while (!empty($row)) {
        $member = array(
            'cid' => $row['cid']
            , 'contact' => array(
                'cid' => $row['cid']
                , 'firstName' => $row['firstName']
                , 'middleName' => $row['middleName']
                , 'lastName' => $row['lastName']
                , 'email' => $row['email']
                , 'phone' => $row['phone']
                , 'createdBy' => $row['createdBy']
                , 'createdDate' => $row['createdDate']
                , 'createdTime' => $row['createdTime']
            )
            , 'user' => array(
                'cid' => $row['cid']
                , 'username' => $row['username']
                , 'hash' => $row['hash']
            )
            , 'member' => array(
                'address1' => $row['address1']
                , 'address2' => $row['address2']
                , 'address3' => $row['address3']
                , 'town_city' => $row['town_city']
                , 'zipcode' => $row['zipcode']
            )
        );
        $members[] = $member;
        $row = mysqli_fetch_assoc($res);
    }
    // Return data
    return $members;
}

/**
 * Implementation of hook_data_alter().
 * @param $type The type of the data being altered.
 * @param $data An array of structures of the given $type.
 * @param $opts An associative array of options.
 * @return An array of modified structures.
 */
function member_data_alter ($type, $data = array(), $opts = array()) {
    switch ($type) {
        case 'contact':
            // Get cids of all contacts passed into $data
            $cids = array();
            foreach ($data as $contact) {
                $cids[] = $contact['cid'];
            }
            // Add the cids to the options
            $member_opts = $opts;
            $member_opts['cid'] = $cids;
            // Get an array of member structures for each cid
            $member_data = crm_get_data('member', $member_opts);
            // Create a map from cid to member structure
            $cid_to_member = array();
            foreach ($member_data as $member) {
                $cid_to_member[$member['cid']] = $member;
            }
            // Add member structures to the contact structures
            foreach ($data as $i => $contact) {
                if (array_key_exists($contact['cid'], $cid_to_member)) {
                    $member = $cid_to_member[$contact['cid']];
                    $data[$i]['member'] = $member;
                }
            }
            break;
    }
    return $data;
}

/**
 * Update member data when a contact is updated.
 * @param $contact The contact data array.
 * @param $op The operation being performed.
 */
function member_contact_api ($contact, $op) {
    global $db_connect;
    // Check whether the contact is a member
    if (!isset($contact['member'])) {
        return $contact;
    }
    $esc_cid = mysqli_real_escape_string($db_connect, $contact['cid']);
    $fields = array(
        'address1', 'address2', 'address3', 'town_city', 'zipcode'
    );
    $escaped = array();
    foreach ($fields as $field) {
        $escaped[$field] = mysqli_real_escape_string($db_connect, $contact['member'][$field]);
    }
    switch ($op) {
        case 'create':
            // Add member
            $sql = "
                INSERT INTO `member`
                (`cid`, `address1`, `address2`, `address3`, `town_city`, `zipcode`)
                VALUES
                ('$esc_cid', '$escaped[address1]', '$escaped[address2]', '$escaped[address3]', '$escaped[town_city]', '$escaped[zipcode]')
            ";
            $res = mysqli_query($db_connect, $sql);
            if (!$res) crm_error(mysqli_error($res));
            $contact['member']['cid'] = $contact['cid'];
            // Add role entry
            $sql = "
                SELECT `rid`
                FROM `role`
                WHERE `name`='member'
            ";
            $res = mysqli_query($db_connect, $sql);
            if (!$res) crm_error(mysqli_error($res));
            $row = mysqli_fetch_assoc($res);
            $esc_rid = mysqli_real_escape_string($db_connect, $row['rid']);
            if ($row) {
                $sql = "
                    INSERT INTO `user_role`
                    (`cid`, `rid`)
                    VALUES
                    ('$esc_cid', '$esc_rid')
                ";
                $res = mysqli_query($db_connect, $sql);
                if (!$res) crm_error(mysqli_error($res));
            }
            break;
        case 'update':
            // TODO
            break;
        case 'delete':
            member_delete($esc_cid);
            break;
    }
    return $contact;
}

/**
 * Saves a member.
 */
function member_save ($member) {
    global $db_connect;
    $fields = array(
        'cid', 'address1', 'address2', 'address3', 'town_city', 'zipcode'
    );
    $escaped = array();
    foreach ($fields as $field) {
        $escaped[$field] = mysqli_real_escape_string($db_connect, $member[$field]);
    }
    if (isset($member['cid'])) {
        // Update member
        $sql = "
            UPDATE `member`
            SET `address1`='$escaped[address1]'
                , `address2`='$escaped[address2]'
                , `address3`='$escaped[address3]'
                , `town_city`='$escaped[town_city]'
                , `zipcode`='$escaped[zipcode]'
            WHERE `cid`='$escaped[cid]'
        ";
        $res = mysqli_query($db_connect, $sql);
        if (!$res) crm_error(mysqli_error($res));
        if (mysqli_affected_rows() < 1) {
            return null;
        }
    }
}

/**
 * Delete member data for a contact.
 * @param $cid - The contact id.
 */
function member_delete ($cid) {
    global $db_connect;
    $esc_cid = mysqli_real_escape_string($db_connect, $cid);
    $sql = "
        DELETE FROM `member`
        WHERE `cid`='$esc_cid'
    ";
    $res = mysqli_query($db_connect, $sql);
    if (!$res) crm_error(mysqli_error($res));
    message_register("Deleted member info for: " . theme('contact_name', $esc_cid));
}

<?php

/**
 * Return data for one or more members.
 *
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
        `member`.`cid`, `firstName`, `middleName`, `lastName`, `email`, `phone`,
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
            $sql .= " AND `member`.`cid` IN $esc_list ";
        } else {
            $esc_cid = mysqli_real_escape_string($db_connect, $opts['cid']);
            $sql .= " AND `member`.`cid`='$esc_cid'";
        }
    }
    $sql .= " GROUP BY `member`.`cid` ";
    $sql .= " ORDER BY `lastName`, `firstName`, `middleName` ASC ";
    
    $res = mysqli_query($db_connect, $sql);
    if (!$res) crm_error(mysqli_error($res));
    
    // Store data
    $members = array();
    $row = mysqli_fetch_assoc($res);
    while (!empty($row)) {
        $member = array(
            'cid' => $row['cid'],
            'contact' => array(
                'cid' => $row['cid']
                , 'firstName' => $row['firstName']
                , 'middleName' => $row['middleName']
                , 'lastName' => $row['lastName']
                , 'email' => $row['email']
                , 'phone' => $row['phone']
            ),
            'user' => array(
                'cid' => $row['cid'],
                'username' => $row['username'],
                'hash' => $row['hash']
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
    
    switch ($op) {
        case 'create':
            // Add member
            $member = $contact['member'];
            $sql = "
                INSERT INTO `member`
                (`cid`)
                VALUES
                ('$esc_cid')
            ";
            $res = mysqli_query($db_connect, $sql);
            if (!$res) crm_error(mysqli_error($res));
            $contact['member']['cid'] = $contact['cid'];
            // Add role entry
            $sql = "SELECT `rid` FROM `role` WHERE `name`='member'";
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
 * Delete member data for a contact.
 * @param $cid - The contact id.
 */
function member_delete ($cid) {
    global $db_connect;
    // Store name
    $contact_data = crm_get_data('contact', array('cid'=>$cid));
    $contact = $contact_data[0];
    $name = theme('contact_name', $contact);
    // Delete member
    $esc_cid = mysqli_real_escape_string($db_connect, $cid);
    $sql = "DELETE FROM `member` WHERE `cid`='$esc_cid'";
    $res = mysqli_query($db_connect, $sql);
    if (!$res) crm_error(mysqli_error($res));
    message_register("Deleted member info for: $name");
}

/**
 * Return data for one or more contacts.  Use contact_data() instead.
 * 
 * @param $opts An associative array of options, possible keys are:
 *   'cid' If specified returns the corresponding member (or members for an array);
 *   'filter' An array mapping filter names to filter values
 * @return An array with each element representing a contact.
 * @deprecated
*/ 
function member_contact_data ($opts = array()) {
    return contact_data($opts);
}

<?php

/**
 * Handle member add request.
 * @return The url to display when complete.
 */
function command_member_add () {
    global $db_connect;
    global $esc_post;
    // Verify permissions
    if (!user_access('member_add')) {
        error_register('Permission denied: member_add');
        return crm_url('members');
    }
    if (!user_access('contact_add')) {
        error_register('Permission denied: contact_add');
        return crm_url('members');
    }
    if (!user_access('user_add')) {
        error_register('Permission denied: user_add');
        return crm_url('members');
    }
    // Find username or create a new one
    $username = $_POST['username'];
    $n = 0;
    while (empty($username) && $n < 100) {
        // Construct test username
        $test_username = strtolower($_POST['firstName']{0} . $_POST['lastName']);
        if ($n > 0) {
            $test_username .= $n;
        }
        // Check whether username is taken
        $esc_test_name = mysqli_real_escape_string($db_connect, $test_username);
        $sql = "
            SELECT *
            FROM `user`
            WHERE `username`='$esc_test_name'
        ";
        $res = mysqli_query($db_connect, $sql);
        if (!$res) crm_error(mysqli_error($res));
        $user_row = mysqli_fetch_assoc($res);
        if (!$user_row) {
            $username = $test_username;
        }
        $n++;
    }
    if (empty($username)) {
        error_register('Please specify a username');
        return crm_url('members&tab=add');
    }
    // Check for duplicate usernames
    if (!empty($username)) {
        // Check whether username is in use
        $test_username = $username;
        $esc_test_username = mysqli_real_escape_string($db_connect, $test_username);
        $sql = "
            SELECT *
            FROM `user`
            WHERE `username`='$esc_test_username'
        ";
        $res = mysqli_query($db_connect, $sql);
        if (!$res) crm_error(mysqli_error($res));
        $username_row = mysqli_fetch_assoc($res);
        if (!$username_row) {
            $username = $test_username;
        } else {
            error_register('Username already in use, please specify a different username');
            return crm_url('members&tab=add');
        }
    }
    // Check for duplicate email addresses
    $email = $_POST['email'];
    if (!empty($email)) {
        // Check whether email address is in use
        $test_email = $email;
        $esc_test_email = mysqli_real_escape_string($db_connect, $test_email);
        $sql = "
            SELECT *
            FROM `contact`
            WHERE `email`='$esc_test_email'
        ";
        $res = mysqli_query($db_connect, $sql);
        if (!$res) crm_error(mysqli_error($res));
        $email_row = mysqli_fetch_assoc($res);
        if (!$email_row) {
            $email = $test_email;
        } else {
            error_register('Email address already in use');
            error_register('Please specify a different email address');
            return crm_url('members&tab=add');
        }
    }
    // Build contact object
    $contact = array(
        'firstName' => $_POST['firstName']
        , 'middleName' => $_POST['middleName']
        , 'lastName' => $_POST['lastName']
        , 'email' => $email
        , 'phone' => $_POST['phone']
    );
    // Add user fields
    $user = array('username' => $username);
    $contact['user'] = $user;
    // Add member fields
    $member = array(
        'member' => $member
        , 'address1' => $_POST['address1']
        , 'address2' => $_POST['address2']
        , 'address3' => $_POST['address3']
        , 'town_city' => $_POST['town_city']
        , 'zipcode' => $_POST['zipcode']
    );
    $contact['member'] = $member;
    // Save to database
    $contact = contact_save($contact);
    $esc_cid = mysqli_real_escape_string($db_connect, $contact['cid']);
    // Notify admins
    $from = get_org_name() . " <" . get_email_from() . ">";
    $headers = "From: $from\r\nContent-Type: text/html; charset=ISO-8859-1\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    if (!empty(get_email_to())) {
        $name = theme_contact_name($contact['cid']);
        $content = theme('member_created_email', $contact['cid']);
        mail(get_email_to(), "New Member: $name", $content, $headers);
    }
    // Notify user
    $confirm_url = user_reset_password_url($contact['user']['username']);
    $content = theme('member_welcome_email', $contact['user']['cid'], $confirm_url);
    mail($_POST['email'], "Welcome to " . get_org_name(), $content, $headers);
    return crm_url("contact&cid=$esc_cid");
}

/**
 * Handle member edit request.
 * @return The url to display when complete.
 */
function command_member_edit () {
    global $db_connect;
    global $esc_post;
    $esc_cid = mysqli_real_escape_string($db_connect, $_POST['cid']);
    $member_data = crm_get_data('member', array('cid'=>$esc_cid));
    $member = $member_data[0]['member'];
    // Add member fields
    $member = array(
        'cid'=> $esc_cid
        , 'address1' => $_POST['address1']
        , 'address2' => $_POST['address2']
        , 'address3' => $_POST['address3']
        , 'town_city' => $_POST['town_city']
        , 'zipcode' => $_POST['zipcode']
    );
    // Save to database
    $member = member_save($member);
    return crm_url("contact&cid=$esc_cid");
}

/**
 * Handle member filter request.
 * @return The url to display on completion.
 */
function command_member_filter () {
    // Set filter in session
    $_SESSION['member_filter_option'] = $_GET['filter'];
    // Set filter
    if ($_GET['filter'] == 'all') {
        $_SESSION['member_filter'] = array();
    }
    if ($_GET['filter'] == 'active') {
        $_SESSION['member_filter'] = array('active'=>true);
    }
    // Construct query string
    $params = array();
    foreach ($_GET as $k=>$v) {
        if ($k == 'command' || $k == 'filter' || $k == 'q') {
            continue;
        }
        $params[] = urlencode($k) . '=' . urlencode($v);
    }
    if (!empty($params)) {
        $query = '&' . join('&', $params);
    }
    return crm_url('members') . $query;
}

/**
 * Handle member import request.
 * @return The url to display on completion.
 */
function command_member_import () {
    global $db_connect;
    // Verify permissions
    if (!user_access('member_add')) {
        error_register('Permission denied: member_add');
        return crm_url('members');
    }
    if (!user_access('contact_add')) {
        error_register('Permission denied: contact_add');
        return crm_url('members');
    }
    if (!user_access('user_add')) {
        error_register('Permission denied: user_add');
        return crm_url('members');
    }
    if (!array_key_exists('member-file', $_FILES)) {
        error_register('No member file uploaded');
        return crm_url('members&tab=import');
    }
    $csv = file_get_contents($_FILES['member-file']['tmp_name']);
    $data = csv_parse($csv);
    foreach ($data as $row) {
        // Convert row keys to lowercase and remove spaces
        foreach ($row as $key => $value) {
            $new_key = str_replace(' ', '', strtolower($key));
            unset($row[$key]);
            $row[$new_key] = $value;
        }
        // Find username or create a new one
        $username = $row['username'];
        $n = 0;
        while (empty($username) && $n < 100) {
            // Construct test username
            $test_username = strtolower($row['firstname']{0} . $row['lastname']);
            if ($n > 0) {
                $test_username .= $n;
            }
            // Check whether username is taken
            $esc_test_name = mysqli_real_escape_string($db_connect, $test_username);
            $sql = "
                SELECT *
                FROM `user`
                WHERE `username`='$esc_test_name'
            ";
            $res = mysqli_query($db_connect, $sql);
            if (!$res) crm_error(mysqli_error($res));
            $user_row = mysqli_fetch_assoc($res);
            if (!$user_row) {
                $username = $test_username;
            }
            $n++;
        }
        if (empty($username)) {
            error_register('Please specify a username');
            return crm_url('members&tab=import');
        }
        // Check for duplicate usernames
        if (!empty($username)) {
            // Check whether username is in use
            $test_username = $username;
            $esc_test_username = mysqli_real_escape_string($db_connect, $test_username);
            $sql = "
                SELECT *
                FROM `user`
                WHERE `username`='$esc_test_username'
            ";
            $res = mysqli_query($db_connect, $sql);
            if (!$res) crm_error(mysqli_error($res));
            $username_row = mysqli_fetch_assoc($res);
            if (!$username_row) {
                $username = $test_username;
            } else {
                error_register('Username already in use, please specify a different username');
                return crm_url('members&tab=import');
            }
        }
        // Check for duplicate email addresses
        $email = $row['email'];
        if (!empty($email)) {
            // Check whether email address is in use
            $test_email = $email;
            $esc_test_email = mysqli_real_escape_string($db_connect, $test_email);
            $sql = "
                SELECT *
                FROM `contact`
                WHERE `email`='$esc_test_email'
            ";
            $res = mysqli_query($db_connect, $sql);
            if (!$res) crm_error(mysqli_error($res));
            $email_row = mysqli_fetch_assoc($res);
            if (!$email_row) {
                $email = $test_email;
            } else {
                error_register('Email address already in use');
                error_register('Please specify a different email address');
                return crm_url('members&tab=import');
            }
        }
        // Build contact object
        $contact = array(
            'firstName' => $row['firstname']
            , 'middleName' => $row['middlename']
            , 'lastName' => $row['lastname']
            , 'email' => $email
            , 'phone' => $row['phone']
        );
        // Add user fields
        $user = array('username' => $username);
        $contact['user'] = $user;
        // Add member fields
        $member = array(
            'member' => $member
            , 'address1' => $row['address1']
            , 'address2' => $row['address2']
            , 'address3' => $row['address3']
            , 'town_city' => $row['town_city']
            , 'zipcode' => $row['zipcode']
        );
        $contact['member'] = $member;
        // Save to database
        $contact = contact_save($contact);
        $esc_cid = mysqli_real_escape_string($db_connect, $cid);
        // Notify admins
        $from = get_org_name() . " <" . get_email_from() . ">";
        $headers = "From: $from\r\nContent-Type: text/html; charset=ISO-8859-1\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        if (!empty(get_email_to())) {
            $name = theme_contact_name($contact['cid']);
            $content = theme('member_created_email', $contact['cid']);
            mail(get_email_to(), "New Member: $name", $content, $headers);
        }
        // Notify user
        $confirm_url = user_reset_password_url($user['username']);
        $content = theme('member_welcome_email', $user['cid'], $confirm_url);
        mail($email, "Welcome to " . get_org_name(), $content, $headers);
    }
    return crm_url('members');
}

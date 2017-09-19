<?php

/**
 * Handle member add request.
 *
 * @return The url to display when complete.
 */
function command_member_add () {
    global $db_connect;
    global $esc_post;
    global $config_org_name;
    global $config_email_to;
    global $config_email_from;
    
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
        $sql = "SELECT * FROM `user` WHERE `username`='$esc_test_name'";
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
    
    // Build contact object
    $contact = array(
        'firstName' => $_POST['firstName']
        , 'middleName' => $_POST['middleName']
        , 'lastName' => $_POST['lastName']
        , 'email' => $_POST['email']
        , 'phone' => $_POST['phone']
    );
    
    // Add user fields
    $user = array('username' => $username);
    $contact['user'] = $user;
    
    // Add member fields
    $member = array(
        'member' => $member
    );
    $contact['member'] = $member;
    
    // Save to database
    $contact = contact_save($contact);
    
    $esc_cid = mysqli_real_escape_string($db_connect, $contact['cid']);
    
    // Notify admins
    $from = "\"$config_org_name\" <$config_email_from>";
    $headers = "From: $from\r\nContent-Type: text/html; charset=ISO-8859-1\r\n";
    if (!empty($config_email_to)) {
        $name = theme_contact_name($contact['cid']);
        $content = theme('member_created_email', $contact['cid']);
        mail($config_email_to, "New Member: $name", $content, $headers);
    }
    
    // Notify user
    $confirm_url = user_reset_password_url($contact['user']['username']);
    $content = theme('member_welcome_email', $contact['user']['cid'], $confirm_url);
    mail($_POST['email'], "Welcome to $config_org_name", $content, $headers);
    
    return crm_url("contact&cid=$esc_cid");
}

/**
 * Handle member filter request.
 *
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
 *
 * @return The url to display on completion.
 */
function command_member_import () {
    global $db_connect;
    global $config_org_name;
    global $config_email_to;
    global $config_email_from;
    
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
            $sql = "SELECT * FROM `user` WHERE `username`='$esc_test_name'";
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
        
        // Build contact object
        $contact = array(
            'firstName' => $row['firstname']
            , 'middleName' => $row['middlename']
            , 'lastName' => $row['lastname']
            , 'email' => $row['email']
            , 'phone' => $row['phone']
        );
        
        // Add user fields
        $user = array('username' => $username);
        $contact['user'] = $user;
        
        // Add member fields
        $member = array(
            'member' => $member
        );
        $contact['member'] = $member;
        
        // Save to database
        $contact = contact_save($contact);
        
        $esc_cid = mysqli_real_escape_string($db_connect, $cid);
        
        // Notify admins
        $from = "\"$config_org_name\" <$config_email_from>";
        $headers = "From: $from\r\nContent-Type: text/html; charset=ISO-8859-1\r\n";
        if (!empty($config_email_to)) {
            $name = theme_contact_name($contact['cid']);
            $content = theme('member_created_email', $contact['cid']);
            mail($config_email_to, "New Member: $name", $content, $headers);
        }
        
        // Notify user
        $confirm_url = user_reset_password_url($user['username']);
        $content = theme('member_welcome_email', $user['cid'], $confirm_url);
        mail($email, "Welcome to $config_org_name", $content, $headers);
    }
    
    return crm_url('members');
}

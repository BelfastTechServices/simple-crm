<?php

/**
 * @return The themed html for an active member email report.
 */
function theme_member_email_report ($opts) {
    $output = '<div class="member-email-report">';
    $title = '';
    if (isset($opts['filter']) && isset($opts['filter']['active'])) {
        $title = $opts['filter']['active'] ? 'Active ' : 'Lapsed ';
    }
    $title .= 'Email Report';
    $output .= "<h2>$title</h2>";
    $output .= '<textarea rows="10" cols="80">';
    $output .= member_email_report($opts);
    $output .= '</textarea>';
    $output .= '</div>';
    return $output;
}

/**
 * Return the text of an email notifying administrators that a user has been created.
 * @param $cid The contact id of the new member.
 */
function theme_member_created_email ($cid) {
    // Get info on the logged in user
    $data = contact_data(array('cid'=>user_id()));
    $admin = $data[0];
    $adminName = theme_contact_name($admin['cid']);
    // Get info on member
    $data = member_data(array('cid'=>$cid));
    $member = $data[0];
    $contact = $member['contact'];
    $name = theme_contact_name($contact['cid']);
    $output = "<p>Contact info:<br/>\n";
    $output .= "Name: $name<br/>\n";
    $output .= "Email: $contact[email]<br/>\n";
    $output .= "Phone: $contact[phone]\n</p>\n";
    if (user_id()) {
        $output .= "<p>Entered by: $adminName</p>\n";
    } else {
        $output .= "<p>User self-registered</p>\n";
    }
    return $output;
}

/**
 * Return the text of an email welcoming a new member.
 * @param $cid The contact id of the new member.
 * @param $confirm_url The url for the new user to confirm their email.
 */
function theme_member_welcome_email ($cid, $confirm_url) {
    $contact = crm_get_one('contact', array('cid'=>$cid));
    $vars = array(
        'type' => 'welcome'
        , 'confirm_url' => $confirm_url
        , 'username' => $contact['user']['username']
    );
    return template_render('email', $vars);
}

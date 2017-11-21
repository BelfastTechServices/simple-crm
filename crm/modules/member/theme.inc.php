<?php

/**
 * Return the themed html for a table of members.
 *
 * @param $opts Options to pass to member_data().
 * @return The themed html string.
*/
function theme_member_table ($opts = NULL) {
    return theme('table', crm_get_table('member', $opts));
}

/**
 * Return the themed html for a single member's contact info.
 * 
 * @param $opts The options to pass to member_contact_data().
 * @return The themed html string.
*/
function theme_member_contact_table ($opts = NULL) {
    return theme('table_vertical', crm_get_table('member_contact', $opts));
}

/**
 * Returned the themed html for edit member form.
 *
 * @param $cid The cid of the member to edit.
 * @return The themed html.
*/
function theme_member_edit_form ($cid) {
    return theme('form', crm_get_form('member_edit', $cid));
}

/**
 * @return The themed html for a member filter form.
*/
function theme_member_filter_form () {
    return theme('form', crm_get_form('member_filter'));
}

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
 * Return the themed html for a contact's name.
 *
 * @param $cid The cid of the contact.
 * @return The themed html string.
 */
function theme_member_contact_name ($cid) {
    
    // Get member data
    $data = member_data(array('cid' => $cid));
    if (count($data) < 1) {
        return '';
    }
    
    $output = member_name(
        $data[0]['contact']['firstName']
        , $data[0]['contact']['middleName']
        , $data[0]['contact']['lastName']
    );
    return $output;
}

/**
 * Return the text of an email notifying administrators that a user has been created.
 * @param $cid The contact id of the new member.
 */
function theme_member_created_email ($cid) {
    
    // Get info on the logged in user
    $data = member_contact_data(array('cid'=>user_id()));
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
    if (user_id()){
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

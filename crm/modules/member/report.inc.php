<?php

/**
 * @return A comma-separated list of user emails.
 * @param $opts - Options to pass to member_data().
 */
function member_email_report ($opts) {
    $result = array();
    $data = member_data($opts);
    foreach ($data as $row) {
        $email = trim($row['contact']['email']);
        if (!empty($email)) {
            $result[] = $email;
        }
    }
    return join($result, ', ');
}

<?php

/**
 * Install or upgrade this module.
 * @param $old_revision The last installed revision of this module, or 0 if the
 *   module has never been installed.
 */
function member_install($old_revision = 0) {
    global $db_connect;
    if ($old_revision < 1) {
        // Create member table
        $sql = '
            CREATE TABLE IF NOT EXISTS `member` (
              `cid` mediumint(8) unsigned NOT NULL,
              PRIMARY KEY (`cid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ';
        $res = mysqli_query($db_connect, $sql);
        if (!$res) crm_error(mysqli_error($res));
        // Create default permissions
        $roles = array(
            '1' => 'authenticated'
            , '2' => 'member'
            , '3' => 'webAdmin'
        );
        $default_perms = array(
            'member' => array('member_view')
            , 'webAdmin' => array('member_list', 'member_view', 'member_add', 'member_edit', 'member_delete')
        );
        foreach ($roles as $rid => $role) {
            if (array_key_exists($role, $default_perms)) {
                foreach ($default_perms[$role] as $perm) {
                    $sql = "INSERT INTO `role_permission` (`rid`, `permission`) VALUES ('$rid', '$perm')";
                    $res = mysqli_query($db_connect, $sql);
                    if (!$res) crm_error(mysqli_error($res));
                }
            }
        }
    }
}

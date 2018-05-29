<?php

/**
 * @return This module's revision number.  Each new release should increment
 * this number.
 */
function core_revision () {
    return 1;
}

/**
 * Install or upgrade this module.
 * @param $old_revision The last installed revision of this module, or 0 if the
 *   module has never been installed.
 */
function core_install ($old_revision = 0) {
    global $db_connect;
    if ($old_revision < 1) {
        $sql = 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";';
        $res = mysqli_query($db_connect, $sql);
        if (!$res) crm_error(mysqli_error($res));
        
        $sql = '
            CREATE TABLE IF NOT EXISTS `module` (
                `did` MEDIUMINT(8) unsigned NOT NULL AUTO_INCREMENT
                , `name` VARCHAR(255) NOT NULL
                , `revision` MEDIUMINT(8) unsigned NOT NULL
                , PRIMARY KEY (`did`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ';
        $res = mysqli_query($db_connect, $sql);
        if (!$res) crm_error(mysqli_error($res));
    }
}

/**
 * @return An array of the permissions provided by this module.
 */
function core_permissions () {
    $permissions = array_merge(
        module_permissions()
        , array('report_view')
    );
    return $permissions;
}

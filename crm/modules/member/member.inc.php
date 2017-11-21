<?php

/**
 * @return This module's revision number.  Each new release should increment
 * this number.
 */
function member_revision () {
    return 1;
}

/**
 * @return Array of paths to stylesheets relative to this module's directory.
 */
function member_stylesheets () {
    return array('style.css');
}

/**
 * @return An array of the permissions provided by this module.
 */
function member_permissions () {
    return array(
        'member_list'
        , 'member_view'
        , 'member_add'
        , 'member_edit'
        , 'member_delete'
    );
}

// Installation functions //////////////////////////////////////////////////////
require_once('install.inc.php');

// Utility functions ///////////////////////////////////////////////////////////
require_once('utility.inc.php');

// DB to Object mapping ////////////////////////////////////////////////////////
require_once('data.inc.php');

// Table data structures ///////////////////////////////////////////////////////
require_once('table.inc.php');

// Forms ///////////////////////////////////////////////////////////////////////
require_once('form.inc.php');

// Request Handlers ////////////////////////////////////////////////////////////
require_once('command.inc.php');

// Member pages ////////////////////////////////////////////////////////////////
require_once('page.inc.php');

// Member reports //////////////////////////////////////////////////////////////
require_once('report.inc.php');

// Themeing ////////////////////////////////////////////////////////////////////
require_once('theme.inc.php');

?>
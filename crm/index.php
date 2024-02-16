<?php

// Save path of directory containing index.php
$crm_root = dirname(__FILE__);

// Bootstrap the site
require_once('include/crm.inc.php');

// Check for GET/POST command
$post_command = array_key_exists('command', $_POST) ? $_POST['command'] : '';
$get_command = array_key_exists('command', $_GET) ? $_GET['command'] : '';
$command = !empty($post_command) ? $post_command : $get_command;

if (!empty($command)) {
    // Handle command and redirect
    header('Location: ' . command($command));
    die();
}

if(isset($_GET['q'])) {
    if ($_GET['q'] == 'logout') {
        session_destroy();
    }
}

$template_vars = array('path' => path());
if (!user_id()) {
    if (path() != 'login' && path() != 'reset' && path() != 'reset-confirm' && path() != 'register') {
        $template_vars = array('path' => 'login');
    }
}
print template_render('page', $template_vars);

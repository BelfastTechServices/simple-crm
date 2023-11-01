<?php

// Save path of directory containing index.php
$crm_root = dirname(__FILE__);

// Bootstrap the site
include('include/crm.inc.php');

$handler = $_GET['command'] . '_autocomplete';
if (function_exists($handler)) {
    print json_encode(call_user_func($handler, $_GET['term']));
}

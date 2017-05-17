<?php

// Save path of directory containing index.php
$crm_root = dirname(__FILE__);

// Bootstrap the site
require_once('include/crm.inc.php');

if (!user_id()) {
    crm_error("ERROR: User not logged in");
}

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="export.csv"');
print theme('table_csv', $_GET['name'], json_decode($_GET['opts'], true));

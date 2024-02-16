<?php

// Save path of directory containing index.php
$crm_root = dirname(__FILE__);

// Bootstrap the site
require_once('include/crm.inc.php');

$template_vars = array('path' => 'install');
print template_render('page', $template_vars);

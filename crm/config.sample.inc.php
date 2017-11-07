<?php

// Database configuration
$config_db_host = 'localhost';
$config_db_user = 'simple-crm';
$config_db_password = '';
$config_db_db = 'simple-crm';

// Site info

// The title to display in the title bar
$config_site_title = 'Simple CRM';

// The name of the organization to insert into templates
$config_org_name = 'Belfast Tech Services';

// The organization's website address
$config_org_website = 'www.belfasttechservices.co.uk';

// The From: address to use when sending email to members
$config_email_from = 'customerservice@belfasttechservices.co.uk';

// The email address to notify when a user is created
$config_email_to = 'chris@belfasttechservices.co.uk';

// The hostname of the server
$config_host = $_SERVER['SERVER_NAME'];

// The url path of the app directory
$config_base_path = '/';

// The name of the theme you want to use
// (currently there is only one, "inspire".)
$config_theme = "inspire";

// Base modules
$config_modules = array(
    "contact",
    "user",
    "variable",
    "member"
);

// Optional modules, uncomment to enable

// Assign a profile picture using gravatar
$config_modules[] = "profile_picture";

// Developer tools
//$config_modules[] = "devel";

// User self-registration
$config_modules[] = "register";

// Links to show in the main menu
$config_links = array(
    '<front>' => 'Home'
    , 'members' => 'Members'
    , 'permissions' => 'Permissions'
    , 'upgrade' => 'Upgrade'
);

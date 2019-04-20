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

// The currency code for dealing with payments, can be GBP, USD, or EUR
$config_currency_code = 'GBP';

// The From: address to use when sending email to users
$config_email_from = 'customerservice@belfasttechservices.co.uk';

// The email address to notify when a user is created
$config_email_to = 'chris@belfasttechservices.co.uk';

// The postal address of the space (used when sending out new member emails)
$config_address1 = '';
$config_address2 = '';
$config_address3 = '';
$config_town_city = '';
$config_zipcode = '';

// The hostname of the server
$config_host = $_SERVER['SERVER_NAME'];

// The url path of the app directory
$config_base_path = '/';

// Github username & repo used to construct footer
$config_github_username = 'chris18890';
$config_github_repo = 'simple-crm';

// The name of the theme you want to use
// (currently there is only one, "inspire".)
$config_theme = "inspire";

// Base modules
$config_modules = array(
    "contact"
    , "user"
    , "variable"
    , "member"
);

// Optional modules, uncomment to enable

// Assign a profile picture using gravatar
$config_modules[] = "profile_picture";

// Developer tools
//$config_modules[] = "devel";

// User self-registration
$config_modules[] = "register";

// Email list management
$config_modules[] = "email_list";

// Links to show in the main menu
$config_links = array(
    '<front>' => 'Home'
    , 'members' => 'Members'
    , 'email_lists' => 'Email Lists'
    , 'permissions' => 'Permissions'
    , 'upgrade' => 'Upgrade'
);

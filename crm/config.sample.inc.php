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

// The postal address of the organization (used when sending out new user emails)
$config_address1 = 'Unit 1 Weavers Court';
$config_address2 = 'Linfield Road';
$config_address3 = '';
$config_town_city = 'Belfast';
$config_zipcode = 'BT12 5GH';

// The hostname of the server
$config_host = $_SERVER['SERVER_NAME'];

// The url path of the app directory
$config_base_path = '/';

// Github username & repo used to construct footer
$config_github_username = 'BelfastTechServices';
$config_github_repo = 'simple-crm';

// sets the protocol for URLs in outgoing emails, can be set to http or https
$config_protocol_security = 'https';

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

// Track user meta data
$config_modules[] = "user_meta";

// Assign a profile picture using gravatar
$config_modules[] = "profile_picture";

// Assign members a mentor
$config_modules[] = "mentor";

// Email list management
$config_modules[] = "email_list";

// Links to show in the main menu
$config_links = array(
    '<front>' => 'Home'
    , 'members' => 'Members'
    , 'user_metas' => 'User Meta Data'
    , 'email_lists' => 'Email Lists'
    , 'permissions' => 'Permissions'
    , 'global_options' => 'Global Options'
    , 'upgrade' => 'Upgrade'
);

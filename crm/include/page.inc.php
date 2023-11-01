<?php

/**
 * @return An array of pages provided by this module.
 */
function core_page_list () {
    $pages = array();
    $pages[] = '<front>';
    $pages[] = 'install';
    $pages[] = 'register';
    $pages[] = 'login';
    $pages[] = 'reset';
    $pages[] = 'reset-confirm';
    $pages[] = 'delete';
    if (user_access('report_view')) {
        $pages[] = 'reports';
    }
    if (user_access('user_permissions_edit')) {
        $pages[] = 'permissions';
    }
    if (user_access('global_options_view')) {
        $pages[] = 'global_options';
    }
    if (user_access('module_upgrade')) {
        $pages[] = 'upgrade';
    }
    return $pages;
}

/**
 * Page hook. Adds member module content to a page before it is rendered.
 * @param &$page_data Reference to data about the page being rendered.
 * @param $page_name The name of the page being rendered.
 * @param $options The array of options passed to theme('page').
 */
function core_page (&$page_data, $page_name, $options) {
    $latestNews = '<p>Welcome to ' . title() . ' version ' . crm_version() . '!</p>';
    // Modify this variable with valid HTML between the apostrophes to display update text to users on login
    $latestNews = $latestNews . '
        <p></p>
    ';
    switch ($page_name) {
        case '<front>':
            page_add_content_top($page_data, $latestNews);
            break;
        case 'install':
            page_add_content_top($page_data, theme('form', crm_get_form('module_install')));
            break;
        case 'login':
            page_add_content_top($page_data, theme('form', crm_get_form('login')));
            break;
        case 'reset':
            page_add_content_top($page_data, theme('form', crm_get_form('user_reset_password')));
            break;
        case 'reset-confirm':
            page_add_content_top($page_data, theme('form', crm_get_form('user_reset_password_confirm', $_GET['v'])));
            break;
        case 'delete':
            page_add_content_top($page_data, theme('form', crm_get_form('delete', $_GET['type'], $_GET['id'])));
            break;
        case 'reports':
            if (user_access('report_view')) {
                page_set_title($page_data, 'Reports');
            }
            break;
        case 'permissions':
            if (user_access('user_permissions_edit')) {
                page_set_title($page_data, 'Permissions');
                page_add_content_top($page_data, theme('form', crm_get_form('user_permissions')));
            }
            break;
        case 'global_options':
            if (user_access('global_options_view')) {
                page_set_title($page_data, 'Global Options');
                page_add_content_top($page_data, theme('form', crm_get_form('global_options')));
            }
            break;
        case 'upgrade':
            if (user_access('module_upgrade')) {
                page_set_title($page_data, 'Upgrade Modules');
                $content = theme('table', crm_get_table('module_upgrade'));
                $content .= theme('form', crm_get_form('module_upgrade'));
                page_add_content_top($page_data, $content);
            }
    }
}

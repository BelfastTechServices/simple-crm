<?php

/**
 * @return An array of pages provided by this module.
 */
function member_page_list () {
    $pages = array();
    if (user_access('member_list')) {
        $pages[] = 'members';
    }
    return $pages;
}

/**
 * Page hook.  Adds member module content to a page before it is rendered.
 *
 * @param &$page_data Reference to data about the page being rendered.
 * @param $page_name The name of the page being rendered.
 * @param $options The array of options passed to theme('page').
*/
function member_page (&$page_data, $page_name, $options) {
    
    switch ($page_name) {
        
        case 'members':
            
            // Set page title
            page_set_title($page_data, 'Members');
            
            // Add view tab
            if (user_access('member_list')) {
                $view = theme('table', crm_get_table('member', $opts));
                page_add_content_top($page_data, $view, 'View');
            }
            
            // Add add tab
            if (user_access('member_add')) {
                page_add_content_top($page_data, theme('form', crm_get_form('member_add')), 'Add');
            }
            
            // Add import tab
            if (user_access('contact_add') && user_access('user_add') && user_access('member_add')) {
                page_add_content_top($page_data, theme('form', crm_get_form('member_import')), 'Import');
            }
            
            break;
        
        case 'contact':
            
            // Capture member id
            $cid = $_GET['cid'];
            if (empty($cid)) {
                return;
            }
            
            // Add role tab
            if (user_access('member_edit')) {
                $roles = theme('form', crm_get_form('user_role_edit', $cid));
                page_add_content_top($page_data, $roles, 'Roles');
            }
            
            break;
    }
}


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
 * Page hook. Adds member module content to a page before it is rendered.
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
            // Add view tab
            $view_content = '';
            if (user_id() == $_GET['cid'] || ((user_access('contact_edit') && user_access('member_edit')))) {
                $view_content .= '<h3>Member Details</h3>';
                $view_content .= theme('table_vertical', crm_get_table('member_details', array('cid' => $cid)));
                page_add_content_bottom($page_data, $view_content, 'View');
            }
            // Add edit tab
            if (user_id() == $_GET['cid'] || ((user_access('contact_edit') && user_access('member_edit')))) {
                $edit = theme('form', crm_get_form('member_edit', $cid), 'Edit Member Details');
                page_add_content_bottom($page_data, $edit, 'Edit');
            }
            break;
        case 'reports':
            if (user_access('member_view')) {
                if (user_access('member_list')) {
                    $reports = theme('member_email_report', array('filter'=>array('active'=>true)));
                    $reports .= theme('member_email_report', array('filter'=>array('active'=>false)));
                }
                page_add_content_bottom($page_data, $reports);
            }
            break;
    }
}

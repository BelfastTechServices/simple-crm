<?php

/**
 * @return This module's revision number. Each new release should increment
 * this number.
 */
function devel_revision () {
    return 1;
}

/**
 * Page hook. Adds module content to a page before it is rendered.
 * @param &$page_data Reference to data about the page being rendered.
 * @param $page_name The name of the page being rendered.
 */
function devel_page (&$page_data, $page_name) {
    switch ($page_name) {
        case 'contact':
            // Capture contact id
            $cid = $_GET['cid'];
            if (empty($cid)) {
                return;
            }
            if (!user_access('contact_view')) {
                return;
            }
            $contact = crm_get_one('contact', array('cid'=>$cid));
            // Add devel tab
            page_add_content_top($page_data, '<pre>' . print_r($contact, true) . '</pre>', 'Devel');
            break;
    }
}

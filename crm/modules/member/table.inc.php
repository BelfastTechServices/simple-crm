<?php

/**
 * Return a table structure representing members.
 *
 * @param $opts Options to pass to member_data().
 * @return The table structure.
*/
function member_table ($opts = NULL) {
    
    // Ensure user is allowed to view members
    if (!user_access('member_view')) {
        return NULL;
    }
    
    // Determine settings
    $export = false;
    foreach ($opts as $option => $value) {
        switch ($option) {
            case 'export':
                $export = $value;
                break;
        }
    }
    
    // Get member data
    $members = member_data($opts);
    
    // Create table structure
    $table = array(
        'id' => ''
        , 'class' => ''
        , 'rows' => array()
    );
    
    // Add columns
    $table['columns'] = array();
    
    if (user_access('member_view')) {
        if ($export) {
            $table['columns'][] = array('title'=>'Contact ID','class'=>'');
            $table['columns'][] = array('title'=>'Last','class'=>'');
            $table['columns'][] = array('title'=>'First','class'=>'');
            $table['columns'][] = array('title'=>'Middle','class'=>'');
        } else {
            $table['columns'][] = array('title'=>'Name','class'=>'');
        }
        $table['columns'][] = array('title'=>'E-Mail','class'=>'');
        $table['columns'][] = array('title'=>'Phone','class'=>'');
    }
    // Add ops column
    if (!$export && (user_access('member_edit') || user_access('member_delete'))) {
        $table['columns'][] = array('title'=>'Ops','class'=>'');
    }
    
    // Loop through member data
    foreach ($members as $member) {
        
        // Add user data
        $row = array();
        if ((user_access('member_view') && $member['contact']['cid'] == user_id()) || user_access('member_list')) {
            
            // Construct name
            $contact = $member['contact'];
            $name_link = theme('contact_name', $contact, true);
            
            // Add cells
            if ($export) {
                $row[] = $member['contact']['cid'];
                $row[] = $member['contact']['lastName'];
                $row[] = $member['contact']['firstName'];
                $row[] = $member['contact']['middleName'];
            } else {
                $row[] = $name_link;
            }
            $row[] = $member['contact']['email'];
            $row[] = $member['contact']['phone'];
            // Construct ops array
            $ops = array();
            
            // Add edit op
            if (user_access('member_edit')) {
                $ops[] = '<a href=' . crm_url('contact&cid=' . $member['cid'] . '&tab=edit') .'>edit</a>';
            }
            
            // Add delete op
            if (user_access('member_delete')) {
                $ops[] = '<a href=' . crm_url('delete&type=contact&amp;id=' . $member['cid']) . '>delete</a>';
            }
            
            // Add ops row
            if (!$export && (user_access('member_edit') || user_access('member_delete'))) {
                $row[] = join(' ', $ops);
            }
            
            // Add row to table
            $table['rows'][] = $row;
        }
    }
    
    // Return table
    return $table;
}

/**
 * Return a table structure representing contact info.
 *
 * @param $opts Options to pass to member_contact_data().
 * @return The table structure.
*/
function member_contact_table ($opts) {
    
    // Get contact data
    $data = member_contact_data($opts);
    $contact = $data[0];
    if (empty($contact) || count($contact) < 1) {
        return array();
    }
    
    // Initialize table
    $table = array(
        "id" => ''
        , "class" => ''
        , "rows" => array()
        , "columns" => array()
    );
    
    // Add columns
    $table['columns'][] = array("title"=>'Name', 'class'=>'', 'id'=>'');
    $table['columns'][] = array("title"=>'Email', 'class'=>'', 'id'=>'');
    $table['columns'][] = array("title"=>'Phone', 'class'=>'', 'id'=>'');
    
    // Add row
    $table['rows'][] = array(
        theme('contact_name', $contact)
        , $contact['email']
        , $contact['phone']
    );
    
    return $table;
}

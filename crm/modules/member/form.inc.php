<?php

// Members /////////////////////////////////////////////////////////////////////

/**
 * @return The form structure for adding a member.
*/
function member_add_form () {
    
    // Ensure user is allowed to add members
    if (!user_access('member_add')) {
        error_register('Permission denied: member_add');
        return NULL;
    }
    
    // Start with contact form
    $form = crm_get_form('contact');
    
    // Generate default start date, first of current month
    $start = date("Y-m-d");
    
    // Change form command
    $form['command'] = 'member_add';
    
    // Add member data
    $form['fields'][] = array(
        'type' => 'fieldset',
        'label' => 'User Info',
        'fields' => array(
            array(
                'type' => 'text',
                'label' => 'Username',
                'name' => 'username'
            )
        )
    );
    return $form;
}

// Filters /////////////////////////////////////////////////////////////////////

/**
 * Return the form structure for a member filter.
 * 
 * @return The form structure.
*/
function member_filter_form () {

    // Available filters    
    $filters = array(
        'all' => 'All',
        'active' => 'Active'
    );
    
    // Default filter
    $selected = empty($_SESSION['member_filter_option']) ? 'all' : $_SESSION['member_filter_option'];
    
    // Construct hidden fields to pass GET params
    $hidden = array();
    foreach ($_GET as $key => $val) {
        $hidden[$key] = $val;
    }
    
    $form = array(
        'type' => 'form',
        'method' => 'get',
        'command' => 'member_filter',
        'hidden' => $hidden,
        'fields' => array(
            array(
                'type' => 'fieldset',
                'label' => 'Filter',
                'fields' => array(
                    array(
                        'type' => 'select',
                        'name' => 'filter',
                        'options' => $filters,
                        'selected' => $selected
                    ),
                    array(
                        'type' => 'submit',
                        'value' => 'Filter'
                    )
                )
            )
        )
    );
    
    return $form;
}

// Imports /////////////////////////////////////////////////////////////////////

/**
 * @return the form structure for a member import form.
*/
function member_import_form () {
    return array(
        'type' => 'form'
        , 'method' => 'post'
        , 'enctype' => 'multipart/form-data'
        , 'command' => 'member_import'
        , 'fields' => array(
            array(
                'type' => 'message'
                , 'value' => '<p>To import members, upload a csv.  The csv should have a header row with the following fields:</p>
                <ul>
                <li>First Name</li>
                <li>Middle Name</li>
                <li>Last Name</li>
                <li>Email</li>
                <li>Phone</li>
                <li>Username</li>
                </ul>'
            )
            , array(
                'type' => 'file'
                , 'label' => 'CSV File'
                , 'name' => 'member-file'
            )
            , array(
                'type' => 'submit'
                , 'value' => 'Import'
            )
        )
    );
}

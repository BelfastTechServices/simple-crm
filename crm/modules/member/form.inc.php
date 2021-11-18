<?php

// Members /////////////////////////////////////////////////////////////////////

/**
 * @return The form structure for adding a member.
 */
function member_add_form () {
    // Start with contact form
    $form = crm_get_form('contact');
    // Generate default start date, first of current month
    $start = date("Y-m-d");
    // Change form command
    $form['command'] = 'member_add';
    // Add member data
    $form['fields'][] = array(
        'type' => 'fieldset'
        , 'label' => 'User Info'
        , 'fields' => array(
            array(
                'type' => 'text'
                , 'label' => 'Username'
                , 'name' => 'username'
            )
        )
    );
    $form['fields'][] = array(
        'type' => 'fieldset'
        , 'label' => 'Member Address Details'
        , 'fields' => array(
            array(
                'type' => 'text'
                , 'label' => 'Address 1'
                , 'name' => 'address1'
            )
            , array(
                'type' => 'text'
                , 'label' => 'Address 2'
                , 'name' => 'address2'
            )
            , array(
                'type' => 'text'
                , 'label' => 'Address 3'
                , 'name' => 'address3'
            )
            , array(
                'type' => 'text'
                , 'label' => 'Town/City'
                , 'name' => 'town_city'
            )
            , array(
                'type' => 'text'
                , 'label' => 'Zip/Postal Code'
                , 'name' => 'zipcode'
            )
        )
    );
    return $form;
}

/**
 * @return The form structure for editing a member.
 */
function member_edit_form ($cid) {
    // Create form
    if ($cid) {
        $memb_data = crm_get_data('member', array('cid'=>$cid));
        $member = $memb_data[0]['member'];
    }
    $form = array(
        'type' => 'form'
        , 'method' => 'post'
        , 'command' => 'member_edit'
        , 'hidden' => array(
            'cid' => $cid
        )
        , 'fields' => array()
        , 'submit' => 'Update'
    );
    // Edit member data
    $form['fields'][] = array(
        'type' => 'fieldset'
        , 'label' => 'Edit Member Address Details'
        , 'fields' => array(
            array(
                'type' => 'text'
                , 'label' => 'Address 1'
                , 'name' => 'address1'
                , 'value' => $member['address1']
            )
            , array(
                'type' => 'text'
                , 'label' => 'Address 2'
                , 'name' => 'address2'
                , 'value' => $member['address2']
            )
            , array(
                'type' => 'text'
                , 'label' => 'Address 3'
                , 'name' => 'address3'
                , 'value' => $member['address3']
            )
            , array(
                'type' => 'text'
                , 'label' => 'Town/City'
                , 'name' => 'town_city'
                , 'value' => $member['town_city']
            )
            , array(
                'type' => 'text'
                , 'label' => 'Zip/Postal Code'
                , 'name' => 'zipcode'
                , 'value' => $member['zipcode']
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
                <li>Address 1</li>
                <li>Address 2</li>
                <li>Address 3</li>
                <li>Town/City</li>
                <li>Zip/Postal Code</li>
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

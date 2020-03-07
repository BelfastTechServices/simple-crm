<?php

/**
 * Return the form structure for a delete confirmation form.
 * @param $type The type of element to delete.
 * @param $id The id of the element to delete.
 * @return The form structure.
 */
function delete_form ($type, $id) {
    $function = $type . '_delete_form';
    if (function_exists($function)) {
        return $function($id);
    }
    return array();
}

/**
 * Return the form structure for a filtering form.
 * @param $filters Array of filter keys and labels.
 * @param $default The default filter.
 * @param $action URL to submit to.
 * @param $get HTTP GET params to pass.
 * @return The form structure.
 */
function filter_form ($filters, $selected, $action, $get) {
    // Construct hidden fields to pass GET params
    $hidden = array();
    foreach ($get as $key => $val) {
        if ($key != 'filter') {
            $hidden[$key] = $val;
        }
    }
    $form = array(
        'type' => 'form'
        , 'method' => 'get'
        , 'action' => $action
        , 'hidden' => $hidden
        , 'fields' => array(
            array(
                'type' => 'fieldset'
                , 'label' => 'Filter'
                , 'fields' => array(
                    array(
                        'type' => 'select'
                        , 'name' => 'filter'
                        , 'options' => $filters
                        , 'selected' => $selected
                    )
                    , array(
                        'type' => 'submit'
                        , 'value' => 'Filter'
                    )
                )
            )
        )
    );
    return $form;
}

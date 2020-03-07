<?php

/**
 * Register an error.
 * @param $error The error.
 */
function error_register ($error) {
    $_SESSION['errorList'][] = $error;
}

/**
 * Register a message.
 * @param $message The message.
 */
function message_register ($message) {
    $_SESSION['messageList'][] = $message;
}

/**
 * Return all registered errors and clear the list.
 * @return An array of error strings.
 */
function error_list () {
    $errors = $_SESSION['errorList'];
    $_SESSION['errorList'] = array();
    return $errors;
}

/**
 * Return all registered messages and clear the list.
 * @return An array of message strings.
 */
function message_list () {
    $errors = $_SESSION['messageList'];
    $_SESSION['messageList'] = array();
    return $errors;
}

/**
 * @return The themed html string for any errors currently registered.
 */
function theme_errors () {
    // Pop and check errors
    $errors = error_list();
    if (empty($errors)) {
        return '';
    }
    $output = '<fieldset><ul>';
    // Loop through errors
    foreach ($errors as $error) {
        $output .= '<li>' . $error . '</li>';
    }
    $output .= '</ul></fieldset>';
    return $output;
}

/**
 * @return The themed html string for any registered messages.
 */
function theme_messages () {
    // Pop and check messages
    $messages = message_list();
    if (empty($messages)) {
        return '';
    }
    $output = '<fieldset><ul>';
    // Loop through errors
    foreach ($messages as $message) {
        $output .= '<li>' . $message . '</li>';
    }
    $output .= '</ul></fieldset>';
    return $output;
}

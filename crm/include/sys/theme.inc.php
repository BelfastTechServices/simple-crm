<?php

/**
 * @return the path to the theme folder without leading or trailing slashes.
 */
function path_to_theme() {
    if (strlen(get_theme()) > 0) {
        return "themes/" . get_theme();
    } else {
        return 'themes/inspire';
    }
}

/**
 * Map theme calls to appropriate theme handler.
 * At least one parameter is required, namely the element being themed.
 * Additional parameters will be passed on to the theme handler.
 * @param $element The element to theme.
 * @return The themed html string for the specified element.
 */
function theme () {
    // Check for arguments
    if (func_num_args() < 1) {
        return "";
    }
    $args = func_get_args();
    // Construct handler name
    $element = $args[0];
    $handler = 'theme_' . $element;
    // Construct handler arguments
    $handler_args = array();
    for ($i = 1; $i < count($args); $i++) {
        $handler_args[] = $args[$i];
    }
    // Check for undefined handler
    if (!function_exists($handler)) {
        return "";
    }
    return call_user_func_array($handler, $handler_args);
}

/**
 * @return Themed html for script includes.
 */
function theme_scripts () {
    global $core_scripts;
    $output = '';
    foreach ($core_scripts as $script) {
        $output .= '<script type="text/javascript" src="' . $script . '"></script>';
    }
    return $output;
}

/**
 * @return Themed html for stylesheet includes.
 */
function theme_stylesheets () {
    global $core_stylesheets;
    $output = '';
    foreach ($core_stylesheets as $sheet) {
        $output .= '<link rel="stylesheet" type="text/css" href="' . $sheet . '"/>';
    }
    return $output;
}

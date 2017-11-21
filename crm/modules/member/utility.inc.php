<?php

/**
 * Convert first, middle, last into a single name string. Deprecated, use
 * theme_contact_name() instead.
 *
 * @param $first First name
 * @param $middle Middle name
 * @param $last Last name
 *
 * @return the name string.
 * @deprecated.
 */
function member_name ($first, $middle, $last) {
    $name = $last . ", ";
    $name .= $first;
    if (!empty($middle)) {
        $name .= ' ' . $middle;
    }
    return $name;
}

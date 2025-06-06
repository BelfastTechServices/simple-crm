<?php

/**
 * @return This module's revision number. Each new release should increment
 * this number.
 */
function profile_picture_revision () {
    return 2;
}

// Installation functions //////////////////////////////////////////////////////

/**
 * Install or upgrade this module.
 * @param $old_revision The last installed revision of this module, or 0 if the
 *   module has never been installed.
 */
function profile_picture_install ($old_revision = 0) {
    global $db_connect;
    if ($old_revision < 1) {
        // There is nothing to install. Do nothing
    }
    if ($old_revision < 2) {
        // Create a table to associate pictures with a CID
        $sql = "
            CREATE TABLE IF NOT EXISTS `profile_picture` (
                `cid` mediumint(8) unsigned NOT NULL
                , `filename` varchar(255) NOT NULL
                , PRIMARY KEY (`cid`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";
        $res = mysqli_query($db_connect, $sql);
        if (!$res) crm_error(mysqli_error($db_connect));
        // Create folder directory if it does not exist to store uploaded profile pictures in.
        if (!file_exists('./files/profile_picture')) {
            if (!mkdir('./files/profile_picture/', 0775, true)) {
                error_register('Failed to create folder. Please check folder permissions.');
            }
        }
    }
}

// Pages ///////////////////////////////////////////////////////////////////////

/**
 * @return An array of pages provided by this module.
 */
function profile_picture_page_list () {
    $pages = array();
    return $pages;
}

/**
 * Page hook. Adds profile_picture module content to a page before it is rendered.
 * @param &$page_data Reference to data about the page being rendered.
 * @param $page_name The name of the page being rendered.
 */
function profile_picture_page (&$page_data, $page_name) {
    switch ($page_name) {
        case 'contact':
            // Capture contact id
            $cid = $_GET['cid'];
            if (empty($cid)) {
                return;
            }
            $contact_data = crm_get_data('contact', array('cid'=>$cid));
            $contact = $contact_data[0];
            // Add view tab
            $view_content = '';
            if (user_access('contact_view')) {
                $view_content .= '<h3>Profile Picture</h3>';
                $opts = array(
                    'cid' => $cid
                    , 'ops' => false
                );
                $view_content .= theme('profile_picture', $contact);
            }
            //only allow users to upload photos for their OWN profile!
            // OR if they have contact_edit permission.
            if ($cid == user_id() || user_access('contact_edit')) {
                $view_content .= theme('form', crm_get_form('profile_picture_upload',  $cid));
            }
            if (!empty($view_content)) {
                page_add_content_top($page_data, $view_content, 'View');
            }
            break;
    }
}

/**
 * @return a profile picture upload form structure.
 */
function profile_picture_upload_form ($cid) {
    return array(
        'type' => 'form'
        , 'method' => 'post'
        , 'enctype' => 'multipart/form-data'
        , 'command' => 'profile_picture_upload'
        , 'hidden' => array(
            'cid' => $cid
        )
        , 'fields' => array(
            array(
                'type' => 'fieldset'
                , 'label' => 'Upload Picture'
                , 'fields' => array(
                    array(
                        'type' => 'message'
                        , 'value' => 'Use this form to upload a different profile picture'
                    )
                    , array(
                        'type' => 'file'
                        , 'label' => 'Picture'
                        , 'name' => 'profile-picture-file'
                    )
                    , array(
                        'type' => 'submit'
                        , 'value' => 'Upload'
                    )
                )
            )
        )
    );
}

/**
 * Handle profile picture upload request.
 * @return The url to display on completion.
 */
function command_profile_picture_upload () {
    global $db_connect;
    $cid = $_POST['cid'];
    if (!array_key_exists('profile-picture-file', $_FILES)) {
        error_register('No profile picture uploaded');
        return crm_url('contact&cid=' . $_POST['cid']);
    }
    //qualify file as an image that is less than 20mb
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES['profile-picture-file']['name']);
    $extension = end($temp);
    if ((($_FILES['profile-picture-file']['type'] == "image/gif")
    || ($_FILES['profile-picture-file']['type'] == "image/jpeg")
    || ($_FILES['profile-picture-file']['type'] == "image/jpg")
    || ($_FILES['profile-picture-file']['type'] == "image/pjpeg")
    || ($_FILES['profile-picture-file']['type'] == "image/x-png")
    || ($_FILES['profile-picture-file']['type'] == "image/png"))
    && ($_FILES['profile-picture-file']['size'] < 1000*1024)
    && in_array($extension, $allowedExts)) {
        if ($_FILES['profile-picture-file']["error"] > 0) {
            error_register("Error: " . $_FILES['profile-picture-file']['error']);
            return crm_url('contact&cid=' . $_POST['cid']);
        } else {
            //------- Resize the image -------
            if (!(extension_loaded('gd') && function_exists('gd_info'))) {
                error_register("It looks like GD, an image manipulation library,
                               is not configured for your PHP installation. Therefore,
                               Image Resizing is disabled.");
            } else {
                define('THUMBNAIL_IMAGE_MAX_WIDTH', 120);
                define('THUMBNAIL_IMAGE_MAX_HEIGHT', 120);
                $source_image_path = $_FILES['profile-picture-file']['tmp_name'];
                list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
                switch ($source_image_type) {
                    case IMAGETYPE_GIF:
                        $source_gd_image = imagecreatefromgif($source_image_path);
                        break;
                    case IMAGETYPE_JPEG:
                        $source_gd_image = imagecreatefromjpeg($source_image_path);
                        break;
                    case IMAGETYPE_PNG:
                        $source_gd_image = imagecreatefrompng($source_image_path);
                        break;
                }
                if ($source_gd_image === false) {
                    return false;
                }
                $source_aspect_ratio = $source_image_width / $source_image_height;
                $thumbnail_aspect_ratio = THUMBNAIL_IMAGE_MAX_WIDTH / THUMBNAIL_IMAGE_MAX_HEIGHT;
                if ($source_image_width <= THUMBNAIL_IMAGE_MAX_WIDTH && $source_image_height <= THUMBNAIL_IMAGE_MAX_HEIGHT) {
                    $thumbnail_image_width = $source_image_width;
                    $thumbnail_image_height = $source_image_height;
                } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
                    $thumbnail_image_width = (int) (THUMBNAIL_IMAGE_MAX_HEIGHT * $source_aspect_ratio);
                    $thumbnail_image_height = THUMBNAIL_IMAGE_MAX_HEIGHT;
                } else {
                    $thumbnail_image_width = THUMBNAIL_IMAGE_MAX_WIDTH;
                    $thumbnail_image_height = (int) (THUMBNAIL_IMAGE_MAX_WIDTH / $source_aspect_ratio);
                }
                $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
                imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
                imagejpeg($thumbnail_gd_image, $_FILES['profile-picture-file']['tmp_name'], 90);
                imagedestroy($source_gd_image);
                imagedestroy($thumbnail_gd_image);
            }
            // ------- End Image Resizing -------
            //generate md5 hash from the contents of the uploaded resized image file
            $hash = hash_file('md5', $_FILES['profile-picture-file']['tmp_name']);
            //generate filepath to save file
            $destFileName = $hash . '.' . $extension;
            $destFilePath = "files/profile_picture/" . $destFileName;
            // ------- update SQL server and files -------
            if (!profile_picture_delete($cid)){
                return crm_url('contact&cid=' . $_POST['cid']);
            }
            $esc_cid = mysqli_real_escape_string($db_connect, $cid);
            // Associate this CID with uploaded file by storing a cid=>filepath row in the profile_picture table
            $sql = "
                INSERT INTO `profile_picture`
                (`cid`, `filename`)
                VALUES
                ('$esc_cid', '$destFileName')
            ";
            $res = mysqli_query($db_connect, $sql);
            if (!$res) crm_error(mysqli_error($db_connect));
            //save the file. Literally just moving from /tmp/ to the right directory
            if(!move_uploaded_file($_FILES['profile-picture-file']['tmp_name'], $destFilePath)){
                error_register('Error Saving Image to Server');
                error_register('Tried moving: ' .  $_FILES['profile-picture-file']['tmp_name'] . 'to: ' . $destFilePath);
            } else {
              message_register("Successfully uploaded new user profile picture");
            }
        }
    } else {
        error_register('Invalid file. Did you upload an image (gif, jpeg, jpg, png) that is less than 20mb and no bigger than 1000x1024?');
        error_register('File Type is: ' . $_FILES['profile-picture-file']['type']);
        error_register('File Size is: ' . $_FILES['profile-picture-file']['size'] / 1024 . "kB");
    }
    return crm_url('contact&cid=' . $_POST['cid']);
}

// DB to Object mapping ////////////////////////////////////////////////////////

/**
 * Delete a profile picture.
 * @param $cid the cid of the profile picture to delete
 * @return bool true if succeded, false if failed.
 */
function profile_picture_delete ($cid) {
    global $db_connect;
    //Remove existing profile picture associated with this CID (both the file, and the row in the database)
    //Attempt to fetch a picture filename in the database associated with this cid.
    $esc_cid = mysqli_real_escape_string($db_connect, $cid);
    $sql = "
        SELECT `cid`, `filename`
        FROM `profile_picture`
        WHERE 1 AND `cid` = '$esc_cid'
    ";
    $res = mysqli_query($db_connect, $sql);
    if (!$res) crm_error(mysqli_error($db_connect));
    $row = mysqli_fetch_assoc($res);
    if (!empty($row)) {
            $oldProfilePictureFilePath = "files/profile_picture/" . $row['filename'];
            //First, delete the profile picture file associated with this cid.
            if (!unlink($oldProfilePictureFilePath)){
                error_register('Not able to remove profile picture file: ' . $oldProfilePictureFilePath . ". Please check file permissions.");
                return false;
            }
            //Next, Attempt to delete the existing profile picture filename association with this cid.
            $sql = "
                DELETE FROM `profile_picture`
                WHERE `cid`='$esc_cid'
            ";
            $res = mysqli_query($db_connect, $sql);
            if (!$res) crm_error(mysqli_error($db_connect));
            if (mysqli_affected_rows($db_connect) > 0) {
            message_register('Existing profile picture removed');
        }
    }
    return true;
}

// Themeing ////////////////////////////////////////////////////////////////////

/**
 * Theme a contact's profile picture.
 * @param $contact The contact data structure or cid.
 * @return The html of the user's profile picture.
 */
function theme_profile_picture ($contact) {
    global $db_connect;
    if (!is_array($contact)) {
        $contact = crm_get_one('contact', array('cid'=>$contact));
    }
    $cid = $contact['cid'];
    //Attempt to fetch a picture filename in the database associated with this cid.
    $sql = "
        SELECT `cid`, `filename`
        FROM `profile_picture`
        WHERE 1 AND `cid` = '$cid'
    ";
    $res = mysqli_query($db_connect, $sql);
    if (!$res) crm_error(mysqli_error($db_connect));
    $row = mysqli_fetch_assoc($res);
    if (!empty($row)) {
        // If a row exists in the database that associates this user's CID with a filename, return the HTML that
        // shows that user's profile picture.
        $html = '<div class="userimage"><img src = "'. "files/profile_picture/" . $row['filename'] .'"></div>';
    } else {
        //else, grab a gravatar associated with this email (if there is one).
        $email = $contact['email'];
        $size = 120; //default size of the gravatar
        $grav_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email)))."?d=retro&s=" .$size;
        $html = '<div class="userimage"><img src = "'.$grav_url.'"></div>';
    }
    return $html;
}

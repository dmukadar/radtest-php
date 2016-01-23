<?php
include "common.inc.php";

session_start();
try {   
    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['logfile']['error']) ||
        is_array($_FILES['logfile']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    // Check $_FILES['logfile']['error'] value.
    switch ($_FILES['logfile']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }

    // You should also check filesize here. 
    $maxLimit = 20 * 1024 * 1024; //20MB
    if ($_FILES['logfile']['size'] > $maxLimit) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    if ($_FILES['logfile']['type'] != 'text/plain') {
        throw new RuntimeException('Invalid file format.');
    }

    if (move_uploaded_file(
        $_FILES['logfile']['tmp_name'],
        $filepath
    )) {
        header('location: progress.php');
    }
    else {   
        throw new RuntimeException('Failed to move uploaded file.');
    }

} catch (RuntimeException $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header('location: index.php');
}
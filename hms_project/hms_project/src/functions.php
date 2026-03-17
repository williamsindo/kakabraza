<?php

// Sanitize inputs
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Redirect helper
function redirect($url) {
    header("Location: " . $url);
    exit;
}

<?php
    // Set site to use HTTPS if we're not using it already
    $https = $_SERVER['HTTPS'];
    if ($https != "on") {
        $host = $_SERVER['HTTP_HOST'];
        $uri = $_SERVER['REQUEST_URI'];
        $url = 'https://' . $host . $uri;
        header("Location: " . $url);
        exit();
    }
?>
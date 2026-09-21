<?php
    // Very simple page for when users are registered.
	require 'includes/header.php';

    if (isset($_COOKIE['username'])) {
        $username = $_COOKIE['username'];
        echo "<main><p>Thank you $username for registering!</p><p>Please use the menu to login.</p></main>";
    } else
        echo "<main><p>You have reached this page in error.</p></main>";

    include 'includes/footer.php';
    exit;
?>
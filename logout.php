<?php 
	// Destroy the session, if it exists
	if (isset($_COOKIE['PHPSESSID'])) {
		session_start();
		$username = htmlspecialchars($_SESSION['username']);
		$_SESSION=array();
		session_destroy();
		setcookie('PHPSESSID', '', time()-3600, '/');
		$message = "<p>You have been logged out successfully.</p>";
	} else { 
		$message = '<p>You have reached this page in error.</p>';
	}

	require 'includes/header.php';
	echo '<main>'.$message.'</main>';
	include ('includes/footer.php'); 	
?>
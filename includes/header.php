<?php
    // Start session if we have one
    if (isset($_COOKIE['PHPSESSID'])) {
        session_start();
    }
    
    // Set time zone for timestamps
    date_default_timezone_set("America/New_York");
?>

<?php 
    // Code written by web development teacher
	// Name redacted for my privacy
    //Retrieve the name of the current page and strip off .php
    $currentPage = basename($_SERVER['SCRIPT_FILENAME'], '.php'); 
    //Replace underscore with space
    $title = str_replace('_', ' ', $currentPage);
    if ($title == 'index')
        $title = 'home';
    //Make uppercase
    $title = ucwords($title);


    // If we're on a user page, show the username in the titlebar
    if ($currentPage=='user_page' && isset($_GET['user'])) {
        $title = $title." - {$_GET['user']}";
    }
?>
<!DOCTYPE HTML>
<html>
    <head>
        <!-- wish-22 -->

        <!-- Stylesheets -->
        <link rel="stylesheet" media="screen" href="styles/main.css">
        <link rel="stylesheet" media="print" href="styles/print.css">
        
        <!-- Metadata -->
        <title>1tickM - <?php echo $title; ?></title>
        <meta name="description" content="Upload one minute long songs for fun">
        <meta name="keywords" content="music, upload, competition">
    </head>

    <body>
    <header>
        <!-- Logo -->
        <a href="index.php"><img src="images/logo.png" alt="1tickM logo"></a>
    </header>
    <nav>
        <!-- Navigation -->
        <ul>
            <li><a href="index.php" <?php if ($currentPage == 'index') echo 'id="here"'; ?>>Home</a></li>
            <li><a href="posts.php" <?php if ($currentPage == 'posts') echo 'id="here"'; ?>>Posts</a></li>
            <?php
                // If we're logged in, show Upload, page link, and logout.
                if (isset($_SESSION['username'])) {
            ?>
                <li><a href="song_upload.php" <?php if ($currentPage == 'song_upload') echo 'id="here"'; ?>>Upload</a></li>
                <li><a href="user_page.php?user=<?= $_SESSION['username']; ?>" <?php if ($currentPage == 'user_page' && isset($_GET['user']) && $_GET['user']==$_SESSION['username']) echo 'id="here"'; else echo 'id="profile"'; ?>><?= $_SESSION['username']; ?></a></li>
                <li><a href="logout.php" <?php if ($currentPage == 'logout') echo 'id="here"'; ?>>Logout</a></li>
            <?php
                // Otherwise, show register and login
                } else {
            ?>
                <li><a href="register.php" <?php if ($currentPage == 'register') echo 'id="here"'; ?>>Register</a></li>
                <li><a href="login.php" <?php if ($currentPage == 'login') echo 'id="here"'; ?>>Login</a></li>
            <?php
                }
            ?>
        </ul>
    </nav>
<?php
    require('secure_conn.php');
    require('includes/header.php');
?>
<?php
    // If we've submitted, run through code
    if (isset($_POST['submit']) && $_POST['submit']=="Login") {
        // Define missing array
        $missing = [];

        // Obtain username
        if (!empty($_POST['username']))
            $username = trim(htmlspecialchars($_POST['username']));
        else
            $missing['username'] = 'Please enter a username.';

        // Obtain password
        if (!empty($_POST['password']))
            $password = trim(htmlspecialchars($_POST['password']));
        else
            $missing['password'] = 'Please enter a password.';

        // If we have a username, check to see if the password matches.
        if (isset($username)) {
            try {
                // Database connect.
                require_once '../../pdo_connect.php';

                // Find our user.
                $sql = "SELECT * FROM onetickm_users WHERE username = :user";
                $stmt = $dbc->prepare($sql);
                $stmt->bindParam(':user', $username);
                $stmt->execute();
                $numRows = $stmt->rowCount();

                // If we don't find a user, this username doesn't exist
                if ($numRows == 0)
                    $missing['login_fail'] = "Username not found.";
                else {
                    // See if the password hashes match
                    $result = $stmt->fetch();
                    $pw_hash=$result['pw'];

                    if (password_verify($password, $pw_hash )) {
                        session_start();
                        $_SESSION['username'] = $username;
                        header('Location: index.php');
                        exit;
                    } else {
                        // Wrong password
                        $missing['login_fail'] = "Invalid password.";
                    }
                }
            } catch (PDOException $e) {
                echo $e->getMessage();	
            }
        }
    }

?>
<main>
    <form method="post" action="login.php">
        <fieldset>
            <?php if (isset($missing['username'])) echo '<p class="formerror">'.$missing['username'].'</p>';?>
            <p class="required">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php if (isset($username)) echo $username;?>">
            </p>
            <?php if (isset($missing['password'])) echo '<p class="formerror">'.$missing['password'].'</p>';?>
            <p class="required">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="">
            </p>
            
            <?php if (isset($missing['login_fail'])) echo '<p class="formerror">'.$missing['login_fail'].'</p>';?>
            <p>
                <input type="submit" name="submit" value="Login"> <input type= "reset" name="reset" value="Reset">
            </p>
        </fieldset>
    </form>
</main>
<?php require('includes/footer.php'); ?>
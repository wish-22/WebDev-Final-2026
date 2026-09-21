<?php
    require('secure_conn.php');
    require('includes/header.php');
?>
<?php
    // If we've submitted, run through code
    if (isset($_POST['submit']) && $_POST['submit']=="Register") {
        // Define missing array
        $missing = [];

        // Username
        if (!empty($_POST['username'])) {
            // Trim and sanitize
            $username = trim(htmlspecialchars($_POST['username']));

            // Username can only contain alphanumeric characters, hypens, and underscores
            if (!filter_var(
                $username,FILTER_VALIDATE_REGEXP,
                array("options"=>array("regexp"=>"/^[A-Za-z0-9\-_]+$/"))
            )) {
                $missing['username'] = 'Invalid username.';
            }
            
            // Username must be 20 characters or less.
            if (strlen($username) > 20) {
                $missing['username'] = 'Username too long.';
            }
        } else $missing['username'] = 'Please enter a username.';

        // Password
        if (!empty($_POST['password1'])) {
            // Trim and sanitize
            $password = trim(htmlspecialchars($_POST['password1']));
            // Password must be 8 characters or more.
            if (strlen($password) < 8) {
                $missing['password1'] = 'Password must be at least 8 characters';
            }
        } else $missing['password1'] = 'Please enter a password.';

        if (!empty($_POST['password2'])) {
            // Trim and sanitize
            $password_check = trim(htmlspecialchars($_POST['password2']));
            // Passwords must match.
            if ($password!=$password_check) {
                $missing['password2'] = 'Passwords must match.';
            }
        } else $missing['password2'] = 'Please confirm password.';

        // Favorite number. Not required.
        if (!empty($_POST['fav_number'])) {
            $fav_number = (int) $_POST['fav_number'];
            if (!is_int($fav_number)) {
                $fav_number = null;
            }
        }
    
        // Agree to the terms of service
		if (!empty($_POST['agreeterms']))
			$agreeterms = true;
		else
			$missing['agreeterms'] = "Please agree to the terms.";


		try{
            // Connect to the database.
			require_once '../../pdo_connect.php';

            // See if this user exists already.
			$sql = "SELECT * FROM onetickm_users WHERE username = :user";
			$stmt = $dbc->prepare($sql);
			$stmt->bindParam(':user', $username);
			$stmt->execute();
			$numRows = $stmt->rowCount();

            // If this user exists, get out
			if ($numRows >= 1)
				$missing['username'] = "This username already exists.";
			
            // Otherwise, put them into the database
			if (empty($missing)) {
				$sql = "INSERT INTO onetickm_users (username, pw, fav_number) VALUES (?, ?, ?)";
				$stmt = $dbc->prepare($sql);
				$pw_hash = password_hash($password, PASSWORD_DEFAULT);
				$stmt->bindParam(1, $username);
				$stmt->bindParam(2, $pw_hash);
				$stmt->bindParam(3, $fav_number);
				$stmt->execute();
				$numRows = $stmt->rowCount();

				if ($numRows != 1)
					echo "<main><h2>We are unable to process your request at  this  time. Please try again later.</h2></main>";
				else
                    // Make user folder and redirect to account creation.
					$dirPath = "../../onetick_uploads/".$username;
					mkdir($dirPath,0777);

					setcookie ('username', $username, time()+5);
				
					header('Location: acct_created.php');
					exit;
				include 'includes/footer.php'; 
				exit;
					
			} 
		}catch (PDOException $e){
			echo $e->getMessage();	
		}
    }

?>
<main>
    <form method="post" action="register.php">
        <fieldset>
            <p>Must be 20 characters or less. You can use characters a-z, A-Z, 0-9, dashes, and underscores.</p>
            <?php if (isset($missing['username'])) echo '<p class="formerror">'.$missing['username'].'</p>';?>
            <p class="required">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php if (isset($username)) echo $username;?>">
            </p>
            <p>Your password must be at least 8 characters long.</p>
            <?php if (isset($missing['password1'])) echo '<p class="formerror">'.$missing['password1'].'</p>';?>
            <p class="required">
                <label for="password1">Password</label>
                <input type="password" id="password1" name="password1" value="">
            </p>
            <?php if (isset($missing['password2'])) echo '<p class="formerror">'.$missing['password2'].'</p>';?>
            <p class="required">
                <label for="password2">Confirm Password</label>
                <input type="password" id="password2" name="password2" value="">
            </p>
            
            <p>
                <label for="fav_number">Favorite Number</label>
                <input type="number" id="fav_number" name="fav_number" min="-99999" max="99999" style="width:6em;" value="<?php if (isset($fav_number)) echo $fav_number;?>">
            </p>
            
            <?php if (isset($missing['agreeterms'])) echo '<p class="formerror">'.$missing['agreeterms'].'</p>';?>
            <p class="required">
                <label><input type="checkbox" name="agreeterms" value="Agree"<?php if (isset($_POST['agreeterms']) && $_POST['agreeterms']=="Agree") echo ' checked'?>> I agree to the Terms of Service</label>
            </p>
            <p>
                <input type="submit" name="submit" value="Register"> <input type= "reset" name="reset" value="Reset">
            </p>
        </fieldset>
    </form>
</main>
<?php require('includes/footer.php'); ?>
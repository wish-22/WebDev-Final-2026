<?php
    require('secure_conn.php');
    require('includes/header.php');
?>
<?php
// Significant code regarding the placing of files in the correct folder was written by my web development teacher,
// referenced from another assignment.
// Their name has been redacted for my own privacy.

// Create errors array
$errors = array();

if (isset($_SESSION['username'])) {
    if (isset($_POST['submit']) && $_POST['submit']=="Upload") {
        
        // Obtain song name and description.
        // Current SQL setup simply trims these rather than converts
        // to HTML special characters;
        // strings are sanitized upon echo
        $songname = trim($_POST['songname']);
        $description = trim($_POST['description']);

        // Song name
        if (empty($songname))
            $errors['songname'] = 'You must have a title for your song.';

        // Description
        if (empty($description))
            $errors['description'] = 'Please enter a description.';

        // If all fields are set, continue
        if (empty($errors) && isset($_FILES['songfile'])) {
            $allowed = array ('audio/mpeg', 'audio/ogg'); // MP3, OGG
            $folder = $_SESSION['username']; // Songs are stored in user folders
            $filename_str = substr($_FILES['songfile']['name'],-29); // Files have a max character limit of 30
            if (in_array($_FILES['songfile']['type'], $allowed)) {
                $folder = $_SESSION['username'];
                $image_path = $_FILES['songfile']['tmp_name'];

                // Each file can only exist once per user.
                try {
                    require_once ('../../pdo_connect.php'); // Connect to the db.

                    $sql = "SELECT * FROM onetickm_songfiles WHERE username = :user AND filename = :file";
                    $stmt = $dbc->prepare($sql);
                    
                    $songCount = 1;
                    $attempts = 10;

                    while ($songCount>0 and $attempts>0) {
                        // Randomize first part of string to avoid files having the same name.
                        $filename_str = rand(0,99999999).substr($_FILES['songfile']['name'],-20);

                        $stmt->bindParam(':user', $folder);
                        $stmt->bindParam(':file', $filename_str);
                        $stmt->execute();
                        $songCount = $stmt->rowCount();

                        $attempts -= 1;
                    }

                    if ($songCount>0) {
                        $errors['filetoomuch'] = 'Could not find a place to store the file. Please rename the file and try again.';
                    }
                } catch (PDOException $e) {
                    $errors['pdo_exception'] = 'There was an error involving the database.';
                    //echo $e->getMessage();
                }

                if (empty($errors) && move_uploaded_file ($_FILES['songfile']['tmp_name'], "../../onetick_uploads/$folder/$filename_str")) {
                    // File uploaded!
                    $type=$_FILES['songfile']['type'];
                    $upload_time=time();

                    try {
                        // Prepare statement...
                        $sql = "INSERT into onetickm_songfiles (username, fileName, uploadDate, title, description, type) VALUES (:username, :fileName, :uploadDate, :title, :description, :type)";
                        $stmt = $dbc->prepare($sql);
                        $stmt->bindParam(":username", $folder); 
                        $stmt->bindParam(":fileName", $filename_str);
                        $stmt->bindParam(":uploadDate", $upload_time);
                        $stmt->bindParam(":title", $songname);
                        $stmt->bindParam(":description", $description);
                        $stmt->bindParam(":type", $type);
                        $stmt->execute();
                        $numRows = $stmt->rowCount();
                        
                        if ($numRows == 1){
                            // File upload worked!
                            echo '<main><p>The file was saved! Good luck!</p></main>';
                            include 'includes/footer.php'; 
                            exit;
                        }
                        else {
                            $errors['general'] = 'Unable to upload the file.';
                        }		
                    } catch (PDOException $e) {
                        $errors['pdo_exception'] = 'There was an error involving the database.';
                        //$errors['pdo_message'] = $e->getMessage();
                    }
                    // Delete the file if it still exists:
                    if (file_exists ($_FILES['songfile']['tmp_name']) && is_file($_FILES['songfile']['tmp_name'])) {
                        unlink ($_FILES['songfile']['tmp_name']);
                    }
                }
            } else {
                $errors['type'] = 'Invalid type.';
            }
        }
        if ($_FILES['songfile']['error'] > 0) {
            // Obtain error.
            // Since we're returning to the file upload prompt,
            // we ought to explain the errors...
            switch ($_FILES['songfile']['error']) {
                case 1:
                    $errors['general'] = 'The file exceeds the upload_max_filesize setting in php.ini.';
                    break;
                case 2:
                    $errors['general'] = 'The file exceeds the MAX_FILE_SIZE setting in the HTML form.';
                    break;
                case 3:
                    $errors['general'] = 'The file was only partially uploaded.';
                    break;
                case 4:
                    $errors['general'] = 'No file was uploaded.';
                    break;
                case 6:
                    $errors['general'] = 'No temporary folder was available.';
                    break;
                case 7:
                    $errors['general'] = 'Unable to write to the disk.';
                    break;
                case 8:
                    $errors['general'] = 'File upload stopped.';
                    break;
                default:
                    $errors['general'] = 'A system error occurred.';
                    break;
            } // End of switch.		
        } // End of error IF.
    }
} else {
    // Users must be logged in to upload.
    echo '<main><p>Please <a href="login.php">log in</a> or <a href="register.php">register</a> to upload a song.</p></main>';
    include ('./includes/footer.php');
    exit;
}
?>
<main>
    <form enctype="multipart/form-data" method="post" action="song_upload.php">
        <fieldset>
            <?php if (isset($errors['general'])) echo '<p class="formerror">'.$errors['general'].'</p>';?>
            <?php if (isset($errors['pdo_exception'])) echo '<p class="formerror">'.$errors['pdo_exception'].'</p>';?>
            <?php if (isset($errors['pdo_message'])) echo '<p class="formerror">'.$errors['pdo_message'].'</p>';?>
            <?php if (isset($errors['songname'])) echo '<p class="formerror">'.$errors['songname'].'</p>';?>
            <p class="required">
                <label for="songname">Song Name</label>
                <input type="text" id="songname" name="songname" maxlength="64" value="<?php if (isset($songname)) echo htmlspecialchars($songname);?>">
            </p>

            <p>Must be 1 minute or less. Accepted file formats include MP3 or OGG.</p>
            <?php if (isset($errors['filetoomuch'])) echo '<p class="formerror">'.$errors['filetoomuch'].'</p>';?>
            <?php if (isset($errors['type'])) echo '<p class="formerror">'.$errors['type'].'</p>';?>
            <p class="required">
                <label for="songfile">File</label>
                <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
                <input type="file" name="songfile" id="songfile">
            </p>

            <?php if (isset($errors['description'])) echo '<p class="formerror">'.$errors['description'].'</p>';?>
            <p class="required">
                <label>Description</label>
            </p>
            <textarea name="description" id="description" cols=60 rows=4 maxlength="1024"><?php if (isset($description)) echo htmlspecialchars($description); ?></textarea>

            <p>
                <input type="submit" name="submit" value="Upload">
            </p>
        </fieldset>
    </form>
</main>
<?php require('includes/footer.php'); ?>
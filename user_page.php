<?php
    require('secure_conn.php');
    require('includes/header.php');

	echo '<script src="includes/function.js"></script>';
?>
<?php 
    $currentProfile = $_GET['user'];

    $errors = array();

    // Check if this user's page exists.
    try {
		require_once '../../pdo_connect.php';
		$sql = 'SELECT * FROM onetickm_users WHERE username = :user';
        $stmt = $dbc->prepare($sql);
        $stmt->bindParam(':user', $currentProfile);
        $stmt->execute();
        $numRows = $stmt->rowCount();

        if ($numRows==1) 
            $userinfo = $stmt->fetch();
        else $errors['user'] = true;
	} catch (PDOException $e) {
		echo $e->getMessage();
        $errors['user'] = true;
	}

    // If it doesn't exist, back out
    if (isset($errors['user'])) {
        echo "<main><p>The user $currentProfile could not be found.</p></main>";
        require('includes/footer.php');
        exit;
    }
    

    // Find user songs.
    $folder = "../../onetick_uploads/$currentProfile";

	try{
		$sql = "SELECT * FROM onetickm_songfiles WHERE username = :user ORDER BY id DESC";
        $stmt = $dbc->prepare($sql);
        $stmt->bindParam(':user', $currentProfile);
        $stmt->execute();
        $songCount = $stmt->rowCount();
	}catch (PDOException $e){
		echo $e->getMessage();
	}
?>
<aside>
    <p>PROFILE - <?= $currentProfile ?></p>
</aside>
<main class="gridmain">
    <section id="user_info">
        <h2>User Info</h2>
        <?php
            echo '<p>';
            if ($userinfo['fav_number']!=null)
                echo 'Favorite number: '.$userinfo['fav_number'];
            else
                echo 'Favorite number: None';
            echo '</p>';
        ?>
        <?= '<p>Songs uploaded: '.$songCount.'</p>'; ?>
    </section>
    <section id="user_badges">
        <h2>Badges</h2>
        <p>This user has no badges.</p>
    </section>
    <section id="user_songs">
        <h2>Songs</h2>
        <?php 
            if ($songCount==0) {
                echo '<p>This user has no songs.</p>';
            } else {
        ?>
        <table>
            <tr>
                <th>Title</th>
                <th></th>
                <th>Upload</th>
                <th>Description</th>
            </tr>
        <?php
            foreach ($stmt as $row) {
        ?>
            <tr>
                <td><?= htmlspecialchars($row['title']); ?></td>
                <?= "<td><a href=\"javascript:create_window('{$row['id']}')\">PLAY</a></td>"; ?>
                <td><?= date('Y-m-d H:i:s',$row['uploadDate']); ?></td>
                <td><?= htmlspecialchars($row['description']); ?></td>
            </tr>
        <?php
            }}
        ?>
        </table>
    </section>
</main>
<?php require('includes/footer.php'); ?>
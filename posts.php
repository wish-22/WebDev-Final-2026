<?php
    require('secure_conn.php');
    require('includes/header.php');

    // Song playback function
	echo '<script src="includes/function.js"></script>';
?>
<?php
	try {
        // Connect to database and obtain all rows.
		require_once '../../pdo_connect.php';
		$sql = "SELECT * FROM onetickm_songfiles ORDER BY id DESC";
        $stmt = $dbc->prepare($sql);
        $stmt->execute();
	} catch (PDOException $e) {
		echo $e->getMessage();
	}
?>
<main>
    <table class="songlist" style="margin: 0 auto;">
        <tr>
            <th>User</th>
            <th>Title</th>
            <th></th>
            <th>Upload</th>
            <th>Description</th>
        </tr>
    <?php
        // Construct rows in the table.
        foreach ($stmt as $row) {
    ?>
        <tr>
            <?= "<td><a href=\"user_page.php?user={$row['username']}\">{$row['username']}</a></td>"; ?>
            <td><?= htmlspecialchars($row['title']); ?></td>
            <?= "<td><a href=\"javascript:create_window('{$row['id']}')\">PLAY</a></td>"; ?>
            <td><?= date('Y-m-d H:i:s',$row['uploadDate']); ?></td>
            <td><?= htmlspecialchars($row['description']); ?></td>
        </tr>
    <?php
        }
    ?>
    </table>
</main>
<?php require('includes/footer.php'); ?>
<?php // This page displays an image uploaded by a user.
$songid = $_GET['song'];

try {
	// Connect to database
	require_once '../../pdo_connect.php';

	// Get the row matching the ID
	$sql = 'SELECT * FROM onetickm_songfiles WHERE id = :id';
	$stmt = $dbc->prepare($sql);
	$stmt->bindParam(':id', $songid);
	$stmt->execute();
	$numRows = $stmt->rowCount();
} catch (PDOException $e) {
	echo $e->getMessage();
	exit;
}

if ($numRows==0) {
	// Return error if we don't find a song
	echo 'Could not find file.';
	exit;
}

$result = $stmt->fetch();

// Song files are stored in onetick_uploads/username
$folder = $result['username'];
$filename = $result['filename'];

$songfile = "../../onetick_uploads/$folder/$filename";

$fs = filesize($songfile);

// Send the content information:
header ("Content-Type: {$result['type']}\n");
header ("Content-Length: $fs\n");

// Send the file:
readfile ($songfile);
//fpassthru ($fp);
?>
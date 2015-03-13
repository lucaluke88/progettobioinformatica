<?php
	ob_start();
	// per il firebug
	require "./vendor/autoload.php";
	use PhpOrient\PhpOrient;
	$db_name = 'UnifiedPathwayDB';
	$client->dbDrop($db_name);
	echo "Database deleted! </br>";
	echo "<a href='../admin.php'>Back</a>";
?>
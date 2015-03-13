<html>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Admin Page - DB Unificato per Pathways - UNICT (Costantino-Ramo)</title>
		<link rel="stylesheet" href="stile.css"/>
	</head>
		<body>
			<div id="container">
				<h1> Admin Page </h1>
				<h3> Database List</h3>
				<?php
					$client->query( 'select from Pathway_by limit 10' );
				?>
				
				<form action="functions/drop_database.php" method="POST">
					<input type="hidden" name="delete" value="1"/>
					<input type="submit" value="Drop database"/>
				</form>
				<div id="footer">
					Developed by: Illuminato Luca Costantino and Daniela Ramo - <a href="index.php">Back</a>
				</div>
			</div>
			
		</body>
</html>
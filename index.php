<html>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>DB Unificato per Pathways - UNICT (Costantino-Ramo)</title>
		<link rel="stylesheet" href="stile.css"/>
	</head>
		<body>
			<div id="container">
				<h1> Unified Database for Pathway Analysis </h1>
				<div id="searchform"> 
					<form action="#" method="POST">
						<input id="searchbox" type="text" name="query"/>
						</br></br>
						<input type="submit"/> <input type="reset"/>
					</form>
				</div>
				<div id="output">
					<?php
						ob_start(); // per il firebug
						include('PhpOrient.php');
						$client = new PhpOrient('127.0.0.1', 2424);
						$db_username = 'root';
						$db_password = '061288';
						$db_data = $client->dbOpen('UnifiedPathwayDB', $db_username, $db_password);
						if(isset($_POST['query'])) {
							$query = $_POST['query'];
							echo "La tua query è : ".$query;
						}
						
						if(isset($db_data)) {
							echo $db_data;
						}
					?>
				</div>
				<div id="footer">
					Developed by: Illuminato Luca Costantino & Daniela Ramo
				</div>
			</div>
			
		</body>
</html>
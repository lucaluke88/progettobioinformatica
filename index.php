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
						include('PhpOrient.php');
						if(isset($_POST['query'])) {
							$query = $_POST['query'];
							echo "La tua query è : ".$query;
						}
					?>
				</div>
				<div id="footer">
					Developed by: Illuminato Luca Costantino & Daniela Ramo
				</div>
			</div>
			
		</body>
</html>
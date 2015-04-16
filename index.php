<?php
	//ob_start(); // per il firebug
	require "vendor/autoload.php"; // sto usando Composer p
	use PhpOrient\PhpOrient; // i namespace vanno usati nello scope più esterno altrimenti danno errore!
	session_start();
	// VARIABILI GLOBALI
	$_SESSION['user'] = 'root';
	$_SESSION['passwd'] = 'root';
	$_SESSION['hostname'] = 'localhost';
	$_SESSION['$port'] = 2424; // porta usata da OrientDB (motore e console)
	$_SESSION['$db_name'] = 'Human_Pathway_Analysis_DB';
?>
<html>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Human Pathway Analysis</title>
		<link rel="stylesheet" href="stile.css"/>
		<?php
		try 
		{
			// inizializzazione
			$_SESSION['client'] = new PhpOrient();
			$_SESSION['client']->configure( array(
			    'username' => $_SESSION['user'],
			    'password' => $_SESSION['passwd'],
			    'hostname' => $_SESSION['hostname'],
			    'port'     => $_SESSION['port'],
			) );
			
			if($_POST['action']=='createdb')
				include('functions/create_database.php');
		
		}
		catch (Exception $e) 
		{
		    echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		?>
	</head>
		<body>
			<div id="container">
				<h1> Human Pathway Analysis </h1>
				
					<form action="functions/manual_command.php" method="POST">
						<label>Scrivi comando manuale qui</label> 
						<input type="text" size='100' name="manual_command"/>
						<input type="submit"/> <input type="reset"/>
					</form>
					<form action="functions/manual_query.php" method="POST">
						<label>Scrivi query manuale qui</label> 
						<input type="text" size='100' name="manual_query"/>
						<input type="submit"/> <input type="reset"/>
					</form>
					<form action='#' method='POST'>
						<input type='hidden' name='action' value='createdb'/>
						<input type='submit' value='Create DB'/>
					</form>
				<div id="footer">
					Developed by: Illuminato Luca Costantino and Daniela Ramo - <a href="admin.php">Admin</a>
				</div>
			</div>
			
		</body>
</html>
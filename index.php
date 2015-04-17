<?php
	//ob_start(); // per il firebug
	require "vendor/autoload.php"; // sto usando Composer
	use PhpOrient\PhpOrient; // i namespace vanno usati nello scope più esterno altrimenti danno errore!
	session_start();
	// VARIABILI GLOBALI
	$_SESSION['user'] = 'root';
	$_SESSION['passwd'] = 'root';
	$_SESSION['hostname'] = 'localhost';
	$_SESSION['port'] = 2424; // porta usata da OrientDB (motore e console)
	$_SESSION['dbname'] = 'Human_Pathway_Analysis_DB';
?>
<html>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Human Pathway Analysis</title>
		<link rel="stylesheet" href="stile.css"/>
	</head>
		<body>
			<div id="container">
				<h1> Human Pathway Analysis </h1>
				
					<form action="functions/manual_command.php" method="POST">
						<label>Scrivi comando manuale qui</label> 
						<input type="text" size='100' name='azione' value="manual_command"/>
						<input type="submit"/> <input type="reset"/>
					</form>
					<form action="functions/manual_query.php" method="POST">
						<label>Scrivi query manuale qui</label> 
						<input type="text" size='100' name='azione' value="manual_query"/>
						<input type="submit"/> <input type="reset"/>
					</form>
					<form action='#' method='POST'>
						<input type='hidden' name='azione' value='createdb'/>
						<input type='submit' value='Crea DB'/>
					</form>
					<form action='#' method='POST'>
						<input type='hidden' name='azione' value='dropdb'/>
						<input type='submit' value='Cancella DB'/>
					</form>
					<form action='#' method='POST'>
						<input type='hidden' name='action' value='destroysession'/>
						<input type='submit' value='Svuota sessione'/>
					</form>
					<div id='command_output'>
						<label>Command output(s):</label>
						</br></br>
						<?php
							try 
							{
								if($_POST['azione']=='createdb') 
									include('functions/create_database.php');
								else if($_POST['azione']=='dropdb')
									include('functions/drop_database.php');
								else if($_POST['azione']=='destroysession')
									session_destroy();
							}
							catch (Exception $e) 
							{
								echo 'Caught exception: ',  $e->getMessage(), "\n";
							}
						?>l
					</div>
				<div id="footer">
					Developed by: Illuminato Luca Costantino and Daniela Ramo
				</div>
			</div>
			
		</body>
</html>
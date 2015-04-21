<?php //ob_start(); // per il firebug
require_once "vendor/autoload.php";
// sto usando Composer
use PhpOrient\PhpOrient;
// i namespace vanno usati nello scope più esterno altrimenti danno errore!
session_start();
if (!(isset($_SESSION['login_ok'])) || ($_SESSION['login_ok'] = FALSE))
	header('Location: index.php');
// VARIABILI GLOBALI
$_SESSION['user'] = 'root';
$_SESSION['passwd'] = 'root';
$_SESSION['hostname'] = 'localhost';
$_SESSION['port'] = 2424;
// porta usata da OrientDB (motore e console)
$_SESSION['dbname'] = 'Human_Pathway_Analysis_DB';
?>
<html>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Human Pathway Analysis</title>
		<link rel="stylesheet" href="styles/stile.css"/>
		<link rel="stylesheet" href="styles/stile_buttons.css"/>
	</head>
	<body>
		
		<!-- esamino gli eventuali input inviati a questa paginetta -->
		<?php
			try {
				if ($_POST['azione'] == 'createdb')
					include ('functions/create_database.php');
				else if ($_POST['azione'] == 'dropdb')
					include ('functions/drop_database.php');
				else if ($_POST['azione'] == 'destroysession')
					session_destroy();
				else if ($_POST['azione'] == 'manual_command')
					include ('functions/manual_command.php');
				else if ($_POST['azione'] == 'manual_query')
					include ('functions/manual_query.php');
			} catch (Exception $e) {
				echo 'Caught exception: ', $e -> getMessage(), "\n";
			}
		?>
		
		<div id="container" class="hot-container">
			<h1 style="color: white"> Human Pathway Analysis - Admin Panel </h1>
			<form action="#" method="POST">
				<input class="input_box" text" size='100' name='content' value='Scrivi comando manuale qui' />
				<input type="hidden" name='azione' value="manual_command"/>
				<input class="btn btn-blue" type="submit"/> <input class="btn btn-red" type="reset"/>
				</form>
				<form action="#" method="POST">
				<input class="input_box"  type="text" size='100' name='content2' value="Scrivi query manuale qui"/>
				<input type="hidden" name='azione' value="manual_query"/>
				<input class="btn btn-blue" type="submit"/> <input class="btn btn-red" type="reset"/>
				</form>
				<div id='actiondbcontainer' style="margin-left: 30%">
				<form action='#' method='POST' class="managedbform">
				<input type='hidden' name='azione' value='createdb'/>
				<input class="btn btn-blue" type='submit' value='Crea DB'/>
				</form>
				<form action='#' method='POST' class="managedbform">
				<input type='hidden' name='azione' value='dropdb'/>
				<input class="btn btn-blue" type='submit' value='Cancella DB'/>
				</form>
				<form action='#' method='POST' class="managedbform">
				<input type='hidden' name='azione' value='destroysession'/>
				<input class="btn btn-blue" type='submit' value='Svuota sessione'/>
			</form>
			<form action='functions/logout.php' method='POST' class="managedbform">
				<input type='hidden' name='azione' value='logout'/>
				<input class="btn btn-red" type='submit' value='Logout'/>
			</form>
		</div>
		</br></br></br></br>
		<label>Command output(s):</label>
		<div id='command_output'>
			</br></br>
			<?php
				$xmlstring = file_get_contents("file/hsa00010.xml");	
				$xmlobj = simplexml_load_string($xmlstring);
				print_r($xmlobj);
			?>
		</div>
		<div id="footer">
			Developed by: Illuminato Luca Costantino and Daniela Ramo
		</div>
		</div>
	</body>
</html>
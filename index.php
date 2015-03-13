<?php
	ob_start();
	// per il firebug
	require "vendor/autoload.php";
	use PhpOrient\PhpOrient; // i namespace vanno usati nello scope più esterno altrimenti danno errore!
?>
<html>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>DB Unificato per Pathways - UNICT (Costantino-Ramo)</title>
		<link rel="stylesheet" href="stile.css"/>
		<?php
		try 
		{
			// inizializzazione
			$client = new PhpOrient();
			$client->configure( array(
			    'username' => 'root',
			    'password' => '061288',
			    'hostname' => 'localhost',
			    'port'     => 2424,
			) );
			$db_name = 'UnifiedPathwayDB';
			
			if(isset($_POST['action']) && $_POST['action']=="reset")
			{
				$_POST['action'] = "none";
			}
			
			else if(isset($_POST['action']) && $_POST['action']=="delete")
			{
				$client->connect();
				$client->dbDrop( $db_name, 
					 PhpOrient::STORAGE_TYPE_MEMORY  # optional, default: STORAGE_TYPE_PLOCAL
				);
				$_POST['action'] = "none";
			}
			
			else if(isset($_POST['action']) && $_POST['action']=="mquery")
			{
				$client->connect();
				$client->dbOpen($db_name, 'root', '061288' );
				echo $client->query($_POST['manual_query']);
			}
			
			else if(isset($_POST['action']) && $_POST['action']=="mcommand")
			{
				$client->connect();
				$client->dbOpen($db_name, 'root', '061288' );
				echo $client->command($_POST['manual_command']);
			}
			
			else 
			{
				$client->connect();
			
				if(!($client->dbExists($db_name,PhpOrient::DATABASE_TYPE_GRAPH)))
				{
					include('functions/create_database.php');
				}
				
				// connettiamoci al db che ora è sicuramente esistente
				$ClusterMap = $client->dbOpen($db_name, 'root', '061288' );
			}
			
			
		}
		catch (Exception $e) 
		{
		    echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		?>
	</head>
		<body>
			<div id="container">
				<h1> Unified Database for Pathway Analysis </h1>
				<div id="searchform">
					<label>Scrivi codice pathway qui</label> 
					<form action="#" method="POST">
						<input id="searchbox" type="text" name="query"/>
						</br></br>
						<input type="submit"/> <input type="reset"/>
					</form>
				</div>
				<div id="output">
					<?php
						if(isset($_POST['query']))
						{
							$_code = $_POST['query'];
							include('functions/kegg.php');
							include('functions/database.php');
							$jsoncontent = getPathwayInfoFromItsCode($_code);
							$attributes = writeIntoDB($jsoncontent);
						}
					?>
				</div>
				<div id="actions">
					<form action="#" method="POST">
						<input type="hidden" name="action" value="delete"/>
						<input type="submit" value="Drop Database"/>
					</form>
					<form action="#" method="POST">
						<input type="hidden" name="action" value="reset"/>
						<input type="submit" value="Reset action manually "/>
					</form>
					<form action="#" method="POST">
						<label>Manual query</label>
						<input type="hidden" name="action" value="mquery"/>
						<input type="text" name="manual_query"/>
						<input type="submit" value="Go"/>
					</form>
					<form action="#" method="POST">
						<label>Manual command</label>
						<input type="hidden" name="action" value="mcommand"/>
						<input type="text" name="manual_command"/>
						<input type="submit" value="Go"/>
					</form>
				</div>
				<div id="footer">
					Developed by: Illuminato Luca Costantino and Daniela Ramo - <a href="admin.php">Admin</a>
				</div>
			</div>
			
		</body>
</html>
<?php
    $_SESSION['client']->connect();
	$_SESSION['client']->dbOpen($db_name, 'root', 'root' );
	$mcommand = $_POST['manual_command'];
	$client->command($_POST['manual_command']);
	// qui dovremo scrivere il codice della query manuale
	// dobbiamo gestire il risultato della query (se per esempio è di tipo select, list, ecc)
?>
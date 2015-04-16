<?php
    $_SESSION['client']->connect();
	$_SESSION['client']->dbOpen($db_name, 'root', 'root' );
	$mquery = $_POST['manual_query'];
	$_SESSION['client']->query($mquery);
	// qui dovremo scrivere il codice della query manuale
	// dobbiamo gestire il risultato della query (se per esempio è di tipo select, list, ecc)
?>
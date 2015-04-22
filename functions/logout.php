<?php
	/**
	 * MITHrIL: miRNA enriched pathway impact analysis
	 * REST Web Service
	 *
	 * @author Illuminato Luca Costantino - Daniela Ramo
	 */
	session_start();
	unset($_SESSION['login_ok']);
	header('Location: ../index.php');
?>
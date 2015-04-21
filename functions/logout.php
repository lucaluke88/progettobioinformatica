<?php
	session_start();
	unset($_SESSION['login_ok']);
	header('Location: ../index.php');
?>
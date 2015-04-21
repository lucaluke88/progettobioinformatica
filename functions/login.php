<?php
	$user = $_POST['user'];
	$pass= $_POST['password'];
	
	$hash_password = md5(pass);
	
	$pass_corretta = '1a1dc91c907325c69271ddf0c944bc72'; // sarebbe root
	$user_corretto = 'root';

	if(($hash_password==$pass_corretta)&&($user==$user_corretto))
	{
		// success !
		session_start();
		$_SESSION['login_ok'] = TRUE;
		header('Location: ../index_root.php');
	}
	else
	{
		// failure !
		session_start();
		$_SESSION['login_ok'] = FALSE;
		echo $hash_password;
		echo $user;
		//header('Location: ../index.php');
	}
	

?>
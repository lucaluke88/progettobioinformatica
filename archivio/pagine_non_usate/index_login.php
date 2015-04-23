<!-- @author Illuminato Luca Costantino - Daniela Ramo -->

<html>

	<head>
		<link rel="stylesheet" href="styles/stile_login.css"/>
		<title>Login</title>
	</head>

	<body>
		<div id="login" style="text-align: center">
			<h1>Pannello amministratore - Login</h1>
			<form action="functions/login.php" method="POST">
				<input type="user" placeholder="User" name="user"/>
				<input type="password" placeholder="Password" name="password"/>
				<input type="submit" value="Log in" />
				</br></br>
				<label style="color: red"> <?php
					session_start();
					if (!(isset($_SESSION['login_ok'])))// non ho mai tentato un login
						echo "";
					else
					if ($_SESSION['login_log'] == FALSE)// ho sbagliato i dati del login
						echo "Dati errati. Riprova";
					?></label>
			</form>

		</div>

	</body>
</html>

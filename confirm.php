<?php
	// Connect to database secure_login
	$servername = "localhost";
	$DBusername = "root";
	$DBpassword = "password";
	$db = new PDO("mysql:host=$servername;dbname=secure_login", $DBusername, $DBpassword);
	// Set PDO error mode to exception
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	if (isset($_GET['auth'])) {
		$auth = $_GET['auth'];
		$errorMessage = "Sorry, there was an error. Please try again.";
		$validateHash = $db->prepare("SELECT * FROM Users WHERE `auth_hash`=?");
										
		$activateUser = $db->prepare("UPDATE Users
																	SET active=1, `auth_hash`=NULL
																	WHERE `auth_hash`=?");
		
		try {
			$validateHash->execute(array($auth));
			$content = $validateHash->fetch();
			
			if (empty($content)) {
				header("Location: index.php");
			} else {
				$activateUser->execute(array($auth));
				echo "Account Confirmed!";
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
?>
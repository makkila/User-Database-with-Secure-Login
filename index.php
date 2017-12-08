<?php	
	require_once "Includes/secure_session.php";
	
	SessionManager::sessionStart("user", 0, '/', null, false);
	
	// Connect to database secure_login
	$servername = "localhost";
	$DBusername = "root";
	$DBpassword = "password";
	$db = new PDO("mysql:host=$servername;dbname=secure_login", $DBusername, $DBpassword);
	// Set PDO error mode to exception
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<!DOCTYPE html>
<html>
	<head>
		<link href='https://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>
		<link href="website.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="nav-wrapper">
			<nav>
				<li><a href="signup.php">Sign Up</a></li>
				<li><a href="login.php">Login</a></li>
				<li><a href="upload.php">Upload</a></li>
			</nav>
		</div>
		<?php
			if ($_SESSION["user"]) {
				$username = $_SESSION["user"]["username"];
				
				// Destroy session, logout user
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					// Unset existing remember me cookie and remove from database
					if (isset($_COOKIE["site_authenticate"])) {
						$tokenValue = substr($_COOKIE["site_authenticate"],
																 1,
																 strlen($_COOKIE["site_authenticate"]));
						$removeValueCookie = $db->prepare("DELETE FROM login_tokens WHERE token=?");
						$removeValueCookie->execute(array(hash("sha256", $tokenValue)));
						
						setcookie("site_authenticate", "", time()-1000);
					}
					// Unset all of the session variables.
					$_SESSION = array();
					// Destroy session
					session_destroy();;
					header("Location: login.php");
				}
		?>
			<h2 class="center vert-center"><?php echo "Welcome, $username!" ?></h2>
			<form method="post" action="<?php $_SERVER["PHP_SELF"] ?>" class="center vert-center">
				<input type="submit" value="Sign Out">
			</form>
		<?php
			} else {
				header("Location: login.php");
			}
		?>
	</body>
</html>
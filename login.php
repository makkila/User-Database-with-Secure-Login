<?php
	require_once "Includes/secure_session.php";
	
	SessionManager::sessionStart(0, '/', null, false);
	
	// Connect to database secure_login
	$servername = "localhost";
	$DBusername = "root";
	$DBpassword = "password";
	$db = new PDO("mysql:host=$servername;dbname=secure_login", $DBusername, $DBpassword);
	// Set PDO error mode to exception
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// Auto login if session still exists
	if (!isset($_SESSION["user"])) {
		include_once("autologin.php");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<link href='https://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>
		<link type="text/css" rel="stylesheet" href="website.css">
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
			// Redirect to index if user is already logged in
			if (isset($_SESSION["user"])) {
				header("Location: index.php");
			}	

			$username = $password = $rememberMe = "";
			$loginError = "";
			
			if (isset($_GET["upload"])) {
				if (!isset($loginRedirect)) {
					$loginRedirect = "upload.php";
				}
			} else {
				if (!isset($loginRedirect)) {
					$loginRedirect = "index.php";
				}
			}
		
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$username = trim($_POST["username"]);
				$password = trim($_POST["password"]);
				$rememberMe = (isset($_POST["remember-me"]) ? $_POST["remember-me"] : 0);
				$getUserInfo = $db->prepare("SELECT * FROM Users WHERE username=?");
				$getUserInfo->execute(array($username));
				$userInfo = $getUserInfo->fetch();
				
				if (!empty($userInfo)) {
					if ($userInfo["active"] == 1 && password_verify($password, $userInfo["password"])) {
						$_SESSION["user"] = $userInfo;
						$loginErrors = "";
						// Implement 'remember me' feature when requested
						if (isset($rememberMe)) {
							$random = bin2hex(mcrypt_create_iv(5, MCRYPT_DEV_URANDOM));
							$randomHash = hash("sha256", $random);
							// Set remember me cookie with value $randomHash appended to user id to last for 30 days
							setcookie("site_authenticate", 
											 $userInfo["id"] . $random, 
											 time() + (3600 * 24 * 30),
											 null,
											 null,
											 0,
											 1);
							$insertCookie = $db->prepare("INSERT INTO login_tokens(token, identifier) VALUES (?, ?)");
							$insertCookie->execute(array($randomHash, $userInfo["id"]));
						}
						
						header("Location: $loginRedirect");
					} else {
						if ($userInfo["active"] == 0) {
							$loginError = "This account hasn't been activated yet.<br/>Click on the link in your email to activate your account.";
						} else {
							$loginError = "You have entered an incorrect username or password.";
						}
					}
				} else {
					$loginError = "You have entered an incorrect username or password.";						
				}
			}
		?>
		<div class="vert-center">
			<form name="login-form" method="post" action="<?php $_SERVER["PHP_SELF"] ?>">
				<div class="center">
					<input type="text" name="username" maxlength="20" 
									placeholder="Username">
									<div style="height: 3em;"></div>
									<br/>
									<br/>
					<input type="password" name="password" maxlength="90"
									placeholder="Password">
									<div id="login-errors">
										<ul>
											<li class="error"><?php echo $loginError ?></li>
										</ul>
									</div>
									<br/>
									<br/>
					<input type="checkbox" name="remember-me" class="center"> remember me <br/><br/>
					<input type="submit" name="login" value="Login">
				</div>
			</form>
			<div class="center"><a href="#" id="recover-password">forgot your password?</a></div>
		</div>

		<script src="login.js" defer></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
	</body>
</html>
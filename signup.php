<?php
	require_once "Includes/secure_session.php";
	
	SessionManager::sessionStart("user", 0, '/', null, false);
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
			require_once "Includes/lib/swift_required.php";
			$username = $email = $password = $repeatPassword = "";
			$usernameError = $emailError = $passwordError = $repeatPasswordError = "";
			$usernameTaken = $emailTaken = false;
			$success = "";
			
			// Connect to database secure_login
			$servername = "localhost";
			$DBusername = "root";
			$DBpassword = "password";
			$db = new PDO("mysql:host=$servername;dbname=secure_login", $DBusername, $DBpassword);
			// Set PDO error mode to exception
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			if ($_SERVER["REQUEST_METHOD"] == "POST") {	
				if (!isset($_POST["resend_email"])) {
					$username = trim($_POST["username"]);
					$password = trim($_POST["password"]);
					$email = trim($_POST["email"]);
					$confirmPassword = trim($_POST["confirm-password"]);
				}
					
				if (!isset($_POST["resend_email"]) && (empty($password) || 
						$emailTaken || empty($confirmPassword) || 
						strlen($password) < 8 || $password !== $confirmPassword ||
						$usernameTaken || strlen($username) > 20 || 
						strlen($username) < 5 || empty($email) || 
						!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 90)) {
						
					if (empty($email)) {
						$emailError = "Email required";						
					}
					
					if ($emailTaken) {
						$emailError = "Email exists on record";
					}
							
					if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$emailError = "Email is not valid";
						$email = "";
					}
					
					if (strlen($email) > 90) {
						$emailError = "Email must be less than 90 characters";
						$email = "";
					}

					if (empty($username)) {
						$usernameError = "Username required";
					}
					
					if ($usernameTaken) {
						$usernameError = "Username taken";		
						$username = "";
					}
					
					if (strlen($username) > 20 || strlen($username) < 5) {
						$usernameError = "Username must be 5 - 20 characters long";		
						$username	= "";
					}
					
					if (empty($password)) {
						$passwordError = "Password required";
					}
					
					if (strlen($password) < 8) {
						$passwordError = "Password must be at least 8 characters";
					}
					
					if (empty($confirmPassword)) {
						$repeatPasswordError = "Confirm password required";
					}

					if ($password !== $confirmPassword && strlen($password) > 8) {
						$repeatPasswordError = "Does not match password";
					}
				} else {
					// Create the mail transport configuration using 
					// Swift Mail (swiftmailer.org, PHP mailing library)
					$transport = Swift_MailTransport::newInstance();
					
					// Generate confirmation link
					$auth = md5(uniqid(rand(), true));
					$link = "localhost/confirm.php?auth=$auth";

					// Create the message
					$message = Swift_Message::newInstance();
					$message->setTo(array(
						$email => $username
					));
					$message->setSubject("Account Confirmation");
					$message->setBody("Welcome! Click on the link below to activate your account.\n$link");
					$message->setFrom("mostafaakkilawaterloo@gmail.com", "Email Confirmation");

					// Send confirmation email
					$mailer = Swift_Mailer::newInstance($transport);
					$mailer->send($message);

					// Insert active state, username, email, hashed password into database					
					$password = password_hash($password, PASSWORD_DEFAULT);
					$insert = $db->prepare("INSERT INTO users(username, password, email, auth_hash) VALUES(?, ?, ?, ?)");
					$insert->execute(array($username, $password, $email, $auth));
					$success = "Confirm your account by clicking the link in the email we sent you.
											<br/><form method='POST' name='resend_email' action='signup.php'>
												<input type='submit' value='Send another email'>
											</form>";
					$username = "";
					$email = "";
				}
				
				$db = null;
			}
		?>
		<form method="post" action="<?php $_SERVER["PHP_SELF"] ?>">
			<div class="center">
				<h2>Sign Up</h2>
				<input type="text" name="username" maxlength="20" 
								value="<?php echo $username ?>" placeholder="Username *">
								<div id="username-errors">
									<ul>
									</ul>
								</div>
								<br/>
								<br/>
				<input type="text" name="email" maxlength="90" 
							  value="<?php echo $email ?>" placeholder="Email *">
								<div id="email-errors">
									<ul>
									</ul>
								</div>
								<br/>
								<br/>
				<input type="password" name="password" placeholder="Password *">
								<div id="password-errors">
									<ul>
									</ul>
								</div>
								<br/>
								<br/>
				<input type="password" name="confirm-password" placeholder="Confirm Password *">
								<div id="confirm-password-errors">
									<ul>
									</ul>
								</div>
								<br/>
								<br/>
				<input type="submit" name="sign-up" disabled value="Sign Up">
		</form>
		<h3 class="center"><?php echo $success ?></h3>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
		<script src="signup.js" defer></script>
	</body>
</html>
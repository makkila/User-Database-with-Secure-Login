<?php
	require_once "Includes/secure_session.php";
	
	SessionManager::sessionStart(0, '/', null, false);

	if (empty($_SESSION["user"])) {
		header("Location: login.php?upload=true");
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
		<form method="post" action="<?php $_SERVER["PHP_SELF"] ?>" class="center">
			<h2>Title</h2><br/>
			<input type="text" name="upload-title" class="center"><br/>
			<h2>Cover Photo</h2><br/>
			<form method="post" action="<?php $_SERVER["PHP_SELF"] ?>" enctype="multipart/form-data">
		    Select image to upload:
				<input type="file" name="fileToUpload" id="fileToUpload">
				<input type="submit" value="Upload Image" name="submit">
			</form>
			<h2>Description</h2><br/>
			<textarea cols="45" rows="10"></textarea>
		</form>
	</body
</html>
		
		
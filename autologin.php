<?php
if (!isset($_SESSION["user"]) && isset($_COOKIE["site_authenticate"])) {
	// Seperate random token and user id
	$token = substr($_COOKIE["site_authenticate"], 
								  strlen($_COOKIE["site_authenticate"])-10,
								  strlen($_COOKIE["site_authenticate"]));
	$usr = substr($_COOKIE["site_authenticate"],
								0, 
								strlen($_COOKIE["site_authenticate"])-10);

	$getCookieInfo = $db->prepare("SELECT * FROM login_tokens WHERE identifier=?");
	$getCookieInfo->execute(array($usr));
	$cookieInfo = $getCookieInfo->fetch();
	
	// Check if series identifier is correct
	if (!empty($cookieInfo) && $cookieInfo["identifier"] == $usr) {
		// Check token hash
		if ($cookieInfo["token"] == hash("sha256", $token)) {
			$getUserInfo = $db->prepare("SELECT * FROM users WHERE id=?");
			$getUserInfo->execute(array($usr));
			$userInfo = $getUserInfo->fetch();
			
			if (!empty($userInfo)) {
				$_SESSION["user"] = $userInfo;
			}
		} else {
			$dropColumn = $db->prepare("DELETE FROM login_tokens WHERE identifier=?");
			$dropColumn->execute(array($cookieInfo['identifier']));
		}
	}
}

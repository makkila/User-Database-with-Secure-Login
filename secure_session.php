<?php
	class SessionManager {
		static function sessionStart($limit = 0, $path = '/', $domain = null, $secure = null) {
			// If $domain isn't set, default to current domain
			$domain = isset($domain) ? $domain : $_SERVER["SERVER_NAME"];
			// Check whether connection is secure or not
			$secure = isset($secure) ? $secure : isset($_SERVER["HTTPS"]);

			session_set_cookie_params($limit, $path, $domain, $secure, true);
			// Restrict session tracking to cookies for security
			if (!ini_get("session.use_only_cookies")) {
				ini_set("session.use_only_cookies", 1);
			}
			
			session_start();
		}
	}
?>
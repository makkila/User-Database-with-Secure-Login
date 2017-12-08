$(document).ready(function() {
	function validateEmail(emailAddress) {
		// Regex to check email validity
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
		return pattern.test(emailAddress);
	}
	
	// Stop user from submitting form more than one time
	$("form").submit(function() {
		$("[name=sign-up]").prop("disabled", "disabled");
	});
	// Disable 'sign up' button when entering info
	$("input[name=email], input[name=username], input[name=password], input[name=confirm-password]").focusin(
		function() {
			$("[name=sign-up]").prop("disabled", "disabled");
			$("#" + $(this).attr("name") + "-errors ul").empty();
			$(this).prop("placeholder", "");
			$(this).css("border", "solid 1.5px #0000b3");
			$(this).css("background", "#FFFFFF");
			$(this).css("box-shadow", "inset 0 1px 5px -1px black");
		});
	// Check for errors when all text boxes deselected
	$("input[name=email], input[name=username], input[name=password], input[name=confirm-password]").focusout(
		function() {
			$(this).css("box-shadow", "none");
			var name = $(this).attr("name");
			// Text box styling for when an error is found
			function errorStyle(el) {
				$(el).css("background", "#ffe6e6");
				$(el).css("border", "solid 1.5px red");
			}
			// Styling for no error
			function normalStyle(el) {
				$(el).css("background", "#FFFFFF");
				$(el).css("border", "solid 1.5px #000000");
			}

			// Check validity of email
			if (name == "email") {
				$(this).prop("placeholder", "Email *");
				var email = $(this).val().trim()

				if (!email) {
					$("#email-errors ul").empty();
					$("#email-errors ul").append("<li class='error'>Email is required</li>");
					$("[name=email]").prop("value", "");
					errorStyle(this);
				} else {
					$("#email-errors ul").empty();

					if (!validateEmail(email)) {
						$("#email-errors ul").append("<li class='error'>That email isn't valid</li>");
						errorStyle(this);
					} else {
						// Check for existing email
						var e_mail = $(this).val().trim();
						check_email_ajax(e_mail);
						// AJAX function to check email availability with checkEmailAvailable.php
						function check_email_ajax(email){
							$.post('Includes/checkEmailAvailable.php', {'email':email}, function(data) {
								console.log(data);
								if (data) {
									$("#email-errors ul").append(data);
									$("[name=email]").css("background", "#ffe6e6");
									$("[name=email]").css("border", "solid 1.5px red");
								} else {
									$("[name=email]").css("background", "#FFFFFF");
									$("[name=email]").css("border", "solid 1.5px #000000");
								}
							});
						}
					}
				}
			// Check validity of username
			} else if (name == "username") {
				$("#username-errors ul").empty();

				$(this).prop("placeholder", "Username *");
				var username = $(this).val().trim();
				// Empty username
				if (!username) {
					$("#username-errors ul").empty();
					$("#username-errors ul").append("<li class='error'>Your username can't be left empty</li>");
					errorStyle(this);
				} else {
					$("#username-errors ul").empty();
					// Regex to check validity of characters in username (a-z, 0-9, and periods allowed)
					var usernamePattern = /^[A-Z0-9.]+$/i;

					if ((username.length < 5 || username.length > 20) || (!usernamePattern.test(username))) {
						errorStyle(this);

						if (username.length < 5 || username.length > 20) {
							$("#username-errors ul").append("<li class='error'>Please make username between 5 and 20 characters</li>");
						}

						if (!usernamePattern.test(username)) {
							$("#username-errors ul").append("<li class='error'>Please make a username with only letter, numbers, and periods</li>");
						}
					} else {
						// Check for existing username
						var user_name = $(this).val().trim();

						check_username_ajax(user_name);

						function check_username_ajax(user_name){
							$.post('Includes/checkUsernameAvailable.php', {'username':username}, function(data) {
								if (data) {
									$("#username-errors ul").append(data);
									$("[name=username]").css("background", "#ffe6e6");
									$("[name=username]").css("border", "solid 1.5px red");
								} else {
									$("[name=username]").css("background", "#FFFFFF");
									$("[name=username]").css("border", "solid 1.5px #000000");
								}
							});
						}
					}
				}
			// Check for passwords issues
			} else if (name == "password") {
				$("#password-errors ul").empty();

				$(this).prop("placeholder", "Password *");
				var userPassword = $(this).val().trim();

				if (!userPassword) {
					$("#password-errors ul").append("<li class='error'>Your password can't be left empty</li>");
					errorStyle(this);
				} else {
					// Password must be > 8 characters
					if (userPassword.length < 8) {
						$("#password-errors ul").append("<li class='error'>Please make your password at least 8 characters long</li>");
						errorStyle(this);		
					// Else no errors if password confirmation is similar to password
					} else if (userPassword === $("[name=confirm-password]").val().trim()) {
						$("#confirm-password-errors ul").empty();
						normalStyle("[name=confirm-password]");
						normalStyle(this);
					// Password confirmation error
					} else if ($("[name=confirm-password]").val().trim() && userPassword !== $("[name=confirm-password]").val().trim()) {
						$("#confirm-password-errors ul").empty();
						$("#confirm-password-errors ul").append("<li class='error'>This doesn't match your password</li>");
						errorStyle("[name=confirm-password]");
						normalStyle(this);
					} else {
						normalStyle(this);
					}
				}
			// Check for password confirmation box errors
			} else {
				$("#confirm-password-errors ul").empty();

				$(this).prop("placeholder", "Confirm Password *");
				var confirmPassword = $(this).val().trim();

				if (!confirmPassword) {
					$("[name='sign-up']").prop("href", "#");
					$("#confirm-password-errors ul").append("<li class='error'>Your password confirmation can't be left empty</li>");
					errorStyle(this);
				} else {
					if (confirmPassword !== $("[name=password]").val().trim()) {
						$("#confirm-password-errors ul").append("<li class='error'>This doesn't match your password</li>");
						errorStyle(this);
					} else {
						normalStyle(this);
					}
				}
			}
			// When there are no more errors, enable sign-up button clickability
			if ((!$("#username-errors ul li").length && $("[name=username]").val().trim().length > 4) &&
					(!$("#email-errors ul li").length && $("[name=email]").val().trim().length > 0) &&
					(!$("#password-errors ul li").length && $("[name=password]").val().trim().length > 7) &&
					(!$("#confirm-password-errors ul li").length && $("[name=confirm-password]").val().trim().length > 7)) {
						
						$("[name=sign-up]").prop("disabled", false);
						
					}
		});
});
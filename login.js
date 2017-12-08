$(document).ready(function() {
	// Stop user from submitting form more than one time
	$("form").submit(function() {
			$(this).submit(function() {
					return false;
			});
			return true;
	});	
	// Focused text box styling
	$("[name=username], [name=password]").focusin( function() {
		$(this).prop("placeholder", "");
		$(this).css("border", "solid 1px #0000b3");
		$(this).css("box-shadow", "inset 0 1px 5px -1px black");
	});
	// Unfocused box styling
	$("[name=username], [name=password]").focusout( function() {
		$(this).css("box-shadow", "none");
		
		if ($(this).attr("name") === "username") {
			$(this).prop("placeholder", "Username");
			$(this).css("border", "solid 1px #000000");
		} else {
			$(this).prop("placeholder", "Password");
			$(this).css("border", "solid 1px #000000");
		}
		// Delete contents of text box if it's just white space
		if ($(this).val().trim() === "") {
			$(this).prop("value", "");
		}
	});
	
});
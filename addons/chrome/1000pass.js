chrome.extension.onRequest.addListener(
	function(request, sender, sendResponse) {
		sendResponse({html: $('body').html()});
	}
);


chrome.extension.onConnect.addListener(function(port) {
	port.onMessage.addListener(function(data) {

		if (data.state == 'opened') {
			return;
		}


		/** Username */
		var tmpUsernameField = data.usernameField.split('|');
		if (tmpUsernameField[0] == 'id') {
			var myUsername = document.getElementById(tmpUsernameField[1]);
		} else if (tmpUsernameField[0] == 'name') {
			var myUsernames = document.getElementsByTagName('input');
			for (var i=0; i<myUsernames.length; i++) {
				if (myUsernames[i].name == tmpUsernameField[1]) {
					var myUsername = myUsernames[i];
					break;
				}
			}
		}
		myUsername.value = data.username;


		/** Password */
		var tmpPasswordField = data.passwordField.split('|');
		if (tmpPasswordField[0] == 'id') {
			var myPassword = document.getElementById(tmpPasswordField[1]);
		} else if (tmpPasswordField[0] == 'name') {
			var myPasswords = document.getElementsByTagName('input');
			for (var i=0; i<myPasswords.length; i++) {
				if (myPasswords[i].type == 'password' && myPasswords[i].name == tmpPasswordField[1]) {
					var myPassword = myPasswords[i];
					break;
				}
			}
		}
		myPassword.value = data.password;


		/** Extra Fields Info */
		var tmpExtra = data.extraField.split('|');
		for (var i=0; i<tmpExtra.length; i++) {
			var tmpExtraInfo = tmpExtra[i].split(':');
			if (tmpExtraInfo[0] == 'id') {
				document.getElementById(tmpExtraInfo[1]).value = tmpExtraInfo[2];
			} else if (tmpExtraInfo[0] == 'name') {
				var myExtras = document.getElementsByTagName('input');
				for (var i=0; i<myExtras.length; i++) {
					if ((myExtras[i].type == 'password' || myExtras[i].type == 'text') && myExtras[i].name == tmpExtraInfo[1]) {
						myExtras[i].value = tmpExtraInfo[2];
						break;
					}
				}
			}
		}


		/** Submit the form */
		var tmpForm = data.form.split('|');
		if (tmpForm[0] == 'id') {
			var myForm = document.getElementById(tmpForm[1]);
		} else if (tmpForm[0] == 'name' || tmpForm[0] == 'action') {
			var myForms = document.getElementsByTagName('form');
			for (var i=0; i<myForms.length; i++) {
				if ((tmpForm[0] == 'name' && myForms[i].name == tmpForm[1])
					|| (tmpForm[0] == 'action' && myForms[i].action == tmpForm[1])) {
					var myForm = myForms[i];
					break;
				}
			}
		} else if (tmpForm[0] == 'class') {
			var myForms = document.getElementsByClassName(tmpForm[1]);
			var myForm = myForms[0];
		}

		var port = chrome.extension.connect({name: "done"});
		port.postMessage(data);

		setTimeout(function(){myForm.submit();}, 2000);

	});
});


var bind_events = function() {

	var location = window.location.toString();
	if (location.substr(0, 17) != 'http://localhost/' && location.substr(0, 24) != 'http://www.1000pass.com/' && location.substr(0, 25) != 'https://www.1000pass.com/') {
		return;
	}

	/** Modify the dom to tell the addon is present */
	$('div#1000pass_add_on').addClass('installed');
	$('div#1000pass_add_on_version').addClass('1.0');


	$("img.remote_site_logo").css('cursor', 'pointer');
	$("img.remote_site_logo").click(function() {

		var plugin = $(this).parent().parent();
		var data = {
			id: $('#plugin_identifier', plugin).html(),
			title: $('#title', plugin).html(),
			url: $('#url', plugin).html().replace('&amp;', '&'),
			logout_url: $('#logout_url', plugin).html().replace('&amp;', '&'),
			logout_type: $('#logout_url', plugin).attr('class'),
			username: $('#username', plugin).html(),
			usernameField: $('#username', plugin).attr('class'),
			password: $('#password', plugin).html(),
			passwordField: $('#password', plugin).attr('class'),
			extraField: $('#extra', plugin).html(),
			form: $('#submit', plugin).attr('class')
		};
		var port = chrome.extension.connect({name: "go"});
		port.postMessage(data);
	});
}


if (document.readyState == "complete") {
	bind_events();
} else {
	window.addEventListener("load", bind_events, false);
}
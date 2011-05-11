var objects = new Array();

/*
var mover = function(event) {
	$(event.target).addClass('tp_over');
}

var mout = function(event) {
	$(event.target).removeClass('tp_over');
}
*/
var mdown = function(event) {

	var finishSelection = false;
	var object;
	if (objects.length == 0) {
		if (event.target.tagName != 'INPUT' || $(event.target).attr('type') != 'text') {
			alert('Debe seleccionar un campo para el ingreso del nombre de usuario');
			return;
		} else if ($(event.target).val().trim().length == 0) {
			alert('Debe completar el campo nombre de usuario antes de continuar con la seleccion');
			$(event.target).focus();
			return;
		}
		object = event.target;

	} else if (objects.length == 1) {
		if (event.target.tagName != 'INPUT' || $(event.target).attr('type') != 'password') {
			alert('Debe seleccionar un campo para el ingreso de la clave');
			return;
		} else if ($(event.target).val().trim().length == 0) {
			alert('Debe completar el campo clave antes de continuar con la seleccion');
			$(event.target).focus();
			return;
		}
		object = event.target;

	} else if (objects.length >= 2) {

		if (event.target.tagName == 'INPUT' && $(event.target).attr('type') == 'text') {

			if ($(event.target).val().trim().length == 0) {
				alert('Debe completar el campo extra antes de continuar con la seleccion');
				$(event.target).focus();
				return;
			} else {
				object = event.target;
			}

		} else {

			if ((event.target.tagName == 'IMG' || event.target.tagName == 'SPAN')
				&& event.target.parentNode.tagName == 'A') {

				object = event.target.parentNode;
			} else {
				object = event.target;
			}

			finishSelection = true;
		}

	}



	objects.push(object);
	$(object).addClass('tp_selected');

	if (finishSelection) {

		var loginInput = objects[0];
		var passwordInput = objects[1];
		if (objects.length == 3) {
			var extraInput = null;
			var submitInput = objects[2];
		} else {
			var extraInput = objects[2];
			var submitInput = objects[3];
		}


		save(loginInput, extraInput, passwordInput, submitInput);

		document.body.removeEventListener('mousedown', mdown, false);
		//document.body.removeEventListener('mouseover', mover, false);
		//document.body.removeEventListener('mouseout', mout, false);

	}

}

chrome.extension.onRequest.addListener(
	function(request, sender, sendResponse) {

		if (request == 'add_to_1000pass_manual') {

			document.body.addEventListener('mousedown', mdown, true);
			//document.body.addEventListener('mouseover', mover, true);
			//document.body.addEventListener('mouseout', mout, true);

		} else if (request == 'add_to_1000pass_auto') {


			var passwordFields = $('input[type="password"]');
			if (passwordFields.length == 0) {
				//alert('No es posible encontrar los campos de usuario y contraseña en esta pagina. Se buscara mas profundo...');
			} else if (passwordFields.length == 1) {
				saveLoginInfo(passwordFields[0].form);
			} else {

				var rightForm = null;
				var foundRightForm = 0;
				passwordFields.each(
					function(i, elem) {
						if ($('input[type="text"]', $(elem.form)).length == 1) {
							rightForm = elem.form;
							foundRightForm++;
						}
					}
				);

				// just one, got it
				if (foundRightForm == 1) {
					saveLoginInfo(rightForm);
				} else {
					// manual selection
				}
			}


		} else {
			sendResponse({html: $('body').html()});
		}
	}
);


var saveLoginInfo = function (form) {

	var loginInput = null;
	var extraInput = null;
	var passwordInput = null;

	if ($('input[type="text"]', $(form)).length > 1) {
		loginInput = $('input[type="text"]', $(form)).eq(0);
		extraInput = $('input[type="text"]', $(form)).eq(1);

		if ($(extraInput).val().trim().length > 0) {
			loginInput = extraInput;
			extraInput = null;
		}
	} else {
		loginInput = $('input[type="text"]', $(form));
	}
	passwordInput = $('input[type="password"]', $(form));
	submitInput = $('input[type="submit"]', $(form));


	if (loginInput == undefined || loginInput == null || loginInput.length == 0) {

		$('input').each(function(i, elem) {
			if ($(elem).attr('type') == 'text'
				&& getIdentifier(form) == getIdentifier($(elem).attr('form'))
				&& $(elem).val().trim().length > 0) {

				loginInput = elem;
				return;
			}
		});

		$('input[type="password"]').each(function(i, elem) {
			if (getIdentifier(form) == getIdentifier($(elem).attr('form'))) {
				passwordInput = elem;
				return;
			}
		});

	}



	$(loginInput).addClass('tp_selected');
	if (extraInput != undefined && extraInput != null) {
		$(extraInput).addClass('tp_selected');
	}
	$(passwordInput).addClass('tp_selected');


	if (typeof(submitInput) == 'object') {
		$(submitInput).addClass('tp_selected');
	}	

	save(loginInput, extraInput, passwordInput, submitInput);

}


var save = function(loginInput, extraInput, passwordInput, submitInput) {

	if ($(loginInput).val().trim().length == 0) {
		alert('Debe ingresar nombre de usuario antes de agregar el sitio a 1000pass.com');
		$(loginInput).focus();
		return;
	}

	if (extraInput != undefined && extraInput != null && $(extraInput).val().trim().length == 0) {
		alert('Debe ingresar la informacion adicional antes de agregar el sitio a 1000pass.com');
		$(extraInput).focus();
		return;
	}

	if ($(passwordInput).val().trim().length == 0) {
		alert('Debe ingrear su clave antes de agregar el sitio a 1000pass.com');
		$(passwordInput).focus();
		return;
	}


	var resp = confirm('Confirma que desea agregar el nuevo sitio a 1000pass.com?');
	if (resp == true) {

		var selections = new Array();
		selections.push(getIdentifier($(loginInput)) + '|' + $(loginInput).val());
		if (extraInput != undefined) {
			selections.push(getIdentifier($(extraInput)) + '|' + $(extraInput).val());
		} else {
			selections.push('');
		}
		selections.push(getIdentifier($(passwordInput)) + '|' + $(passwordInput).val());

		if (typeof(submitInput) == 'object') {
			selections.push(getIdentifier(submitInput));
		} else {
			selections.push('');
		}


		var port = chrome.extension.connect({name: 'finish_adding'});
		port.postMessage(selections);

	} else {
		$('.tp_selected').each(
			function() {
				$(this).removeClass('tp_selected');
			}
		);

	}
}

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
		var tmpExtraField = data.extraField.split('|');
		if (tmpExtraField[0] == 'id') {
			var myExtra = document.getElementById(tmpExtraField[1]);
		} else if (tmpExtraField[0] == 'name') {
			var myExtras = document.getElementsByTagName('input');
			for (var i=0; i<myExtras.length; i++) {
				if (myExtras[i].name == tmpExtraField[1]) {
					var myExtra = myExtras[i];
					break;
				}
			}
		}
		if (myExtra != undefined) {
			myExtra.value = data.extra;
		}



		/** Submit the form */
		console.log(data.form);
		if (data.form == '') {

			var myForm = myUsername.form;
			setTimeout(function(){myForm.submit();}, 2000);

		} else {
			var tmpForm = data.form.split('|');
			if (tmpForm[0] == 'id') {
				var mySubmitter= document.getElementById(tmpForm[1]);
			} else if (tmpForm[0] == 'name') {
				var myInputs = document.getElementsByTagName('input');
				for (var i = 0; i < myInputs.length; i++) {
					if (myInputs[i].name == tmpForm[1]) {
						var mySubmitter = myInputs[i];
						break;
					}
				}

				if (mySubmitter == undefined) {
					var myForms = document.getElementsByTagName('form');
					for (var i = 0; i < myForms.length; i++) {
						if (myForms[i].name == tmpForm[1]) {
							var mySubmitter = myForms[i];
							break;
						}
					}
				}
			/* } else if (tmpForm[0] == 'action') {
				var myForms = document.getElementsByTagName('form');
				for (var i = 0; i < myForms.length; i++) {
					if ((tmpForm[0] == 'name' && myForms[i].name == tmpForm[1])
						|| (tmpForm[0] == 'action' && myForms[i].action == tmpForm[1])) {
						var mySubmitter = myForms[i];
						break;
					}
				}
			*/
			} else if (tmpForm[0] == 'class') {
				var myForms = myUsername.form.getElementsByClassName(tmpForm[1]);
				if (myForms.length == 0) {
					myForms = document.getElementsByClassName(tmpForm[1]);
				}
				var mySubmitter = myForms[0];
			}

			setTimeout(
				function(){

					// try with the param, but also old fashion if the first does not work
					if (mySubmitter != undefined && mySubmitter != null && typeof(mySubmitter) == 'object') {
						var evt = document.createEvent('HTMLEvents');
						evt.initEvent('click', true, true ); // event type,bubbling,cancelable
						mySubmitter.dispatchEvent(evt);
						console.log(mySubmitter);

					}
				},
			2000);

			// also try to submit old fashion way...
			setTimeout(
				function(){
					var myForm = myUsername.form;
					myForm.submit();
				},
			4000);

		}

		var port = chrome.extension.connect({name: "done"});
		port.postMessage(data);


	});
});



var getIdentifier = function(elem) {

	var text = '';

	if ($(elem).attr('id') != undefined && $(elem).attr('id') != '') {
		text = 'id|' + $(elem).attr('id');
	} else if ($(elem).attr('name') != undefined && $(elem).attr('name') != '') {
		text = 'name|' + $(elem).attr('name');
	} else if ($(elem).attr('class') != undefined &&  $(elem).attr('class') != '') {
		text = 'class|' + $(elem).attr('class').replace('tp_selected', '').trim();
	}

	return text;

}


var bind_events = function() {

	$('head').append('<style type="text/css"> .tp_over { border:1px solid red; } .tp_selected { border:10px dotted green; }</style>');


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
			url: $('#url', plugin).html().replace(/&amp;/g, '&'),
			logout_url: $('#logout_url', plugin).html().replace(/&amp;/g, '&'),
			logout_type: $('#logout_url', plugin).attr('class'),
			username: $('#username', plugin).html(),
			usernameField: $('#username', plugin).attr('class'),
			password: $('#password', plugin).html(),
			passwordField: $('#password', plugin).attr('class'),
			extra: $('#extra', plugin).html(),
			extraField: $('#extra', plugin).attr('class'),
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
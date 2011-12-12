var objects = {'usernameElement' : null, 'passwordElement' : null, 'enterElement' : null};

function decode64(input) {

	var keyStr = "ABCDEFGHIJKLMNOP" +
				"QRSTUVWXYZabcdef" +
				"ghijklmnopqrstuv" +
				"wxyz0123456789+/" +
				"=";

	var output = "";
	var chr1, chr2, chr3 = "";
	var enc1, enc2, enc3, enc4 = "";
	var i = 0;

	// remove all characters that are not A-Z, a-z, 0-9, +, /, or =
	var base64test = /[^A-Za-z0-9\+\/\=]/g;
	if (base64test.exec(input)) {
		alert("There were invalid base64 characters in the input text.\n" +
			"Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
			"Expect errors in decoding.");
	}
	input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

	do {
		enc1 = keyStr.indexOf(input.charAt(i++));
		enc2 = keyStr.indexOf(input.charAt(i++));
		enc3 = keyStr.indexOf(input.charAt(i++));
		enc4 = keyStr.indexOf(input.charAt(i++));

		chr1 = (enc1 << 2) | (enc2 >> 4);
		chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		chr3 = ((enc3 & 3) << 6) | enc4;

		output = output + String.fromCharCode(chr1);

		if (enc3 != 64) {
		output = output + String.fromCharCode(chr2);
		}
		if (enc4 != 64) {
		output = output + String.fromCharCode(chr3);
		}

		chr1 = chr2 = chr3 = "";
		enc1 = enc2 = enc3 = enc4 = "";

	} while (i < input.length);

	return unescape(output);
}


function addMark(element, text) {

	var bubble = "<p class='1000pass_bubble' style='padding:3px 0 0 0;background: transparent url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAAAUCAYAAAAN+ioeAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9sIGhc6EnSAoLIAAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAACCUlEQVRYw+3Xv09TURjG8e9zaUVFC8RqQ0pcMDJBQghhIEyExQ0G/wiZXPwTnFhbZxlxcHIwDoaxM5ogi2I00URCcknKj17v44ANcNOAwHXqeZezvcPnvHnOe0SOdbj5er71YWU5jbfG3fyJEF1ZNo6KDdLWqqS67f3cJPbeP3uUfH77xs0foB6LqHudwbJlCewNYKInj8at7c3B5OPKC8df7ks9SOpaZACBOEK2pLvATiGPxgfvlq453rppG6mLhbPgkrAhKj7OBTr9tQ5Hkxx0szEimbQ1HV22QVyrHt9cVIiBJLB2jpELJelufZjbT75lsReAUWBR0lSIjjPBzwWu2L4FDEiaAGZtzwAjbdS/wP6XfgH6eErnJJVtT0sq2Z6UNARU2hMbYC8BHdeqT4F7khZt90nqBwq2r2diIMBeoaLjz4xPne3JDfWfomO3PjxnuwxMAyVgUtKQ7crJTD7aXMLLl/tjKGnA9gQwC8wAI5nlPETLVaBP7s2lpe/ZS1gARm0vAlPtyAl1Beiz4ONa9QawJmkqkJ7zGF60Tk23f5eAQuDs8AUHY18e+tRtlcdQsS9sKZ0iw5ajQiMX6N752mE0+KAZ8jkzzbYtQZqs5gJdvPNwJyqPPae3H6cJduoQF25/RjaAeq4jeLD+cj759Go5jb+Oe2+7ezcQG6JCgzRZNdQl7f8BgazjmglumqIAAAAASUVORK5CYII=) no-repeat;margin-left:70px;width:90px;height:29px;position:absolute;font-size:14px;text-align:center;font-weight:bold;color:#eeeeee;'>##TEXT##</p>";


	var myBubble = bubble.replace(/##TEXT##/, text);
	$(myBubble).insertBefore($(element));

}

var mdown = function(event) {

	if (event.target.tagName == 'INPUT' && $(event.target).attr('type') == 'text') {
		if ($(event.target).val() == '') {
			alert('Debe ingresar su nombre de usuario antes de continuar.');
		} else {
			objects.usernameElement = event.target;
			addMark($(event.target), 'Usuario');
		}
	}


	if (event.target.tagName == 'INPUT' && $(event.target).attr('type') == 'password') {
		if ($(event.target).val() == '') {
			alert('Debe ingresar su clave antes de continuar.');
		} else {
			objects.passwordElement = event.target;
			addMark($(event.target), 'Clave');
		}
	}



	var blackList = ['HEAD', 'BODY', 'IFRAME', 'FRAME', 'FRAMESET'];
	if (!(event.target.tagName == 'INPUT' &&
		($(event.target).attr('type') == 'hidden' || $(event.target).attr('type') == 'text' || $(event.target).attr('type') == 'password'))
		&& $.inArray(event.target.tagName, blackList) == -1) {

		// prevent default "submitting"
		event.target.onclick = function() {return false;};

		objects.enterElement = event.target;
		addMark($(event.target), 'Entrar');

	}


	if (objects.passwordElement == null) {
		alert('Haga click sobre el campo clave');
	} else if (objects.usernameElement == null) {
		alert('Haga click sobre el campo usuario');
	}


	if (objects.usernameElement != null && objects.passwordElement != null && objects.enterElement != null) {

		var data = {
			'usernameElement' 	: findXPath(objects.usernameElement),
			'username' 			: objects.usernameElement.value,
			'passwordElement' 	: findXPath(objects.passwordElement),
			'password' 			: objects.passwordElement.value,
			'enterElement' 		: findXPath(objects.enterElement)
		};

		var port = chrome.extension.connect({name: 'finish_adding'});
		port.postMessage(data);

	}

}


chrome.extension.onRequest.addListener(
	function(request, sender, sendResponse) {

		if (request == 'add_to_1000pass_manual') {

			document.body.addEventListener('mousedown', mdown, false);

		} else if (request == 'add_to_1000pass_auto') {

			var passwordElement = null;
			var usernameElement = null;
			var enterElement = null;

			var passwordElements = $('input:password');
			if (passwordElements.length == 1) {
				passwordElement = passwordElements[0];
			} else {

				// try finding the first not empty password field
				var found = 0;
				passwordElements.each(
					function(i, elem) {
						if ($(elem).val().trim().length > 0) {
							passwordElement = elem;
							found++;
						}
					}
				);

				// just one, got it
				if (found != 1) {
					passwordElement = null;
				}
			}


			if (passwordElement != null) {

				var possibleInputs = $('input:text', $(passwordElement.form));
				if (possibleInputs.length == 1) {
					usernameElement = possibleInputs[0];
				} else if (possibleInputs.length > 1) {
					var c = 0;
					for (var i = 0; i < possibleInputs.length; i++) {
						if (possibleInputs[i].value != '') {
							usernameElement = possibleInputs[i];
							c++;
						}
					}
					if (c > 1) {
						usernameElement = null;
					}
				}


				var possibleInputs = $('input:submit', $(passwordElement.form));
				if (possibleInputs.length == 1) {
					enterElement = possibleInputs[0];
				}


				if (enterElement == null) {
					var possibleInputs = $('button:submit', $(passwordElement.form));
					if (possibleInputs.length == 1) {
						enterElement = possibleInputs[0];
					}
				}


				addMark($(passwordElement), 'Clave');
				if (usernameElement != null) {
					addMark($(usernameElement), 'Usuario');
				}
				if (enterElement != null) {
					addMark($(enterElement), 'Entrar');
				}



				if (passwordElement != null
					&& usernameElement != null
					&& enterElement != null)
				{

					if (usernameElement.value.length == 0) {
						alert('No ha ingresado su usuario. Reintentelo luego de completar los datos necesarios.');
						window.location.reload();
						return;
					}

					if (passwordElement.value.length == 0) {
						alert('No ha ingresado su clave. Reintentelo luego de completar los datos necesarios.');
						window.location.reload();
						return;
					}


					var data = {
						'usernameElement' 	: findXPath(usernameElement),
						'username' 			: usernameElement.value,
						'passwordElement' 	: findXPath(passwordElement),
						'password' 			: passwordElement.value,
						'enterElement' 		: findXPath(enterElement)
					};


					var port = chrome.extension.connect({name: 'finish_adding'});
					port.postMessage(data);

				} else {

					objects.usernameElement = usernameElement;
					objects.passwordElement = passwordElement;
					objects.enterElement = enterElement;

					if (usernameElement != null) {
						addMark($(usernameElement), 'Usuario');
					}
					if (passwordElement != null) {
						addMark($(passwordElement), 'Clave');
					}
					if (enterElement != null) {
						addMark($(enterElement), 'Entrar');
					}


					alert('No es posible encontrar todos los elementos. Por favor, haga click sobre los elementos que no pudieron encorntrarse automaticamente.');

					document.body.addEventListener('mousedown', mdown, false);
				}

			}

		} else {
			sendResponse({html: $('body').html()});
		}
	}
);




function findXPath(theElement) {

	try {
		var xpath = '##id=' + $(theElement).attr('id');
		xpath += ';name=' + $(theElement).attr('name');
		xpath += ';class=' + $(theElement).attr('class');

		var path = new Array();
		do {
			parent = theElement.parentNode;
			var toPush = theElement.tagName;
			for (i = 1, sib = theElement.previousSibling; sib; sib = sib.previousSibling) {
				if (sib.localName == theElement.localName)
					i++;
			};
			if (i > 1) {
				toPush += '[' + i + ']';
			}
			path.push(toPush);

			theElement = theElement.parentNode;

		} while (parent.tagName != 'BODY');

		path.push('BODY');
		return xpath = '/' + path.reverse().join('/') + xpath;
	} catch (e) {
		//console.log(e);
		return '';
	}
}


function findElement(xPath) {

	// should never happend, means an error collecting the info
	if (xPath == undefined || xPath.indexOf('##') == -1) {
		return;
	}

	var tmp = xPath.split('##');

	var elementAttributes = tmp[1];
	var attributes = elementAttributes.split(';');
	var attributeId = attributes[0].replace(/id=/, '');
	var attributeName = attributes[1].replace(/name=/, '');
	var attributeClass = attributes[2].replace(/class=/, '');


	var xPathParts = tmp[0].split('/');
	var elementTagName = xPathParts[xPathParts.length - 1];
	if (elementTagName.indexOf('[') >= 0) {
		elementTagName = elementTagName.split('[')[0];
	}


	var theElement = null;


	// try in first place the id
	var possibleElements = $(elementTagName + '[id="' + attributeId + '"]');
	if (possibleElements.length == 1) {
		theElement = possibleElements.get(0);
	}


	if (theElement == null && attributeName != '') {
		var possibleElements = $(elementTagName + '[name="' + attributeName + '"]');
		if (possibleElements.length == 1) {
			theElement = possibleElements.get(0);
		}
	}



	// try in second place the exact path
	if (theElement == null) {
		possibleElements = document.getElementsByTagName(elementTagName);

		for (var i = 0; i < possibleElements.length; i++) {
			if (findXPath(possibleElements[i]) == xPath) {
				theElement = possibleElements[i];
				break;
			}
		}
	}


	// next, try the path skipping attributes
	if (theElement == null) {
		var cleanXPath = tmp[0];
		for (var i = 0; i < possibleElements.length; i++) {

			var possibleXPath = findXPath(possibleElements[i]);
			var possibleTmp = possibleXPath.split('##');
			var cleanPossibleXPath = possibleTmp[0];

			if (cleanPossibleXPath == cleanXPath) {
				theElement = possibleElements[i];
				break;
			}
		}
	}


	// next try inside iframes the the id
	try {
		if (theElement == null) {
			$('iframe').each(function(i, elem) {

				if ($(elem).attr('src').indexOf('chrome-extension') == -1) {
					var possibleElements = $(elementTagName + '[id="' + attributeId + '"]', $(elem).contents());
					if (possibleElements.length == 1) {
						theElement = possibleElements.get(0);
						return;
					}
				}
			});
		}


		if (theElement == null && attributeName != '') {
			$('iframe').each(function(i, elem) {

				if ($(elem).attr('src').indexOf('chrome-extension') == -1) {
					var possibleElements = $(elementTagName + '[name="' + attributeName + '"]', $(elem).contents());
					if (possibleElements.length == 1) {
						theElement = possibleElements.get(0);
						return;
					}
				}
			});
		}


		// next try inside iframes the exact path
		if (theElement == null) {
			$('iframe').each(function(i, elem) {

				if ($(elem).attr('src').indexOf('chrome-extension') == -1) {
					possibleElements = $(elementTagName, $(elem).contents());
					for (var i = 0; i < possibleElements.length; i++) {
						if (findXPath(possibleElements[i]) == xPath) {
							theElement = possibleElements[i];
							return;
							break;
						}
					}
				}
			});
		}



		// next, try inside iframes the path skipping attributes
		if (theElement == null) {
			var cleanXPath = tmp[0];
			$('iframe').each(function(i, elem) {

				if ($(elem).attr('src').indexOf('chrome-extension') == -1) {
					for (var i = 0; i < possibleElements.length; i++) {
						var possibleXPath = findXPath(possibleElements[i]);
						var possibleTmp = possibleXPath.split('##');
						var cleanPossibleXPath = possibleTmp[0];

						if (cleanPossibleXPath == cleanXPath) {
							theElement = possibleElements[i];
							return;
							break;
						}
					}
				}
			});
		}
	} catch (e) {
		console.log(e);
	}


	// one more chance
	if (theElement == null && attributeName != '') {
		var possibleElements = $(elementTagName + '[name="' + attributeName + '"]');
		if (possibleElements.length >= 1) {
			theElement = possibleElements.get(0);
		}
	}

	console.log(theElement);
	return theElement;
}


var fillFields = function(data) {

	if (data.state == 'opened') {
		return;
	}

	var myUsername = findElement(data.usernameField);
	var myPassword = findElement(data.passwordField);
	var myEnter = findElement(data.submitField);

	if (myUsername == null || myPassword == null) {
		return;
	}

	myUsername.value = data.username;
	myPassword.value = data.password;


	if (myEnter != undefined && myEnter != null && typeof(myEnter) == 'object') {
		var evt = document.createEvent('HTMLEvents');
		evt.initEvent('click', true, true ); // event type, bubbling, cancelable
		myEnter.dispatchEvent(evt);

	}

	// If something goes wrong, try this way too
	try {
		var myForm = myUsername.form;
		setTimeout(function(){myForm.submit();}, 2000);
	} catch (e) {}

	var port = chrome.extension.connect({name: 'done'});
	port.postMessage(data);

}

chrome.extension.onConnect.addListener(function(port) {
	port.onMessage.addListener(function(data) {
		fillFields(data);
	});
});



var bind_events = function() {

	var location = window.location.toString();
	if (location.substr(0, 17) != 'http://localhost/' && location.substr(0, 24) != 'http://www.1000pass.com/' && location.substr(0, 25) != 'https://www.1000pass.com/') {

		return;
	}



	/** Modify the dom to tell the addon is present */
	$('div#1000pass_add_on').addClass('installed');
	$('div#1000pass_add_on_version').text('1.0');


	if (typeof($('div#1000pass_add_on')) == 'object') {

		var token = $('div#1000pass_add_on').attr('token');
		var port = chrome.extension.connect({name: '1000pass'});
		port.postMessage(token);
	}



	$('img.remote_site_logo').css('cursor', 'pointer');
	$('img.remote_site_logo').click(function() {

		var plugin = $(this).parent().parent();
		var data = {
			id: $('#plugin_identifier', plugin).html(),
			title: $('#title', plugin).html(),
			url: $('#url', plugin).html().replace(/&amp;/g, '&'),
			username: $('#username', plugin).html(),
			usernameField: $('#username', plugin).attr('class'),
			password: decode64($('#password', plugin).html()),
			passwordField: $('#password', plugin).attr('class'),
			extra: $('#extra', plugin).html(),
			extraField: $('#extra', plugin).attr('class'),
			submitField: $('#submit', plugin).attr('class')
		};

		var port = chrome.extension.connect({name: 'go'});
		port.postMessage(data);

	});
}


if (document.readyState == 'complete') {
	bind_events();
} else {
	window.addEventListener('load', bind_events, false);
}
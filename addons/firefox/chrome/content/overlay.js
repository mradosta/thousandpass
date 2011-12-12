var thousandpass = function () {

	return {

		l : function(m) {
			Firebug.Console.log(m);
		},


		decode64 : function (input) {

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
		}, //decode64


		init : function() {

			gBrowser.addEventListener('load', function () {
				var location = window.content.document.location.toString();
				if (location.substr(0, 17) != 'http://localhost/' && location.substr(0, 24) != 'http://www.1000pass.com/' && location.substr(0, 25) != 'https://www.1000pass.com/') {
					return;
				}
				thousandpass.bindEvents();
			}, true);
		}, //init

		token : null,

		objects : {'usernameElement' : null, 'passwordElement' : null, 'enterElement' : null},

		findXPath : function(theElement) {

			try {
				var xpath = '##id=' + $(theElement, window.content.document).attr('id');
				xpath += ';name=' + $(theElement, window.content.document).attr('name');
				xpath += ';class=' + $(theElement, window.content.document).attr('class');

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
				//thousandpass.l(e);
				return '';
			}
		}, //findXPath


		getFrames : function() {

			var myFrames = new Array();

			for (var i = 0; i < window.content.top.frames.length; i++) {
				if (window.content.top.frames[i] != undefined
					&& window.content.top.frames[i].document != undefined) {
					myFrames.push(window.content.top.frames[i]);
				}
			}


			for (var i = 0; i < myFrames.length; i++) {
				if (myFrames[i].document != undefined) {
					var iframes = myFrames[i].document.getElementsByTagName('iframe');
					for (var j = 0; j < iframes.length; j++) {
						if (iframes[j].document != undefined) {
							myFrames.push(iframes[j]);
						}
					}
				}
			}


			if (window.content.document != undefined) {
			//if (myFrames.length == 0) {
				myFrames.push(window.content);
			}

			return myFrames;
		},


		mdown : function(event) {


			if (event.target.tagName == 'INPUT' && $(event.target).attr('type') == 'text') {
				if ($(event.target).val() == '') {
					alert('Debe ingresar su nombre de usuario antes de continuar.');
				} else {
					thousandpass.objects.usernameElement = event.target;
					thousandpass.addMark(event.target, 'Usuario');
				}
			}


			if (event.target.tagName == 'INPUT' && $(event.target).attr('type') == 'password') {
				if ($(event.target).val() == '') {
					alert('Debe ingresar su clave antes de continuar.');
				} else {
					thousandpass.objects.passwordElement = event.target;
					thousandpass.addMark(event.target, 'Clave');
				}
			}


			if (!(event.target.tagName == 'INPUT' &&
				($(event.target).attr('type') == 'text' || $(event.target).attr('type') == 'password'))) {

				thousandpass.objects.enterElement = event.target;
				thousandpass.addMark(event.target, 'Entrar');
			}


			if (thousandpass.objects.passwordElement == null) {
				alert('Haga click sobre el campo clave');
			} else if (thousandpass.objects.usernameElement == null) {
				alert('Haga click sobre el campo usuario');
			}


			if (thousandpass.objects.usernameElement != null && thousandpass.objects.passwordElement != null && thousandpass.objects.enterElement != null) {

				var data = {
					'usernameElement' 	: thousandpass.findXPath(thousandpass.objects.usernameElement),
					'username' 			: thousandpass.objects.usernameElement.value,
					'passwordElement' 	: thousandpass.findXPath(thousandpass.objects.passwordElement),
					'password' 			: thousandpass.decode64(thousandpass.objects.passwordElement.value),
					'enterElement' 		: thousandpass.findXPath(thousandpass.objects.enterElement)
				};

				thousandpass.save(data);
			}
		}, // mdown


		addMark : function(element, text) {

			var bubble = "<p class='1000pass_bubble' style='padding:3px 0 0 0;background: transparent url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFoAAAAUCAYAAAAN+ioeAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9sIGhc6EnSAoLIAAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAACCUlEQVRYw+3Xv09TURjG8e9zaUVFC8RqQ0pcMDJBQghhIEyExQ0G/wiZXPwTnFhbZxlxcHIwDoaxM5ogi2I00URCcknKj17v44ANcNOAwHXqeZezvcPnvHnOe0SOdbj5er71YWU5jbfG3fyJEF1ZNo6KDdLWqqS67f3cJPbeP3uUfH77xs0foB6LqHudwbJlCewNYKInj8at7c3B5OPKC8df7ks9SOpaZACBOEK2pLvATiGPxgfvlq453rppG6mLhbPgkrAhKj7OBTr9tQ5Hkxx0szEimbQ1HV22QVyrHt9cVIiBJLB2jpELJelufZjbT75lsReAUWBR0lSIjjPBzwWu2L4FDEiaAGZtzwAjbdS/wP6XfgH6eErnJJVtT0sq2Z6UNARU2hMbYC8BHdeqT4F7khZt90nqBwq2r2diIMBeoaLjz4xPne3JDfWfomO3PjxnuwxMAyVgUtKQ7crJTD7aXMLLl/tjKGnA9gQwC8wAI5nlPETLVaBP7s2lpe/ZS1gARm0vAlPtyAl1Beiz4ONa9QawJmkqkJ7zGF60Tk23f5eAQuDs8AUHY18e+tRtlcdQsS9sKZ0iw5ajQiMX6N752mE0+KAZ8jkzzbYtQZqs5gJdvPNwJyqPPae3H6cJduoQF25/RjaAeq4jeLD+cj759Go5jb+Oe2+7ezcQG6JCgzRZNdQl7f8BgazjmglumqIAAAAASUVORK5CYII=) no-repeat;margin-left:70px;width:90px;height:29px;position:absolute;font-size:14px;text-align:center;font-weight:bold;color:#eeeeee;'>##TEXT##</p>";


			var myBubble = bubble.replace(/##TEXT##/, text);
			$(myBubble, window.content.document).insertBefore($(element));
		},

		save : function(data) {

			var req = new XMLHttpRequest();
			//req.open('POST', "http://localhost/thousandpass/sites_users/extension_add", false);
			req.open('POST', "http://www.1000pass.com/sites_users/extension_add", false);
			req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=ISO-8859-1');
			req.onreadystatechange = function (aEvt) {
				if (req.readyState == 4) {
					if (req.status == 200) {

						alert(req.responseText);

					} else {
						alert('Ocurrio un error al agregar el nuevo sitio a 1000pass.com');
					}
				}
			};


			var url = window.top.getBrowser().selectedBrowser.contentWindow.location.href.replace(/&/g, '**||**');

			var d = 'token=' + thousandpass.token + '&title=' + encodeURI(window.content.document.title) + '&login_url=' + encodeURI(url) + '&username_field=' + data.usernameElement + '|' + data.username + '&password_field=' + data.passwordElement + '|' + data.password + '&submit=' + data.enterElement;

			req.send(d);
		}, // save


		addTo1000PassAuto : function () {

			if (thousandpass.token == null) {
				alert('Debe ingresar a su cuenta de 1000Pass.com en una nueva pestaña del navegador antes de poder agregar el nuevo sitio.')
				return;
			}


			var answer = confirm ('Para poder agregar el nuevo sitio a 1000Pass.com es necesario que haya ingresado su usuario y clave. Ya lo ha hecho?')
			var answer = true;
			if (!answer) {
				return;
			}


			var passwordElement = null;
			var usernameElement = null;
			var enterElement = null;


			var myFrames = thousandpass.getFrames();
			for (var i = 0; i < myFrames.length; i++) {
				var passwordElements = $('input:password', myFrames[i].document);
				if (passwordElements.length > 0) {
					break;
				}
			}

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


				thousandpass.addMark($(passwordElement), 'Clave');
				if (usernameElement != null) {
					thousandpass.addMark($(usernameElement), 'Usuario');
				}
				if (enterElement != null) {
					thousandpass.addMark($(enterElement), 'Entrar');
				}


				if (passwordElement != null
					&& usernameElement != null
					&& enterElement != null)
				{

					if (usernameElement.value.length == 0) {
						alert('No ha ingresado su usuario. Reintentelo luego de completar los datos necesarios.');
						window.content.location.reload();
						return;
					}

					if (passwordElement.value.length == 0) {
						alert('No ha ingresado su clave. Reintentelo luego de completar los datos necesarios.');
						window.content.location.reload();
						return;
					}

					var data = {
						'usernameElement' 	: thousandpass.findXPath(usernameElement),
						'username' 			: usernameElement.value,
						'passwordElement' 	: thousandpass.findXPath(passwordElement),
						'password' 			: passwordElement.value,
						'enterElement' 		: thousandpass.findXPath(enterElement)
					};

					thousandpass.save(data);

				} else {

					thousandpass.objects.usernameElement = usernameElement;
					thousandpass.objects.passwordElement = passwordElement;
					thousandpass.objects.enterElement = enterElement;


					alert('No es posible encontrar todos los elementos. Por favor, haga click sobre los elementos que no pudieron encorntrarse automaticamente.');


					var myFrames = thousandpass.getFrames();
					for (var i = 0; i < myFrames.length; i++) {

						$('*', myFrames[i].document).each(function() {
							$(this).click(function(e) {
								e.preventDefault();
								e.stopPropagation();
							});
						});

						myFrames[i].document.body.addEventListener('mousedown', thousandpass.mdown, false);

					}
					
				}
			}
		},

		addTo1000PassManual: function () {

			if (thousandpass.token == null) {
				alert('Debe ingresar a su cuenta de 1000Pass.com en una nueva pestaña del navegador antes de poder agregar el nuevo sitio.')
				return;
			}


			var answer = confirm ('Para poder agregar el nuevo sitio a 1000Pass.com es necesario que haya ingresado su usuario y clave. Ya lo ha hecho?')
			if (!answer) {
				return;
			}


			var myFrames = thousandpass.getFrames();
			for (var i = 0; i < myFrames.length; i++) {

				$('*', myFrames[i].document).each(function() {
					$(this).click(function(e) {
						e.preventDefault();
						e.stopPropagation();
					});
				});

				myFrames[i].document.body.addEventListener('mousedown', thousandpass.mdown, false);
			}

			alert('Haga click sobre el boton ingresar');

		},


		findElement : function(xPath, doc) {

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
			var possibleElements = $(elementTagName + '[id="' + attributeId + '"]', doc);
			if (possibleElements.length == 1) {
				theElement = possibleElements.get(0);
			}


			if (theElement == null && attributeName != '') {
				var possibleElements = $(elementTagName + '[name="' + attributeName + '"]', doc);
				if (possibleElements.length == 1) {
					theElement = possibleElements.get(0);
				}
			}


			// try in second place the exact path
			if (theElement == null) {
				possibleElements = doc.getElementsByTagName(elementTagName);

				for (var i = 0; i < possibleElements.length; i++) {
					if (thousandpass.findXPath(possibleElements[i]) == xPath) {
						theElement = possibleElements[i];
						break;
					}
				}
			}


			// next, try the path skipping attributes
			if (theElement == null) {
				var cleanXPath = tmp[0];
				for (var i = 0; i < possibleElements.length; i++) {

					var possibleXPath = thousandpass.findXPath(possibleElements[i]);
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

						var possibleElements = $(elementTagName + '[id="' + attributeId + '"]', $(elem).contents());
						if (possibleElements.length == 1) {
							theElement = possibleElements.get(0);
							return;
						}
					});
				}


				if (theElement == null && attributeName != '') {
					$('iframe').each(function(i, elem) {

						var possibleElements = $(elementTagName + '[name="' + attributeName + '"]', $(elem).contents());
						if (possibleElements.length == 1) {
							theElement = possibleElements.get(0);
							return;
						}
					});
				}


				// next try inside iframes the exact path
				if (theElement == null) {
					$('iframe').each(function(i, elem) {

						possibleElements = $(elementTagName, $(elem).contents());
						for (var i = 0; i < possibleElements.length; i++) {
							if (thousandpass.findXPath(possibleElements[i]) == xPath) {
								theElement = possibleElements[i];
								return;
								break;
							}
						}
					});
				}



				// next, try inside iframes the path skipping attributes
				if (theElement == null) {
					var cleanXPath = tmp[0];
					$('iframe').each(function(i, elem) {

						for (var i = 0; i < possibleElements.length; i++) {
							var possibleXPath = thousandpass.findXPath(possibleElements[i]);
							var possibleTmp = possibleXPath.split('##');
							var cleanPossibleXPath = possibleTmp[0];

							if (cleanPossibleXPath == cleanXPath) {
								theElement = possibleElements[i];
								return;
								break;
							}
						}
					});
				}
			} catch (e) {
				//console.log(e);
			}

			return theElement;

		}, // findElement


		onclick : function () {

			var wm = Components.classes["@mozilla.org/appshell/window-mediator;1"]
								.getService(Components.interfaces.nsIWindowMediator);
			var browserEnumerator = wm.getEnumerator("navigator:browser");

			var tabbrowser = browserEnumerator.getNext().gBrowser;

			// Create tab
			var newTab = tabbrowser.addTab('http://www.1000pass.com');

			// Focus tab
			tabbrowser.selectedTab = newTab;

			// Focus *this* browser window in case another one is currently focused
			tabbrowser.ownerDocument.defaultView.focus();
		}, //onclick


		getHostNameFromUrl : function (url) {
			return url.match(/:\/\/(.[^/]+)/)[1]; //.replace('www.','');
		},


		beginsWith : function (string, text) {
			var pos = string.indexOf(text);
			if (pos == 0) {
				return true;
			} else {
				return false;
			}
		},

		deleteCookies : function (url) {
			var cookieManager = Components.classes["@mozilla.org/cookiemanager;1"].getService(Components.interfaces.nsICookieManager);

			current_url = url.toUpperCase()
			
			// Add a "." before the URL, as some cookies are stored as .www.domain.com
			if (thousandpass.beginsWith(current_url, 'HTTP://') && current_url.length > 7) {
				//alert("beginswith 1 true " + current_url);
				var s = current_url;
				current_url = s.substring(0, 7) + '.' + s.substring(7);
			} else if (thousandpass.beginsWith(current_url, 'HTTPS://') && current_url.length > 8) {
				var s = current_url;
				current_url = s.substring(0, 8) + '.' + s.substring(8);
			}


			var iter = cookieManager.enumerator;
			while (iter.hasMoreElements()) {
				var cookie = iter.getNext();
				if (cookie instanceof Components.interfaces.nsICookie) {
					if (current_url.indexOf(cookie.host.toUpperCase()) != -1) {
						cookieManager.remove(cookie.host, cookie.name, cookie.path, cookie.blocked);
					}
				}
			}
		},


		openAndReuseOneTabPerAttributeValue : function (data) {

			var domain = thousandpass.getHostNameFromUrl(data.url);

			var attrName = 'my-attribute-mark';

			var wm = Components.classes["@mozilla.org/appshell/window-mediator;1"]
								.getService(Components.interfaces.nsIWindowMediator);
			for (var found = false, index = 0, tabbrowser = wm.getEnumerator('navigator:browser').getNext().gBrowser;
				index < tabbrowser.tabContainer.childNodes.length && !found;
				index++) {

					// Get the next tab
					var currentTab = tabbrowser.tabContainer.childNodes[index];

					// Does this tab contain our custom attribute?
					if (currentTab.hasAttribute(attrName)) {

						var attrData = currentTab.getAttribute(attrName).split('|');

						if (attrData[0] == domain) {

							tabbrowser.selectedTab = currentTab;

							if (attrData[1] != data.id) {
								
								thousandpass.deleteCookies(data.url);

								setTimeout(function() {

									gBrowser.removeCurrentTab();

									// Our tab isn't open. Open it now.
									var browserEnumerator = wm.getEnumerator("navigator:browser");
									var tabbrowser = browserEnumerator.getNext().gBrowser;

									// Create tab
									var newTab = tabbrowser.addTab(data.url.replace(/&amp;/g, '&'));
									newTab.setAttribute(attrName, domain + '|' + data.id);

									// Focus tab
									tabbrowser.selectedTab = newTab;

									// Focus *this* browser window in case another one is currently focused
									tabbrowser.ownerDocument.defaultView.focus();

								}, 300);

							} else {

								// Focus *this* browser window in case another one is currently focused
								tabbrowser.ownerDocument.defaultView.focus();
							}

							found = true;
							break;
						}
					}
			} //for


			if (!found) {


				thousandpass.deleteCookies(data.url);

				// Our tab isn't open. Open it now.
				var browserEnumerator = wm.getEnumerator("navigator:browser");
				var tabbrowser = browserEnumerator.getNext().gBrowser;

				// Create tab
				var newTab = tabbrowser.addTab(data.url.replace(/&amp;/g, '&'));
				newTab.setAttribute(attrName, domain + '|' + data.id);

				// Focus tab
				tabbrowser.selectedTab = newTab;

				// Focus *this* browser window in case another one is currently focused
				tabbrowser.ownerDocument.defaultView.focus();
			}

			return tabbrowser;
		}, //openAndReuseOneTabPerAttribute


		bindEvents : function () {

			/** Modify the dom to tell the addon is present */
			$('div#1000pass_add_on', window.content.document).addClass('installed');
			$('div#1000pass_add_on_version', window.content.document).text('1.0');;

			var lToken = $('div#1000pass_add_on', window.content.document).attr('token');
			if (lToken != undefined && lToken.length > 10) {
				thousandpass.token = lToken;
			}

			/*
			function refreshOnTabSelected(event) {
				alert(window.content.document.getElementById('1000pass_add_on'));
				gBrowser.tabContainer.removeEventListener("TabSelect", refreshOnTabSelected, false);
			}
			*/



			$('img.remote_site_logo', window.content.document).css('cursor', 'pointer');
			var clickableLogos = window.content.document.getElementsByClassName('remote_site_logo');
			for (var i=0; i<clickableLogos.length; i++) {
				clickableLogos[i].addEventListener('click', openFillFieldsAndSubmit, false);
			}


			function openFillFieldsAndSubmit() {

				/** Get necessary data */
				var plugin = $(this).parent().parent();

				var data = {
					id: $('#plugin_identifier', plugin).html(),
					title: $('#title', plugin).html(),
					url: $('#url', plugin).html().replace(/&amp;/g, '&'),
					username: $('#username', plugin).html(),
					usernameField: $('#username', plugin).attr('class'),
					password: $('#password', plugin).html(),
					passwordField: $('#password', plugin).attr('class'),
					extra: $('#extra', plugin).html(),
					extraField: $('#extra', plugin).attr('class'),
					submitField: $('#submit', plugin).attr('class')
				};


				var onLoadTabListener = function (data) {

					var selectedDoc = null;
					var myFrames = thousandpass.getFrames();
					for (var i = 0; i < myFrames.length; i++) {
						var myUsername = thousandpass.findElement(data.usernameField, myFrames[i].document);
						if (myUsername != null) {
							var myPassword = thousandpass.findElement(data.passwordField, myFrames[i].document);
							var myEnter = thousandpass.findElement(data.submitField, myFrames[i].document);

							selectedDoc = myFrames[i].document;
							break;
						}
					}


					if (myUsername == null || myPassword == null) {
						return;
					}

					myUsername.value = data.username;
					myPassword.value = data.password;


					if (myEnter != undefined && myEnter != null && typeof(myEnter) == 'object') {

						var evt = document.createEvent('MouseEvents');
						evt.initMouseEvent('click', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
						myEnter.dispatchEvent(evt);

					}

					// If something goes wrong, try this way too
					try {
						var myForm = myUsername.form;
						setTimeout(function(){
							if (data.url == window.top.getBrowser().selectedBrowser.contentWindow.location.href) {
								myForm.submit();
							}
						}, 4000);
					} catch (e) {}


					/** When finished, must remove event listener to prevent re-posting data when page re-loading */
					this.removeEventListener('load', onLoadTabListener, true);
				}; // onLoadTabListener


				var myTab = thousandpass.openAndReuseOneTabPerAttributeValue(data);
				if (data.usernameField != '') {

					var doTheJob = function(event) {

						if (event.originalTarget instanceof HTMLDocument) {

							var win = event.originalTarget.defaultView;
							if (win.frameElement) {
								//setTimeout(function() {onLoadTabListener(data);}, 8000);
								setTimeout(function() {
									onLoadTabListener(data);
									myTab.removeEventListener('load', doTheJob, true);
								}, 8000);
							} else {
								setTimeout(function() {
									onLoadTabListener(data);
									myTab.removeEventListener('load', doTheJob, true);
								}, 1000);
							}
						}
					}
					myTab.addEventListener('load', doTheJob, true);
				}

			} //openFillFieldsAndSubmit

		} // bindEvents
	} // return

}();
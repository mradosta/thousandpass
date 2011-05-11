var thousandpass = function () {

	return {
		init : function () {

			gBrowser.addEventListener("load", function () {
				var location = window.content.document.location.toString();
				if (location.substr(0, 17) != 'http://localhost/' && location.substr(0, 24) != 'http://www.1000pass.com/' && location.substr(0, 25) != 'https://www.1000pass.com/') {
					return;
				}
				thousandpass.bindEvents();
			}, true);
		}, //init


		objects : [],

		mdown : function(event) {

			var finishSelection = false;
			var object;
			if (thousandpass.objects.length == 0) {
				if (event.target.tagName != 'INPUT' || $(event.target).attr('type') != 'text') {
					alert('Debe seleccionar un campo para el ingreso del nombre de usuario');
					return;
				} else if ($(event.target).val().trim().length == 0) {
					alert('Debe completar el campo nombre de usuario antes de continuar con la seleccion');
					$(event.target).focus();
					return;
				}
				object = event.target;

			} else if (thousandpass.objects.length == 1) {
				if (event.target.tagName != 'INPUT' || $(event.target).attr('type') != 'password') {
					alert('Debe seleccionar un campo para el ingreso de la clave');
					return;
				} else if ($(event.target).val().trim().length == 0) {
					alert('Debe completar el campo clave antes de continuar con la seleccion');
					$(event.target).focus();
					return;
				}
				object = event.target;

			} else if (thousandpass.objects.length >= 2) {

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



			thousandpass.objects.push(object);
			$(object).addClass('tp_selected');

			if (finishSelection) {

				var loginInput = thousandpass.objects[0];
				var passwordInput = thousandpass.objects[1];
				if (thousandpass.objects.length == 3) {
					var extraInput = null;
					var submitInput = thousandpass.objects[2];
				} else {
					var extraInput = thousandpass.objects[2];
					var submitInput = thousandpass.objects[3];
				}


				thousandpass.save(loginInput, extraInput, passwordInput, submitInput);

				window.content.document.body.removeEventListener('mousedown', mdown, false);
			}

		},

		getIdentifier : function(elem) {

			var text = '';

			if ($(elem).attr('id') != undefined && $(elem).attr('id') != '') {
				text = 'id|' + $(elem).attr('id');
			} else if ($(elem).attr('name') != undefined && $(elem).attr('name') != '') {
				text = 'name|' + $(elem).attr('name');
			} else if ($(elem).attr('class') != undefined &&  $(elem).attr('class') != '') {
				text = 'class|' + $(elem).attr('class').replace('tp_selected', '').trim();
			}

			return text;

		}, //getIdentifier

		save : function(loginInput, extraInput, passwordInput, submitInput) {

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
				selections.push(thousandpass.getIdentifier($(loginInput)) + '|' + $(loginInput).val());
				if (extraInput != undefined) {
					selections.push(thousandpass.getIdentifier($(extraInput)) + '|' + $(extraInput).val());
				} else {
					selections.push('');
				}
				selections.push(thousandpass.getIdentifier($(passwordInput)) + '|' + $(passwordInput).val());

				if (typeof(submitInput) == 'object') {
					selections.push(thousandpass.getIdentifier(submitInput));
				} else {
					selections.push('');
				}



				var req = new XMLHttpRequest();
				req.open('POST', "http://localhost/thousandpass/sites_users/extension_add", true);
				//req.open('POST', "http://www.1000pass.com/sites_users/extension_add", true);
				req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=ISO-8859-1');
				req.onreadystatechange = function (aEvt) {
					if (req.readyState == 4) {
						if (req.status == 200) {

							if (req.responseText == 'ok') {
								alert('El sitio se agrego correctamente a su cuenta 1000pass.com');
							} else if (req.responseText == 'er') {
								alert('No fue posible agregar el nuevo sitio a su cuenta 1000pass.com. Verifique que su sesion en 1000pass.com este activa');
							} else if (req.responseText == 'du') {
								alert('El sitio que intenta agregar ya existe en su cuenta de 1000pass.com');
							} else {
								alert('Ocurrio un error al agregar el nuevo sitio a 1000pass.com');
							}

						} else {
							alert('Ocurrio un error al agregar el nuevo sitio a 1000pass.com');
						}
					}
				};


				var url = window.top.getBrowser().selectedBrowser.contentWindow.location.href.replace(/&/g, '**||**');
				var d = 'title=' + window.content.document.title + '&login_url=' + url + '&logo=' + url.match(/([http|https]+):\/\/(.[^/]+)/)[0] + '/favicon.ico' + '&username_field=' + selections[0] + '&extra_field=' + selections[1] + '&password_field=' + selections[2] + '&submit=' + selections[3];
				alert(d);
				req.send(d);

			} else {
				$('.tp_selected').each(
					function() {
						$(this).removeClass('tp_selected');
					}
				);

			}
		}, //save


		saveLoginInfo : function (form) {

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
						&& thousandpass.getIdentifier(form) == thousandpass.getIdentifier($(elem).attr('form'))
						&& $(elem).val().trim().length > 0) {

						loginInput = elem;
						return;
					}
				});

				$('input[type="password"]').each(function(i, elem) {
					if (thousandpass.getIdentifier(form) == thousandpass.getIdentifier($(elem).attr('form'))) {
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

			thousandpass.save(loginInput, extraInput, passwordInput, submitInput);

		}, //saveLoginInfo


		addTo1000PassAuto : function () {

			var passwordFields = $('input[type="password"]', window.content.document);
			if (passwordFields.length == 0) {
				//alert('No es posible encontrar los campos de usuario y contraseña en esta pagina. Se buscara mas profundo...');
			} else if (passwordFields.length == 1) {
				thousandpass.saveLoginInfo(passwordFields[0].form);
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
					thousandpass.saveLoginInfo(rightForm);
				} else {
					// manual selection
				}
			}
		},

		addTo1000PassManual: function () {
			window.content.document.body.addEventListener('mousedown', thousandpass.mdown, true);
		},

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
								
								/** Must close previous session from first site before continue */
								if (data.logout_type == 'scrap') {

									var logout_url = data.logout_url + tabbrowser.contentDocument.body.innerHTML.split(data.logout_url).pop().replace('\'', '"').split('"').shift();
								} else {
									var logout_url = data.logout_url;
								}

								$.get(logout_url, function() {

									gBrowser.removeCurrentTab();

									// Our tab isn't open. Open it now.
									var browserEnumerator = wm.getEnumerator("navigator:browser");
									var tabbrowser = browserEnumerator.getNext().gBrowser;

									// Create tab
									var newTab = tabbrowser.addTab(data.url);
									newTab.setAttribute(attrName, domain + '|' + data.id);

									// Focus tab
									tabbrowser.selectedTab = newTab;
									
									// Focus *this* browser window in case another one is currently focused
									tabbrowser.ownerDocument.defaultView.focus();

								});

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

				// Our tab isn't open. Open it now.
				var browserEnumerator = wm.getEnumerator("navigator:browser");
				var tabbrowser = browserEnumerator.getNext().gBrowser;

				// Create tab
				var newTab = tabbrowser.addTab(data.url);
				newTab.setAttribute(attrName, domain + '|' + data.id);

				// Focus tab
				tabbrowser.selectedTab = newTab;
				
				// Focus *this* browser window in case another one is currently focused
				tabbrowser.ownerDocument.defaultView.focus();
			}

			return tabbrowser;
		}, //openAndReuseOneTabPerAttribute


		bindEvents : function () {

			$('head').append('<style type="text/css"> .tp_over { border:1px solid red; } .tp_selected { border:10px dotted green; }</style>');

			/** Modify the dom to tell the addon is present */
			$('div#1000pass_add_on', window.content.document).addClass('installed');
			$('div#1000pass_add_on_version', window.content.document).addClass('1.0');


			$("img.remote_site_logo", window.content.document).css('cursor', 'pointer');
			var clickableLogos = window.content.document.getElementsByClassName("remote_site_logo");
			for (var i=0; i<clickableLogos.length; i++) {
				clickableLogos[i].addEventListener('click', openFillFieldsAndSubmit, false);
			}


			function openFillFieldsAndSubmit() {

				/** Get necessary data */
				var plugin = $(this).parent().parent();
				var data = {
					id: $('#plugin_identifier', plugin).html(),
					url: $('#url', plugin).html().replace('&amp;', '&'),
					logout_url: $('#logout_url', plugin).html().replace('&amp;', '&'),
					logout_type: $('#logout_url', plugin).attr('class'),
					username: $('#username', plugin).html(),
					usernameField: $('#username', plugin).attr('class'),
					password: $('#password', plugin).html(),
					passwordField: $('#password', plugin).attr('class'),
					extra: $('#extra', plugin).html(),
					extraField: $('#extra', plugin).attr('class'),
					form: $('#submit', plugin).attr('class')
				};


				var onLoadTabListener = function () {

					/** Username */
					var tmpUsernameField = data.usernameField.split('|');
					if (tmpUsernameField[0] == 'id') {
						var myUsername = content.document.getElementById(tmpUsernameField[1]);
					} else if (tmpUsernameField[0] == 'name') {
						var myUsernames = content.document.getElementsByTagName('input');
						for(var i=0; i<myUsernames.length; i++) {
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
						var myPassword = content.document.getElementById(tmpPasswordField[1]);
					} else if (tmpPasswordField[0] == 'name') {
						var myPasswords = content.document.getElementsByTagName('input');
						for(var i=0; i<myPasswords.length; i++) {
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
						var myExtra = content.document.document.getElementById(tmpExtraField[1]);
					} else if (tmpExtraField[0] == 'name') {
						var myExtras = content.document.document.getElementsByTagName('input');
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
					if (data.form == '') {

						var myForm = myUsername.form;
						setTimeout(function(){myForm.submit();}, 2000);

					} else {
						var tmpForm = data.form.split('|');
						if (tmpForm[0] == 'id') {
							var mySubmitter= window.content.document.getElementById(tmpForm[1]);
						} else if (tmpForm[0] == 'name') {
							var myInputs = window.content.document.getElementsByTagName('input');
							for (var i = 0; i < myInputs.length; i++) {
								if (myInputs[i].name == tmpForm[1]) {
									var mySubmitter = myInputs[i];
									break;
								}
							}

							if (mySubmitter == undefined) {
								var myForms = window.content.document.getElementsByTagName('form');
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
								myForms = window.content.document.getElementsByClassName(tmpForm[1]);
							}
							var mySubmitter = myForms[0];
						}

						setTimeout(
							function(){

								// try with the param, but also old fashion if the first does not work
								if (mySubmitter != undefined && mySubmitter != null && typeof(mySubmitter) == 'object') {
									var evt = window.content.document.createEvent('HTMLEvents');
									evt.initEvent('click', true, true ); // event type,bubbling,cancelable
									mySubmitter.dispatchEvent(evt);
									tab.removeAttribute('my-attribute-mark');

								}
							},
						2000);

						// also try to submit old fashion way...
						setTimeout(
							function(){
								var myForm = myUsername.form;
								myForm.submit();
								tab.removeAttribute('my-attribute-mark');
							},
						4000);

					}


					/** Submit the form 
					var tmpForm = data.form.split('|');
					if (tmpForm[0] == 'id') {
						var myForm = content.document.getElementById(tmpForm[1]);
					} else if (tmpForm[0] == 'name' || tmpForm[0] == 'action') {
						var myForms = content.document.getElementsByTagName('form');
						for(var i=0; i<myForms.length; i++) {
							if ((tmpForm[0] == 'name' && myForms[i].name == tmpForm[1])
								|| (tmpForm[0] == 'action' && myForms[i].action == tmpForm[1])) {
								var myForm = myForms[i];
								break;
							}
						}
					} else if (tmpForm[0] == 'class') {
						var myForms = content.document.getElementsByClassName(tmpForm[1]);
						var myForm = myForms[0];
					}

					var tab = this;
					setTimeout(function(){
						myForm.submit();
						tab.removeAttribute('my-attribute-mark');
					}, 2000);
					*/


					/** When finished, must remove event listener to prevent re-posting data when page re-loading */
					this.removeEventListener('load', onLoadTabListener, true);
				};

				var myTab = thousandpass.openAndReuseOneTabPerAttributeValue(data);
				if (data.form != '') {
					myTab.addEventListener('load', onLoadTabListener, true);
				}
			} //openFillFieldsAndSubmit
		} // bindEvents
	} // return

}();
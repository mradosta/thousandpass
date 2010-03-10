var thousandpass = function () {

	return {
		init : function () {
			gBrowser.addEventListener("load", function () {
				//if (window.content.document.location == 'http://localhost/epl/sites_users/') {
					thousandpass.bindEvents();
				//}
			}, false);
		}, //init


		getHostNameFromUrl : function (url) {
			return url.match(/:\/\/(.[^/]+)/)[1]; //.replace('www.','');
		},
		


		deleteCookies : function (url) {

			/** TODO: Remove cookies for only current domain */

			var cookieManager = Components.classes["@mozilla.org/cookiemanager;1"]
								.getService(Components.interfaces.nsICookieManager);


			cookieManager.removeAll();

			/*
			return;

			var iter = cookieManager.enumerator;
			while (iter.hasMoreElements()){
				var cookie = iter.getNext();
				if (cookie instanceof Components.interfaces.nsICookie){
					//if (cookie.host == host) {
						alert('in ' + cookie.host);
						//alert(cookie.name);
						//alert(cookie.value);
					//}
				}
			}
			*/

		}, //deleteCookies


		openAndReuseOneTabPerAttributeValue : function (attrValue, url) {

			
			var domain = thousandpass.getHostNameFromUrl(url);

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

							// Yes--select and focus it.
							tabbrowser.selectedTab = currentTab;

							if (attrData[1] != attrValue) {
								thousandpass.deleteCookies(url);
								gBrowser.removeCurrentTab();
							} else {

								// Focus *this* browser window in case another one is currently focused
								tabbrowser.ownerDocument.defaultView.focus();
								found = true;
							}
							break;
						}
					}
			} //for


			if (!found) {

				// Our tab isn't open. Open it now.
				var browserEnumerator = wm.getEnumerator("navigator:browser");
				var tabbrowser = browserEnumerator.getNext().gBrowser;

				// Create tab
				var newTab = tabbrowser.addTab(url);
				newTab.setAttribute(attrName, domain + '|' + attrValue);

				// Focus tab
				tabbrowser.selectedTab = newTab;
				
				// Focus *this* browser window in case another one is currently focused
				tabbrowser.ownerDocument.defaultView.focus();
			}

			return tabbrowser;
		}, //openAndReuseOneTabPerAttribute


		bindEvents : function () {
			//alert(window.content.document.location);
			//$("div", window.content.document).css('background-color', 'red');

			$("img.remote_site_logo", window.content.document).css('cursor', 'pointer');
			var clickableLogos = window.content.document.getElementsByClassName("remote_site_logo");
			for (var i=0; i<clickableLogos.length; i++) {
				clickableLogos[i].addEventListener('click', openFillFieldsAndSubmit, false);
			}


			function openFillFieldsAndSubmit() {

				/** Get necessary data */
				var plugin = $(this).parent();
				var data = {
					id: $('#plugin_identifier', plugin).html(),
					url: $('#url', plugin).html().replace('&amp;', '&'),
					username: $('#username', plugin).html(),
					usernameField: $('#username', plugin).attr('class'),
					password: $('#password', plugin).html(),
					passwordField: $('#password', plugin).attr('class'),
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


					/** Submit the form */
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
					setTimeout(function(){myForm.submit();}, 2000);

					/** When finished, must remove event listener to prevent re-posting data when page re-loading */
					this.removeEventListener('load', onLoadTabListener, true);
				};

				var myTab = thousandpass.openAndReuseOneTabPerAttributeValue(data.id, data.url);
				if (data.form != '') {
					myTab.addEventListener('load', onLoadTabListener, true);
				}
			} //openFillFieldsAndSubmit
		} // bindEvents
	} // return

}();
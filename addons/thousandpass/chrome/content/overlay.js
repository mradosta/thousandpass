var thousandpass = function () {

	return {
		init : function () {
			gBrowser.addEventListener("load", function () {
				//if (window.content.document.location == 'http://localhost/epl/sites_users/') {
					thousandpass.bindEvents();
				//}
			}, false);
		}, //init


		openAndReuseOneTabPerAttributeValue : function (attrValue, url) {

			var attrName = 'my-attribute-mark';

			var wm = Components.classes["@mozilla.org/appshell/window-mediator;1"]
								.getService(Components.interfaces.nsIWindowMediator);
			for (var found = false, index = 0, tabbrowser = wm.getEnumerator('navigator:browser').getNext().gBrowser;
				index < tabbrowser.tabContainer.childNodes.length && !found;
				index++) {

					// Get the next tab
					var currentTab = tabbrowser.tabContainer.childNodes[index];

					// Does this tab contain our custom attribute?
					if (currentTab.hasAttribute(attrName) && currentTab.getAttribute(attrName) == attrValue) {

						// Yes--select and focus it.
						tabbrowser.selectedTab = currentTab;

						// Focus *this* browser window in case another one is currently focused
						tabbrowser.ownerDocument.defaultView.focus();
						found = true;
					}
			}


			if (!found) {

				// Our tab isn't open. Open it now.
				var browserEnumerator = wm.getEnumerator("navigator:browser");
				var tabbrowser = browserEnumerator.getNext().gBrowser;

				// Create tab
				var newTab = tabbrowser.addTab(url);
				newTab.setAttribute(attrName, attrValue);

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
					url: $('#url', plugin).html(),
					username: $('#username', plugin).html(),
					usernameField: $('#username', plugin).attr('class'),
					password: $('#password', plugin).html(),
					passwordField: $('#password', plugin).attr('class'),
					form: $('#submit', plugin).attr('class')
				};


				var myTab = thousandpass.openAndReuseOneTabPerAttributeValue(data.id, data.url);
				myTab.addEventListener('load', function () {

					/** Username */
					var tmpUsernameField = data.usernameField.split('|');
					if (tmpUsernameField[0] == 'id') {
						content.document.getElementById(tmpUsernameField[1]).value = data.username;
					}

					/** Password */
					var tmpPasswordField = data.passwordField.split('|');
					if (tmpPasswordField[0] == 'id') {
						content.document.getElementById(tmpPasswordField[1]).value = data.password;
					}
					//$(data.usernameField, content.document).val( data.username );

					/** Submit the form */
					var tmpForm = data.form.split('|');
					if (tmpForm[0] == 'id') {
						var myForm = content.document.getElementById(tmpForm[1]);
					} else if (tmpForm[0] == 'name') {
						var myForms = content.document.getElementsByTagName('form');
						for(var i=0; i<myForms.length; i++) {
							if (myForms[i].name == tmpForm[1]) {
								var myForm = myForms[0];
								break;
							}
						}
					} else if (tmpForm[0] == 'class') {
						var myForms = content.document.getElementsByClassName(tmpForm[1]);
						var myForm = myForms[0];
					}
					myForm.submit();

				}, true);

			} //openFillFieldsAndSubmit
		} // bindEvents
	} // return

}();
var thousandpass = function () {

	return {
		init : function () {
			alert('xxxxxxxxxxxx');
			gBrowser.addEventListener("load", function () {
				//if (window.content.document.location == 'http://localhost/epl/sites_users/') {
					thousandpass.bindEvents();
				//}
			}, false);
		},


		bindEvents : function () {
			//alert(window.content.document.location);
			//$("div", window.content.document).css('background-color', 'red');

			$("img.remote_site_logo", window.content.document).css('cursor', 'pointer');
			var clickableLogos = window.content.document.getElementsByClassName("remote_site_logo");
			for (var i=0; i<clickableLogos.length; i++) {
				clickableLogos[i].addEventListener('click', openCompleteAndSubmit, false);
			}


			//$("img.remote_site_logo", window.content.document).bind('dblclick',
				function openCompleteAndSubmit() {

					/** Get necessary data */
					var plugin = $(this).parent();
					var data = {
						url: $('#url', plugin).html(),
						username: $('#username', plugin).html(),
						usernameField: $('#username', plugin).attr('class'),
						password: $('#password', plugin).html(),
						passwordField: $('#password', plugin).attr('class'),
						form: $('#submit', plugin).attr('class')
					};


					/** Open url in new tab and give it focus */
					var newTab = gBrowser.addTab(data.url);
					gBrowser.selectedTab = newTab;
					//var newTabBrowser = gBrowser.getBrowserForTab(newTab);

					/** Add a listener for the new page when finished */
  					//newTabBrowser.contentDocument.body.innerHTML = "<div>hello world</div>";

					newTab.addEventListener("load", function () {

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

				} //function
			//) //click
		} // bindEvents
	} // return

}();
//window.addEventListener("load", thousandpass.init, false);
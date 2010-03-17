<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $html->meta('icon');

		
		//echo $html->meta('description', 'Sports football tennis', array('type'=>'description'));
		//echo $html->meta('keywords', 'Sports football tennis', array('type'=>'keywords'));


		//echo $html->css('cake.generic');
		echo $html->css('app.generic');

		$js = null;
		$js[] = 'jquery/jquery-1.4.1.min';
		$js[] = 'jquery/jquery.cookie';
		echo $javascript->link($js);

		echo $javascript->codeBlock("

			$(document).ready(function($) {
				$('#language').bind('change', function() {
					var options = {  path: '/', expires: 1000 };
					$.cookie('CakeCookie[language]', $(this).val(), options);
					location.reload(true);
					return false;
				});
			});
		");

		echo $scripts_for_layout;
	?>
</head>


<body>
	<div id="header">
		<div class="logo">
			<?php
				$loggedIn = $session->read('Auth.User');
				echo $html->image('logo.png', array(
					'alt'	=> __('Home', true),
					'title'	=> __('Home', true),
					'url' 	=> ($loggedIn)?'/home':'/'));
			?>
		</div>


		<div class="addsense top_addsense">
			<script type="text/javascript">
				google_ad_client = "pub-0846414566912792";
				google_ad_slot = "1083049098";
				google_ad_width = 234;
				google_ad_height = 60;
			</script>
			<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
		</div>


		<div id="languages">
			<?php
				echo $form->input('language', array(
					'label'		=> __('Select your language', true),
					'default'	=> Configure::read('Config.language'),
					'options' 	=> Configure::read('Config.languages'))
				);

				if ($loggedIn['username'] === 'root') {
					$out = null;
					$out[] = '<div id="menu">';
					$out[] = $html->link(__('Sites', true), array('controller' => 'sites'));
					$out[] = $html->link(__('User', true), array('controller' => 'users'));
					$out[] = '</div>';
					echo implode('', $out);
				}
			?>
		</div>


		<div id="top_bar">
		<?php
			if ($loggedIn) {
				echo '<div class="actions">';

					echo $html->image('add.png', array(
						'alt'	=> __('Add web site', true),
						'title'	=> __('Add web site', true),
						'url' 	=> array('controller' => 'sites_users', 'action' => 'add')));
					echo $html->tag('span', __('Add web site', true), array('class' => 'label'));

					echo $html->image('logout.jpg', array(
						'alt'	=> __('Logout', true),
						'title'	=> __('Logout', true),
						'url' 	=> array('controller' => 'users', 'action' => 'logout')));
					echo $html->tag('span', __('Logout', true), array('class' => 'label'));

					echo $html->image('notes.png', array(
						'alt'	=> __('Notes', true),
						'title'	=> __('Notes', true),
						'url' 	=> array('controller' => 'notes', 'action' => 'add')));
					echo $html->tag('span', __('Notes', true), array('class' => 'label'));

				echo '</div>';
			} else {
				echo '<div class="login">';
				echo $form->create('User', array('action' => 'login'));
				echo $form->input('username', array('error' => false));
				echo $form->input('password', array('error' => false));
				echo $form->submit(__('enter', true));
				echo $form->end();
				echo '</div>';
			}
		?>

			<div id="google_search">
				<form method="get" action="http://www.google.com/search">
					<input type="text" name="q" size="31" maxlength="255" value="" />
					<input type="submit" value="<?php __('Search'); ?>" />
				</form>
			</div> <!--google_search-->

		</div> <!--search-->
	</div>



<?php

	/** Session Flash
	if ($session->check('Message.flash') || $session->flash('auth')) {
		ob_start();
		$session->flash();
		$out[] = ob_get_clean();
	}
 	*/

	//$out = null;


	/** VIEWS */
	echo $html->tag('div', $content_for_layout, array('id' => 'container'));




?>

	<div class="addsense bottom_addsense">
		<script type="text/javascript">
			google_ad_client = "pub-0846414566912792";
			google_ad_slot = "9749307972";
			google_ad_width = 728;
			google_ad_height = 90;
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
	</div>


</body>

</html>
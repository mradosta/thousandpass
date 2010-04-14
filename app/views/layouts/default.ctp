<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
				$('#languages img').bind('click', function() {
					var options = {  path: '/', expires: 1000 };
					$.cookie('CakeCookie[language]', $(this).attr('class'), options);
					location.reload(true);
					return false;
				});
			});
		");

		echo $scripts_for_layout;
	?>
</head>


<body>

	<div id="container">

		<div id="header">
			<div class="logo">
				<?php
					$loggedIn = $session->read('Auth.User');
					echo $html->image('logo.png', array(
						'alt'	=> __('Home', true),
						'title'	=> __('Home', true),
						'url' 	=> ($loggedIn)?'/home':'/'));
				?>
			</div> <!--logo-->


			<div id="google_search">
				<form method="get" action="http://www.google.com/search">
					<input type="text" name="q" size="31" maxlength="255" value="" />
					<input class="submit" type="submit" value="<?php __('Search'); ?>" />
				</form>
			</div> <!--google_search-->


			<?php
				$debugLevel = Configure::read('debug');
				if ($debugLevel == 0) {
					echo '
						<div class="addsense top_addsense">
							<script type="text/javascript">
								google_ad_client = "pub-0846414566912792";
								google_ad_slot = "1083049098";
								google_ad_width = 234;
								google_ad_height = 60;
							</script>
							<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
						</div>';
				}
			?>


			<div id="languages">
				<?php
					echo $html->image('eng.png', array('class' => 'eng'));
					echo $html->image('spa.png', array('class' => 'spa'));
				?>
			</div>


			<div id="admin_menus">
				<?php
					if ($loggedIn['username'] === 'root') {
						$out = null;
						$out[] = '<div id="menu">';
						$out[] = $html->link(__('Sites', true), array('controller' => 'sites'));
						$out[] = $html->link(__('User', true), array('controller' => 'users'));
						$out[] = '</div>';
					}
					echo implode('', $out);
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
							'onclick'	=> 'alert("' . __('You are about to close session at 1000Pass.com, remember to close other sites sessions too', true) . '");',
							'alt'	=> __('Logout', true),
							'title'	=> __('Logout', true),
							'url' 	=> array('controller' => 'users', 'action' => 'logout')));
						echo $html->tag('span', __('Logout', true), array('class' => 'label'));
					echo '</div>';

					echo '<div class="toolbar">';
						echo $html->image('notes.png', array(
							'alt'	=> __('Notes', true),
							'title'	=> __('Notes', true),
							'url' 	=> array('controller' => 'notes', 'action' => 'add')));
						//echo $html->tag('span', __('Notes', true), array('class' => 'label'));

						echo $html->image('change_password.png', array(
							'alt'	=> __('Change Password', true),
							'title'	=> __('Change Password', true),
							'url' 	=> array('controller' => 'users', 'action' => 'change_password')));
						//echo $html->tag('span', __('Change Password', true), array('class' => 'label'));

						echo $html->image('help.png', array(
							'alt'	=> __('Help', true),
							'title'	=> __('Help', true),
							'url' 	=> array('controller' => 'users', 'action' => 'help')));
						//echo $html->tag('span', __('Help', true), array('class' => 'label'));

					echo '</div>';
				} else {
					echo '<div class="login">';
					echo $form->create('User', array('action' => 'login'));
					echo $form->input('username', array(
						'id'		=> null,
						'label'		=> __('Username', true),
						'error' 	=> false));
					echo $form->input('password', array(
						'id'		=> null,
						'label'		=> __('Password', true),
						'error' 	=> false,
						'after' 	=> $html->link(__('Forgot my password', true),
						array('controller' => 'users', 'action' => 'recover_password'), array(
							'xonclick' 	=> 'this.href = this.href + "/" + $("#UserUsername").val()',
						))));
					echo $form->submit(__('enter', true), array('class' => 'submit'));
					echo $form->end();
					echo '</div>';
				}
			?>

			</div> <!--search-->
		</div> <!--header-->



		<?php

			/** Session Flash
			*/
			$out = '';
			if ($session->check('Message.flash') || $session->flash('auth')) {
				ob_start();
				$session->flash();
				$out = ob_get_clean();
			}

			//$out = null;


			/** VIEWS */
			//echo $html->tag('div', $out . $content_for_layout, array('id' => 'container'));
			echo $out . $content_for_layout;

			if ($debugLevel == 0) {
				echo '
					<div class="addsense bottom_addsense">
						<script type="text/javascript">
							google_ad_client = "pub-0846414566912792";
							google_ad_slot = "9749307972";
							google_ad_width = 728;
							google_ad_height = 90;
						</script>
						<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
					</div>';
			}
		?>

	</div><!--container-->

</body>

</html>
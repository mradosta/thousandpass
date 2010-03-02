<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $html->meta('icon');

		echo $html->css('cake.generic');
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
				echo $html->image('logo.png', array(
					'alt'	=> __('Home', true),
					'title'	=> __('Home', true),
					'url' 	=> array('controller' => 'sites_users', 'action' => 'index')));
			?>
		</div>


		<div class="languages">
			<?php
				echo $form->input('language', array(
					'label'		=> false,
					'default'	=> Configure::read('Config.language'),
					'options' 	=> Configure::read('Config.languages'))
				);
			?>
		</div>


		<div class="search">
			<?php echo $html->image('add.png', array(
				'alt'	=> __('Add new site', true),
				'title'	=> __('Add new site', true),
				'class'	=> 'add',
				'url' 	=> array('controller' => 'sites_users', 'action' => 'add'))); ?>
		</div> <!--search-->
	</div>



<?php

	/** Session Flash */
	if ($session->check('Message.flash')) {
		ob_start();
		$session->flash();
		$out[] = ob_get_clean();
	}


	$out = null;


	/** VIEWS */
	$out[] = $html->tag('div', $content_for_layout, array('id' => 'container'));


	$out[] = '<ul>';
	$out[] = '<li>';
	$out[] = $html->link(__('Home', true), array('controller' => 'sites_users'));
	$out[] = '</li>';
	$out[] = '<li>';
	$out[] = $html->link(__('Sites', true), array('controller' => 'sites'));
	$out[] = '</li>';
	$out[] = '<li>';
	$out[] = $html->link(__('User', true), array('controller' => 'users'));
	$out[] = '</li>';
	$out[] = '<li>';
	$out[] = $html->link(__('User\'s Sites', true), array('controller' => 'sites_users'));
	$out[] = '</li>';
	$out[] = '<li>';
	$out[] = $html->link(__('Sign Up', true), array('controller' => 'users', 'action' => 'register'));
	$out[] = '</li>';
	$out[] = '<li>';
	$out[] = $html->link(__('Logout', true), array('controller' => 'users', 'action' => 'logout'));
	$out[] = '</li>';
	$out[] = '</ul>';

	echo implode('', $out);


?>

</body>

</html>
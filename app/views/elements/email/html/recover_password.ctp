<?php echo $html->image('http://www.1000pass.com/img/' . Configure::read('Config.language') . '_spa_recover_password_mail.jpg', array('url' => 'http://www.1000pass.com')); ?>

<div style="margin-top: 600px;">
	<?php
		__('Your new password is: ');
		echo '<b>' . $data['newpassword'] . '</b>';
	?>
</div>
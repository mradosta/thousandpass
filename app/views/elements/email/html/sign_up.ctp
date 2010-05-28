<div style="background: url(<?php echo 'http://www.1000pass.com/img/' . Configure::read('Config.language') . '_welcome_mail.jpg'; ?>) no-repeat; font-size: 80%; padding: 281px 0 0 100px;">
	<?php
		echo $data['User']['username'] . '<br/>';
		echo $data['User']['repassword'] . '<br/><br/><br/>';
	?>
</div>
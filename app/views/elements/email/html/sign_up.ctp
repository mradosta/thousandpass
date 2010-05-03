<div style="background: url(<?php echo $html->image('http://www.1000pass.com/img/' . Configure::read('Config.language') . '_welcome_mail.jpg', array('url' => 'http://www.1000pass.com')); ?>) no-repeat; font-size: 80%; padding: 281px 0 0 100px;">
	<?php
		echo $data['User']['username'] . '<br/>';
		echo $data['User']['password'] . '<br/><br/><br/>';
	?>
</div>
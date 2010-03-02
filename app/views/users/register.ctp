<?php

	echo '<h2>Create an Account</h2>';

	echo $form->create('User', array('action' => 'register'));
	echo $form->input('username', array('between' => 'Pick a username'));

	// Force the FormHelper to render a password field, and change the label.
	echo $form->input('passwrd', array('type' => 'password', 'label' => 'Password'));
	echo $form->input('email', array('between' => 'We need to send you a confirmation email'));
	echo $form->submit('Create Account');
	echo $form->end();
?>
<?php

	echo '<h2>'.__('Sign Up', true).'</h2>';

	echo $form->create('User', array('action' => 'register'));
	echo $form->input('username', array('between' => __('Pick a username', true)));

	// Force the FormHelper to render a password field, and change the label.
	echo $form->input('passwrd', array('type' => 'password', 'label' => __('Password', true)));
	echo $form->input('email', array('between' => __('We need to send you a confirmation email', true)));
	echo $form->submit('Create Account');
	echo $form->end();
?>
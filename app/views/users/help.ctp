<?php
	$this->pageTitle = '1000Pass.com - ' . __('Help', true);
?>


<div class="inner_container_border">

	<div class="inner_container">

		<div class="left help">
			<?php
				if (!empty($movie)) {
					echo '
						<object width="425" height="300">
							<param name="movie" value="' . $movie . '"></param>
							<param name="allowFullScreen" value="true"></param>
							<param name="allowscriptaccess" value="always"></param>
							<embed src="' . $movie . '" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="300"></embed>
						</object>';
				}
			?>
		</div>

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Help', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

			<?php


				echo $html->link(__('How to register?', true), array('controller' => 'users', 'action' => 'help', 'register'), array('class' => 'help'));
				echo $html->link(__('How to add a site?', true), array('controller' => 'users', 'action' => 'help', 'add_site'), array('class' => 'help'));
				echo $html->link(__('How to add a note?', true), array('controller' => 'users', 'action' => 'help', 'add_note'), array('class' => 'help'));

			?>
		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->


<?php

/*
Como registrarce español

http://www.youtube.com/watch?v=XQ35djMbjWs

 

Nuevo sitio español

http://www.youtube.com/watch?v=9ZGi-oa-MLQ

 

agergar nota español

http://www.youtube.com/watch?v=PXcotlz8we8

 

---------------------------------------------------

Como registrarce Ingles

http://www.youtube.com/watch?v=q-lUjh3hzyA
http://www.youtube.com/watch?v=9P_LBEPOCao

http://www.youtube.com/watch?v=5p_jCCCmbfU
*/

?>
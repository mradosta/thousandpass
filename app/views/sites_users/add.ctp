<div class="inner_container_border">

	<div class="inner_container">

		<div class="left"></div>

		<div class="right">
			<div class="text_and_logo">
				<?php
					echo $html->tag('span', __('Add a new web site to', true));
					echo $html->image('logo_black.png');
				?>
				<span class="subtitle"><?php __('We order your home page') ?></span>
			</div>

			<?php echo $form->create('SitesUser');?>
				<?php
					echo $form->input('site_id');
					echo $form->input('username');
					echo $form->input('password');
					echo $form->input('description');
				?>
			<?php
				echo $form->end(__('Add', true));
				//echo $html->link(__('Back', true), '/home');
			?>
		</div> <!--right-->

	</div> <!--inner_container-->
</div> <!--inner_container_border-->

<script type="text/javascript">
	$('#SitesUserSiteId').change(function() {
		var label = '<?php __('Description'); ?>';
		var parent = $('#SitesUserDescription').parent();
		$('#SitesUserDescription').remove();
		if ($(this).val() == 0) {
			parent.find('label').html('Url');
			parent.append('<input name="data[SitesUser][description]" type="text" value="" id="SitesUserDescription" />');
		} else {
			parent.find('label').html(label);
			parent.append('<textarea name="data[SitesUser][description]" cols="30" rows="6" id="SitesUserDescription" ></textarea>');
		}
	});
</script>
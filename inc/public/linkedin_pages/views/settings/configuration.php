<div class="alert alert-solid-brand">
	<span class="fw-6"><?php _e("Callback URL:")?></span>
	<a href="<?php _e( get_url("linkedin_pages") )?>" target="_blank"><?php _e( get_url("linkedin_pages") )?></a>
</div>

<div class="alert alert-solid-danger">
	<span class="fw-6"><?php _e("Note:")?></span> <span><?php _e("To can add Linkedin pages you need register Marketing Developer Platform of Linkedin")?></span>
</div>

<div class="form-group">
	<label for="status"><?php _e('Linkedin page')?></label>
	<div>
		<label class="i-radio i-radio--tick i-radio--brand m-r-10">
			<input type="radio" name="linkedin_page_status" <?php _e( get_option('linkedin_page_status', 1)  == 0?"checked":"" )?> value="0"> <?php _e('Disable')?>
			<span></span>
		</label>
		<label class="i-radio i-radio--tick i-radio--brand m-r-10">
			<input type="radio" name="linkedin_page_status" <?php _e( get_option('linkedin_page_status', 1)  == 1?"checked":"" )?> value="1"> <?php _e('Enable')?>
			<span></span>
		</label>
	</div>
</div>
<div class="form-group">
	<label for="status"><?php _e('Log in to Pinterest via')?></label>
	<div>
		<label class="i-checkbox i-checkbox--tick i-checkbox--brand m-r-10">
			<input type="hidden" name="pinterest_login_button" value="0">
			<input type="checkbox" name="pinterest_login_button" <?php _e( get_option('pinterest_login_button', 1)  == 1?"checked":"" )?> value="1"> <?php _e('Button')?>
			<span></span>
		</label>
		<label class="i-checkbox i-checkbox--tick i-checkbox--brand m-r-10">
			<input type="hidden" name="pinterest_login_app" value="0">
			<input type="checkbox" name="pinterest_login_app" <?php _e( get_option('pinterest_login_app', 1)  == 1?"checked":"" )?> value="1"> <?php _e('Your Pinterest app')?>
			<span></span>
		</label>
		<label class="i-checkbox i-checkbox--tick i-checkbox--brand m-r-10">
			<input type="hidden" name="pinterest_login_user" value="0">
			<input type="checkbox" name="pinterest_login_user" <?php _e( get_option('pinterest_login_user', 1)  == 1?"checked":"" )?> value="1"> <?php _e('Username & Password')?>
			<span></span>
		</label>
	</div>
</div>
<h5 class="fs-16 fw-4 text-info m-b-20 m-t-40"><i class="fas fa-caret-right"></i> <?php _e("Log in to Pinterest via button")?></h5>
<div class="alert alert-solid-brand">
	<span class="fw-6"><?php _e("Callback URL:")?></span>
	<a href="<?php _e( get_url("pinterest_boards") )?>" target="_blank"><?php _e( get_url("pinterest_boards") )?></a>
	<br/>
	<span class="fw-6"><?php _e("Click this link to create Pinterest app:")?></span>
	<a href="https://developers.pinterest.com/apps/" target="_blank">https://developers.pinterest.com/apps/</a>
</div>
<div class="form-group">
    <label for="pinterest_app_id"><?php _e('Pinterest App ID')?></label>
    <input type="text" class="form-control" id="pinterest_app_id" name="pinterest_app_id" value="<?php _e( get_option('pinterest_app_id', '') )?>">
</div>
<div class="form-group">
    <label for="pinterest_app_secret"><?php _e('Pinterest App Secret')?></label>
    <input type="text" class="form-control" id="pinterest_app_secret" name="pinterest_app_secret" value="<?php _e( get_option('pinterest_app_secret', '') )?>">
</div>

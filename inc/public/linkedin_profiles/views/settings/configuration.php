<div class="alert alert-solid-brand">
	<span class="fw-6"><?php _e("Callback URL:")?></span>
	<a href="<?php _e( get_url("linkedin_profiles") )?>" target="_blank"><?php _e( get_url("linkedin_profiles") )?></a>	
	<br/>
	<span class="fw-6"><?php _e("Click this link to create Linkedin app:")?></span>
	<a href="https://www.linkedin.com/developers/apps/new" target="_blank">https://www.linkedin.com/developers/apps/new</a>
</div>

<div class="form-group">
    <label for="linkedin_api_key"><?php _e('Linkedin api id')?></label>
    <input type="text" class="form-control" id="linkedin_api_key" name="linkedin_api_key" value="<?php _e( get_option('linkedin_api_key', '') )?>">
</div>
<div class="form-group">
    <label for="linkedin_api_secret"><?php _e('Linkedin api secret')?></label>
    <input type="text" class="form-control" id="linkedin_api_secret" name="linkedin_api_secret" value="<?php _e( get_option('linkedin_api_secret', '') )?>">
</div>
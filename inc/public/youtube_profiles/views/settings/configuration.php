<div class="alert alert-solid-brand">
	<span class="fw-6"><?php _e("Callback URL:")?></span>
	<a href="<?php _e( get_url("youtube_profiles") )?>" target="_blank"><?php _e( get_url("youtube_profiles") )?></a>
	<br/>
	<span class="fw-6"><?php _e("Click this link to create Google app:")?></span>
	<a href="https://console.developers.google.com/projectcreate" target="_blank">https://console.developers.google.com/projectcreate</a>	
</div>

<div class="form-group">
    <label for="google_youtube_client_id"><?php _e('Google Client ID')?></label>
    <input type="text" class="form-control" id="google_youtube_client_id" name="google_youtube_client_id" value="<?php _e( get_option('google_youtube_client_id', '') )?>">
</div>
<div class="form-group">
    <label for="google_youtube_api_secret"><?php _e('Google Client Secret')?></label>
    <input type="text" class="form-control" id="google_youtube_api_secret" name="google_youtube_api_secret" value="<?php _e( get_option('google_youtube_api_secret', '') )?>">
</div>
<div class="form-group">
    <label for="google_youtube_api_key"><?php _e('Google API Key')?></label>
    <input type="text" class="form-control" id="google_youtube_api_key" name="google_youtube_api_key" value="<?php _e( get_option('google_youtube_api_key', '') )?>">
</div>
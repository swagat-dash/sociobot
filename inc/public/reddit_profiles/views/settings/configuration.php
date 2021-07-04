<div class="alert alert-solid-brand">
	<span class="fw-6"><?php _e("Redirect URI:")?></span>
	<a href="<?php _e( get_url("reddit_profiles") )?>" target="_blank"><?php _e( get_url("reddit_profiles") )?></a>	
	<br/>
	<span class="fw-6"><?php _e("Click this link to create Reddit app:")?></span>
	<a href="https://www.reddit.com/prefs/apps" target="_blank">https://www.reddit.com/prefs/apps</a>	
</div>

<div class="form-group">
    <label for="reddit_client_id"><?php _e('Reddit client id')?></label>
    <input type="text" class="form-control" id="reddit_client_id" name="reddit_client_id" value="<?php _e( get_option('reddit_client_id', '') )?>">
</div>
<div class="form-group">
    <label for="reddit_client_secret"><?php _e('Reddit client secret')?></label>
    <input type="text" class="form-control" id="reddit_client_secret" name="reddit_client_secret" value="<?php _e( get_option('reddit_client_secret', '') )?>">
</div>
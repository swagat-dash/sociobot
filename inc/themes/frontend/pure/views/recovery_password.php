<?php include 'top.php'?>
<div class="login-page">
	
	<div class="login-main">

		<div class="login-box">
			
			<div class="login-logo">
				<a href="<?php _e( get_url() )?>">
					<img src="<?php _e( get_option('website_black', get_url("inc/themes/backend/default/assets/img/logo-black.png")) )?>">
				</a>		
			</div>
			<div class="login-form">
				<h3 class="fs-20"><?php _e("Recovery password")?></h3>
				<p class="m-b-20"><?php _e("Please enter new password to reset your password")?></p>
				<form class="actionLogin" action="<?php _e( get_module_url('ajax_recovery_password', $this) )?>" data-hide-overplay='false'>
					<input type="hidden"  name="recovery_key" value="<?php _e( segment(2) )?>">
					<div class="m-b-25">
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="fas fa-lock"></i>
						    	</span>
						  	</div>
						  	<input type="password" class="form-control" name="password" autocomplete="off" placeholder="<?php _e('New password')?>">
						</div>
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="fas fa-lock"></i>
						    	</span>
						  	</div>
						  	<input type="password" class="form-control" name="confirm_password" autocomplete="off" placeholder="<?php _e('Confirm new password')?>">
						</div>
					</div>

					<?php if(get_option('google_recaptcha_status', 0)){?>
					<div class="g-recaptcha m-b-15" data-sitekey="<?=get_option('google_recaptcha_site_key', '')?>"></div>
					<?php }?>

					<span class="show-message"></span>

					<div class="text-center">
						<button type="submit" class="btn btn-info rounded-pill m-t-20 btn-loading d-none">
							<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php _e('Submit')?>
						</button>
						<button type="submit" class="btn btn-info rounded-pill m-t-20 btn-no-loading"><i class="fas fa-sign-in-alt"></i> <?php _e('Submit')?></button>
					</div>
				</form>
			</div>

		</div>
	</div>

	<div class="login-bg">



	</div>

</div>

<?php include 'bottom.php'?>
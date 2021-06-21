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
				<h3 class="fs-20"><?php _e("Forgot password")?></h3>
				<p class="m-b-20"><?php _e("Please enter your email we will to reset password")?></p>
				<form class="actionLogin" action="<?php _e( get_module_url('ajax_forgot_password', $this) )?>" data-hide-overplay='false'>
					<div class="form-group card">
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text p-l-20 p-r-0">
						    		<i class="far fa-envelope"></i>
						    	</span>
						  	</div>
						  	<input type="email" class="form-control" name="email" autocomplete="off" placeholder="<?php _e('Email')?>">
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

		<?php if( get_option("signup_status", 1) ){?>
		<div class="signup-ask text-center">
			<?php _e("Don't have an account yet?")?> <a href="<?php _e( get_url("signup") )?>"><?php _e('Sign Up')?></a>
		</div>
		<?php }?>
	</div>

	<div class="login-bg">



	</div>

</div>

<?php include 'bottom.php'?>
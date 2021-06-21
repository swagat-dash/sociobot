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
				<form class="actionLogin" action="<?php _e( get_module_url('ajax_login', $this) )?>" data-hide-overplay='false' data-redirect="<?php _e( post('redirect')?post('redirect'):get_url('dashboard') )?>">
					<div class="form-group">
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="far fa-envelope"></i>
						    	</span>
						  	</div>
						  	<input type="email" class="form-control" name="email" autocomplete="off" placeholder="<?php _e('Email')?>">
						</div>

						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="fas fa-lock"></i>
						    	</span>
						  	</div>
						  	<input type="password" class="form-control" name="password" autocomplete="off" placeholder="<?php _e('Password')?>">
						</div>
					</div>

					<div class="form-group wrap-m">
						
						<div class="wrap-c">
							<label class="i-checkbox i-checkbox--tick i-checkbox--brand m-r-10">
								<input type="checkbox" name="remember" value="1"> <?php _e('Remember me')?><span></span>
							</label>
						</div>

						<div class="wrap-c fs-12">
							<a href="<?php _e( get_url("forgot_password") )?>" alt=""><?php _e('Forget password?')?></a>
						</div>
					</div>

					<?php if(get_option('google_recaptcha_status', 0)){?>
					<div class="g-recaptcha  mb-15" data-sitekey="<?=get_option('google_recaptcha_site_key', '')?>"></div>
					<?php }?>

					<span class="show-message"></span>

					<div class="text-center">
						<button type="submit" class="btn btn-info rounded-pill m-t-20 btn-loading d-none">
							<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php _e('Sign In')?>
						</button>
						<button type="submit" class="btn btn-info rounded-pill m-t-20 btn-no-loading"><i class="fas fa-sign-in-alt"></i> <?php _e('Sign In')?></button>
					</div>
					<?php if( get_option('facebook_login_status', 0) || get_option('google_login_status', 0) || get_option('twitter_login_status', 0) ){?>
					<div class="login-social m-t-20">
						<div class="desc text-center m-b-20 fs-12"><?php _e('Or signin using')?></div>
						<?php if( get_option('facebook_login_status', 0) ){?>
						<a href="<?php _e( get_url("login/facebook") )?>" class="btn btn-block btn-facebook text-left"><i class="fab fa-facebook-f"></i> <?php _e("Sign In with Facebook")?></a>
						<?php }?>
						<?php if( get_option('google_login_status', 0) ){?>
					  	<a href="<?php _e( get_url("login/google") )?>" class="btn btn-block btn-google text-left"><img src="<?php _e( get_theme_frontend_url('assets/img/google-logo.png') )?>"> <?php _e("Sign In with Google")?></a>
					  	<?php }?>
					  	<?php if( get_option('twitter_login_status', 0) ){?>
					  	<a href="<?php _e( get_url("login/twitter") )?>" class="btn btn-block btn-twitter text-left"><i class="fab fa-twitter"></i> <?php _e("Sign In with Twitter")?></a>
					  	<?php }?>
					</div>
					<?php }?>
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
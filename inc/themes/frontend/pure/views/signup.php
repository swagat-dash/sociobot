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
				<h3 class="fs-20"><?php _e("Sign Up")?></h3>
				<p class="m-b-20"><?php _e("Create your account and enjoy")?></p>
				<form class="actionLogin" action="<?php _e( get_module_url('ajax_signup', $this) )?>" data-hide-overplay='false' data-redirect="<?php _e( get_url('login') )?>">
					<div class="form-group">
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="far fa-user"></i>
						    	</span>
						  	</div>
						  	<input type="fullname" class="form-control" name="fullname" autocomplete="off" placeholder="<?php _e('Fullname')?>">
						</div>
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="far fa-envelope"></i>
						    	</span>
						  	</div>
						  	<input type="email" class="form-control" name="email" autocomplete="off" placeholder="<?php _e('Email')?>" value="<?php _e( post("email") )?>">
						</div>
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="fas fa-lock"></i>
						    	</span>
						  	</div>
						  	<input type="password" class="form-control" name="password" autocomplete="off" placeholder="<?php _e('Password')?>">
						</div>
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="fas fa-lock"></i>
						    	</span>
						  	</div>
						  	<input type="password" class="form-control" name="confirm_password" autocomplete="off" placeholder="<?php _e('Confirm password')?>">
						</div>
						<div class="input-group">
						  	<div class="input-group-prepend">
						    	<span class="input-group-text">
						    		<i class="far fa-clock"></i>
						    	</span>
						  	</div>
						  	<select name="timezone" class="form-control auto-select-timezone" aria-describedby="basic-addon-timezone">
		                        <?php if(!empty(tz_list())){
		                        foreach (tz_list() as $zone => $time) {
		                        ?>
		                        <option value="<?php _e( $zone )?>"><?php _e( $time )?></option>
		                        <?php }}?>
		                    </select>
						</div>
					</div>

					<div class="form-group wrap-m">
						
						<div class="wrap-c">
							<label class="i-checkbox i-checkbox--tick i-checkbox--brand m-r-10">
								<input type="checkbox" name="terms" value="1"> <?php _e('I agree to all Terms of Services')?><span></span>
							</label>
						</div>
					</div>

					<?php if(get_option('google_recaptcha_status', 0)){?>
					<div class="g-recaptcha m-b-15" data-sitekey="<?=get_option('google_recaptcha_site_key', '')?>"></div>
					<?php }?>

					<span class="show-message"></span>

					<div class="text-center">
						<button type="submit" class="btn btn-info rounded-pill m-t-20 btn-loading d-none">
							<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?php _e('Sign Up')?>
						</button>
						<button type="submit" class="btn btn-info rounded-pill m-t-20 btn-no-loading"><i class="fas fa-sign-in-alt"></i> <?php _e('Sign Up')?></button>
					</div>
				</form>
			</div>

		</div>

		<div class="signup-ask text-center">
			<?php _e("Already have an account?")?> <a href="<?php _e( get_url("login") )?>"><?php _e('Sign In')?></a>
		</div>

	</div>

	<div class="login-bg">



	</div>

</div>

<?php include 'bottom.php'?>
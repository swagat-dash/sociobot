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
				<h3 class="fs-20"><?php _e("Activation")?></h3>
				<p class="m-b-20"><?php _e("Activate your account")?></p>
				<p>
					<span class="show-message"><span class="text-success"><?php _e("Your account has been activated successfully. Let's start experiencing the great features.")?></span></span>
				</p>
				<div class="text-center">
					<a class="btn btn-info m-auto" href="<?php _e( get_url("login") )?>"><?php _e("Login")?></a>
				</div>
			</div>

		</div>
	</div>

	<div class="login-bg">



	</div>

</div>

<?php include 'bottom.php'?>
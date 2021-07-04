<div class="widget-box-add-account">
	<div class="headline m-b-30">
		<div class="title fs-18 fw-5 text-info"><i class="far fa-plus-square"></i> <?php _e('Pinterest login')?></div>
		<div class="desc"><?php _e("Select option below to get pinterest boards")?></div>
	</div>

	<ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
		<?php if( get_option('pinterest_login_button', 1) ){?>
	  	<li class="nav-item">
	    	<a class="nav-link active" id="pinteres-button-tab" data-toggle="tab" href="#pinteres-button" role="tab" aria-controls="pinteres-button" aria-selected="true"><i class="fab fa-pinterest"></i> <?php _e('Button')?></a>
	  	</li>
	  	<?php }?>
	  	<?php if( get_option('pinterest_login_app', 1)  ){?>
	  	<li class="nav-item ">
	    	<a class="nav-link <?php _e( !get_option('pinterest_login_button', 1)?"active":"" )?>" id="pinterest-app-tab" data-toggle="tab" href="#pinterest-app" role="tab" aria-controls="pinterest-app" aria-selected="false"><i class="fab fa-pinterest"></i> <?php _e("Your app")?></a>
	  	</li>
	  	<?php }?>
	  	<?php if( get_option('pinterest_login_user', 1) ){?>
	  	<li class="nav-item">
	    	<a class="nav-link <?php _e( (!get_option('pinterest_login_button', 1) && !get_option('pinterest_login_app', 1) )?"active":"" )?>" id="pinterest-user-tab" data-toggle="tab" href="#pinterest-user" role="tab" aria-controls="pinterest-user" aria-selected="false"><i class="fab fa-pinterest"></i> <?php _e("Username & Password")?></a>
	  	</li>
	  	<?php }?>
	</ul>
	<div class="tab-content p-t-25" id="myTabContent">
		<?php if( get_option('pinterest_login_button', 1) ){?>
	  	<div class="tab-pane fade show active" id="pinteres-button" role="tabpanel" aria-labelledby="pinteres-button-tab">
	  		<a href="<?php _e( get_module_url('oauth_button') )?>" class="btn btn-social btn-block"><i class="fab fa-pinterest"></i> <?php _e('Connect with Pinterest')?></a>
	  	</div>
	  	<?php }?>
	  	<?php if( get_option('pinterest_login_app', 1) ){?>
	  	<div class="tab-pane fade <?php _e( !get_option('pinterest_login_button', 1)?"show active":"" )?>" id="pinterest-app" role="tabpanel" aria-labelledby="pinterest-app-tab">
	  		
	  		<form class="actionForm" action="<?php _e( get_module_url('oauth_app') )?>" method="POST" data-redirect="<?php _e( get_module_url("oauth_app") )?>">
				<div class="form-group">
					<label for="app_id"><?php _e("Pinterest app id")?></label>
					<input type="text" class="form-control" id="app_id" name="app_id">
				</div>

				<div class="form-group">
					<label for="app_secret"><?php _e("Pinterest app secret")?></label>
					<input type="text" class="form-control" id="app_secret" name="app_secret">
				</div>

				<button type="submit" class="btn btn-block btn-info m-t-15"><?php _e('Submit')?></button>
			</form>

	  	</div>
	  	<?php }?>
	  	<?php if( get_option('pinterest_login_user', 1) ){?>
	  	<div class="tab-pane fade <?php _e( (!get_option('pinterest_login_button', 1) && !get_option('pinterest_login_app', 1) )?"show active":"" )?>" id="pinterest-user" role="tabpanel" aria-labelledby="pinterest-user-tab">
	  		
	  		<form class="actionForm" action="<?php _e( get_module_url('oauth_user') )?>" method="POST" data-redirect="<?php _e( get_module_url() )?>">
				<div class="form-group">
					<label for="username"><?php _e("Pinterest username")?></label>
					<input type="text" class="form-control" id="username" name="username">
				</div>

				<div class="form-group">
					<label for="password"><?php _e("Pinterest password")?></label>
					<input type="password" class="form-control" id="password" name="password">
				</div>
				<?php if(get_option('user_proxy', 1)){?>
				<div class="form-group">
					<label for="proxy"><?php _e("Proxy")?></label>
					<input type="text" class="form-control" id="proxy" name="proxy">
				</div>
				<?php }?>

				<button type="submit" class="btn btn-block btn-info m-t-15"><?php _e('Submit')?></button>
			</form>

	  	</div>
	  	<?php }?>
	</div>
</div>
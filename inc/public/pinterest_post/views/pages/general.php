<div class="post post-create">
	
	<?php _e( $block_post_type, false) ?>

	<div class="post-content m-b-15">
		
		<div class="item-post-type" data-type="photo">
			<?php _e( $file_manager_photo, false) ?>
		</div>

		<div class="item-post-type  m-t-15" data-type="link">
			
		</div>

		<?php _e( $block_caption, false)?>

		<div class="post-advance m-t-15">
			<ul class="nav nav-tabs">
			  	<li class="nav-item">
			    	<a class="nav-link active" href="#"><i class="fas fa-magic text-info"></i> <?php _e("Advance option")?> <span class="arrow"><i class="ft-chevron-down"></i></span></a>
			  	</li>
			</ul>
			<div class="advance-content">
				<?php _e( $block_link, false)?>
			</div>
		</div>

	</div>

	<?php _e( $block_schedule, false)?>

</div>
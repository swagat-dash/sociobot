<div class="post post-create">
	
	<?php _e( $block_post_type, false) ?>

	<div class="post-content m-b-15">
		
		<div class="form-group">
			<input class="form-control reddit-title" name="advance[title]" required="" placeholder="<?php _e("Enter title")?>">
		</div>	

		<div class="item-post-type" data-type="photo">
			<?php _e( $file_manager_photo, false) ?>
		</div>

		<div class="item-post-type m-t-15" data-type="link">
			<?php _e( $block_link, false)?>
		</div>

		<div class="item-post-type m-t-15" data-type="text">
			<?php _e( $block_caption, false)?>
		</div>

		<input name="caption" class="form-control d-none" value="1">
	</div>

	<?php _e( $block_schedule, false)?>

</div>
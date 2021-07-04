<div class="post post-create">
	
	<?php _e( $block_post_type, false) ?>

	<div class="post-content m-b-15">
	
		<div class="form-group">
			<input type="text" class="form-control" name="advance[title]" placeholder="<?php _e('Enter your title')?>">
		</div>
		<div class="form-group">
			<select class="form-control" name="advance[category]">
	            <option value="0"><?php _e('Category')?></option>
	            <option value="1"><?php _e('Film & Animation')?></option>
	            <option value="2"><?php _e('Autos & Vehicles')?></option>
	            <option value="10"><?php _e('Music')?></option>
	            <option value="15"><?php _e('Pets & Animals')?></option>
	            <option value="17"><?php _e('Sports')?></option>
	            <option value="19"><?php _e('Travel & Events')?></option>
	            <option value="20"><?php _e('Gaming')?></option>
	            <option value="22"><?php _e('People & Blogs')?></option>
	            <option value="23"><?php _e('Comedy')?></option>
	            <option value="24"><?php _e('Entertainment')?></option>
	            <option value="25"><?php _e('News & Politics')?></option>
	            <option value="26"><?php _e('Howto & Style')?></option>
	            <option value="27"><?php _e('Education')?></option>
	            <option value="28"><?php _e('Science & Technology')?></option>
	            <option value="29"><?php _e('Nonprofits & Activism')?></option>
	        </select>
	    </div>

		<?php _e( $block_caption, false)?>

		<div class="item-post-type m-t-15" data-type="video">
			<?php _e( $file_manager_video, false) ?>
		</div>
		
		<div class="post-advance m-t-15">
			<ul class="nav nav-tabs">
			  	<li class="nav-item">
			    	<a class="nav-link active" href="#"><i class="fas fa-magic text-info"></i> <?php _e("Advance option")?> <span class="arrow"><i class="ft-chevron-down"></i></span></a>
			  	</li>
			</ul>
			<div class="advance-content">
				<div class="form-group">
		        	<label><?php _e("Tags")?></label>
					<input class="form-control" name="advance[tags]" placeholder="<?php _e("video,funny,smile")?>">
				</div>
			</div>
		</div>

	</div>

	<?php _e( $block_schedule, false)?>

</div>
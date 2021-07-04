<div class="post post-create">
	
	<?php _e( $block_post_type, false) ?>

	<div class="post-content m-b-15">
		
		<div class="form-group">
			<input class="form-control" name="advance[title]" required="" placeholder="<?php _e("Enter title")?>">
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
			<?php _e( $file_manager, false) ?>
		</div>

		<div class="post-advance m-t-15 <?php _e($post?"active":"")?>">
			<ul class="nav nav-tabs">
			  	<li class="nav-item">
			    	<a class="nav-link active" href="#"><i class="fas fa-magic text-info"></i> <?php _e("Advance option")?> <span class="arrow"><i class="ft-chevron-down"></i></span></a>
			  	</li>
			</ul>
			<div class="advance-content">
				<div class="form-group">
					<label class="i-checkbox i-checkbox--tick i-checkbox--brand">
						<input type="checkbox" name="advance[show]" value="1"><?php _e('Show schedule to this profile')?>
						<span></span>
					</label>
				</div>
				<div class="form-group">
					<label for="status"><?php _e("Privacy status")?></label>
					<div>
						<label class="i-radio i-radio--tick i-radio--brand m-r-10">
							<input type="radio" name="advance[privacy_status]" checked="" value="public"> <?php _e("Public")?><span></span>
						</label>
						<label class="i-radio i-radio--tick i-radio--brand m-r-10">
							<input type="radio" name="advance[privacy_status]" value="private"> <?php _e("Private")?><span></span>
						</label>
					</div>
				</div>
				<div class="form-group">
		            <label for="thumbnail"><?php _e('Thumbnail')?></label>
		            <div class="input-group">
		                <input type="text" class="form-control" id="thumbnail" name="advance[thumbnail]">
		                <div class="input-group-append">
		                    <button class="btn btn-info btnOpenFileManager" data-id="thumbnail" data-select="single" data-file-type="image" type="button"><i class="far fa-folder-open"></i></button>
		                </div>
		            </div>
		        </div>
		        <div class="form-group">
		        	<label><?php _e("Tags")?></label>
					<input class="form-control" name="advance[tags]" placeholder="<?php _e("video,funny,smile")?>">
				</div>
				<div class="form-group">
					<label><?php _e("Loop video (times)")?></label>
					<select class="form-control" name="advance[loop]">
						<?php for($i = 1; $i <= 60; $i++){?>
							<option value="<?php _e($i)?>"><?php _e($i)?></option>
						<?php }?>
					</select>
				</div>
			</div>
		</div>

	</div>

	<div class="post-schedule m-b-15 <?php _e( $post?"active":"" )?>">
		<?php if(!$post){?>
		<label class="i-checkbox i-checkbox--tick i-checkbox--brand m-b-15">
			<input type="checkbox" name="is_schedule" value="1" > <?php _e('Schedule')?>
			<span></span>
		</label>
		<?php }else{?>
			<input type="hidden" name="is_schedule" value="1" >
		<?php }?>

		<div class="post-schedule-content">
			<div class="row">
				<div class="col-12">
					<div class="form-group">
						<label><?php _e('Time post')?></label>
						<input type="text" class="form-control datetime" autocomplete="off" name="time_post" value="">
					</div>
				</div>
			</div>
			<div class="row post-repost">
				<div class="col-6">
					<div class="form-group">
						<label><?php _e('Repost frequency (day)')?></label>
						<select class="form-control" name="repost_frequency">
							<?php for ($i=0; $i <= 60; $i++) {?>
								<option value="<?php _e($i)?>"><?php _e($i)?></option>
							<?php }?>
						</select>
						<span class="small"><?php _e('Set 0 to disable repost')?></span>
					</div>
				</div>
				<div class="col-6">
					<div class="form-group">
						<label><?php _e('Repost until')?></label>
						<input type="text" class="form-control datetime" autocomplete="off" name="repost_until" value="">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="fm-action text-right">
		<?php if(!$post){?>
		<button type="submit" data-action="<?php _e( get_module_url("save") )?>" class="btn btn-info btn-schedule d-none"><?php _e('Schedule')?></a>
		<button type="submit" data-action="<?php _e( get_module_url("save") )?>" class="btn btn-info btn-post-now"><?php _e('Post now')?></a>
		<?php }else{?>
		<input type="hidden" class="form-control" autocomplete="off" name="ids" value="<?php _e( $post->ids )?>">
		<button type="submit" data-action="<?php _e( get_module_url("save") )?>" class="btn btn-info btn-post-now"><?php _e('Update')?></a>
		<?php }?>
	</div>

</div>

<?php if($post){?>
	<script type="text/javascript">
		
		var post_type = '<?php _e( trim($post->type) )?>';
		var post_data = <?php _e($post->data)?>;
		var time_post = '<?php _e( datetime_show($post->time_post) )?>';
		var interval_per_post = <?php _e( $post->delay )?>;
		var repost_frequency = <?php _e( $post->repost_frequency )?>;
		var repost_until = '<?php _e( datetime_show($post->repost_until) )?>';

		$(function(){

			setTimeout(function(){
				$(".post .post-type input[value='"+post_type+"']").parents("a").trigger("click");

				var advance = JSON.parse( post_data.advance );

				$("[name='advance[title]']").val(advance.title);
				$("[name='advance[category]']").val(advance.category);
				$("[name='advance[loop]']").val(advance.loop);
				$("[name=time_post]").val(time_post);
				$("[name=interval_per_post]").val(interval_per_post);
				$("[name=repost_frequency]").val(repost_frequency);
				if(repost_until != "" && repost_frequency != 0){
					$("[name=repost_until]").val(repost_until);
				}

				if(advance.thumbnail != null){
					$("[name='advance[thumbnail]']").val(advance.thumbnail);
				}

				if(advance.show != undefined){
					$("[name='advance[show]']").prop('checked', true);
				}

				var el = $("textarea[name=caption]").emojioneArea();
            	el[0].emojioneArea.setText(post_data.caption);

            	var medias = post_data.medias;
            	if(medias != null){
	            	for (var i = 0; i < medias.length; i++) {
	            		File_Manager.addFile(medias[i], medias[i]);
	            	}
            	}
			}, 1000);

		});

	</script>
<?php }?>
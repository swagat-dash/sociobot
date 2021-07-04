<?php
$allow_packages = json_decode( get_data($result, 'packages') );
?>


<div class="subheadline wrap-m">
	
	<div class="sh-main wrap-c">
		<div class="sh-title text-info fs-18 fw-5"><i class="fas fa-file-import"></i> <?php _e('Import proxy')?></div>
	</div>
	<div class="sh-toolbar wrap-c">
		<div class="btn-group" role="group">
	    	<a 
	    		class="btn btn-label-info actionItem" 
	    		data-result="html" 
	    		data-content="column-two"
	    		data-history="<?php _e( get_module_url() )?>" 
	    		data-call-after="Layout.inactive_subsidebar();" 
	    		href="<?php _e( get_module_url() )?>"
	    	>
	    		<i class="fas fa-chevron-left"></i> <?php _e('Back')?>
	    	</a>
		</div>
	</div>

</div>

<div class="m-t-10">
		
	<div class="row">
		<div class="col-md-6">
			<form class="" action="<?php _e( get_module_url( 'do_export' ) )?>" data-redirect="<?php _e( get_module_url() )?>">
				<h5 class="fs-16 fw-4 text-info m-b-20"><i class="fas fa-caret-right"></i> <?php _e("Step 1: Create template import")?></h5>
			  	<div class="form-group">
			    	<label for="packages"><?php _e('Packages')?></label>
			    	<div>
			    		<?php if(!empty($packages)){
			    			foreach ($packages as $package) {
			    		?>
			    		<label class="i-checkbox i-checkbox--tick i-checkbox--brand m-r-10">
							<input type="checkbox" name="packages[]" value="<?php _e( $package->id )?>" <?php _e( (!empty( $allow_packages ) && in_array($package->id, $allow_packages ))?"checked":"" )?> > <?php _e( $package->name )?>
							<span></span>
						</label>
						<?php }}?>
			    	</div>
			  	</div>
			  	<div class="form-group">
			    	<label for="limit"><?php _e('Limit')?></label>
			    	<input type="number" class="form-control" id="limit" name="limit" value="<?php _e( get_data($result, 'limit') )?>">
			    	<div class="small m-t-5">
			    		<div class="text-info"><?php _e("Limit accounts can use this proxy on each social network, Set empty to unlimited")?></div>
			    	</div>
			  	</div>
			  	<button type="submit" class="btn btn-info"><?php _e('Creator')?></button>
			</form>

			<h5 class="fs-16 fw-4 text-info m-t-40 m-b-20"><i class="fas fa-caret-right"></i> <?php _e("Step 2: Import proxy")?></h5>
			<button type="button" class="btn btn-info btn-block fileinput-button">
				<i class="fas fa-upload"></i> <?php _e("Upload")?>
				<input id="import_proxy" type="file" name="files[]" multiple="">
			</button>
		</div>
	</div>


</div>
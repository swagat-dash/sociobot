<div class="modal fade proxy-advance-manager-modal" id="proxy-advance-manager-modal" tabindex="-1"
    role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        	<form class="actionForm" action="<?php _e( get_module_url("do_assign/".segment(3)) )?>" data-redirect="<?php _e( get_module_url("index/assign") )?>" method="POST">

	            <div class="modal-header">
	                <h3 class="modal-title"><i class="fas fa-user text-info"></i> <?php _e("Assign proxy")?></h3>
	                <button type="button" class="close" data-dismiss="modal"
	                    aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="modal-body">
	            	<div class="form-group">
			            <label for="proxy"><?php _e('Select proxy')?></label>
			            <select class="form-control select-proxy-assign" name="proxy" data-live-search="true">
			            	<option value=""><?php _e("Select proxy")?></option>
			            	<?php if(!empty($proxies)){
			            	foreach ($proxies as $proxy) {
			            	?>
			            	<option value="<?php _e( $proxy->ids )?>"><?php _e( "[".$proxy->location."] ".$proxy->address )?></option>
			            	<?php }}?>
			            </select>
			        </div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e("Close")?></button>
	                <button type="submit" class="btn btn-info"><?php _e("Submit")?></button>
	            </div>

	        </form>
        </div>
    </div>
</div>
<div class="subheader-main"> 
	<button class="btn btn-label-info m-r-10 subheader-toggle"><i class="fas fa-bars"></i></button>
	<h3 class="title"><i class="text-info <?php _e( $module_icon )?>"></i> <?php _e( $module_name )?></h3>
</div>	

<div class="subheader-toolbar">
	<div class="btn-group" role="group">
	    <a 
	    	class="actionItem btn btn-secondary" 
			data-result="html" 
			data-content="column-two" 
			href="<?php _e( get_module_url('index/assign') )?>" 
			data-history="<?php _e( get_module_url('index/assign') )?>"
	    ><i class="fas fa-user"></i> <?php _e('Assign proxy')?></a>
		<a 
	    	class="actionItem btn btn-secondary" 
			data-result="html" 
			data-content="column-two" 
			href="<?php _e( get_module_url('index/import') )?>" 
			data-history="<?php _e( get_module_url('index/import') )?>"
			data-call-after="Proxy_advance_manager.import();"
	    ><i class="fas fa-file-import"></i> <?php _e('Import')?></a>
	</div>
</div>
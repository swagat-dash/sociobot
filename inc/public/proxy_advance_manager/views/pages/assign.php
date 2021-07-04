<form class="" action="<?php _e( get_module_url("index/assign") )?>">
	<div class="subheadline wrap-m">
		
		<div class="sh-main wrap-c">
			<div class="sh-title text-info fs-18 fw-5"><i class="fas fa-user"></i> <?php _e('Assign proxy')?></div>
		</div>
		<div class="sh-toolbar wrap-c">
			<div class="input-group box-search-one">
			  	<input type="text" class="form-control" name="k" placeholder="<?php _e('Search')?>" >
			  	<div class="input-group-append" id="button-addon4">
			  		<button class="btn" type="submit"><i class="fa fa-search"></i></button>
			  	</div>
			</div>
		</div>

	</div>

	<div class="m-t-50 table-mod table-responsive">
		
		<table class="table">
			<thead>
				<tr>
					<th scope="col"></th>
					<th scope="col" colspan="2">
						<a href="<?php _e( table_sort('link', 1, 'index/assign/') )?>"><?php _e('Basic info')?></a>
						<span class="sort-caret <?php _e( table_sort('icon', 1) )?>">
	                		<i class="asc fas fa-sort-up" aria-hidden="true"></i>
	                		<i class="desc fas fa-sort-down" aria-hidden="true"></i>
	                	</span>		
					</th>
					<th scope="col">
						<a href="<?php _e( table_sort('link', 2, 'index/assign/') )?>"><?php _e('Social network')?></a>
						<span class="sort-caret <?php _e( table_sort('icon', 2) )?>">
	                		<i class="asc fas fa-sort-up" aria-hidden="true"></i>
	                		<i class="desc fas fa-sort-down" aria-hidden="true"></i>
	                	</span>
					</th>
					<th scope="col">
						<a href="<?php _e( table_sort('link', 3, 'index/assign/') )?>"><?php _e('Account name')?></a>
						<span class="sort-caret <?php _e( table_sort('icon', 3) )?>">
	                		<i class="asc fas fa-sort-up" aria-hidden="true"></i>
	                		<i class="desc fas fa-sort-down" aria-hidden="true"></i>
	                	</span>
					</th>
					<th scope="col">
						<a href="<?php _e( table_sort('link', 4, 'index/assign/') )?>"><?php _e('Account username')?></a>
						<span class="sort-caret <?php _e( table_sort('icon', 4) )?>">
	                		<i class="asc fas fa-sort-up" aria-hidden="true"></i>
	                		<i class="desc fas fa-sort-down" aria-hidden="true"></i>
	                	</span>
					</th>
					<th scope="col">
						<a href="<?php _e( table_sort('link', 5, 'index/assign/') )?>"><?php _e('Proxy assigned')?></a>
						<span class="sort-caret <?php _e( table_sort('icon', 4) )?>">
	                		<i class="asc fas fa-sort-up" aria-hidden="true"></i>
	                		<i class="desc fas fa-sort-down" aria-hidden="true"></i>
	                	</span>
					</th>
					<th scope="col">
						<?php _e('Proxy user')?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php if(!empty($result)){?>

				<?php foreach ($result as $key => $row): ?>
				<tr class="item">
					<td>
						<div class="btn-group">
						  	<button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
						  	<div class="dropdown-menu dropdown-menu-anim">
							    <a class="dropdown-item actionProxyAssign" href="<?php _e( get_module_url('assign/'.get_data($row, 'ids') ) )?>" data-result="html"><i class="far fa-eye"></i> <?php _e('Assign')?></a>
							    <a class="dropdown-item actionItem" href="<?php _e( get_module_url('cancel_assign/'.get_data($row, 'ids') ) )?>" data-id="<?php _e( get_data($row, 'ids') )?>" data-confirm="<?php _e('Are you sure to canncel assign for this account?')?>" data-redirect=""><i class="fas fa-times"></i> <?php _e('Cancel')?></a>
						  	</div>
						</div>
					</td>				
					<td class="avatar" ><img src="<?php _e( get_avatar( $row->fullname ) )?>"></td>
					<td>
						<span class="fw-5 text-info"><?php _e( $row->fullname )?></span><br/>
						<?php _e( $row->email )?>
					</td>
					<td><?php _e( ucfirst($row->social_network) )?></td>
					<td><?php _e( $row->name )?></td>
					<td><?php _e( $row->username )?></td>
					<td><?php _e( $row->address )?></td>
					<td><?php _e( !is_numeric($row->proxy)?$row->proxy:"" )?></td>
				</tr>
				<tr class="spacer"></tr>
				<?php endforeach ?>

			<?php }else{?>
				<tr>
					<td colspan="10">
						<div class="empty m-t-50">
							<div class="icon"></div><br/>
							<a 
					    		class="btn btn-info actionItem" 
					    		data-result="html" 
					    		data-content="column-two"
					    		data-history="<?php _e( get_module_url('index/update') )?>" 
					    		href="<?php _e( get_module_url('index/update') )?>"
					    	>
					    		<?php _e('Add new')?>
					    	</a>
						</div>	
					</td>
				</tr>
			<?php }?>
			</tbody>
		</table>

	</div>
</form>

<nav class="m-t-30">
<?php _e( $pagination, false)?>
</nav>
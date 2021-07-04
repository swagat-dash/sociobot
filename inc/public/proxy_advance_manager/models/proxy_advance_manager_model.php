<?php
class proxy_advance_manager_model extends MY_Model {
	public $tb_proxy_manager = "sp_proxy_manager";
	public $tb_account_manager = "sp_account_manager";
	public $tb_team = "sp_team";
	public $tb_users = "sp_users";
	
	public function __construct(){
		parent::__construct();

		$this->fields = ["d.fullname", "a.social_network", "a.name", "a.username", "b.address"];
		
		//
		$module_path = get_module_directory(__DIR__);
		$this->module_name = get_module_config( $module_path, 'name' );
		$this->module_icon = get_module_config( $module_path, 'icon' );
		$this->module_color = get_module_config( $module_path, 'color' );
		//
	}

	public function block_permissions($path = ""){
		$dir = get_directory_block(__DIR__, get_class($this));
		return [
			'position' => 1000,
			'name' => $this->module_name,
			'color' => $this->module_color,
			'icon' => $this->module_icon, 
			'id' => str_replace("_model", "", get_class($this)),
			'html' => view( $dir.'pages/block_permissions', ['path' => $path], true, $this ),
		];
	}

	public function get_data()
	{
		$page   = (int)post("p");
		$limit  = 100;
		$result = $this->get_list($limit, $page);
		$total  = $this->get_list(-1, -1);

		$query = [];
		$query_string = "";
		if(post("c")) $query["c"] = post("c");
		if(post("t")) $query["t"] = post("t");
		if(post("k")) $query["k"] = post("k");

		if( ! empty($query) )
		{
			$query_string = "?".http_build_query($query);
		}

		$configs = [
			"base_url"   => get_module_url($query_string), 
			"total_rows" => $total, 
			"per_page"   => $limit
		];

		$this->pagination->initialize($configs);

		$data = [
			"result"     => $result,
			"total"      => $total,
			"page"       => $page,
			"limit"      => $limit,
			"pagination" => $this->pagination->create_links()
		];

		return $data;
	}

	public function get_list($limit=-1, $page=-1)
	{
		$c = (int)post('c');
		$t = post('t'); 
		$k = post('k');

		if($limit == -1)
		{
			$select = " SELECT COUNT(*) as count";
		}else{
			$select = " SELECT a.username, a.name, a.social_network, b.address, d.fullname, d.email, a.ids, a.proxy ";
		}
		$from = " FROM ".$this->tb_account_manager." as a ";
		$join1 = " LEFT JOIN ".$this->tb_proxy_manager." as b ON a.proxy = b.id ";
		$join2 = " JOIN ".$this->tb_team." as c ON a.team_id = c.id ";
		$join3 = " JOIN ".$this->tb_users." as d ON c.owner = d.id ";

		$where1 = " WHERE ( ( a.social_network = 'instagram' OR a.social_network = 'pinterest') AND a.login_type = 2 ) ";

		$where2 = "";
		if($k)
		{
			$i = 1;
			foreach ($this->fields as $field)
			{
				if($i == 1)
				{
					$where2 = " AND ( ".$field." LIKE '%".$k."%' ESCAPE '!' ";
				}
				else
				{
					$where2 .= " OR ".$field." LIKE '%".$k."%' ESCAPE '!' ";
				}
				$i++;
			}
		}

		if( $where2 ){
			$where2 .= " ) ";
		}

		$group_by = "";

		$order_by = "";
		if($c){
			$i = 1;
			$s = ( $t && ( $t == "asc" || $t == "desc") )? $t : "desc";
			foreach ($this->fields as $field)
			{
				if($i == $c)
				{
					$order_by = " ORDER BY ".$field." ".$s." ";
				}
				$i++;
			}
		}
		else
		{
			$order_by = "  ORDER BY b.created DESC ";
		}
 	
 		$limit_row = " LIMIT 100 ";

		$query = $this->db->query( $select . $from . $join1 . $join2 . $join3 . $where1 . $where2 . $group_by . $order_by . $limit_row );

		if($limit == -1)
		{
			return $query->row()->count;
		}else{
			return $query->result();
		}
	}
}

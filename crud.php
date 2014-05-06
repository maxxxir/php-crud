<?php
    require_once('db.php');


class crud{
	
	private $db ;
	public $last_query = '' ;
	public $affected_rows = 0 ;

         public function __construct(){
			 
			 $this->db =  new mysqldatabase();
		 }
		
		
		 public function get ( $table , $conds = array() , $limit = 0 , $offset = 0 , $order = array('id'=>'desc')  ){

			$sql = "SELECT * from `$table` WHERE 1=1 ";	
			if(!empty($conds))
			foreach($conds as $k=>$v)
			{
				$sql .= " && $k= '$v' ";
			}
			 
			 if(!empty($order))
			 {
				 $sql .=" order by ";
				 $o = array();
				 foreach($order as $k=>$v)
				 {
					 $o[] = " $k $v " ; 
				 }
                 $sql .= implode( ',' , $o );
			 }
			
			if($limit != 0 )
			 $sql .=" LIMIT $limit OFFSET $offset ";
			 
			
            $this->last_query = $sql;
			$records = $this->db->query($sql);

			return $this->return_objrct_array($records);
		}		


		
	

		 public function delete ( $table  ,  $conds = array() ){
			$sql = "delete from `$table` WHERE 1=1 ";	
			if(!empty($conds))
			foreach($conds as $k=>$v)
			{
				$sql .= " && $k= '$v' ";
			}
			 
			 $this->last_query = $sql ;
			 $this->db->query($sql);
			 $this->affected_rows = $this->db->affected_rows();
		}		

		
		 public function insert ( $table , $dt = array() ){
			
			if(empty($dt)) return false;
			
				
				$sql = "insert into `$table` ( ";
				$c= 0 ;
				foreach($dt as $k=>$v)
				{
				  $sql .= " `$k`  ";
				  $c++;
				  if($c < count($dt))
				  $sql .= " , ";
				}
				$sql .= " ) values(";
				$c = 0 ;
				foreach($dt as $k=>$v)
				{
				   $sql .= " '$v'  ";
				  $c++;
				  if($c < count($dt))
				  $sql .= " , ";
				}
				$sql .= ") ";
				
				$this->last_query = $sql;
				$this->db->query($sql);
				return  $this->db->insert_id();
		}		


		 public function edit ( $table , $data = array() , $cond = array() ){
			
			if(empty($data)) return false;
			
				$sql = "update `$table`  ";
				$sql .=" SET  ";
				$edit = array();
				foreach($data as $k=>$v)
				{
				  $edit[] = " `$k` = '$v' ";
				}
				$sql .= implode(',' , $edit);
				
				$sql .=" WHERE 1  ";
				if(!empty($cond))
				foreach($cond as $k=>$v)
				{
				  $sql .= " && `$k` = '$v'  ";
				}
				$this->last_query = $sql;
				$this->db->query($sql);
			    $this->affected_rows = $this->db->affected_rows();
		}



   function get_join(  $tbl = ''  , $join = array() ,   $cond = array() , $select = array() , $limit = 0  , $offset = 0 , $order=array()){
        
		
		if(!empty($select))
		$sql = 'select '.implode(',' , $select);
        else
		$sql = 'select '. $tbl.'.* ,'. $join[0].'.*';
		
		$sql .= " from $tbl ";
		
		foreach($join as $join)
		{
			$join_type = isset($join[3]) ? $join[3] : 1 ; 
			$sql .= " JOIN $join[0] , $join[1] .'='.$join[2] , $join_type ";
		}
		
		$sql .= " where 1 ";
		if(!empty($cond))
		{
			foreach($cond as $k=>$v)
			$sql .= " , $k = $v ";
		}

		 if(!empty($order))
		 {
			 $sql .=" order by ";
			 $o = array();
			 foreach($order as $k=>$v)
			 {
				 $o[] = " $k $v " ; 
			 }
			 $sql .= implode( ',' , $o );
		 }
		
		if($limit != 0 )
		 $sql .=" LIMIT $limit OFFSET $offset ";

		$this->last_query = $sql;
		$records = $this->db->query($sql);

		return $this->return_objrct_array($records);
   }   
   
   
   
   
		 public function query ( $q  ){
			$this->last_query = $q;
			$records = $this->db->query($q);
			$this->affected_rows = $this->db->affected_rows();
			return $this->return_objrct_array($records);
		}	
		
		
		 function increment( $tbl , $cond = array(), $upfild = '' , $value = 1)
		{
				$sql = " update $tbl set $upfild = $upfild +1 where 1 ";
				if(!empty($cond))
				foreach($cond as $k=>$v)
				{
				  $sql .= " && `$k` = '$v'  ";
				}
				
				$this->last_query = $sql;
				$this->db->query($sql);
			    $this->affected_rows = $this->db->affected_rows();
		}
					
	/////////////////// instantiate //////////////////////////
		
		 private function instantiate($record){
			$object = new crud;
			foreach($record as $key=>$val){
			  $object->$key = $val ;				
			}
			return $object;
		}
			

		 function has_attr($key){
			$object_vars = get_object_vars($this);
			return array_key_exists($key , $object_vars );	
		}
		
		 private function return_objrct_array($records){
	
			$object_array = array();
			
			while($row= $this->db->fetch_array($records)){
				
				$obj = $this->instantiate($row);
				$object_array[] = $obj ;
			}
			return $object_array ;	
		}
	
	
	
		
}




?>
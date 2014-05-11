<?php
    require_once('db.php');


class crud{
	
	private $db ;
	public $last_query = '' ;
	public $affected_rows = 0 ;

         public function __construct(){
			 
			 $this->db =  new mysqldatabase();
		 }
		
		
		 public function get ( $table , $conds = array() , $limit = 0 , $offset = 0 , $order = array()  ){

			$sql = "SELECT * from `$table` ";
			$sql .= $this->condition($conds);	
			$sql .= $this->order($order);	
			if($limit != 0 )
			 $sql .=" LIMIT $limit OFFSET $offset ";
			 
			
           echo  $this->last_query = $sql;
		   
		   echo '<br />';
			$records = $this->db->query($sql);

			return $this->return_objrct_array($records);
		}
		
		
	

		 public function delete ( $table  ,  $conds = array() ){
			$sql  = "delete from `$table`  ";	
			$sql .= $this->condition($conds);	

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


		 public function edit ( $table , $data = array() , $conds = array() ){
			
			if(empty($data)) return false;
			
				$sql = "update `$table`  ";
				$sql .=" SET  ";
				$edit = array();
				foreach($data as $k=>$v)
				{
				  $edit[] = " `$k` = '$v' ";
				}
				$sql .= implode(',' , $edit);
			    $sql .= $this->condition($conds);	

				$this->last_query = $sql;
				$this->db->query($sql);
			    $this->affected_rows = $this->db->affected_rows();
		}



   function get_join(  $tbl = ''  , $join = array() ,   $conds = array() , $select = array() , $limit = 0  , $offset = 0 , $order=array()){
        
		
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
		
		$sql .= $this->condition($conds);	
		$sql .= $this->order($order);	
		
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
		
		
		 function increment( $tbl , $conds = array(), $upfild = '' , $value = 1)
		{
				$sql = " update $tbl set $upfild = $upfild +1  ";
			    $sql .= $this->condition($conds);	
				
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
	
	
	
		function condition( $conds ){
			if(empty($conds)) return '';
			
            $cond = ' WHERE ';
			$cntr = 0 ;

			foreach($conds as $k=>$v)
			{
				$cntr++;
				$operatpr = "=";
				$type = $cntr == 1 ? '' : "&&";
				$prt  = false ;
				
				if(is_array($v))
				{
					 
					 foreach($v as $kk=>$vv  )
					 {
						 $vv = trim($vv);
						 if(in_array($vv , array('&&' , '||', 'AND' , 'OR')))
						 {
							$type = $vv ;
							unset($v[$kk]); 
						 }
						 else if(in_array($vv , array('=' ,'!=' , '>' , '<'  , 'LIKE'  , 'BETWEEN')))
						 {
							$operatpr = $vv ;
							unset($v[$kk]); 
						 }
						 else if(in_array($vv , array('(', ')')))
						 {
							 if($vv == '(')
							 $prt = 'o' ;
							 else if($vv == ')')
							 $prt = 'c' ;
							 unset($v[$kk]); 
						 }
						 
					 }
					 
					 $v    = array_values($v);
				     $type = $cntr == 1 ? '' : $type;
					 
					 
					 $cond .= " $type ";
					 
					 if($prt && $prt == 'o' )
					 $cond .= '(';
					 
					 if(count($v) > 1 )
					 {
						 if(in_array($operatpr  , array('BETWEEN', 'between')))
						 {
						   $cond .= "  `$k` $operatpr $v[0] AND $v[1] ";
						 }
						 else
						 {
						   $operatpr = $operatpr == '!=' ? 'NOT IN' : 'IN';
						   $cond .= "  `$k` $operatpr (".implode( ',' ,  $v).")";
						 }
					 }
					 else
					 $cond .= " `$k` $operatpr '$v[0]' ";
					 
					 if($prt && $prt == 'c' )
					 $cond .= ')';
					 

				}
				else
				$cond .= " $type `$k` = '$v' ";
			}
			return $cond ;
		}
		
		
		
		function order( $order ){
            $ord = '';
			 if(!empty($order))
			 {
				 $ord .=" order by ";
				 $o = array();
				 foreach($order as $k=>$v)
				 {
					 $o[] = " $k $v " ; 
				 }
                 $ord .= implode( ',' , $o );
			 }
			
			return $ord ;
		}		
		
	
		
}




?>
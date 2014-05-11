<?php
  require_once('crud.php');
  $c = new crud;
 //echo $c->insert('test' , array('title'=>'title' , 'name'=>'max'));
 // $c->edit('test' , array('title'=>'this is important') , array('id'=>2)); 
 // echo $c->affected_rows;
 //$c->delete('test' , array('id'=>2));
// $c->increment('test' , array('id'=>3) , 'id');
// echo $c->last_query;

 $res = $c->get('test' , array('id'=>array('BETWEEN' , 1,5)) );
 echo $c->last_query;
 foreach($res as $obj )
 {
	 echo '<br />';
	 echo $obj->title ;
 }

?>
php-crud by maxxxir@gmail.com
========

simple but powerful crud system

1 - add your database auth info in db.php

2 - include crud.php in your script 

3 - create a new object 



    require_once('crud.php');
    $c = new crud; 


functions : 


    $c->insert
    $c->edit
    $c->delete
    $c->get
    $c->get_join
    $c->increment


in this examples we have a table clled users :
   
    users : id , username , tell 


1 - insert :

 function insert ( $table , $data = array() )
 
adding a  new user ( username = max , tell = 123 ) to users table 

    $c = new crud ;
    $id = $c->insert('users' , array('username'=>'max' , 'tell'=>'123')); 

this function will return the id of inserted user

------------------------------------------------------------------------------

2 - edit :

 function edit ( $table , $data = array() , $conditions = array() )
 
 i want to change tell to 321 for max
 
      $c->edit('users' , array('tell'=>'321') , array('username'=>'max')); 

------------------------------------------------------------------------------


3 - delete :

 function delete ( $table , $conditions = array() )
 
 
 deleteing user alex from users table 
   
     $c->delete('users' , array('username'=>'alex')); 
 
 deleteing user with id = 3 and tell = 666
  
       $c->delete('users' , array('id'=>'3' , 'tell'=>'666')); 


 
------------------------------------------------------------------------------

4 - get : 

 function get ( $table , $conditions = array() , $limit = 0 , $offset = 0 , $order = array('id'=>'desc') ) 
 
 we want to get users who has username = max , we want 3 rows max and we want to sort them by id asc and username desc
 
     $users = $c->get('users' , array('username'=>'max') , 3 , 0 , array('id'=>'asc' , 'username'=>'desc'));
     foreach($users as $u)
     {
         echo $u->id.' - '.$u->username ; 
     }
    


--------------------------------------------------------------------------------

5 - advanced conditioning 

  lets say you to get user with id = 1 
  
     $c->get('users' , array('id'=>1));
  
  by default this will generate 
    
	 && id = 1 
	 
  what if we want to change operator ? 
  
     $c->get('users' , array('id'=> array('!=' , '1' ));
	 
  this wil generate 
	  
	 && id != 1
	 
  note the order doesn't matter ! 
  
     array('id'=> array('!=' , '1' ) 
     array('id'=> array( '1'  , '!=') 
	 
  will generate the same result and this goes for all examples  
  

  what if we want to use || , OR , AND  instead of && ? 
  
     $c->get('users' , array('id'=> array('||' , '!=' , '1' ));

  
  this wil generate 
  
  	 || id != 1
	 
	 
	 
  here is list of avalibal operators : 
   
     array('=' ,'!=' , '>' , '<'  , 'LIKE'  , 'BETWEEN' , '&&' , '||', 'AND' , 'OR')
	 
  again order doesn't matter 
  we can write 
	
     $c->get('users' , array('id'=> array(,'1' , '!=' , '||'  ));

	 
   what if we want to get users with id in 1,2,3,4,5 
   
     $c->get('users' , array('id'=> array(1,2,3,4,5));
	 
   this will generate 
   
      && id IN (1,2,3,4,5)
	  
   OR :
	  
     $c->get('users' , array('id'=> array('||' , '!=' , 1,2,3,4,5));
	 
   this will generate 
   
      || id NOT IN (1,2,3,4,5)
	  
	
   we want to get users who has id > 30 and username = max or name = max 
   what we want is this query : 
  
     where age > 30 && (  username = 'max' || name = 'max' ) 
	 
   here is how :
     
     $c->get('users' ,  array(
	 'id'=>array('>' , '30') ,
	 'username'=>array( '(' , 'max' ) , 
	 'name'=>array( '||' ,  'max'  , ')' )
	 ));

--------------------------------------------------------------------------------


  
after each function we can see executed query by :

    echo $c->last_query ;

we can see affected rows by

    echo $c->affected_rows;

 

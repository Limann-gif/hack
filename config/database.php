<?php
 class Database{
	
	  
    
     public function getConnection(){

      $host  = "localhost";
      $user  = "root";
      $password   = "";
      $database  = "cms"; 
	  
		$conn = new mysqli($host, $user, $password, $database);
		if($conn->connect_error){
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			//return $conn;
			echo "connection sucessful";
	    }
        
    }
 }
?>

<html>
<title>Hello</title>

<body>WeLcome</body>
</html>
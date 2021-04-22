<html>


<title></title>



<body>

<?php
var categoryData = $('#categoryList').DataTable({
	"lengthChange": false,
	"processing":true,
	"serverSide":true,
	"order":[],
	"ajax":{
		url:"manage_categories.php",
		type:"POST",
		data:{action:'categoryListing'},
		dataType:"json"
	},
	"columnDefs":[
		{
			"targets":[0, 2, 3],
			"orderable":false,
		},
	],
	"pageLength": 10
});	

public function getCategoryListing(){	
		
	$sqlQuery = "
		SELECT id, name
		FROM ".$this->categoryTable."  
		 ";
	
	if(!empty($_POST["search"]["value"])){
		$sqlQuery .= ' name LIKE "%'.$_POST["search"]["value"].'%" ';				
	}
	
	if(!empty($_POST["order"])){
		$sqlQuery .= 'ORDER 
BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	} else {
		$sqlQuery .= 'ORDER BY id DESC ';
	}
	if($_POST["length"] != -1){
		$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
	}

	$stmt = $this->conn->prepare($sqlQuery);
	$stmt->execute();
	$result = $stmt->get_result();	
	
	$stmtTotal = $this->conn->prepare("SELECT * 
FROM ".$this->categoryTable);
	$stmtTotal->execute();
	$allResult = $stmtTotal->get_result();
	$allRecords = $allResult->num_rows;		
	
	$displayRecords = $result->num_rows;
	$categories = array();		
	while ($category = $result->fetch_assoc()) { 				
		$rows = array();				
		$rows[] = $category['id'];
		$rows[] = $category['name'];					
		$rows[] = '<a href="add_categories.php?id='.$category["id"].'" 
class="btn btn-warning btn-xs 
update">Edit</a>';
		$rows[] = '<button type="button" 
name="delete" 
id="'.$category["id"].'" 
class="btn btn-danger btn-xs 
delete" >Delete</button>';
		$categories[] = $rows;
	}
	
	$output = array(
		"draw"	=>	intval($_POST["draw"]),			
		"iTotalRecords"	=> 	$displayRecords,
		"iTotalDisplayRecords"	=>  $allRecords,
		"data"	=> 	$categories
	);
	
	echo json_encode($output);	
}	
?>


<div class="panel-body">
	<div class="panel-heading">
		<div class="row">
			<div class="col-md-10">
				<h3 class="panel-title"></h3>
			</div>
			<div class="col-md-2" align="right">
				<a href="add_categories.php" 
class="btn btn-default btn-xs">Add New</a>				
			</div>
		</div>
	</div>
	</body>
</html>
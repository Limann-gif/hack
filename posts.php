<section id="main">
<div class="container">
<div class="row">	
<?php include "left_menus.php"; ?>
<div class="col-md-9">
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title">Post Listing</h3>
</div>
<div class="panel-body">
<div class="panel-heading">
<div class="row">
<div class="col-md-10">
<h3 class="panel-title"></h3>
</div>
<div class="col-md-2" align="right">
<a href="compose_post.php" 
class="btn btn-default btn-xs">Add New</a>				
</div>
</div>
</div>
<table id="postsList" class="table table-bordered table-striped">
<thead>
<tr>
<th>Title</th>
<th>Category</th>
<th>User</th>
<th>Status</th>	
<th>Created</th>
<th>Updated</th>															
<th></th>
<th></th>	
</tr>
</thead>
</table>
</div>
</div>
</div>
</div>
</div>
</section>

<?php
var postsData = $('#postsList').DataTable({
	"lengthChange": false,
	"processing":true,
	"serverSide":true,
	"order":[],
	"ajax":{
		url:"manage_posts.php",
		type:"POST",
		data:{action:'postListing'},
		dataType:"json"
	},
	"columnDefs":[
		{
			"targets":[0, 6, 7],
			"orderable":false,
		},
	],
	"pageLength": 10
});	

public function getPostsListing(){		
		
	$whereQuery = '';
	if($_SESSION['user_type'] == 2) {
		$whereQuery = "WHERE p.userid ='".$_SESSION['userid']."'";
	}	
	
	$sqlQuery = "
		SELECT p.id, p.title, p.category_id, u.first_name, u.last_name, 
p.status, p.created, p.updated, c.name 
		FROM ".$this->postTable." p
		LEFT JOIN ".$this->categoryTable." c ON c.id = p.category_id
		LEFT JOIN ".$this->userTable." u ON u.id = p.userid
		$whereQuery";
	
	if(!empty($_POST["search"]["value"])){
		$sqlQuery .= ' title LIKE "%'.$_POST["search"]["value"].'%" ';
		$sqlQuery .= ' OR message LIKE "%'.$_POST["search"]["value"].'%" ';
		$sqlQuery .= ' OR created LIKE "%'.$_POST["search"]["value"].'%" ';
		$sqlQuery .= ' OR updated LIKE "%'.$_POST["search"]["value"].'%" ';			
	}
	if(!empty($_POST["order"])){
		$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].'
 '.$_POST['order']['0']['dir'].' ';
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
FROM ".$this->postTable);
	$stmtTotal->execute();
	$allResult = $stmtTotal->get_result();
	$allRecords = $allResult->num_rows;
	
	
	$displayRecords = $result->num_rows;
	$posts = array();		
	while ($post = $result->fetch_assoc()) { 				
		$rows = array();	
		$status = '';
		if($post['status'] == 'published')	{
			$status = '<span class="label 
label-success">Published</span>';
		} else if($post['status'] == 'draft') {
			$status = '<span class="label 
label-warning">Draft</span>';
		} else if($post['status'] == 'archived') {
			$status = '<span class="label
 label-danger">Archived</span>';
		}
		
		$rows[] = ucfirst($post['title']);
		$rows[] = $post['name'];	
		$rows[] = ucfirst($post['first_name'])." ".$post['last_name'];	
		$rows[] = $status;				
		$rows[] = $post['created'];	
		$rows[] = $post['updated'];
		$rows[] = '<a href="compose_post.php?id='.$post["id"].'" 
class="btn btn-warning btn-xs 
update">Edit</a>';
		$rows[] = '<button type="button" 
name="delete" id="'.$post["id"].'"
 class="btn btn-danger btn-xs 
delete" >Delete</button>';
		$posts[] = $rows;
	}
	
	$output = array(
		"draw"	=>	intval($_POST["draw"]),			
		"iTotalRecords"	=> 	$displayRecords,
		"iTotalDisplayRecords"	=>  $allRecords,
		"data"	=> 	$posts
	);
	
	echo json_encode($output);	
}	
?>
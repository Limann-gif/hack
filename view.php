<?php 
while ($post = $result->fetch_assoc()) {
	$date = date_create($post['created']);
	$message = str_replace("\n\r", "<br><br>", $post['message']);
?>
	<div class="col-md-10 blogShort">
	<h2><?php echo $post['title']; ?></h2>
	<em><strong>Published on</strong>: <
?php echo date_format($date, "d F Y");	?></em>
	<em><strong>Category:</strong> <a href="#" 
target="_blank"><?php echo $post['category']; ?></a></em>
	<br><br>
	<article>
		<p><?php echo $message; ?> 	</p>
	</article>		
	</div>
<?php } ?> 
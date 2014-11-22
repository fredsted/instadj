<?php
try {
	$db = new PDO('sqlite:searches.sqlite');
	$q = "SELECT search, count(search) AS count FROM searches WHERE date >= date('now','-100 day') GROUP BY search ORDER BY count(search) DESC LIMIT 10";
	
	$result = $db->query($q);
	
	$i = 1;
	
	foreach($result as $row) {
		if (strpos(urldecode($row['search']), 'http') === false) {
			echo '<span style="color:#CCC">'.$i.'. </span>&nbsp;
			<a href="#" class="recent" data-href="'.$row['search'].'">
			 '.str_replace('+', ' ', htmlspecialchars(urldecode($row['search']))).'<!-- ('.$row['count'].')--></a><br />';
		}
		$i++;
	}
	
	$db = NULL;

} catch(PDOException $e) {
	print 'Exception : '.$e->getMessage();
}
?>
</table>
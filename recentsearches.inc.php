<?php
				try {
					$db = new PDO('sqlite:searches.sqlite');
					//$q = sprintf("INSERT INTO searches (search) VALUES ('%s');", sqlite_escape_string(qs('q')));
					//$q = "SELECT search FROM searches ORDER BY id DESC LIMIT 10;";
					$q = "SELECT search, count(search) FROM searches WHERE date >= date('now','-14 day') GROUP BY search ORDER BY count(search) DESC LIMIT 7";
					
					$result = $db->query($q);
					
					

					echo '<h3>Top	 searches</h3>';
					foreach($result as $row) {
						if (strpos(urldecode($row['search']), 'http') === false) {
						echo '<a href="#" class="recent" data-href="'.$row['search'].'" style="font-size:13px;"><i class="icon  icon-search"> </i> '.str_replace('+', ' ', htmlspecialchars(urldecode($row['search']))).'</a><br />';
						}
					}
					
					$db = NULL;

				}
					catch(PDOException $e)
				{
					print 'Exception : '.$e->getMessage();
				}
?>
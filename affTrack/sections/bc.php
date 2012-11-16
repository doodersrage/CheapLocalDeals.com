      <!-- h2 stays for breadcrumbs -->
<?PHP
	switch($_GET['section']){
		case 'users':
			switch($_GET['mode']){
				case 'view':
					if(!empty($_GET['sess_id'])){
					  echo '<h2><a href="?section=users">User Interactions</a> &raquo; <a href="'.curPageURL().'" class="active">Session Page Views</a></h2>';
					}
					if(!empty($_GET['id'])){
					  echo '<h2><a href="?section=users">User Interactions</a> &raquo; <a href="'.curPageURL().'" class="active">Session Page View Detail</a></h2>';
					}
				break;
				default:
				  echo '<h2><a href="?section=users" class="active">User Interactions</a></h2>';
				break;
			}
		break;
		case 'purchases':
			switch($_GET['mode']){
				case 'view':
					  echo '<h2><a href="?section=purchases">Purchases</a> &raquo; <a href="'.curPageURL().'" class="active">Purchase Detail View</a></h2>';
				break;
				default:
				  echo '<h2><a href="?section=purchases" class="active">Purchases</a></h2>';
				break;
			}
		break;
		case 'changepass':
			echo '<h2><a href="?section=changepass" class="active">Change Your Password</a></h2>';
		break;
		default:
			echo '<h2><a href="index.php" class="active">Dashboard</a></h2>';
		break;
	}
?>
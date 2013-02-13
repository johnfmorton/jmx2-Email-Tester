<?php
if(isset($_POST['filename'])){
	$filename = htmlspecialchars($_POST["filename"]);
	//$userFileName = str_replace(' ', '_', (strtolower ('user-'. $newName . '.yaml')));
	if (unlink('../_saved_users/'.$filename)) {
		echo TRUE;
	} else {
		//echo "Couldn't write the YAML file for this user. Check the permissions on the 'saved_users' direcorty. Try 777.";
		echo FALSE;
	}
} 
?>
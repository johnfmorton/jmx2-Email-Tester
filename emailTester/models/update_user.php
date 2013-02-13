<?php

if(isset($_POST['thefilename'])){
	# https://github.com/mustangostang/spyc/
	require_once '../lib/Spyc.php';

	$userFileName = htmlspecialchars($_POST["thefilename"]);
	$username = htmlspecialchars($_POST['thename']);
	$useremail = htmlspecialchars($_POST['theemail']);
	$isChecked = htmlspecialchars($_POST['ischecked']);
	
	$array['email'] = $useremail;
	$array['name'] = $username;
	$array['ischecked'] = $isChecked;

	$yaml = Spyc::YAMLDump($array);


	if (!file_exists('../_saved_users/'.$userFileName)) {
		$success = array(
			'success' => FALSE,
			'msg'=> "File for that user wasn't found."
		);

		echo json_encode($success);
		exit();
	}

	$userFileHandle = fopen('../_saved_users/'.$userFileName, 'w'); 
	if ($userFileHandle) {
		fwrite($userFileHandle, $yaml);
		fclose($userFileHandle);
		$success = array(
			'success' => TRUE,
			'msg'=> "User '$username' was updated."
			);
		echo json_encode($success);
		
	} else {
		$success = array(
			'success' => FALSE,
			'msg'=> "Could not write to '_saved_users' directory."
		);
		echo json_encode($success);
	}
} else {
	$success = array(
		'success' => FALSE,
		'msg'=> "Not a valid filename."
	);

	echo json_encode($success);
}
//die('no post values');
?>
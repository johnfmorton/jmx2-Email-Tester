<?php

function isValidEmail($email){
    return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email);
}

if(isset($_POST['theemail'])){
	# https://github.com/mustangostang/spyc/
	require_once '../lib/Spyc.php';


	$newEmail = htmlspecialchars($_POST["theemail"]);

	if (isValidEmail($newEmail)) {
		$newName = htmlspecialchars($_POST["thename"]);
		$isChecked = htmlspecialchars($_POST['ischecked']);

		$array['email']  = $newEmail;
		$array['name'] = $newName;
		$array['ischecked'] = $isChecked;

		$yaml = Spyc::YAMLDump($array);

		$userFileName = str_replace(' ', '_', (strtolower ('user-'. $newName . '.yaml')));

		if (file_exists('../_saved_users/'.$userFileName)) {
			//$success = array(
			//	'success' => FALSE,
			//	'msg'=> "User names need to be unique."
			//);
			//echo json_encode($success);
			//exit();

			# Add timestamp to filename to make it unique so old user isn't overwritten
			$userFileName = str_replace(' ', '_', (strtolower ('user-'. $newName . '-'. time(). '.yaml')));
		}

		$userFileHandle = fopen('../_saved_users/'.$userFileName, 'w'); # or die("can't open file");
		if ($userFileHandle) {
			fwrite($userFileHandle, $yaml);
			fclose($userFileHandle);
			$success = array(
				'success' => TRUE,
				'msg'=> "User '$newName' was saved."
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
			'msg'=> "Not a valid email address. Try again."
		);

		echo json_encode($success);
	}
} 

//die('no post values');
?>
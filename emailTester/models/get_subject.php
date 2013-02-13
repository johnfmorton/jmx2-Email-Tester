<?php
# https://github.com/mustangostang/spyc/
require_once '../lib/Spyc.php';

// make an array of the files in the user's directory //

if ($handle = opendir('../_saved_email_files/') && file_exists('../_saved_email_files/saved_subject.yaml')) {
 	
    $theSubjectArray = Spyc::YAMLLoad('../_saved_email_files/saved_subject.yaml');
	
    $theSubjectArray['success'] = TRUE;
    $theSubjectArray['msg'] = 'Retrieved saved subject';

	echo json_encode($theSubjectArray);

} else {
    echo FALSE;
}

// echo '<pre>';
// print_r($allUsers);
// echo '</pre>';


// echo '<pre>YAML Data dumped back:<br/>';
// echo Spyc::YAMLDump($allUsers);
// echo '</pre>';
<?php
# https://github.com/mustangostang/spyc/
require_once '../lib/Spyc.php';

// make an array of the files in the user's directory //

$userFileNames = array();

if ($handle = opendir('../_saved_users/')) {
    //echo "Directory handle: $handle\n";
    //echo "Entries:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
        // does the file end in .yaml? 
        if (preg_match('/.yaml$/', $entry)) {
        	// then same it in our files array
        	array_push($userFileNames, $entry);
        }
    }
    // close the connection to the handle getting the file names
    closedir($handle);
}

$allUsers = array();

$cnt = count($userFileNames);
for ($i = 0; $i < $cnt; $i++){
	array_push($allUsers, Spyc::YAMLLoad('../_saved_users/'. $userFileNames[$i]));
    $allUsers[$i]['filename'] = $userFileNames[$i];
}

//$array = Spyc::YAMLLoad('../saved_users/user-john.yaml');

echo json_encode($allUsers);
exit();
// echo '<pre>';
// print_r($allUsers);
// echo '</pre>';


// echo '<pre>YAML Data dumped back:<br/>';
// echo Spyc::YAMLDump($allUsers);
// echo '</pre>';
<?php
require_once '../lib/CssSelector.php';
require_once '../lib/Exception/ParseException.php';
require_once '../lib/InlineStyle.php';
require_once '../lib/Token.php';
require_once '../lib/Tokenizer.php';
require_once '../lib/TokenStream.php';
require_once '../lib/XPathExpr.php';
require_once '../lib/XPathExprOr.php';

require_once '../lib/Node/NodeInterface.php';
require_once '../lib/Node/AttribNode.php';
require_once '../lib/Node/ClassNode.php';
require_once '../lib/Node/CombinedSelectorNode.php';
require_once '../lib/Node/ElementNode.php';
require_once '../lib/Node/FunctionNode.php';
require_once '../lib/Node/HashNode.php';

require_once '../lib/Node/OrNode.php';
require_once '../lib/Node/PseudoNode.php';

use \InlineStyle\InlineStyle;

if(isset($_POST['orgfilename']) && isset($_POST['inlinefilename']) && isset($_POST['thecontent'])){
		
	$orginalFilename = htmlspecialchars($_POST["orgfilename"]);
	$inlineFilename = htmlspecialchars($_POST["inlinefilename"]);
	$theContent = urldecode($_POST["thecontent"]);

	# The inlining tool decodes HTML entities. We want to preserve them though.
	# We'll convert them and then unconvert them when we're done.
	# See: http://lars.st0ne.at/blog/prevent+decoding+of+html+entities+in+textareas

	$convertedContent = preg_replace('/&(\w+;)/', '&amp;$1', $theContent );

	// Make the inline version of the HTML
	$inlinedContent = new InlineStyle($convertedContent);
	$stylesheets = $inlinedContent->extractStylesheets();
	$inlinedContent->applyStylesheet($stylesheets);
	$convertedContentInlined = $inlinedContent->getHTML();
	$unconvertedContentInlined = preg_replace('/&amp;(\w+;)/', '&$1', $convertedContentInlined );

	$fileHandle = fopen('../_saved_email_files/'.$orginalFilename, 'w'); # or die("can't open file");
	if ($fileHandle) {
		fwrite($fileHandle, $theContent);
		fclose($fileHandle);

		$fileHandle2 = fopen('../_saved_email_files/'.$inlineFilename, 'w'); 
		if ($fileHandle2) {
			fwrite($fileHandle2, $unconvertedContentInlined);
			fclose($fileHandle2);
		 	$success = array(
		 		'success1' => TRUE,
		 		'msg1' => $orginalFilename,
		 		'success2' => TRUE,
		 		'msg2' => $inlineFilename
		 	);
		}  else {
			$success = array(
				'success1' => TRUE,
				'msg1'=> $orginalFilename,
				'success2' => FALSE,
				'msg2'=> "ERROR: Unable to save inline version."
			);
		}

		echo json_encode($success);
		
	} else {
		//echo "Couldn't write file.";
		$error = array(
			'success1' => FALSE,
			'msg1'=> "Couldn't write the file to file system. Check the permissions on the '_saved_email_files' directory. Try 777."
		);
		echo json_encode($error);
	}
} else {
	$error = array(
			'success1' => FALSE,
			'msg1'=> "Didn't receive all needed info for file creation."
		);
	echo json_encode($error);
}

//die('no post values');
?>
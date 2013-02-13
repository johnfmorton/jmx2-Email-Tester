<?php 
# For YAML
require_once 'lib/Spyc.php';

# Are the needed directories writable? 
# See the beginning of the JS to see how these 
# access variables are used.

$savedEmailAccess = '0';
$savedUsersAccess = '0';

if (is_writable('_saved_email_files/')) {
	$savedEmailAccess = TRUE;
}

if (is_writable('_saved_users/')) {
	$savedUsersAccess = TRUE;
}

# Get the latest email if it exists

function getLatestSavedEmail() {
	$path = "_saved_email_files"; 

	$latest_ctime = 0;
	$latest_filename = '';    

	$d = dir($path);
	while (false !== ($filename = $d->read())) {
	  $filepath = "{$path}/{$filename}";
	  // load the latest file, but exclude the "saved_subject.yaml", 
	  // any file that ends in "inline.html" and invisible files, like .DS_STORE
	  if (is_file($filepath) && filectime($filepath) > $latest_ctime  && $filename != 'saved_subject.yaml' && !preg_match('/inline.html$/', $filename) && !preg_match('/^\./', $filename)) {
		$latest_ctime = filectime($filepath);
		$latest_filename = $filename;
	  }
	}

	# 	return $latest_filename;
	if ( $latest_filename !='') {
		# The browser will decode HTML enties when displaying in a textarea.
		# Need to prevent that to preserve the HTML you're writing.
		# See: http://lars.st0ne.at/blog/prevent+decoding+of+html+entities+in+textareas
		$raw_html=file_get_contents("{$path}/{$latest_filename}");
		$to_textarea = preg_replace('/&(\w+;)/', '&amp;$1', $raw_html );
		if ($to_textarea === false) 
		{ 
			return '';
		} else { 
			return $to_textarea; 
		} 
	} else { 
		return ''; 
	}
}

$latest_email = getLatestSavedEmail();

?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<link rel="icon" href="jmx2-email-favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="jmx2-email-favicon.ico" type="image/x-icon"> 
<title>JMX2 Email Tester</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/screen.css" media='screen'>

<!-- http://fabien-d.github.com/alertify.js/ -->
<link rel="stylesheet" href="css/alertify.core.css" />
<link rel="stylesheet" href="css/alertify.default.css" />

</head>
<body>
<div class="navbar navbar-inverse">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="brand" href="#">JMX2 Email Tester</a>
			</div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<h2>Who should receive this email?</h2>
				<ul class="nav nav-tabs" id="userManagement">
					<li class="active"><a href="#recipients">Pick Recipients</a></li>
					<li><a href="#addemail" id='addEmailTrigger'>Add Users</a></li>
					<li><a href="#deleteusers">Delete Users</a></li>
				</ul>
				<div class="tab-content" id="userManagementContent">
					<div class="tab-pane active" id="recipients">
						<p><em>Loading saved email addresses.</em></p>
					</div>
					
					<div class="tab-pane" id="addemail">
						<label class="control-label" for="inputIcon">Add a new recipient</label>
						<div class="input-prepend"><span class="add-on"><i class="icon-envelope"></i></span></i><input class="6" type="email" required placeholder='Email address' id='newEmail'></div>
						<div class="input-prepend"><span class="add-on"><i class="icon-user"></i></span><input class="6" type="text" placeholder='Name' id='newName'></i></div>
						<button class="btn btn-primary" type="button" id='addUserBt'><span class="add-on"><i class="icon-plus icon-white"></i></span> Add new recipient</button>
					</div>
					<div class="tab-pane" id="deleteusers">
						<p><em>Loading saved email addresses.</em></p>
					</div>
				
				</div><!-- .tab-content -->
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<hr>
				<h2>Your Subject Line</h2>
				<p>A timestamp will be appended to your subject line. This is to help you associate the correct HTML file in the <strong>&ldquo;_saved_email_files&rdquo;</strong> directory with the sent email.</p>
				<input type="text" id="theSubject" placeholder='Test Email (default)'><button class="btn" type="button" id='saveSubject'>Update Subject</button>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<hr>
				<h2>Your Raw HTML</h2>
				<p>Paste in the HTML for your HTML email here. Styles will be inlined for you, but images need absolute links (i.e. images need to be on a publicly accessible server).</p>
				<textarea name="htmlForEmail" id="htmlForEmail" ><?php echo $latest_email; ?></textarea>
				<p>Saving creates 2 new files in the <strong>&ldquo;_saved_email_files&rdquo;</strong> directory. The 1st file is the content of the textarea above with <em>&ldquo;-org&rdquo;</em> in the file name. The 2nd version is the content above but has its style tag converted into inline styles with <em>&ldquo;-inline&rdquo;</em> in the file name. Only the <em>&ldquo;-inline&rdquo;</em> version is sent to recipients.</p>
				<div id='actionBar'><button class="btn btn-large btn" type="button" id='saveEmailBt'>Save</button> <button class="btn btn-large btn" type="button" id='sendEmailPreviewBt'>Save &amp; Preview</button> <button class="btn btn-large btn-primary" type="button" id='saveSendEmailBt'>Save &amp; Send</button></div>
			</div>
		</div>

	</div> <!-- /container -->
	 <div id="footer">
        <p class="muted credit">By John Morton. Use in good health.</p>
    </div>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/alertify.min.js"></script>

<script>
	if (typeof console == "undefined") {
		window.console = {
			log: function () {}
		};
	}

	// IE will cache AJAX requests making thing difficult. 
	$.ajaxSetup({ cache: false });

	var goodNoticeTime  = 7500;
	var errorNoticeTime = 8500;
	$(function () {
		var emailaccess = <?php echo $savedEmailAccess; ?>;
		var useraccess = <?php echo $savedUsersAccess; ?>;

		if ( !!emailaccess && !!useraccess ) {
			console.log('I can access all needed folders.');
		} else {
			console.log ('Access issues.');
			if (emailaccess == 1 && useraccess == 0) {
				alertify.alert("<div class='errorheadline'>ERROR: Can't write to directory</div>Can&rsquo;t write to <em>_saved_users</em> directory. Please check permissions.");
			}
			if (emailaccess == 0 && useraccess == 1) {
				alertify.alert("<div class='errorheadline'>ERROR: Can't write to directory</div>Can&rsquo;t write to <em>_saved_email_files</em> directory. Please check permissions.");	
			}
			if (emailaccess == 0 && useraccess == 0) {
				alertify.alert("<div class='errorheadline'>ERROR: Can't write to directories</div>Can&rsquo;t write to <em>_saved_users</em> directory and <em>_saved_email_files</em>. Please check permissions.");
			}
		}

		$('#userManagement a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		if ( <?php echo $latest_email !=='' ? 1 : 0; ?> ) {
			alertify.log( "Previous email loaded.", "success", goodNoticeTime);
		} else {
			alertify.log( "No previous email found.", "notice", goodNoticeTime);
		}

		////////////////////////////////////////
		// GET the current list of recipients //
		//////////////////////////////////////// 

		function getAllRecipientsPickTab(showAlert) {
			showAlert = typeof showAlert !== 'undefined' ? showAlert : false;
			console.log("Getting Recipients for recipients tab");
			$.ajax({
				dataType: "json",
				url: "models/get_users.php",
				success: function(data) {
					if(data.length > 0){ 
						$("#recipients").html('');
						
						$.each(data, function(i, val) {
							var checked = val.ischecked ? "checked" : '';
							$("#recipients").append("<div class='recipient' data-filename='"+val.filename+"'> <input type='checkbox' "+ checked +" class='check'><span><span class='useremail'>"+val.email+"</span> | <span class='username'>"+ val.name +"</span></span></div>");
						});
						if (showAlert === true) {
							if (data.length === 1){
								alertify.log( "One user found.", "success", goodNoticeTime);
							} else {
								alertify.log( data.length + " users found.", "success", goodNoticeTime);
							}
						}
					} else {
						$("#recipients").html("<p>No users were found in the '_saved_users' directory. Take care of that in the 'Add Users' tab.</p>");
						$('#addEmailTrigger').trigger('click');
						if (showAlert === true) {
							alertify.log("No users found.", "notice", goodNoticeTime);
						}
					}
				}
			});
		}

		getAllRecipientsPickTab(true);

		function getAllRecipientDeleteTab(showAlert) {
			showAlert = typeof showAlert !== 'undefined' ? showAlert : false;
			console.log("Getting Recipients for delete tab");
			$.ajax({
				dataType: "json",
				url: "models/get_users.php",
				success: function(data) {
					$("#deleteusers").html('');
					$.each(data, function(i, val) {
						$("#deleteusers").append("<div class='recipient' data-filename='"+val.filename+"'> <i class='icon-trash'></i><span><span class='useremail'>"+val.email+"</span> | <span class='username'>"+ val.name +"</span></span></div>");
					})
				}

			});
		}

		getAllRecipientDeleteTab();

		function getDefaultSubject(showAlert) {
			showAlert = typeof showAlert !== 'undefined' ? showAlert : false;
			console.log("Getting Subject");
			$.ajax({
				dataType: "json",
				url: "models/get_subject.php",
				success: function(e) {
					if (e.subject) {
						$("#theSubject").val(e.subject);
						if (showAlert === true) {
							alertify.log(e.msg, "success", goodNoticeTime);
						}
					}	
				}

			});

		}

		getDefaultSubject(true);

		//////////////////////////////////////
		// AJAX for the "Add user" function //
		//////////////////////////////////////
		
		$('#addUserBt').click(function(e){
			$("#addUserBt").addClass('disabled');
			var newEmail = $("#newEmail").val();
			var newName = $("#newName").val();
			console.log("Adding: " + newName + ' at ' + newEmail);
			var dataToSend = {};
			dataToSend.thename = newName;
			dataToSend.theemail = newEmail;
			dataToSend.ischecked = 1;

			// send to adduser model via ajax
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "models/save_user.php",
				//data: { thename: newName, theemail: newEmail },
				data: dataToSend,
				crossDomain: false,

				success: function(e) {
					$("#addUserBt").removeClass('disabled');
					//console.log(e);
					if (e.success) {
							$("#newEmail").val('');
							$("#newName").val('');
							alertify.log(e.msg, "success", goodNoticeTime);
						} else {
							alertify.log(e.msg, "error", errorNoticeTime);
					}
					getAllRecipientDeleteTab();
					getAllRecipientsPickTab();
				}
			});
			e.preventDefault();
		}); // end of addUserBt click function

		/////////////////////////////////////////
		// AJAX for the "Delete user" function //
		/////////////////////////////////////////

		$('#deleteusers').delegate(".recipient", "click", (function(e) {
			var userToDelete = $(this).find('.username').text();
			//console.log("Preparing to delete user: " + userToDelete);
			console.log("Preparing to delete user: " + $(this).attr('data-filename'));
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "models/delete_user.php",
				data: {filename: $(this).attr('data-filename')},
				crossDomain: false,
				success: function(e) {
					console.log(e);
					alertify.log( userToDelete + " was deleted.", "success", goodNoticeTime);
					getAllRecipientDeleteTab();
					getAllRecipientsPickTab();
				}
			})
		})); // end of deleteusers button click

		//////////////////////////////////////////
		// AJAX for the "Save subject" function //
		//////////////////////////////////////////

		$('#saveSubject').click(function(e){
			console.log('Trying to save subject.');
			$("#saveSubject").addClass('disabled');
			var theNewSubject = $("#theSubject").val();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "models/save_subject.php",
				data: {thesubject: theNewSubject},
				crossDomain: false,
				success: function(e) {
					console.log(e);
					alertify.log( e.msg, "success", goodNoticeTime);
					$("#saveSubject").removeClass('disabled');
				}
			})
		}); // end of saveSubject button click

		///////////////////////////////////////////////
		// AJAX for the converting and sending email //
		///////////////////////////////////////////////
		
		$("#actionBar").delegate("button", "click", function(e){
			e.preventDefault();
			console.log("clicked: " + $(this).attr("id"));
			var savingEmailCallback = new $.Deferred();
			// Math.floor(new Date().getTime()/ 1000) is like a UNIX time stamp
			var timestamp = Math.floor(new Date().getTime()/ 1000);

			switch($(this).attr("id")){
				case 'saveEmailBt':
					$("#saveEmailBt").addClass('disabled');
					savingEmailCallback.then(
						function(){ 
							console.log("Saving Email succeeded");
							$("#saveEmailBt").removeClass('disabled');
						},
						function(){ 
							console.log("Saving Email failed!");
							$("#saveEmailBt").removeClass('disabled');
						}
					);
				break;
				case 'sendEmailPreviewBt':
					$("#sendEmailPreviewBt").addClass('disabled');
					var win = window.open('', 'preview');
					win.open = function(url) { 
						win.location = url;
						win.focus();
					}
					savingEmailCallback.then(
						function(filename){ 
							console.log("Saving Email succeeded… Show the preview."); 
							win.open('_saved_email_files/'+filename);
							$("#sendEmailPreviewBt").removeClass('disabled');
						},
						function(){ 
							console.log("Saving Email failed & no preview.");
							win.close();
							$("#sendEmailPreviewBt").removeClass('disabled');				 
						}
					);
				break;
				case 'saveSendEmailBt':
					$("#saveSendEmailBt").addClass('disabled');
					savingEmailCallback.then(
						function(filename){ 
							console.log("Saving Email succeeded. Now send.");
							sendEmail(filename, timestamp);
						},
						function(){ 
							console.log("Saving Email failed. Nothing to send.");
							$("#saveSendEmailBt").removeClass('disabled');
						}
					);
				break;
			}
			
			saveEmail(savingEmailCallback, timestamp);
		});

		$('#recipients').delegate('input:checkbox.check', 'change', function(e){
			var ischecked = $(this).is(':checked') ? 1 : 0;
			var filename = $(this).parent().attr('data-filename');
			var useremail = $(this).parent().find(".useremail").text();
			var username = $(this).parent().find(".username").text();
			
			var dataToSend = {};
			dataToSend.ischecked = ischecked;
			dataToSend.thefilename = filename;
			dataToSend.thename = username;
			dataToSend.theemail = useremail;
			console.log(dataToSend);
			// send to update_user model via ajax
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "models/update_user.php",
				data: dataToSend,
				crossDomain: false,

				success: function(e) {
					console.log(e);
					if (e.success) {
							//alertify.log(e.msg, "success", goodNoticeTime);
						} else {
							alertify.log(e.msg, "error", errorNoticeTime);
					}
				}
			});
			e.preventDefault();

		});

		function saveEmail(delayedNotification, timestamp) {
			var theContent = $("#htmlForEmail").val();
			if (theContent !== '') {
				delayedNotification = typeof delayedNotification !== 'undefined' ? delayedNotification : false;
				var theSubject = $("#theSubject").val();
				var theContent = $("#htmlForEmail").val();
				if (theSubject == ''){
					theSubject = "Test Email";
				}
				var theFileName = convertToFileName(theSubject);
				
				var orgFileName = theFileName + '-' + timestamp + '-org.html';
				var inlineFileName = theFileName + '-' + timestamp + '-inline.html';
				console.log("Save attempt: " + orgFileName +' (' + timeConverter(timestamp)+")");
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "models/save_email.php",
					data: {orgfilename: orgFileName, inlinefilename: inlineFileName, thecontent : encodeURIComponent(theContent)},
					crossDomain: false,
					success: function(e) {
						console.log(e);
						if (e.success1) {
							alertify.log( 'Saved: '+ e.msg1, "success", goodNoticeTime);
							if(e.success2) {
								alertify.log( 'Saved: '+e.msg2, "success", goodNoticeTime);
								console.log('trying to return TRUE message');
								// passing in the filename of the inline version to open in preview window
								delayedNotification.resolve(e.msg2);
								return true;
							} else {
								alertify.log( e.msg2, "error", errorNoticeTime);
								delayedNotification.reject();
								return false;
							}
						} else {
							// if success1 was in error, there will be no success2, so only show success1 error message
							alertify.log( e.msg1, "error", errorNoticeTime);
							delayedNotification.reject();
							return false;
						}
					}
				});
			} else {
				alertify.alert("<div class='errorheadline'>ERROR: No content to send.</div>Can&rsquo;t send blank document. Please add some HTML first.")
			}
		}

		function sendEmail(filename, timestamp) {
			var recipientsObj = [];
			$('#recipients .recipient .check:checked').each(function(index, value){
				var recipient = {};
				recipient['username'] = $(this).parent().find('.username').text();
				recipient['useremail'] = $(this).parent().find('.useremail').text();
				recipientsObj.push(recipient);
			});
			// Are there any recipients? If so, we can send, but if not, alert of the error
			if (recipientsObj.length > 0){

				var dataToSend = {};
				dataToSend.theRecipients = JSON.stringify(recipientsObj);
				dataToSend.theFileName = filename;
				dataToSend.theSubject = $('#theSubject').val();
				if (dataToSend.theSubject == '') {
					dataToSend.theSubject = "Test Email " + timestamp
				} else {
					dataToSend.theSubject = dataToSend.theSubject + " " + timestamp
				}
			
				$.ajax({
					type: "POST",
					dataType: "json",
					url: "models/send_email.php",
					data: dataToSend,
					crossDomain: false,
					success: function(e) {
						// report back that send was success
						console.log(e);
						if (e.success === 1) {
						alertify.alert("<div class='successheadline'>Email sent successfully.</div><div class='successBody'>The email “"+dataToSend['theSubject']+"” was sent. The file <em><strong>" + dataToSend.theFileName + "</strong></em> contains the HTML that was sent. It can be found in the <em>_saved_email_files</em> directory.</div>");
						$("#saveSendEmailBt").removeClass('disabled');
						} else {
							alertify.alert("<div class='errorheadline'>Email was not sent.</div><div class='errorBody'>"+e.msg+"</div>");
							$("#saveSendEmailBt").removeClass('disabled');
						}
					}
					,
					error: function(e) {
						console.log(e);
						alertify.alert("<div class='errorheadline'>Email was not sent.</div><div class='errorBody'>"+e.statusText+"</div>");
						$("#saveSendEmailBt").removeClass('disabled');
					}
				});
			} else {
				alertify.alert("<div class='errorheadline'>ERROR: No recipients.</div><div class='errorBody'>There are no recipients to send the message to. The save operation was successful though.</div>");
				$("#saveSendEmailBt").removeClass('disabled');
			}
		};
		
		function convertToFileName(orgSubject){
			//replace non whitespace characters with a dash
			return orgSubject.toLowerCase().replace(/[^\w]/gi, '-');
		}

		function timeConverter(UNIX_timestamp){
			var a      = new Date(UNIX_timestamp*1000);
			var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
			var year   = a.getFullYear();
			var month  = months[a.getMonth()];
			var date   = a.getDate();
			var hour   = a.getHours();
			var min    = a.getMinutes();
			var sec    = a.getSeconds();
			var time   = month+" "+date+', '+year+' '+hour+':'+min+':'+sec ;
			return time;
		}

	});
</script>

</body>
</html>
# JMX2 Email Tester

The JMX2 Email Tester is a "web app" attempting to make the process of repeatedly tweaking and sending test HTML emails a bit easier. 

## How it's used.

The Email Tester is not a full featured text editor so I start by writing my HTML in a real text editor, like Sublime Text, then paste it into the Email Tester tweak and send the email repeatedly until any quirks are worked out.

## What it does.

Once you have HTML pasted into the code panel in the Email Tester, every time you click the "Save & Send" button, the Email Tester saves 2 copies of your work. 

The first file contains the HTML in the exact format you see in the Email Tester code window. This file has the phrase "_org" in the file name.

The second file will have the styles in your style block "inlined" for each element in your document. This file has the phrase "_inline" in the file name.

Each file will be also use a timestamp in the file name. This same timestamp is appended to the email that is sent to your selected recipients as well. This is done to help you identify the HTML associated with various test email have sent during your debugging process. 

There are 2 other options in the interface that will save your work. The first is simply called "Save"; it will save the 2 versions of your email as described above but not attempt to send any email. The second option is "Save & Preview" which will save 2 versions of your email as described above and then open the inlined version of the HTML in a new browser window. Since the browser is a much better way to view HTML documents, testing HTML emails in email programs is the best way to debug them. 

## Other features

The Email Tester will use your own SMTP information for sending emails. I use it with Gmail but any SMTP server should work. Set your SMTP settings in the "_config" directory in the "settings.php" file. If you use Google's 2 factor authentication, Email Tester will need it's own application specific password.

All saved emails are saved in the "_saved_email_file" directory. If you reload the browser window, Email Tester will pull the most recent non-inlined version of the email you're working on from this directory. 

Email Tester will also keep contacts that you can select and unselect with checkbox. It remembers the users and whether they are selected by saving YAML files to your file system. 

## Installation

You need PHP 5.3 or higher. (Older versions may work, but it's been used on PHP 5.3. and PHP 5.4.)

The app is ideally used in a local server environment like MAMP or WAMP. If you were to use it on a public server, you should put a password on the directory to keep out unwanted visitors. 

To install it, copy the "emailTester" directory to your web server. The "_saved_users" and "_saved_email_files" directories need to be writable. Depending on your server, this might be 777 permissions. Email Tester will warn you if they are not writable.

## Projects Email Tester Relies On

JMX2 Email Tester incorporates a variety of tools including:

- Swiftmailer, http://swiftmailer.org/
- SPYC, https://github.com/mustangostang/spyc/jj
- InlineStyle, https://github.com/christiaan/InlineStyle
- CssSelector, https://github.com/symfony/CssSelector
- Email-Blueprints, https://github.com/mailchimp/Email-Blueprints
- Bootstrap, https://github.com/twitter/bootstrap
- Glyphicons, http://glyphicons.com/

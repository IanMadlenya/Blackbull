<?php
session_start();
if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
      $_SESSION['user'] = $_COOKIE['cookname'];
      $_SESSION['pass'] = $_COOKIE['cookpass'];
   }

?>
<!DOCTYPE html>
<html class="no-js">
<head>
	<?php require_once("../metacontent.php"); ?>
	<title>Blackbull Investment Company</title>
	<link rel="stylesheet" href="../stylesheets/site.css" media="screen">
	<link rel="stylesheet" href="../stylesheets/users/changepassword.css" media="screen">
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <script src="../javascripts/lib/modernizr-1.5.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script src="http://cdn.jquerytools.org/1.2.4/tiny/jquery.tools.min.js"></script>
        <script src="../javascripts/lib/jquery.jcryption-1.1.min.js"></script>
        <script src="../javascripts/site.js"></script>
        <script src="../javascripts/users/changepassword.js"></script>
</head>
<body>
<?php require_once("../_partial_header.php"); ?>
<div id="content">
<div id="resetpassword">
<h1>Change password</h1>
<p>Fill the following form to change your account password.</p>
<form id="changepassform" method="post">
<label for="email">E-mail address</label>
<input type="email" id="email" class="longlegend"/>
<br/>
<span id="emailresult">Your e-mail is your <b>USERNAME</b>.</span>
<br/>
<label for="tpassword">Existing password</label>
<input type="password" id="tpassword" class="long"/>
<br/>
<label for="upassword">New password</label>
<input type="password" id="upassword" class="longlegend" title="Password must be less than 16 characters."/>
<br/>
<span id="passresult">&nbsp;</span>
<br/>
<label for="repassword">Retype new password</label>
<input type="password" id="repassword" class="long"/>
<br/>
<input type="submit" value="Change password" id="submitform" class="submitbutton"/>
</form>
</div>
</div>
<?php require_once("../_partial_footer.php"); ?>
</body>
</html>
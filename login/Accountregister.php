<?php
session_start();
	//start the session

	//include helper files
require_once("../private/AccountloginHelpers.php");
require_once("../private/inputHelper.php");
$form_userid;
$form_password;
$form_UserName;
$form_email;
$status;
$myerr;	
	
	//declare variables for this form/page
	
	//check if we've submitted the page, and if so, get the user data from the form
	if(isset($_POST['submit'])){
		$form_userid = $_POST['user'];
		$form_password = $_POST['pw'];
		$form_UserName = $_POST['UserName'];
		$form_email = $_POST['email'];
	
		//validate 
		if(!has_presence($form_userid) || !has_presence($form_password) || !has_presence($form_UserName)){
			$myerr = "Sorry, password and user name cannot be empty  <br/>";
		}
		if (!filter_var($form_email, FILTER_VALIDATE_EMAIL)) {
  			$myerr = " Invalid email format <br/>"; 
		}

		if(!isset($myerr) && !has_length($form_userid, ['min'=>3, 'max' =>30])){
			$myerr = "Sorry, the name must be between 3 to 30 characters long  <br/>";
		}else if(!isset($myerr) && !has_length($form_UserName, ['min'=>3, 'max' =>30])){
			$myerr = "Sorry, name must be between 3 to 30 characters long  <br/>";
		}else if(!isset($myerr) && !has_length($form_password, ['min'=>6])){
			$myerr = "Sorry, password must be at least 6 characters long  <br/>";
		}
		
		//create account
		if(!isset($myerr) && createAccount($form_userid, $form_password, $form_UserName, $form_email)){
			$status = 1;
			$_SESSION['UserData']['userid'] = $form_userid;
			header("location:Accountlogin.php");
			exit;
		}else{
			$status = 0;
		}
		
	}
?>
<!DOCTYPE html>
<!-- CSCI311 | Names: Tiffany & Ning | Final Project -->
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
   <meta charset=UTF-8" />
   <link rel="stylesheet" type="text/css" href="./registerStyles.css">
</head>

<body>
<!-- Viu Logo Header -->	
	<header>
		<div class="viuLogo"><a><img id="viuLogo" src="../media/viu-logo.png"/></a></div>
	    <h1 class="head"><b>VIU Cafeteria Express Registration</b></h1>
  </header>
<div id="main">
	<div class="signIn">
		<h1 id="regTitle" >Register here</h1>
		<div id="signInRow">
<form action="Accountregister.php" method="POST">
	<label for="user"><span class="asterisk">*</span> User ID: </label>
	<input class="registerInp" type="text" id="user" name="user" required="required" placeholder="Please enter your student or instructor ID"\></br>
	<label for="pw"><span class="asterisk">*</span> Password: </label>
	<input class="registerInp" type="password" id="pw" name="pw" required="required" placeholder="Please a password 6 characters in length or longer"\></br>
	<label for="UserName"><span class="asterisk">*</span> Name: </label>
	<input class="registerInp" type="text" id="UserName" name="UserName" required="required" placeholder="Please enter your first and last name"\></br>
	<label for="email"><span class="asterisk">*</span> Email: </label>
	<input class="registerInp" type="text" id="email" name="email" required="required" placeholder="Please enter your email" \></br>
	<input type="submit" id="submit" name="submit" value="register"\></br>
</form>
	 	</div>    
    </div>
</div>
<?php 
	//if register fails, show an error
	if(isset($myerr)){
		echo "error: ".$myerr."</br>";
	}else if($status === 0){
		echo "<p>Sorry, that ID is already taken</p>";
	} 
?>
</body>
</html>

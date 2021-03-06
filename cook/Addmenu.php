<?php
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
    if(!isset($_SESSION['CookData']['userid'])){
		header('Location: ../login/Cooklogin.php');	
    }
	require_once("../private/dbinfo.inc");
	$err;
	$success;
    if (($_SERVER['REQUEST_METHOD']=="POST")){
    	$name = $_POST['name'];
    	$price = $_POST['price'];
		$description = $_POST['description'];
    	$quantity = $_POST['quantity'];

		$file = $_FILES["image"];
		$file_name = $_FILES['image']['name'];
		$file_size = $_FILES['image']['size'];
		$file_tmp = $_FILES['image']['tmp_name'];
		$file_type = $_FILES['image']['type'];
		$file_ext = strtolower(end(explode('.',$_FILES['image']['name'])));
	  	$extensions = array('gif','png' ,'jpg','jpeg');
	  	
		if(!isset($name)||trim($name) === ""||strlen($name)>=255){
			$err = "Sorry, name cannot be empty!";
		}else if(!isset($price)|| $price <= 0 ||$price >=99999999){
			$err = "Sorry, you must add price!";
		}else if(!isset($description)||trim($description) === ""||strlen($description)>=255){
			$err = "Sorry, description cannot be empty!";
		}else if(!isset($quantity)|| $quantity <= 0){
			$err = "Sorry, you must add quantity!";
		}else if($file_size == 0){
			$err = "Sorry, you must add a photo so others can see it!";
		}else if(in_array($file_ext,$extensions) === false){
			$err = "That photo extension is not allowed, please choose a JPEG or PNG file.";
		}else if($file_size > 2097152){
			$err = 'File size cannot exceed 2 MB';
		}else{
			try{
				$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
				$photoPath = "../MenuPhoto/".$file_name;
	     		move_uploaded_file($file_tmp, $photoPath);
				chmod($photoPath."",0644);
       
				$myQuery = $myHandle->prepare("insert into menuItems(name, price, photoPath, description, quantity)
												values (:name, :price, :photoPath, :description, :quantity)");
			    $myQuery->bindParam(':name',addslashes($name));
			    $myQuery->bindParam(':price',addslashes($price));
			    $myQuery->bindParam(':photoPath',addslashes($photoPath));
			    $myQuery->bindParam(':description',addslashes($description));
			    $myQuery->bindParam(':quantity',addslashes($quantity));

				if($myQuery->execute() !==false){
					$success .= "Your menu is add!" ;
				}else{
					$err .= "Something went wrong";
				}
				$myHandle = null;
			}catch(PDOException $e){
				$err .= "Connection failed \n";
			}
		}
    }
?>
<!DOCTYPE html>
<!-- https://www.w3schools.com/w3css/w3css_input.asp -->
<html>
<head>
<title>ADD ITEM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<script>
		function preview_image(event) {
 			var reader = new FileReader();
 			reader.onload = function() {
  				var output = document.getElementById('preview_img');
  				output.src = reader.result;
 			}
 			reader.readAsDataURL(event.target.files[0]);
		}
	</script>   
</head>

<body>
<div class="w3-card-4">
  <div class="w3-container w3-teal">
    <h2>Add a Menu Item</h2>
  </div>
  <form class="w3-container" action="./Addmenu.php" method="POST" enctype="multipart/form-data">
   
    <label class="w3-text-teal"><b>Item Name:</b></label>
    <input class="w3-input w3-border w3-sand" name="name" type="text"></p>
    
    <label class="w3-text-teal"><b>Price:</b></label>
    <input class="w3-input w3-border w3-sand" name="price" type="number"></p>

    <label class="w3-text-teal"><b>Description:</b></label>
    <input class="w3-input w3-border w3-sand" name="description" type="text"></p>

    <label class="w3-text-teal"><b>Quantity:</b></label>
    <input class="w3-input w3-border w3-sand" name="quantity" type="number"></p>

	<div class="inputRow">
	    <label class="w3-text-teal" for="image_uploads"><b>Photo: </b></label>
		<div class="w3-input w3-border w3-sand">
			<input class="uploadInput" type="file" id="image_uploads" name="image" accept="image/*" onchange="preview_image(event)"  />
    		<img id="preview_img" src="" /><br/>
		</div>
	</div>
	<div class="w3-row">
    	<div class="w3-col m6" style="text-align: center;"><input class="w3-btn w3-teal" style="float:right; margin-bottom:10px;" type="submit" name="go" value="Add Item!" /></div>	
		<div class="w3-col m6" style="text-align: center;"><input class="w3-btn w3-teal" style="float:right; margin-bottom:10px;" type="reset" name="reset" value="Clear" onclick="verifyClear()"/></div>
	</div>
  </form>
    <?php if(isset($err)) echo "<h3>".$err."</h3>";?>
    <?php if(isset($success)) echo "<h3>".$success."</h3>";?>
</div>

</body>
</html> 

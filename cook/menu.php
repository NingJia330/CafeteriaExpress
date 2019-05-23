<?php
	require_once("../private/dbinfo.inc");
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
    if(!isset($_SESSION['CookData']['userid'])){
		header('Location: ../login/Cooklogin.php');	
    }
?>
<!DOCTYPE html>
<html>
<title>MENU</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<body class="w3-container w3-auto" style="background-color:sand;">

<h2 style="text-align: center" >Daily Menu</h2>
<p style="text-align: center">Below will be the items that your customers can order for the day:</p>

  <a class="w3-button w3-teal" href="./index.php" style="float:left;">Current Orders</a>
<div style="margin-left:70%;" class="w3-container">
<div class="w3-bar">
  <a class="w3-button w3-teal" href="./Addmenu.php">+ Add New Item</a>
  <button class="w3-button w3-black">Log Out</button>
 </div>
</div>

<?php
	session_start();
	ob_start();
	try
	{
		$handle = new PDO("mysql:host=$servername;dbname=$database;", $username, $password);
		if($handle)
		{
			$selectMenuQuery = "SELECT * FROM menuItems";
			$queryResult = $handle->query($selectMenuQuery);
		}
		
		foreach($queryResult as $row)
		{
			$nameValue = $row['name'];
			$priceValue = $row['price'];
			$descriptionValue = $row['description'];
			$quantityValue = $row['quantity'];
			
			echo "<div class='w3-panel w3-pale-yellow w3-border'>";
			echo "<h3>Menu Item</h3>";
			echo "<p><strong>item: </strong>$nameValue</p>";
			echo "<p><strong>price: </strong>$priceValue</p>";
			echo "<p><strong>description: </strong>$descriptionValue</p>";
			echo "<p><strong>quantity: </strong>$quantityValue</p>";
			echo "</div>";
		}
	}catch(PDOException $e)
	{
		echo "Error " .$e->getMessage()." <br/>";
	}			
?>

</body>
</html>

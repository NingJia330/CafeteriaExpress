<?php
	require_once("../private/dbinfo.inc");
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
    if(!isset($_SESSION['UserData']['userid'])){
		header('Location: ../login/Accountlogin.php');	
    }
	$userid= $_SESSION['UserData']['userid'];
?>

<!DOCTYPE html>
<!-- Team Team, Customer Home Page: customer can add orders to their cart -->
<html>
<head>
<title>Order</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
.viuLogo{
	display: table-cell;
}
#viuLogo{
	height: 90px;
}
.head {
	display: table-cell;
    color: #00264d;
	width: 90%;
	padding: 10px 22px;
}
</style>
</head>

<body class="w3-container w3-auto" style="background-color:sand;">
<header>
	<div class="viuLogo"><a href="./index.php"><img id="viuLogo" src="../media/viu-logo.png" alt="viu Logo"/></a></div>
	<h1 class="head"><b>Order</b></h1>
</header>
	<div style="margin-left:70%;" class="w3-container">
	<div class="w3-bar">
<!--	  <button class="w3-button w3-teal" onclick="http://wwwstu.csci.viu.ca/~csci375b/project/cook/editmenu.php">Your Orders</a></button> --> 
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

				$getAccountIdQuery = "SELECT Account_ID from Account WHERE userid = '$userid'";
				$getAccountIdResult = $handle->query($getAccountIdQuery);
				foreach($getAccountIdResult as $row)
				{
						$getAccountId = $row['Account_ID'];
				}
				$getOrderIdQuery = "SELECT * FROM orders WHERE Account_ID = $getAccountId ORDER BY order_ID DESC LIMIT 1";
				//$getOrderIdQuery = "SELECT * FROM orders ORDER BY order_ID DESC LIMIT 1";
				$getOrderIdResult = $handle->query($getOrderIdQuery);
			}
			foreach($getOrderIdResult as $row)
			{
				$getOrderId = $row['order_ID'];
				$getOrderPrice = $row['price'];
				$getStatus = $row['status'];
			}
			
			$getOrderItemsQuery = "SELECT * from orderItems WHERE order_ID = '$getOrderId'";
			$getOrderItemsResult = $handle->query($getOrderItemsQuery);
			echo "<form method='POST' action=''>";
			echo "<div class='w3-panel w3-white w3-border' style='width: 500px;'>";
			foreach($getOrderItemsResult as $row)
			{
				$itemIdValue = $row['menu_ID'];
				$itemQuantityValue = $row['quantity'];
				$getItemNameQuery = "SELECT * from menuItems WHERE menu_ID = '$itemIdValue'";
				$getItemNameResult = $handle->query($getItemNameQuery);
				foreach($getItemNameResult as $name)
				{
					$getItemName = $name['name'];
					$getItemPic = $name['photoPath'];
				}
				
				$getItemPriceQuery = "SELECT price from menuItems WHERE menu_ID = '$itemIdValue'";
				$getItemPriceResult = $handle->query($getItemPriceQuery);
				foreach($getItemPriceResult as $price)
				{
					$getItemPrice = $price['price'];
				}
				$getItemPrice *= $itemQuantityValue;
				
				echo "<div style='display: block; border-bottom: 1px solid black;'>";
				echo "<div style='display: inline-block;'>";
				echo "<p><strong>Item: </strong>$getItemName</p>";
				echo "<p><strong>Quantity: </strong>$itemQuantityValue</p>";
				echo "<p><strong>Price: </strong>$getItemPrice</p>";
				echo "</div>";
				echo "<div style='float: right; display: inline;'>";
				echo "<img src='$getItemPic' width='100' height='75' alt='pic' style='margin-right: 20px; margin-top: 30px;'>";
				echo "</div>";
				echo "</div>";
			}
			echo "</div>";
			echo "<p><strong>Status: </strong>$getStatus</p>";
			echo "<input type='submit' name='Pay' class='w3-button w3-teal w3-xlarge' value='Pay' />";
			echo "</form>";
			echo "<p><strong>Total Price: </strong>$getOrderPrice</p>";
			
			if(isset($_POST['Pay']))
			{
				$updatePaymentQuery = "UPDATE orders SET payment = '1' WHERE order_ID = '$getOrderId'";
				$handle->exec($updatePaymentQuery);
				$updateStatusQuery = "UPDATE orders SET status = 'in progress' WHERE order_ID = '$getOrderId'";
				$handle->exec($updateStatusQuery);
				ob_end_clean();
				header('Location: index.php');	
				exit();	
			}	
		}catch(PDOException $e)
		{
			echo "Error " .$e->getMessage()." <br/>";
		}			
	?>
	
</body>
</html>

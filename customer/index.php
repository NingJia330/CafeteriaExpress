<?php
	require_once("../private/dbinfo.inc");
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
    if(!isset($_SESSION['UserData']['userid'])){
		header('Location: ../login/Accountlogin.php');	
    }
    $userid = $_SESSION['UserData']['userid'];
?>

<!DOCTYPE html>
<!-- Team Team, Customer Home Page: customer can add orders to their cart -->
<html>
<head>
<title>VIU CAFETERIA</title>
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
    color: #00395d;
	width: 90%;
	padding: 10px 22px;
}
/* Style the top navigation bar */
.navbar {
  display: flex;
}

/* Style the navigation bar links */
.navbar a {
  color: white;
  padding: 14px 20px;
  text-decoration: none;
  text-align: center;
  background-color:#00395d ;
}

/* Change color on hover */
.navbar a:hover {
  background-color: #ddd;
  color: black;
}
</style>
</head>

<body class="w3-container w3-auto" style="background-color:sand;">
<header>
	<div class="viuLogo"><a><img id="viuLogo" src="../media/viu-logo.png" alt="viu Logo"/></a></div>
	<h1 class="head"><b>VIU Menu</b></h1>
	<p style="text-align: center">Select an item to add to your cart.</p>
</header>
	<div style="margin-left:70%;" class="w3-container">
	<div class="w3-bar">
	<div class="navbar">
		<a href="./YourOrders.php">Your Orders</a>
	  	<a href="../login/Accountlogout.php" class="logoutButton">Logout</a>
	</div>
			</ul>	  
	 </div>
	</div>
	
	<script>
		function enableQuantity(counter)
		{
			checkBox = document.getElementsByName("menuItem[]")[counter];
			quantityField = document.getElementsByName(counter)[0];
			if(checkBox.checked == true)
			{
				quantityField.disabled = false;
			}
			else
			{
				quantityField.disabled = true;
				quantityField.value = null;			
			}
		}	
	</script>
	
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
			
			echo "<form method='POST' action=''>";
			$counter = 0;
			foreach($queryResult as $row)
			{
				$nameValue = $row['name'];
				$priceValue = $row['price'];
				$photoValue = $row['photoPath'];
				$descriptionValue = $row['description'];
				$quantityValue = $row['quantity'];
				if($quantityValue == 0)
				{
					$soldOut = 'Sold Out';
				}
				else
				{
					$soldOut = $quantityValue;				
				}
				
				echo "<div class='w3-panel w3-white w3-border'>";
				echo "<div style='float: right;'>";
				echo "<div style='display: block;'>";
				echo "<input class='w3-check w3-teal w3-xlarge' style='float:right; margin-top:12px;' type='checkbox' name='menuItem[]' onchange='enableQuantity($counter)' value='$nameValue' >";		
				echo "<label class='w3-xlarge' style='float:right; margin-top:10px; margin-right: 5px;'>Choose Item</label>";
				echo "</div>";
				echo "<div style='display: block;'>";
				echo "<input type='number' style='float:right; margin-top: 10px;' name='$counter' min='1' max='$quantityValue' disabled/>";
				echo "<label class='w3-large' style='float:right; margin-top: 10px; margin-right: 5px;'>Quantity</label>";
				echo "</div>";
				echo "</div>";
				echo "<div style='float: right; margin-right: 300px; margin-top: 30px;'>";
				echo "<img src='$photoValue' width='200' height='150' alt='pic'>";
				echo "</div>";
				echo "<h3>Menu Item</h3>";
				echo "<p><strong>item:</strong> $nameValue</p>";
				echo "<p><strong>price:</strong> $priceValue</p>";
				echo "<p><strong>description:</strong> $descriptionValue</p>";
				echo "<p><strong>quantity:</strong> $soldOut</p>";
				$counter++;
				echo "</div>";
			}			
			echo "<input type='submit' name='placeOrder' value='Submit' class='w3-button w3-teal w3-xlarge' style='float:right; margin-top:10px; margin-bottom:20px;'>";
			echo "</form>";
	
			if(isset($_POST['placeOrder']))
			{
				$mealQuantity = array();
				$isChecked = false;
				$j = 0;
				for($i = 0; $i < $queryResult->rowCount(); $i++) 
				{
					if(isset($_POST[$i]))
					{
						$isChecked = true;
						$mealQuantity[$j] = htmlspecialchars($_POST[$i]);
						$j++;
					}
				}
				
				if($isChecked == true)
				{
					/*loop to calculate total price and update quantity*/
					$totalPrice;				
					$n = 0;
					foreach($_POST['menuItem'] as $check)
					{
						$updateQuantityQuery = "SELECT quantity from menuItems WHERE name = '$check'";
						$getQuantityResult = $handle->query($updateQuantityQuery);
						foreach($getQuantityResult as $row)
						{
							$getQuantity = $row['quantity'];
						}
						
						$getPriceQuery = "SELECT price from menuItems WHERE name = '$check'";
						$getPriceResult = $handle->query($getPriceQuery);
						foreach($getPriceResult as $row)
						{
							$getPrice = $row['price'];
						}
						
						$newQuantity = $getQuantity - $mealQuantity[$n];
						$updateQuantityQuery = "UPDATE menuItems SET quantity='$newQuantity' WHERE name = '$check'";
						$handle->exec($updateQuantityQuery);
						
						$totalPrice += $mealQuantity[$n] * $getPrice;
						$n++;
					}
				
					/*insert record to orders table */
					$getAccountIdQuery = "SELECT Account_ID from Account WHERE userid = '$userid'";
					$getAccountIdResult = $handle->query($getAccountIdQuery);
					foreach($getAccountIdResult as $row)
					{
							$getAccountId = $row['Account_ID'];
					}
					//might be usefull now() + INTERVAL 20 MINUTE
					$insertToOrdersQuery = "INSERT into orders(price, time, pickup, Cook_ID, Account_ID) VALUES(\"$totalPrice\", now(), now() + INTERVAL 20 MINUTE, 0, \"$getAccountId\")";
					$handle->exec($insertToOrdersQuery);
					
					/*loop to insert order details to orderItems table */
					$n = 0;
					$getOrderIdQuery = "SELECT order_ID FROM orders ORDER BY order_ID DESC LIMIT 1";
					$getOrderIdResult = $handle->query($getOrderIdQuery);
					foreach($getOrderIdResult as $row)
					{
						$getOrderId = $row['order_ID'];
					}
					foreach($_POST['menuItem'] as $check)
					{
						$getMenuItemIdQuery = "SELECT menu_ID FROM menuItems WHERE name = '$check'";
						$getMenuItemIdResult = $handle->query($getMenuItemIdQuery);
						foreach($getMenuItemIdResult as $row)
						{
							$getMenuItemId = $row['menu_ID'];
						}
						$insertToOrderItemsQuery = "INSERT into orderItems(order_ID, menu_ID, quantity) VALUES(\"$getOrderId\", \"$getMenuItemId\", \"$mealQuantity[$n]\")";
						$handle->exec($insertToOrderItemsQuery);
						$n++;
					}
					ob_end_clean();
					header('Location: YourOrders.php');	
					exit();	
				}
			}
		}catch(PDOException $e)
		{
			echo "Error " .$e->getMessage()." <br/>";
		}
	?>
</body>
</html>

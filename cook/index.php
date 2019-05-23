<!-- Description: Order Queue for cooks to view order details and click done when an order is complete -->
<?php
	require_once("../private/dbinfo.inc");
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
    if(!isset($_SESSION['CookData']['userid'])){
		header('Location: ../login/Cooklogin.php');	
    }
	$myHandle;
	$Cook_ID;
	try{
		$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
	}catch(PDOException $e){
		$err .= "Connection failed \n";
	}
	$i = 1;
	if($myHandle){
		$stmt = $myHandle->prepare("SELECT Cook_ID FROM Cook WHERE userid=:u_id");
		$stmt->bindParam(':u_id', $_SESSION['CookData']['userid']);
		$stmt->execute();
		$Cook_ID = $stmt->fetchColumn();

		$myorders = $myHandle->prepare("select order_ID from orders where payment=1 and Cook_ID = 0 order by time asc");
		$myorders->execute();
		$rsltmyorders = $myorders->fetchAll();	
		foreach($rsltmyorders as $row){
			foreach($row as $field=>$value){
				$orderIDs[$i][$field] = $value;
			}
			$i++;
		}
		$numorderIDs = sizeof($orderIDs);
	}
	if (isset($_POST['ordernum'])){
		$cooked = $myHandle->prepare("update orders set status = 'Ready', Cook_ID = :Cook_ID where order_ID = :orderID");		
		$cooked->bindParam(':Cook_ID', $Cook_ID);			
		$cooked->bindParam(':orderID', $_POST['ordernum']);
		$cooked->execute();
		header("Location: index.php");
	}
?>

<!DOCTYPE html>
<html>
<!-- Reference for page structure: https://www.w3schools.com/w3css/tryit.asp?filename=tryw3css_templates_portfolio&stacked=h -->
<!-- Number Icons Sourced from: https://www.flaticon.com/authors/roundicons -->
<title>ORDER QUEUE</title>
<meta charset="UTF-8">
<link rel="stylesheet" href="./css/queueStyles.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: sans-serif}
</style>
<body class="w3-light-grey w3-content" style="width:100%;">

<!-- !PAGE CONTENT! -->
<div class="w3-main" >

  <!-- Header -->
  <header id="header">
    <div class="w3-container">
    <h1 style="text-align:center">Order Queue</h1>
		<a class="w3-button w3-white"  href="./menu.php">Edit Menu</a>
		<a class="w3-button w3-white" href="../login/Cooklogout.php" style="float:right; text-decoration:none;"><i class="fa fa-dollar w3-margin-right">
		</i><strong>Log Out</strong></a>
   		<div class="w3-section w3-bottombar w3-padding-16">
    </div>
  </header>
  
  <!-- First Photo Grid-->
  <div class="w3-row-padding">
	<?php
		for ($n=1; $n<=6 && $n<=$numorderIDs; $n++){
			echo "<div class='w3-third w3-container w3-margin-bottom' id='orderBox'>
			  		<img src='../media/numbers/$n.png' alt='$n' style='max-width:50px;'>
			  		<div>";
			$orderID = $orderIDs[$n]['order_ID'];
			echo "<p><b>Order ID: $orderID </b></p>";
			$myorderItems = $myHandle->prepare("select orderItems.menu_ID, menuItems.name, orderItems.quantity 
				from orderItems left join menuItems on orderItems.menu_ID = menuItems.menu_ID where order_ID = :orderID;");
			$myorderItems->bindParam(':orderID', $orderID);
			$myorderItems->execute();
			$rsltmyorderItems = $myorderItems->fetchAll();
			$i = 1;
			foreach($rsltmyorderItems as $row){
				foreach($row as $field=>$value){
					$orderItems[$i][$field] = $value;
				}
				$i++;
			}
			$numorderItems = sizeof($orderItems);
			for($m=1; $m<=$numorderItems; $m++) {
				echo "<p>Menu ID: {$orderItems[$m]['menu_ID']} &nbsp &nbsp <span class='num'>{$orderItems[$m]['name']} x{$orderItems[$m]['quantity']}</span></p> ";
			}
			echo "  </div>
				</div>";
		}

		echo  "<p class='totalMoney'> Total orders: $numorderIDs </p>
			</div>
		<form action='./index.php' method='POST'>
			<input type='hidden' name='ordernum' value='{$orderIDs[1]['order_ID']}'/>
			<input type='submit' class='button button2' value='DONE' />
		</form>";
	?>
</div> <!-- end of page content-->
<div id="footer" class="w3-black w3-center w3-padding-24"> &copy; Team Team Copyright</div>
</body>

</html>

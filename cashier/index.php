<?php
	require_once("../private/dbinfo.inc");
	session_start();
	$_SESSION['url'] = $_SERVER['REQUEST_URI'];
    if(!isset($_SESSION['CashierData']['userid'])){
		header('Location: ../login/Cashierlogin.php');
    }
	$today = date("Y-m-d");

	if (($_SERVER['REQUEST_METHOD']=="POST")){
		if(isset($_POST['start']) || isset($_POST['end']) || isset($_POST['day'])){
			$start_day = $_POST['start'];
			$end_day = $_POST['end'];
			$day = $_POST['day'];
			
		}
		try{
			$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

			$Rstmt = $myHandle->prepare("select DATE(time) as Day, SUM(price) as Total from orders 
											where payment=1 and DATE(time) <= :start_day or DATE(time) <= :end_day
											group by DATE(time);");
			$Rstmt->bindParam(':start_day', $start_day);
			$Rstmt->bindParam(':end_day', $end_day);			
			$Rstmt->execute();
			$rsltR = $Rstmt->fetchAll();
			$i=1;
			foreach($rsltR as $row){
				foreach($row as $field=>$value){
					$RTransaction[$i][$field] = $value;
				}
				$i++;
			}
			$numRrslt = sizeof($RTransaction);

			$SUMRstmt = $myHandle->prepare("select SUM(price) from orders where payment=1 and DATE(time) <= :start_day or DATE(time) <= :end_day;");
			$SUMRstmt->bindParam(':start_day', $start_day);
			$SUMRstmt->bindParam(':end_day', $end_day);
			$SUMRstmt->execute();
			$SUMRTransaction = $SUMRstmt->fetchColumn();
			$myHandle = null;
		}catch(PDOException $e){
			$err .= "Connection failed \n";
		}

		try{
			$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

			$daystmt = $myHandle->prepare("select price, userid, time from orders natural join Account where payment=1 and DATE(time) = :day;");
			$daystmt->bindParam(':day', $day);				
			$daystmt->execute();
			$rsltday = $daystmt->fetchAll();
			$i=1;
			foreach($rsltday as $row){
				foreach($row as $field=>$value){
					$DailyTransaction[$i][$field] = $value;
				}
				$i++;
			}
			$numrslt = sizeof($DailyTransaction);

			$SUMdaystmt = $myHandle->prepare("select SUM(price) from orders natural join Account where payment=1 and DATE(time) = :day;");
			$SUMdaystmt->bindParam(':day', $day);	
			$SUMdaystmt->execute();
			$SUMDailyTransaction = $SUMdaystmt->fetchColumn();
			$myHandle = null;
		}catch(PDOException $e){
			$err .= "Connection failed \n";
		}

	}

?>

<!DOCTYPE html>

<!-- Name: Tiffany | Team: Team Team | Cashier Interface -->
<!-- https://www.w3schools.com/css/tryit.asp?filename=trycss_mediaqueries_website -->
<html>
<head>
<title>Transactions</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" type="text/css" href="myStyles.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
</head>
<body>
<!-- Header -->
<div class="header">
  <img src="../media/logo.jpg" />
  <h1> VIU Cafeteria Transactions</h1>
  <p>Access <b>digital</b> transactions</p>
</div>
<!-- Navigation Bar -->
<div class="navbar">
  <a href="../login/Cashierlogout.php" class="logoutButton">Logout</a>
</div>

<!-- The flexible grid (content) -->
<div class="row">
  <div class="side">
<form action="./index.php" method="POST">
    <h2>Transactions Report</h2>
		<label for="start">Start date:&nbsp</label>
		<input type="date" id="start" name="start" value="<?php echo $today;?>" min="2018-01-01" max="<?php echo $today;?>"></br>
		<label for="end">End date:&nbsp &nbsp </label>
		<input type="date" id="end" name="end" value="<?php echo $today;?>" min="2018-01-01" max="<?php echo $today;?>">
		<input type="submit" value="Submit" class="button"/>
    <div class="fakeimg">
	<?php
			echo "<h5>Transactions Report: From $start_day to $end_day </h5>";
			for($j=1; $j<=$numRrslt; $j++){ 
				echo "	<div class='Info'>
								<p><span>Day:</span> {$RTransaction[$j]['Day']} &nbsp &nbsp
						    	<span>Total:</span>$ {$RTransaction[$j]['Total']}</p>
						</div>\n";
			}
			echo "<div class='w3-section w3-border-top w3-border-black w3-padding-10'><p><span>Total Transactions:</span>$ $SUMRTransaction </div></p><br/>";
	?>
	</div>
    
    <p><a href="#" onclick="window.print();return false;" />Print Report <span class="glyphicon glyphicon-print"></a></span></p>    
</form>
  </div>
  <div class="main">
<form action="./index.php" method="POST">
    <h2>Daily Transaction History</h2>
		<label for="day">Date:</label>
		<input type="date" id="day" name="day" value="<?php echo $today;?>" min="2018-01-01" max="<?php echo $today;?>">
		<input type="submit" value="Submit"  class="button"/>
    <div class="fakeimg">
	<?php
    		echo "<h5>Daily Transaction for: $day</h5>";
			for($j=1; $j<=$numrslt; $j++){ 
				echo "	<div class='Info'>
								
								<p style='font-weight:bold'> Transaction: </p>
								<p><span>&nbsp &nbsp ID:</span> {$DailyTransaction[$j]['userid']} &nbsp
						    	<span>Payment:</span>$ {$DailyTransaction[$j]['price']}</p>
								
						</div>\n";
			}
			echo "<div class='w3-section w3-border-top w3-border-black w3-padding-10'><p><span class='totalTran'>Total Daily Transactions:$ $SUMDailyTransaction</span></p><br/></div>";
	?>
	</div>
    <p><a href="#" onclick="window.print();return false;" /> Print Daily Transactions <span class="glyphicon glyphicon-print"></a></span></p>
  </div>
</form>
</div>

<!-- Footer -->
<div class="footer">
  <footer><small>Copyright &copy; Team Team, 2018</small></footer>
</div>

</body>
</html>


<?php
	//include db connection stuff
	require_once("../private/dbinfo.inc");
	
	function password_encrypt($password){
		$hash_format = "$2y$10$";
		$salt_length = 22;
		$salt = generate_salt($salt_length);
		$format_and_salt = $hash_format.$salt;
		$hash = crypt($password, $format_and_salt);
		return $hash;
	}		
	function generate_salt($length){
		//generate pseudo random string (good enough)
		//returns 32 characters
		$unique_random_string = md5(uniqid(mt_rand(), true));
		
		//convert it to base 64 (valid chars are [a-zA-Z0-0./] )
		$base64_string = base64_encode($unique_random_string);
		
		//remove the '+' characters, just replace with '.'
		$modified_base64_string = str_replace('+', '.', $base64_string);
		
		//truncate off just what we need
		$salt = substr($modified_base64_string, 0, $length);
		
		return $salt;
	}
	function password_check($password, $existing_hash){
		$hash = crypt($password, $existing_hash);
		if($hash === $existing_hash){
			return true;
		}else{
			return false;
		}
	}
	function createAccount($user, $pw, $UserName, $email){
				
		global $servername, $database, $username, $password;
		$myHandle;
		try{
			$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		}catch(PDOException $e){
			$err .="Connection failed \n";
		}		
		if($myHandle){
			$stmt = $myHandle->prepare("SELECT count(*) as total FROM Account WHERE userid=:u_id");
			$stmt->bindParam(':u_id', $user);
			$stmt->execute();
			$count = $stmt->fetchColumn();						
			if($count == 0){
				//we're going to insert, so sanitize the name
				$user = htmlspecialchars($user);//debatable whether or not this needs to be done here
				//https://paragonie.com/blog/2015/06/preventing-xss-vulnerabilities-in-php-everything-you-need-know#avoid-premature-optimization
				$sql = "INSERT into Account (userid, password, UserName, email) VALUES(:u_id, :p_word, :UserName, :mail);";
				$insertStmt = $myHandle->prepare($sql);
				$hashed_pw = password_encrypt($pw);
				$insertStmt->bindParam(':u_id', $user);
				$insertStmt->bindParam(':p_word', $hashed_pw);
				$insertStmt->bindParam(':UserName', $UserName);
				$insertStmt->bindParam(':mail', $email);
				$insertStmt->execute();
				return true;	
			}else{
				echo "count is $count";
				return false;
			}
		}else{
			echo "handle bad";
			return false;
		}
		return false;
	}

	function attempt_login($userID, $passwordAttempt){

		global $servername, $database, $username, $password;
		$myHandle;
		try{
			$myHandle = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
		}catch(PDOException $e){
			$err .="Connection failed \n";
		}		
		if($myHandle){		
			$myStmt = $myHandle->prepare("SELECT userid, password FROM Account WHERE userid=:u_id");
			$myStmt->bindParam(':u_id', $userID);
			$myStmt->execute();
			$rslt = $myStmt->fetchAll();
			if(count($rslt) > 0){
				foreach($rslt as $row){
					$hashed_pw = $row['password'];
				}
				if(password_check($passwordAttempt, $hashed_pw)){
			
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		return false;
	} 
	
	
?>

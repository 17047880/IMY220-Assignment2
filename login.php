<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbuser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false

	if(isset($_POST['picsubmit'])) 
	{ 
	    //echo "User Has submitted the form."; 
	   	$email = $_POST["email"];
	   	$pass = $_POST["pass"];
	   	$uploadFile = $_FILES["picToUpload"];
	   	$numFilesUploaded = count($uploadFile["name"]);
	   	for($i =0; $i < $numFilesUploaded; $i++){
	   		$target_dir = "gallery/";
	   		$target_file = $target_dir . basename($uploadFile["name"][$i]);
	   		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	   		$uploadStatus = true; 
	   		//Check if uploaded file is a legitimate image
	   		$check = getimagesize($uploadFile["tmp_name"][$i]);
	   		if($check !== false){
	   			//echo "File is an image - " . $check["mime"] . "." ;
	   			$uploadStatus = true; 
	   		}
	   		else{
	   			//echo "File is not an image.";
	   			$uploadStatus = false; 
	   		}
	   		if($uploadFile["size"][$i] >= 1000000 ){
	   			//echo "</br> not <1MB";
	   			$uploadStatus = false; 
	   		}
	   		if($uploadFile["type"][$i] !== "image/jpeg"){
	   			//echo "</br> not jpeg";
	   			$uploadStatus = false; 
	   		}
	   		if($uploadStatus == true){
	   			if(move_uploaded_file($uploadFile["tmp_name"][$i], $target_file)){
	   				//echo "". basename($uploadFile["name"][$i]) . " uploaded successfully.";
	   				$query ="SELECT user_id FROM tbusers WHERE email ='$email' AND password = '$pass'";
	   			$uid = $mysqli->query($query);
	   			if($uid->num_rows > 0){
	   				while ($rowt = $uid->fetch_assoc()) {
	   					//echo "User_ID: " . $rowt["user_id"] ."" ;
	   					$uploadedFileName = $uploadFile["name"][$i];

	   					$query ="INSERT INTO tbgallery ( user_id, filename) VALUES ('$rowt[user_id]', '$uploadedFileName')";
	   					if($mysqli->query($query)){
	   						//echo "New tbgallery Record created";
	   					}
	   					else{
	   						//echo "Error: " .$query . "  " . $mysqli->error;
	   					}
	   				}
	   			}
	   			else {
	   				//echo "0 Results";
	   			}
	   			}
	   		}
	   		else {
	   			//echo "</br> Invalid File";
	   		}

	   	}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Morne Roos">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form enctype='multipart/form-data' method='post' action=",htmlspecialchars($_SERVER["PHP_SELF"]),">
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple='multiple' /><br/>
									<input type='hidden' name='email' value=",$email," />
									<input type='hidden' name='pass' value=",$pass," />
									<input type='submit' class='btn btn-standard' value='Upload Image' name='picsubmit' />
								</div>
						  	</form>";


					echo 	"<h4> Image Gallery </h4>
							<div class='row imageGallery'>";
								  	
					$query ="SELECT user_id FROM tbusers WHERE email ='$email' AND password = '$pass'";
		   			$uid = $mysqli->query($query);
		   			if($uid->num_rows > 0){
		   				while ($rowt = $uid->fetch_assoc()) {
		   					$query2 = "SELECT filename FROM tbgallery WHERE user_id ='$rowt[user_id]'";
		   					$userFiles = $mysqli->query($query2);
		   					if($userFiles->num_rows>0){
		   						while($rowImg = $userFiles->fetch_assoc()){
		   							$imgName = $rowImg['filename'];
		   							echo "<div class='col-3' style='background-image: url(gallery/".$imgName.")'>     </div>";
		   						}
		   					}
		   				}
				
					}
					echo "</div>";

					}

				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			
			}
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>
<?php

	include('config.php');
	include 'DBFunctions.php';
	include('dbc.php');



	$login_button = '';

	if(isset($_GET["code"])) {
		$token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

		if(!isset($token['error'])) {
			$google_client->setAccessToken(($token['access_token']));
			$_SESSION['access-token'] = $token['access-token'];
			$google_service = new Google_Service_Oauth2($google_client);
			$data = $google_service->userinfo->get();

			if(!empty($data)) {
				$_SESSION['email'] = $data['email'];
			}
		}
	}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/shorten.css">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100&display=swap" rel="stylesheet">
	<title>URL Shortener</title>
</head>
<body>
	<div class="content">
	
		<h1>Welcome to the URL Shortener made by Naim Verboom</h1>

		<?php 
		
			if(isset($_GET['r'])) {
				$uuid = $_GET['r'];
				$link = GetLink($uuid, $conn);

				if(substr( $string_n, 0, 4 ) != "http") {
					$link = 'https://'.$link;
				}

				header('Location: ' . $link);
			}

			if(isset($_SESSION['email'])) {
				echo "<h2>You're logged in!</h2>";
				echo '<form action="" method="post">
				<input type="text" placeholder="E.g. https://google.com/" name="link" id="">
				<input type="submit" value="Shorten!">
				</form>';

				if(isset($_POST['link'])) {
					$uuid = AddURL($_POST['link'], $_SESSION['email'], $conn);
			
					print("<h2>Your shortened url is: <span class=\"link-span\">naimv.nl/s?r=".$uuid."</span></h2>");
				}
			} else {
				echo '	<h2>Login using Google to continue...</h2>
				<a href="'.$google_client->createAuthUrl().'">Sign in with Google</a>';
			}

		?>

	</div>

</body>
</html>

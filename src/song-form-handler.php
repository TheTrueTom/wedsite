<?php 
$errors = '';
$myemail = 'mariage@delphinethomas.me';

if(empty($_POST['name']))
{
    $errors .= "\n Error: all fields are required";
}

$name = $_POST['name'];
$ip = $_SERVER['REMOTE_ADDR'];

if( empty($errors))
{
	$to = $myemail; 
	$email_subject = "Musique proposée";
	$email_body = "Musique : $name"; 
	
	$headers = "From: $myemail\n"; 
	$headers .= "Reply-To: $myemail";
	
	mail($to,$email_subject,$email_body,$headers);

	//redirect to the 'thank you' page
	header("Location: ../index.html?success=2");
} 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
	<title>Contact form handler</title>
</head>

<body>
<?php
echo nl2br($errors);
?>
</body>
</html>
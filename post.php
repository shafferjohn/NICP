<!-- <form action="login.php">
	username:<input name="username">
	<br>
	password:<input name="username">
</form> -->
<?php
error_reporting(E_ALL);
// $post_data='info='.urlencode(json_encode(
// 	array(
// 	'username'=>"zxf",
// 	'password'=>'b2bd1b80762eae7b692637c0d8bf6bb3'
// 	)
// )).'&adduser='.urlencode(json_encode(
// 	array(
// 	'username'=>"zxf3",
// 	'password'=>'123456',
// 	'role'=>1
// 	)
// ));
$post_data='info='.urlencode(json_encode(
	array(
	'username'=>"zxf",
	'password'=>'5949d48b3e54c09db970fb9de152db0d'
	)
));
// $post_data='&adduser='.urlencode(json_encode(
// 	array(
// 	'username'=>"zxf17",
// 	'password'=>'123456',
// 	'role'=>1
// 	)
// ));


// $post_data='';

$url = 'http://127.0.0.2/1.1/login.php';
// $url = 'http://127.0.0.2/1.1/user.php';


	$header = array( 
		// 'nicp-access-token: 4e9ewixovPv+OPP+HvIVWY8yOCEVbHMeL81dbl/+gRu0P12W8l7XdxTbNy2MDNWNwQBYwu8vkJA',
		// 2e5bY9u7etZG1UwiCiuwYg1ClBIpXQbBmOmMv1570ykLHb9NrW3jQcp4iMxRpLaFRKtm%2B88yn21y

		// 'nicp-access-token: 5d1bzBEwIJZi64atYjG8ZLNtP3jnNPjIQp83RyqhLnL2msKqX4icxqU8PdLE86GYN9l7b3P31AHI',
		// 'nicp-access-token: eca7q7AhqpXmeBI7BqAnB5K1hLNGKyNRGfu6IrIV7J3t\/iJWC9UWo70wdld5Vo6UAo03557DmCL3',
		// 'nicp-access-token: 9282svQ8G5ihTkwJ02j8ew8UJPJ4%2Bn1ECcZr6cvWFlpY8i3MNaFN%2BSfjcoM7pA59FpdkjDGmLdrd',
		'CLIENT-IP: 1.1.1.1'
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	$re = curl_exec($ch);
	curl_close($ch);
	echo $re;

// var_dump(isset($_POST['info']));
?>
<?php
if(!$_POST['giveaway']){
?>

<h1>Batch Giveaway System</h1>

<form action="" method="POST" >
	Amount:<input type="text" name="amount" /> <br />
	Address:<textarea name="address_list" rows=10 cols="100"></textarea> <br />
	<input type="submit" name="giveaway" value="giveaway" />
</form>

<?

}else{

	define('SECURITY',true);

	define('ENABLE_LOG',true);

	include "lib/bitcoin.class.php";


	$giveaway_amount = $_POST['amount'];

	if($giveaway_amount < 1 || $giveaway_amount > 100000000){
		exit('amount error ');
	}

	$rpc_config = array(
		'type' => 'http',
		'username' => 'dimecoinrpc',
		'password' => '2596a6a36d42416b5486386c',
		'host' => '127.0.0.1:11930',
	);


	$BTC_Client = new BitcoinClient($rpc_config['type'], $rpc_config['username'], $rpc_config['password'], $rpc_config['host']);

	$balance = $BTC_Client->getbalance();


	$address_list = $_POST['address_list'];

	if(strlen($address_list) == 0){
		exit('address list error');
	}

	$address_data = explode("\r\n",$address_list);

	if(count($address_data) == 0){
		echo 'no address';
		exit;
	}

	foreach($address_data as $address){
		

		$address = trim($address);

		//check length of address
		if(strlen($address) != 34){
			echo $address.' is a bad address ';
			echo '<br />';
		}

		//call rpc port
		$sendtoaddress = $BTC_Client->sendtoaddress($address,$giveaway_amount);

		echo 'Sent '.$giveaway_amount.' coins to '.$address;
		echo '<br />';
		echo 'txid:'.$sendtoaddress;
		echo '<br />';
		echo '<br />';

		if(ENABLE_LOG == true){

			$log = $address.'|'.$amount.'|'.$sendtoaddress.'|'.date("Y-m-d G:i:s",time())."\r\n";
			file_put_contents("giveaway_log.txt",$log,FILE_APPEND);
		}

	}

	echo '<a href="javascript:history.go(-1);" >Back</a>';

}

?>

Powered By <a href="http://dimecoin.org" >Dimecoin Team </a>
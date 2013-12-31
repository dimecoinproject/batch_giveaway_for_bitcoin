#!/usr/bin/php
<?php

if ($argc != 3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
?>

This is a command line PHP script for batch giveaway .
Author   : Dimecoin Team
Email    : dimecoin.org@gmail.com
Homepage : http://dimecoin.org

  Usage:
  <?php echo $argv[0]; ?> amount address_list.txt

  to print out. With the --help, -help, -h,
  or -? options, you can get this help.

<?php
} else {


	define('SECURITY',true);

	define('ENABLE_LOG',true);

	include "lib/bitcoin.class.php";


	$giveaway_amount = (int)$argv[1];

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

	if(!file_exists($argv[2])){
		exit('address file not exists');
	}
	$address_data = file($argv[2]);

	if(count($address_data) == 0){
		echo 'no address';
		exit;
	}

	foreach($address_data as $address){
		

		$address = trim($address);

		//check length of address
		if(strlen($address) != 34){
			echo $address.' is a bad address ';
			echo "\n";
		}

		//call rpc port
		$sendtoaddress = $BTC_Client->sendtoaddress($address,$giveaway_amount);

		echo 'Sent '.$giveaway_amount.' coins to '.$address;
		echo "\n";
		echo 'txid:'.$sendtoaddress;
		echo "\n";
		echo "\n";

		if(ENABLE_LOG == true){

			$log = $address.'|'.$giveaway_amount.'|'.$sendtoaddress.'|'.date("Y-m-d G:i:s",time())."\r\n";
			file_put_contents("giveaway_log.txt",$log,FILE_APPEND);
		}

	}


}


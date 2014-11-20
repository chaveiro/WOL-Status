<?php
  
	# Wake on LAN - (c) HotKey@spr.at, upgraded by Murzik
	# Modified by Allan Barizo http://www.hackernotcracker.com
	// Port number where the computer is listening. Usually, any number between 1-50000 will do. Normally people choose 7 or 9.
	//$socket_number = "7";
	// MAC Address of the listening computer's network device
	//$mac_addy = "00:0d:9d:d1:e7:07"; //escritorio
	//$mac_addy = "00:0c:6e:b3:47:a5"; //quarto
	// IP address of the listening computer. Input the domain name if you are using a hostname (like when under Dynamic DNS/IP)
	//$ip_addy = gethostbyname("chaveiro.ath.cx");
	//flush();
	function WakeOnLan($addr, $mac,$socket_number) {
	  $addr_byte = explode(':', $mac);
	  $hw_addr = '';
	  for ($a=0; $a <6; $a++) $hw_addr .= chr(hexdec($addr_byte[$a]));
	  $msg = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255);
	  for ($a = 1; $a <= 16; $a++) $msg .= $hw_addr;
	  // send it to the broadcast address using UDP
	  // SQL_BROADCAST option isn't help!!
	  return array(TRUE, "FAKE Magic packet sent");
	  $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	  if ($s == false) {
			return array(FALSE,"Error creating socket. Code is '".socket_last_error($s)."' - " . socket_strerror(socket_last_error($s)));
		}
	  else {
		// setting a broadcast option to socket:
		$opt_ret = socket_set_option($s, 1, 6, TRUE);
		if($opt_ret <0) {
			return array(FALSE, "setsockopt() failed, error: " . strerror($opt_ret)) ;
		  }
		if(socket_sendto($s, $msg, strlen($msg), 0, $addr, $socket_number)) { 
		  socket_close($s);
			return array(TRUE, "Magic packet sent");
		  }
		else { 
			return array(FALSE, "Magic packet failed");
		  } 
		}
	}
 
	function CheckPort($host,$port) {
	    $starttime = microtime(true);
		$conn = @fsockopen($host, $port, $errno, $errstr, 0.3);
		$stoptime  = microtime(true);
		if ($conn) {
			fclose($conn);
			$ping = round(($stoptime - $starttime) * 1000, 2);
			return array(true,$errno,$errstr,$ping);
		}
		$ping = round(($stoptime - $starttime) * 1000, 2);
		return array(false,$errno,$errstr,$ping);
	}
	
?>
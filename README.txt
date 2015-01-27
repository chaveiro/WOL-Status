 __        _____  _         ____  _        _             
 \ \      / / _ \| |       / ___|| |_ __ _| |_ _   _ ___ 
  \ \ /\ / / | | | |   ____\___ \| __/ _` | __| | | / __|
   \ V  V /| |_| | |__|_____|__) | || (_| | |_| |_| \__ \
    \_/\_/  \___/|_____|   |____/ \__\__,_|\__|\__,_|___/
	
Wake On LAN and Server status monitor

https://github.com/chaveiro/WOL-Status
Author: Nuno Chaveiro  nchaveiro[at]gmail.com  Lisbon, Portugal

Requires PHP. Include jQuery and bootstrap.
  

Edit configuration in configure.php array as follow:

$this->networks = array(
new Network("Network A", 
	array(
	  new Machine("Machine 1",	"localhost", 
	  	array("RDP"=>3389,"FTP"=>21)
		, true, "00:00:00:00:00:00"),
	  new Machine("Machine 2",	"localhost", 
	  	array("HTTPS"=>443,"SSH"=>22,"HTTP"=>80)
		, true, "00:00:00:00:00:00")
		)
	),
new Network("Network B",
	array(
	  new Machine("Machine 1",	"localhost", 
	  	array("RDP"=>3389,"HTTP"=>80,"HTTPS"=>443,"FTP"=>21)
		, false)
		)
	)

);
				
				
History:
0.9b - 20-11-2014 - First public release.
1.0  - 27-01-2015 - Added refresh continuous 30 secs interval.

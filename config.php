<?php
    class Network {
        public $name = "";
        public $machines = array();
        function __construct($name,$machines) 
        {
            $this->name = $name;
            $this->machines = $machines;
        }
    }

    class Machine {
        public $name = null;
        public $host = null;
        public $hasWol = false;
        public $wolPort = null;
        public $wolMac = null;
        public $servicePorts = array();
        function __construct($name,$host=null,$servicePorts = array(), $hasWol = false, $wolMac = "", $wolPort = 7) 
        {
            $this->name = $name;
            $this->host = $host;
            $this->servicePorts = $servicePorts;
            $this->wolMac = $wolMac;
            $this->hasWol = $hasWol;
            if ($wolMac) $this->wolPort = $wolPort;
        }
    }
    
// singleton config class
final class Config
{
    private static $instance;
    private $networks ;

    private function __construct() {
        $this->networks = array( new Network("Network A", 
                                   array(
                                          new Machine("Machine 1",  "localhost", array("RDP"=>3389,"FTP"=>21)
                                            , true, "00:00:00:00:00:00"),
                                          new Machine("Machine 2",  "localhost", array("HTTPS"=>443,"SSH"=>22,"HTTP"=>80)
                                            , true, "00:00:00:00:00:00")
                                    )
                                ),
                                new Network("Network B",
                                    array(
                                          new Machine("Machine 1",  "localhost", array("RDP"=>3389,"HTTP"=>80,"HTTPS"=>443,"FTP"=>21)
                                            , false)
                                    )
                                )

                        );
    }

    public static function GetInstance() {
        if ( null == self::$instance ) {
          self::$instance = new self;
        }
        return self::$instance;
    }
    
    function GetNetworks()
    {
        return $this->networks;
    }
    
    function GetNetworksSafe()
    {
        $networksSafe = array();
        foreach($this->networks as $networkId => $network){
                $machineSafe = array();
                foreach($network->machines as $machineId => $machine){
                        $portSafe=array();
                        foreach($machine->servicePorts as $portName => $portVal)
                        {
                            array_push($portSafe, $portName);
                        }
                        array_push($machineSafe, new Machine($machine->name,"",$portSafe,$machine->hasWol));
                };
                array_push($networksSafe, new Network($network->name,$machineSafe));
        }   
        return $networksSafe;
    }

}
    
?>
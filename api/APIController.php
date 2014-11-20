<?php
include_once(dirname(__FILE__) . '/../config.php');
include_once(dirname(__FILE__) . '/../utils.php');

class APIController
{

    /**
     * Returns a JSON  object
     *
     * @url GET /config
     */
    public function config()
    {
		$config = Config::GetInstance();
        //return $config->GetNetworks();
		return $config->GetNetworksSafe();
    }

    /**
     * Executes WOL with the given POSTed id values. 
     *
     * @url POST /wol
     */
    public function wol()
    {
	    $nId = $_POST['nId'];
        $mId = $_POST['mId'];
		$response = array("success" => false, "reason" => "Machine not found");
		if (is_numeric($nId) && is_numeric($mId)) {
			$config = Config::GetInstance();
			foreach($config->GetNetworks() as $networkId => $network){
				if ($networkId == $nId)
				{
					foreach($network->machines as $machineId => $machine){
						if ($machineId == $mId)
						{
							list($result,$reason) = WakeOnLan(gethostbyname($machine->host),$machine->wolMac,$machine->wolPort);
							$response = array("success" => $result,"reason" => $reason
											 ,"mName"=>$machine->name, "nId" => $networkId, "mId" => $machineId);
							break 2;
						}
					};
				}
			}
		}
        return $response;
    }

	
    /**
     * Gets the status of port
     *
     * @url GET /status/$nId/$mId
     */
    public function status($nId,$mId)
    {
		$response = array("success" => false, "reason" => "Machine not found");
		if (is_numeric($nId) && is_numeric($mId)) {
			$config = Config::GetInstance();
			foreach($config->GetNetworks() as $networkId => $network){
				if ($networkId == $nId)
				{
					foreach($network->machines as $machineId => $machine){
						if ($machineId == $mId)
						{
							$response = array();
							foreach($machine->servicePorts as $portName => $portVal)
							{
								list($result,$errno,$reason,$ping) = CheckPort(gethostbyname($machine->host),$portVal);
								$part_response = array("success" => $result, "reason" => $reason, "errorcode" => $errno
												 ,"mName"=>$machine->name, "nId" => $networkId, "mId" => $machineId, "port" => $portName, "ping"=>$ping);
								array_push($response,$part_response);
							}
							break 2;
						}
					};
				}
			}
		}
        return $response;
    }

	
    /**
     * Returns a JSON string object to the browser when hitting the root of the domain
     *
     * @url GET /
     */
    public function root()
    {
        return "API IS LISTENING";
    }
	
    /**
     * Saves a user to the database
     *
     * @url GET /users
     * @url PUT /users/$id
     */
    public function saveUser($id = null, $data)
    {
        // ... validate $data properties such as $data->username, $data->firstName, etc.
        // $data->id = $id;
        // $user = User::saveUser($data); // saving the user to the database
        $user = array("id" => $id, "name" => null);
        return $user; // returning the updated or newly created user object
    }
}
<?php
/**
 * Description
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM: Prototype
 * @version         1.0.1               2016-12-13 16:32:11 SM: Uses database.
 */
try
{
    $dbTriConnection = Database::GetDBConnection();
}
catch(Exception $objException)
{
    die($objException);
}

echo '<link rel="stylesheet" type="text/css" href="../style.css">';

$val = mysqli_query($dbTriConnection, 'select 1 from `services ` LIMIT 1');

if ($val !== FALSE) {
    
} else {

    $createTable = $dbTriConnection->prepare("CREATE TABLE services (serviceID INT(11) AUTO_INCREMENT PRIMARY KEY, serviceName INT(11) NOT NULL, serviceDescription VARCHAR(10000) NOT NULL)");
    $createTable->execute();
    $createTable->close();
}

class services {

    protected $dbConnection;
    public $setGetModuleID;
    public $setPostModuleID;

    function __construct(mysqli $dbTriConnection) {

        $this->dbConnection = $dbTriConnection;
        $this->setGetModuleID = filter_input(INPUT_GET, 'moduleID');
        $this->setPostModuleID = filter_input(INPUT_POST, 'moduleID');
    }

    public function services() {

        $localAction = NULL;

        if (isset($this->setPostModuleID)) {
            $localAction = $this->setPostModuleID;
        } elseif (isset($this->setGetModuleID)) {
            $localAction = urldecode($this->setGetModuleID);
        }

        Switch (strtoupper($localAction)) {

            case "ACTIVATEMODULE" :
                activeModule();
                break;
            case "DEACTIVATEMODULE" :
                deactivateModule();
                break;
            default :
                $this->listServices();
        }
    }

    public function listServices() {

        global $dbTriConnection;

        echo '<div class="something">Services <a href="?id=Services&&moduleID=addService"><button>Add New</button></a></div>';
        echo '<br>';

        $serviceRef = $dbTriConnection->prepare("SELECT serviceID, serviceName FROM services");
        $serviceRef->execute();

        $serviceRef->bind_result($serviceID, $serviceName);

        while ($checkRow = $serviceRef->fetch()) {

            echo '<div class="displayInformation"><a id="supporters" href="?id=Services&&moduleID=editServices&&serviceID=' . $serviceID . '">' . $serviceName . '</a></div>';
        }
    }

    public function AddService() {

        echo '<form method="POST" action="?id=Services&&moduleID=UploadService">';
        echo '<table cellpadding=10>';
        echo '<tr><td><h1>Add Service Information</h1></td></tr>';
        echo '<tr><td><b>Service Name</b></td><td><input type="text" name="serviceName" placeholder="enter service name" required size=100></td></tr>';
        echo '<tr><td valign=top><b>Service Description</b></td><td><textarea name="area2" style="width: 740px; height:300px; background-color: white;" placeholder="enter service description" required></textarea></td></tr>';
        echo '<tr><td colspan=2><input type="Submit" name="Submit" value="Create Service"></td></tr>';
        echo '</table>';
    }

    public function uploadService() {

        global $dbConnection;

        $serviceName = filter_input(INPUT_POST, "serviceName");
        $setContent = filter_input(INPUT_POST, "area2");

        $serviceRef = $dbConnection->prepare("INSERT INTO services (serviceName, serviceDescription) VALUES (?,?)");
        $serviceRef->bind_param('ss', $serviceName, $setContent);
        $status = $serviceRef->execute();

        echo '<br><br>You have successfully added a New Service. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=?id=Services">';
    }

    public function editServices() {

        $getID = filter_input(INPUT_GET, "serviceID");

        if ($stmt = $this->dbConnection->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=? ")) {

            $stmt->bind_param("i", $getID);
            $stmt->execute();

            $stmt->bind_result($serviceName, $serviceDescription);
            $stmt->fetch();
           
            echo '<form method="POST" action="?id=Services&&moduleID=UpdateService">';
            echo '<input type=hidden name=serviceID value='.$getID.'>';
            echo '<table cellpadding=10>';
            echo '<tr><td><h1>Edit Service Information</h1></td></tr>';
            echo '<tr><td><b>Service Name</b></td><td><input type="text" name="serviceName" value="' . $serviceName . '" required size=100></td></tr>';
            echo '<tr><td valign=top><b>Service Description</b></td><td><textarea name="area2" style="width: 740px; height:300px; background-color: white;">' . $serviceDescription . '</textarea></td></tr>';
            echo '<tr><td colspan=2><input type="Submit" name="Submit" value="Update Service"></td></tr>';
            echo '</table>';
        }
    }

    public function updateService() {

        $serviceDescription = filter_input(INPUT_POST, 'area2');
        $serviceID = filter_input(INPUT_POST, 'serviceID');
        $serviceName = filter_input(INPUT_POST, 'serviceName');

        $stmt = $this->dbConnection->prepare("UPDATE services SET serviceDescription=?, serviceName=? WHERE serviceID=?");
        $stmt->bind_param('ssi', $serviceDescription, $serviceName, $serviceID);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Service Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=Services">';
    }

    public function deleteService() {

        echo '<table>';
        echo '<tr><td><h1>Add Service Information</h1></td></tr>';
        echo '</table>';
    }

    public function callToFunction() {

        echo '<div id="full-service">';
        echo '<div class="body-content">';
        echo '<div><br><h1>Services</h1></div>';
        echo '<div class="services-3">';
        if ($stmt = $this->dbConnection->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=1 ")) {

            $stmt->execute();
            $stmt->bind_result($serviceName, $serviceDescription);
            $stmt->fetch();

            echo '<div><h2>' . $serviceName . '</h2></div><div>' . $serviceDescription . '</div>';

            $stmt->close();
        }

        if ($stmt = $this->dbConnection->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=2 ")) {

            $stmt->execute();
            $stmt->bind_result($serviceName, $serviceDescription);
            $stmt->fetch();
            echo '<br>';
            echo '<div><h2>' . $serviceName . '</h2></div><div>' . $serviceDescription . '</div>';

            $stmt->close();
        }

        echo '</div><div class="services-3">';
        if ($stmt = $this->dbConnection->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=3 ")) {

            $stmt->execute();
            $stmt->bind_result($serviceName, $serviceDescription);
            $stmt->fetch();

            echo '<div><h2>' . $serviceName . '</h2></div><div>' . $serviceDescription . '</div>';

            $stmt->close();
        }

        if ($stmt = $this->dbConnection->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=4 ")) {

            $stmt->execute();
            $stmt->bind_result($serviceName, $serviceDescription);
            $stmt->fetch();
            echo '<br>';
            echo '<div><h2>' . $serviceName . '</h2></div><div>' . $serviceDescription . '</div>';

            $stmt->close();
        }
        echo '</div><div class="services-3">';

        if ($stmt = $this->dbConnection->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=5 ")) {

            $stmt->execute();
            $stmt->bind_result($serviceName, $serviceDescription);
            $stmt->fetch();

            echo '<div><h2>' . $serviceName . '</h2></div><div>' . $serviceDescription . '</div>';

            $stmt->close();
        }
        echo '</div></div>';
    }

}
?>

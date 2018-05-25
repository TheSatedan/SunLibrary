<?php
/**
 * Services module.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:32:11 SM:  Uses database.
 * @version         1.1.0               2016-12-14 16:54:40 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 14:38:45 SM:  Fixed broken links, added comments.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * Services module
 */
class services extends SunLibraryModule
{
    /** @var string $setGetModuleID The module ID as read from $_GET. */
    public $setGetModuleID;

    /** @var string $setPostModuleID The module ID as read from $_POST. */
    public $setPostModuleID;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    function __construct(mysqli $dbTriConnection)
    {
        $this->setGetModuleID = filter_input(INPUT_GET, 'moduleID');
        $this->setPostModuleID = filter_input(INPUT_POST, 'moduleID');
        parent::__construct($dbTriConnection);
    }

    /**
     * Services.  This is NOT the constructor.
     *
     * @return void
     */
    public function services()
    {
        $localAction = null;
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

    /**
     * List services.
     *
     * @return void
     */
    public function listServices()
    {
        ?>
        <div class="something">
            Services <a href="?id=Services&&moduleID=addService">
                <button>Add New</button>
            </a>
        </div>
        <br>
        <?php
        $serviceRef = $this->objDB->prepare("SELECT serviceID, serviceName FROM services");
        $serviceRef->execute();
        $serviceRef->bind_result($serviceID, $serviceName);
        while ($checkRow = $serviceRef->fetch()) {
            ?>
            <div class="displayInformation">
                <a id="supporters"
                   href="?id=Services&&moduleID=editServices&&serviceID=<?= $serviceID; ?>"><?= $serviceName; ?></a>
            </div>
            <?php
        }
    }

    /**
     * Add service form rendering.
     *
     * @return void
     */
    public function AddService()
    {
        ?>
        <form method="POST" action="?id=Services&&moduleID=UploadService">
            <table cellpadding="10">
                <tbody>
                <tr>
                    <td>
                        <h1>Add Service Information</h1>
                    </td>
                </tr>
                ';
                <tr>
                    <td>
                        <b>Service Name</b>
                    </td>
                    <td>
                        <input type="text" name="serviceName" placeholder="enter service name" required size="100">
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <b>Service Description</b>
                    </td>
                    <td>
                        <textarea name="area2" style="width: 740px; height:300px; background-color: white;"
                                  placeholder="enter service description" required></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="Submit" name="Submit" value="Create Service">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php
    }

    /**
     * Upload service
     *
     * @return void
     */
    public function uploadService()
    {
        $serviceName = filter_input(INPUT_POST, "serviceName");
        $setContent = filter_input(INPUT_POST, "area2");
        $serviceRef = $this->objDB->prepare("INSERT INTO services (serviceName, serviceDescription) VALUES (?,?)");
        $serviceRef->bind_param('ss', $serviceName, $setContent);
        $status = $serviceRef->execute();
        ?>
        <br><br>
        You have successfully added a New Service.
        <br><br><br>
        Please Wait.....
        <br>
        <meta http-equiv="refresh" content="3;url=?id=Services">
        <?php
    }

    /**
     * Edit services
     *
     * @return void
     */
    public function editServices()
    {
        $getID = filter_input(INPUT_GET, "serviceID");
        if ($stmt = $this->objDB->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=? ")) {
            $stmt->bind_param("i", $getID);
            $stmt->execute();
            $stmt->bind_result($serviceName, $serviceDescription);
            $stmt->fetch();
            ?>
            <form method="POST" action="?id=Services&&moduleID=UpdateService">
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <input type=hidden name=serviceID value="<?= $getID; ?>">
                            <table cellpadding="10">
                                <tbody>
                                <tr>
                                    <td>
                                        <h1>Edit Service Information</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Service Name</b></td>
                                    <td>
                                        <input type="text" name="serviceName" value="<?= $serviceName; ?>" required
                                               size="100">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign=top>
                                        <b>Service Description</b>
                                    </td>
                                    <td>
                                        <textarea name="area2"
                                                  style="width: 740px; height:300px; background-color: white;"><?= $serviceDescriptionl; ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=2>
                                        <input type="Submit" name="Submit" value="Update Service">
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <?php
        }
    }

    /**
     * Update service
     *
     * @return void
     */
    public function updateService()
    {
        $serviceDescription = filter_input(INPUT_POST, 'area2');
        $serviceID = filter_input(INPUT_POST, 'serviceID');
        $serviceName = filter_input(INPUT_POST, 'serviceName');
        $stmt = $this->objDB->prepare("UPDATE services SET serviceDescription=?, serviceName=? WHERE serviceID=?");
        $stmt->bind_param('ssi', $serviceDescription, $serviceName, $serviceID);
        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }
        $status = $stmt->execute();
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        ?>
        <font color="black">
            <b>Service Information Updated <br><br> Please Wait!!!!<br>
                <meta http-equiv="refresh" content="1;url=?id=Services">
        </font>
        <?php
    }

    /**
     * Delete a service
     *
     * @return void
     */
    public function deleteService()
    {
        ?>
        <table>
            <tbody>
            <tr>
                <td>
                    <h1>Add Service Information</h1>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function callToFunction()
    {
        ?>
        <div id="full-service">
            <div class="body-content">
                <div>
                    <br>
                    <h1>Services</h1>
                </div>
                <div class="services-3">
                    <?php
                    if ($stmt = $this->objDB->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=1 ")) {
                        $stmt->execute();
                        $stmt->bind_result($serviceName, $serviceDescription);
                        $stmt->fetch();
                        ?>
                        <div>
                            <h2><?= $serviceName; ?></h2>
                        </div>
                        <div>
                            <?= $serviceDescription; ?>
                        </div>
                        <?php
                        $stmt->close();
                    }

                    if ($stmt = $this->objDB->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=2 ")) {
                        $stmt->execute();
                        $stmt->bind_result($serviceName, $serviceDescription);
                        $stmt->fetch();
                        ?>
                        <br>
                        <div>
                            <h2><?= $serviceName; ?></h2>
                        </div>
                        <div>
                            <?= $serviceDescription; ?>
                        </div>
                        <?php
                        $stmt->close();
                    }
                    ?>
                </div>
                <div class="services-3">
                    <?php
                    if ($stmt = $this->objDB->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=3 ")) {
                        $stmt->execute();
                        $stmt->bind_result($serviceName, $serviceDescription);
                        $stmt->fetch();
                        ?>
                        <div>
                            <h2><?= $serviceName; ?></h2>
                        </div>
                        <div>
                            <?= $serviceDescription; ?>
                        </div>
                        <?php
                        $stmt->close();
                    }

                    if ($stmt = $this->objDB->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=4 ")) {
                        $stmt->execute();
                        $stmt->bind_result($serviceName, $serviceDescription);
                        $stmt->fetch();
                        ?>
                        <br>
                        <div>
                            <h2><?= $serviceName; ?></h2>
                        </div>
                        <div>
                            <?= $serviceDescription; ?>
                        </div>
                        <?php
                        $stmt->close();
                    }
                    ?>
                </div>
                <div class="services-3">
                    <?php
                    if ($stmt = $this->objDB->prepare("SELECT serviceName, serviceDescription FROM services WHERE serviceID=5 ")) {
                        $stmt->execute();
                        $stmt->bind_result($serviceName, $serviceDescription);
                        $stmt->fetch();
                        ?>
                        <div>
                            <h2><?= $serviceName; ?></h2>
                        </div>
                        <div>
                            <?= $serviceDescription; ?>
                        </div>
                        <?php
                        $stmt->close();
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function renderHeaderLinks()
    {
        ?>
        <link rel="stylesheet" type="text/css" href="../style.css">
        <?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function assertTablesExist()
    {
        $objResult = $this->objDB->query('select 1 from `services ` LIMIT 1');
        if ($objResult === false) {
            $createTable = $this->objDB->prepare("CREATE TABLE services (serviceID INT(11) AUTO_INCREMENT PRIMARY KEY, serviceName INT(11) NOT NULL, serviceDescription VARCHAR(10000) NOT NULL)");
            $createTable->execute();
            $createTable->close();
        } else {
            $objResult->free();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string Full verison number as read from the docblock for this file.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}

?>

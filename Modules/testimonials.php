<?php
/**
 * Testimonials page.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:33:48 SM:  Uses database.
 * @version         1.1.0               2016-12-14 16:41:55 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 13:57:59 SM:  Cleaned the code, added phpdoc comments.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

/**
 * Testimonials class
 */
class testimonials extends SunLibraryModule
{
    /**
     * {@inheritdoc}
     *
     * @param mysqli $dbTriConnection Connection to the database.
     */
    function __construct(mysqli $dbTriConnection)
    {
        parent::__construct($dbTriConnection);
    }

    /**
     * Render the testimonials
     *
     * @return void
     */
    public function listTestimonials()
    {
?>
        <div class="something">
            Testimonials <a href="?id=Testimonials&&moduleID=addTestimonial"><button>Add New</button></a>
        </div>
        <br>
        <div class="leftBank">
<?php
            $userRef = $this->objDB->prepare("SELECT testimonialID, userFullName, userHyperlink FROM users LEFT JOIN testimonials ON testimonials.userID=users.userID WHERE userStatus='Client' ");
            $userRef->execute();
            $userRef->bind_result($testimonialID, $userFullName, $userHyperlink);
            while ($checkRow = $userRef->fetch())
            {
                if ($testimonialID)
                {
?>
                    <div class="displayInformation"><?=$userFullName;?>:: <button id="testie">Edit</button> </div><div class="displayInformation"><?=$userHyperlink;?></div>
<?php
                }
                else
                {
?>
                    <div class="displayInformation"><?=$userFullName;?>::<button id="testie">Insert</button> </div><div class="displayInformation"><?=$userHyperlink;?></div>
<?php
                }
?>
                <br>
<?php
            }
?>
        </div>
<?php
        @$userRef->close();
    }

    /**
     * Add testimonial code.
     *
     * @return void
     */
    public function addTestimonial()
    {
?>
        <form method="POST" action="?id=Testimonials&&moduleID=UploadTestimonial">
            <table width="100%"" cellpadding="10">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <h1>Add Testimonial</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>Client</b>
                        </td>
                        <td>
                            <select name="clientID">
                                <option>Select Username</option>
<?php
                                $userRef = $this->objDB->prepare("SELECT userID, userFullname FROM users");
                                $userRef->execute();
                                $userRef->bind_result($userID, $userFullname);
                                while ($checkRow = $userRef->fetch())
                                {
?>
                                    <option value="<?=$userID;?>"><?=$userFullname;?></option>
<?php
                                }
?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td valign=top width="150">
                            <b>Testimonial Content</b>
                        </td>
                        <td>
                            <textarea name="area2" style="width: 740px; height:300px; background-color: white;" placeholder="enter testimonial content"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" name="submit" value="Create">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
<?php
    }

    /**
     * Upload a testimonial
     *
     * @return void
     */
    public function uploadTestimonial()
    {
        $userID = filter_input(INPUT_POST, "clientID");
        $setContent = filter_input(INPUT_POST, "area2");
        $stmt = $this->objDB->prepare("INSERT INTO testimonials (userID, testimonialDescription) VALUES (?,?)");
        $stmt->bind_param('is', $userID, $setContent);
        $status = $stmt->execute();
?>
        <br><br>You have successfully added a New Testimonial. <br><br><br>Please Wait.....<br>
        <meta http-equiv="refresh" content="3;url=?id=Testimonials">
<?php
    }

    /**
     * Edit a testimonial
     *
     * @return void
     */
    function editTestimonial()
    {
        $getID = filter_input(INPUT_GET, "testimonialID");
        if ($stmt = $this->objDB->prepare("SELECT userFullName, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=? "))
        {
            $stmt->bind_param("i", $getID);
            $stmt->execute();
            $stmt->bind_result($setFullName, $setUserID, $testimonialDescription);
            $stmt->fetch();
?>
            <form method="POST" action="?id=Testimonials&&moduleID=UpdateTestimonial">
                <input type="hidden" name="testimonialID" value="<?=$getID;?>">
                <table width="100%"" cellpadding="10">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <h1>Edit Testimonial</h1>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Client</b>
                            </td>
                            <td>
                                <select name="clientID" onchange="showUser(this.value)">
                                    <option value="<?=$setUserID;?>"><?=$setFullName;?></option>
<?php
                                    $userRef = $this->objDB->prepare("SELECT userID, userFullname FROM users");
                                    $userRef->execute();
                                    $userRef->bind_result($getUserID, $userFullname);
                                    while ($checkRow = $userRef->fetch())
                                    {
?>
                                        <option value="<?=$getUserID;?>"><?=$userFullname;?></option>
<?php
                                    }
?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td valign=top>
                                <b>Description</b>
                            </td>
                            <td>
                                <textarea name="area2" style="width: 740px; height:300px; background-color: white;"><?=$testimonialDescription;?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="Submit" name="Submit" value="Update">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
<?php
        }
    }

    /**
     * Update testimonial.
     *
     * @return void
     */
    function updateTestimonial()
    {
        $testimonialBody = filter_input(INPUT_POST, 'area2');
        $clientID = filter_input(INPUT_POST, 'clientID');
        $testimonialID = filter_input(INPUT_POST, 'testimonialID');
        $stmt = $this->objDB->prepare("UPDATE testimonials SET testimonialDescription=?, userID=? WHERE testimonialID=?");
        $stmt->bind_param('sii', $testimonialBody, $clientID, $testimonialID);
        if ($stmt === false) 
            trigger_error($this->objDB->error, E_USER_ERROR);
        $status = $stmt->execute();
        if ($status === false)
            trigger_error($stmt->error, E_USER_ERROR);
?>
        <font color="black">
            <b>Testimonial Information Updated <br><br> Please Wait!!!!<br>
            <meta http-equiv="refresh" content="1;url=?id=Testimonials">
        </font>
<?php
    }

    /**
     * Testimonials.  Note that this is NOT the constructor.
     *
     * @return void.
     *
     */
    function testimonials()
    {
        $setGetModuleID = filter_input(INPUT_GET, 'moduleID');
        $setPostModuleID = filter_input(INPUT_POST, 'moduleID');
        $localAction = NULL;
        if (isset($setPostModuleID))
            $localAction = $setPostModuleID;
        elseif (isset($setGetModuleID))
            $localAction = urldecode($setGetModuleID);

        Switch (strtoupper($localAction))
        {
            case "ACTIVATEMODULE" :
                activeModule();
                break;
            case "DEACTIVATEMODULE" :
                deactivateModule();
                break;
            default :
                $this->listTestimonials();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function callToFunction()
    {
?>
        <div class="testimonial-content">
            <div class="leftBank">
                <h2>Testimonials</h2>
                <br>
<?php
                if ($stmt = $this->objDB->prepare("SELECT userFullName, userHyperlink, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=1 "))
                {
                    $stmt->execute();
                    $stmt->bind_result($setFullName, $userHyperlink, $setUserID, $testimonialDescription);
                    $stmt->fetch();
?>
                    <div><?=$setFullName;?></div>
                    <div><?=$userHyperlink;?></div>
                    <div><br><?=$testimonialDescription;?></div>
<?php
                    $stmt->close();
                }

                if ($stmt = $this->objDB->prepare("SELECT userFullName, userHyperlink, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=2 "))\
                {
                    $stmt->execute();
                    $stmt->bind_result($setFullName, $userHyperlink, $setUserID, $testimonialDescription);
                    $stmt->fetch();
?>
                    <br><br><br>
                    <div><?=$setFullName;?></div>
                    <div><?=$userHyperlink;?></div>
                    <div><br><?=$testimonialDescription;?></div>
<?php
                    $stmt->close();
                }
?>
            </div>
            <div class="rightBank">
                <h2>Our Team</h2>
                <br>
<?php
                if ($stmt = $this->objDB->prepare("SELECT userFullName, userHyperlink, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=1 "))
                {
                    $stmt->execute();
                    $stmt->bind_result($setFullName, $userHyperlink, $setUserID, $testimonialDescription);
                    $stmt->fetch();
?>
                    <div>
                        <img src="Images/CameronSlade.png">
                    </div>
                    <div>
                        Cameron Slade McGauchie<br>Business
                    </div>
                    <div>
                        By building better efficenices we can challenge our competitors and providing a local offering currently unavailable.<br><br>I wont settle anything less then majQa
                    </div>
<?php
                    $stmt->close();
                }

                if ($stmt = $this->objDB->prepare("SELECT userFullName, userHyperlink, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=2 "))
                {
                    $stmt->execute();
                    $stmt->bind_result($setFullName, $userHyperlink, $setUserID, $testimonialDescription);
                    $stmt->fetch();
?>
                    <br>
                    <div>
                        <img src="Images/AndrewJeffries.png">
                    </div>
                    <div>
                        Andrew Jeffries<br>Software Engineer (Dip IT-WEB)
                    </div>
                    <div>
                        No Wordpress, No Wix, No Templates, We make our Own, We Build the best. <br><br> It will run like it was born of Sateda.
                    </div>
<?php
                    $stmt->close();
                }
?>
            </div>
        </div>
<?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function assertTablesExist()
    {
        $objResult=$this->objDB->query('select 1 from `testimonials` LIMIT 1');
        if ($objResult===false)
        {
            $createTable = $this->objDB->prepare("CREATE TABLE testimonials (testimonialID INT(11) AUTO_INCREMENT PRIMARY KEY, userID INT(11) NOT NULL, testimonialDescription VARCHAR(10000) NOT NULL)");
            $createTable->execute();
            $createTable->close();
        }
        else
            $objResult->free();
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
     * @return string Version number as read from the file's docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }    
}
?>

<?php
/**
 * Testimonials page.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:33:48 SM:  Uses database.
 * @version         1.1.0               2016-12-14 16:41:55 SM:  Uses SunLibraryModule.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

class testimonials extends SunLibraryModule
{
    function __construct(mysqli $dbTriConnection) {

        parent::__construct($dbTriConnection);
    }

    public function listTestimonials() {

        echo '<div class="something">Testimonials <a href="?id=Testimonials&&moduleID=addTestimonial"><button>Add New</button></a></div>';
        echo '<br>';
        echo '<div class="leftBank">';
        $userRef = $this->dbConnection->prepare("SELECT testimonialID, userFullName, userHyperlink FROM users LEFT JOIN testimonials ON testimonials.userID=users.userID WHERE userStatus='Client' ");
        $userRef->execute();

        $userRef->bind_result($testimonialID, $userFullName, $userHyperlink);

        while ($checkRow = $userRef->fetch()) {

            if ($testimonialID)
            echo '<div class="displayInformation">' . $userFullName . ' :: <button id="testie">Edit</button> </div><div class="displayInformation"> '.$userHyperlink.'</div>';
            else
            echo '<div class="displayInformation">' . $userFullName . ' :: <button id="testie">Insert</button> </div><div class="displayInformation"> '.$userHyperlink.'</div>';
            echo '<br>';
        }
        $userRef->close();
        echo '</div>';
    }

    public function addTestimonial() {

        echo '<form method="POST" action="?id=Testimonials&&moduleID=UploadTestimonial">';
        echo '<table width=100% cellpadding=10>';
        echo '<tr><td colspan=2><h1>Add Testimonial</h1></td></tr>';
        echo '<tr><td><b>Client</b></td><td>';

        echo '<select name="clientID">';
        echo '<option>Select Username</option>';
        $userRef = $this->dbConnection->prepare("SELECT userID, userFullname FROM users");
        $userRef->execute();

        $userRef->bind_result($userID, $userFullname);

        while ($checkRow = $userRef->fetch()) {

            echo '<option value="' . $userID . '">' . $userFullname . '</option>';
        }
        echo '</select>';
        echo '</td></tr>';

        echo '<tr><td valign=top width=150><b>Testimonial Content</b></td><td><textarea name="area2" style="width: 740px; height:300px; background-color: white;" placeholder="enter testimonial content"></textarea></td></tr>';
        echo '<tr><td><input type="submit" name="submit" value="Create"></td></tr>';
        echo '</table>';
        echo '</form>';
    }

    public function uploadTestimonial() {

        $userID = filter_input(INPUT_POST, "clientID");
        $setContent = filter_input(INPUT_POST, "area2");

        $stmt = $this->dbConnection->prepare("INSERT INTO testimonials (userID, testimonialDescription) VALUES (?,?)");
        $stmt->bind_param('is', $userID, $setContent);
        $status = $stmt->execute();

        echo '<br><br>You have successfully added a New Testimonial. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=?id=Testimonials">';
    }

    function editTestimonial() {

        global $secondaryDBConnection;

        $getID = filter_input(INPUT_GET, "testimonialID");

        if ($stmt = $this->dbConnection->prepare("SELECT userFullName, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=? ")) {

            $stmt->bind_param("i", $getID);
            $stmt->execute();

            $stmt->bind_result($setFullName, $setUserID, $testimonialDescription);
            $stmt->fetch();

            echo '<form method="POST" action="?id=Testimonials&&moduleID=UpdateTestimonial">';
            echo '<input type="hidden" name="testimonialID" value="' . $getID . '">';
            echo '<table width=100% cellpadding=10>';
            echo '<tr><td colspan=2><h1>Edit Testimonial</h1></td></tr>';
            echo '<tr><td><b>Client</b> </td><td>';

            echo '<select name="clientID" onchange="showUser(this.value)">';
            echo '<option value="' . $setUserID . '">' . $setFullName . '</option>';

            $userRef = $secondaryDBConnection->prepare("SELECT userID, userFullname FROM users");
            $userRef->execute();

            $userRef->bind_result($getUserID, $userFullname);

            while ($checkRow = $userRef->fetch()) {

                echo '<option value="' . $getUserID . '">' . $userFullname . '</option>';
            }
            echo '</select>';

            echo '</td></tr>';
            echo '<tr><td valign=top><b>Description</b></td><td><textarea name="area2" style="width: 740px; height:300px; background-color: white;">' . $testimonialDescription . '</textarea></td></tr>';
            echo '<tr><td><input type="Submit" name="Submit" value="Update"></td></tr>';
            echo '</table>';
            echo '</form>';
        }
    }

    function updateTestimonial() {

        global $dbConnection;

        $testimonialBody = filter_input(INPUT_POST, 'area2');
        $clientID = filter_input(INPUT_POST, 'clientID');
        $testimonialID = filter_input(INPUT_POST, 'testimonialID');

        $stmt = $dbConnection->prepare("UPDATE testimonials SET testimonialDescription=?, userID=? WHERE testimonialID=?");
        $stmt->bind_param('sii', $testimonialBody, $clientID, $testimonialID);

        if ($stmt === false) {
            trigger_error($dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Testimonial Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=Testimonials">';
    }

    function testimonials() {

        $setGetModuleID = filter_input(INPUT_GET, 'moduleID');
        $setPostModuleID = filter_input(INPUT_POST, 'moduleID');

        $localAction = NULL;

        if (isset($setPostModuleID)) {
            $localAction = $setPostModuleID;
        } elseif (isset($setGetModuleID)) {
            $localAction = urldecode($setGetModuleID);
        }

        Switch (strtoupper($localAction)) {

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

    public function callToFunction() {

        echo '<div class="testimonial-content">';

        echo '<div class="leftBank">';
        echo '<h2>Testimonials</h2>';
        echo '<br>';
        if ($stmt = $this->objDB->prepare("SELECT userFullName, userHyperlink, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=1 ")) {

            $stmt->execute();

            $stmt->bind_result($setFullName, $userHyperlink, $setUserID, $testimonialDescription);
            $stmt->fetch();

            echo '<div>' . $setFullName . '</div>';
            echo '<div>' . $userHyperlink . '</div>';
            echo '<div><br>' . $testimonialDescription . '</div>';
            $stmt->close();
        }

        if ($stmt = $this->objDB->prepare("SELECT userFullName, userHyperlink, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=2 ")) {

            $stmt->execute();

            $stmt->bind_result($setFullName, $userHyperlink, $setUserID, $testimonialDescription);
            $stmt->fetch();

            echo '<br><br><br>';
            echo '<div>' . $setFullName . '</div>';
            echo '<div>' . $userHyperlink . '</div>';
            echo '<div><br>' . $testimonialDescription . '</div>';
            $stmt->close();
        }

        echo '</div><div class="rightBank">';
        echo '<h2>Our Team</h2>';
        echo '<br>';
        if ($stmt = $this->objDB->prepare("SELECT userFullName, userHyperlink, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=1 ")) {

            $stmt->execute();

            $stmt->bind_result($setFullName, $userHyperlink, $setUserID, $testimonialDescription);
            $stmt->fetch();

            echo '<div><img src="Images/CameronSlade.png"></div><div>Cameron Slade McGauchie<br>Business</div>';
            echo '<div>By building better efficenices we can challenge our competitors and providing a local offering currently unavailable.<br><br>I wont settle anything less then majQa\'</div>';
            $stmt->close();
        }

        if ($stmt = $this->objDB->prepare("SELECT userFullName, userHyperlink, testimonials.userID, testimonialDescription FROM testimonials INNER JOIN users ON users.userID=testimonials.userID WHERE testimonialID=2 ")) {

            $stmt->execute();

            $stmt->bind_result($setFullName, $userHyperlink, $setUserID, $testimonialDescription);
            $stmt->fetch();
            echo '<br>';
            echo '<div><img src="Images/AndrewJeffries.png"></div><div>Andrew Jeffries<br>Software Engineer (Dip IT-WEB)</div>';
            echo '<div>No Wordpress, No Wix, No Templates, We make our Own, We Build the best. <br><br> It will run like it was born of Sateda.</div>';
            $stmt->close();
        }

        echo '</div></div>';
    }

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

    public function renderHeaderLinks()
    {
?>
        <link rel="stylesheet" type="text/css" href="../style.css">
<?php
    }

    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }    
}
?>

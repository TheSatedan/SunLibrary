<?php
/**
 * Client supporters module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 */

try
{
    $dbConnection = databaseConnection();
}
catch(Exception $objException)
{
    die($objException);
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

class clientSupporters {
    const ModuleDescription = 'Access to change all Support Information and Adverting.';
    const ModuleAuthor = 'Sunsetcoders Development Team.';
    const ModuleVersion = '0.1';

    protected $dbConnection;
    private $setPostID;
    private $setGetID;

    function __construct($dbConnection) {
        global $dbConnection;

        $this->dbConnection = $dbConnection;

        $this->setPostID = filter_input(INPUT_POST, 'id');
        $this->setGetID = filter_input(INPUT_GET, 'id');
    }

    public function AddSupporter() {

        echo '<form action="web-settings.php?id=Supporters&&moduleID=UploadSupporter" method="post" enctype="multipart/form-data">';
        echo '<table width=100% cellpadding=15>';
        echo '<tr><td colspan=2><h1>Support Information</h1></td></tr>';
        echo '<tr><td><b>Supporter Name</b></td><td><input type="text" name="supportName" placeholder="enter supporter name" required size=80></td></tr>';
        echo '<tr><td width=200><b>Supporter Hyperlink</b></td><td><input type="text" name="supportHyperlink" placeholder="enter supporter hyperlink" required size=80></td></tr>';
        echo '<tr><td colspan=2><b>Supporter Description: <br><br><textarea name="supportDescription" placeholder="enter frontpage supporter Description" style="width: 740px; height: 200px; "></textarea></td></tr>';
        echo '<tr><td><b>Select Logo to upload:</b></td><td><input type="file" name="fileToUpload" id="fileToUpload"></td></tr>';
        echo '<tr><td><input type="submit" value="Upload Image" name="submit"></td></tr>';

        echo '</table>';
        echo '</form>';
    }

    public function uploadSupporter() {
        $target_dir = "../Images/Logos/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        $supporterHyperlink = filter_input(INPUT_POST, "supportHyperlink");
        $supporterImage = basename($_FILES["fileToUpload"]["name"]);
        $supporterName = filter_input(INPUT_POST, "supportName");
        $supporterDescription = filter_input(INPUT_POST, "supportDescription");


        $stmt = $this->dbConnection->prepare("INSERT INTO supporters (companyName, companyHyperlink, companyDescription, companyLogo ) VALUES (?,?,?,?)");

        $stmt->bind_param('ssss', $supporterName, $supporterHyperlink, $supporterDescription, $supporterImage );

        $status = $stmt->execute();

        echo '<br><br>You have successfully added a Supporter. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Supporters">';
    }

    public function editSupporter() {

        $getID = filter_input(INPUT_GET, 'supporterID');

        if ($stmt = $this->dbConnection->prepare("SELECT supporterID, companyName, companyLogo, companyHyperlink, companyDescription  FROM supporters WHERE supporterID=? ")) {

            $stmt->bind_param("i", $getID);
            $stmt->execute();
            $stmt->bind_result($supporterID, $companyName, $companyLogo, $companyHyperlink, $companyDescription);
            $stmt->fetch();

            echo '<form action="web-settings.php?id=Supporters&&moduleID=UpdateSupporter" method="post" enctype="multipart/form-data">';
            echo '<input type="hidden" name="supporterID" value="' . $getID . '">';
            echo '<table border=0 width=100% cellpadding=5>';
            echo '<tr><td><b>Supporter Name</b><br><input type="text" name="companyName" value="' . $companyName . '" size=40></td><td><img src="../Images/Logos/' . $companyLogo . '" width=200></td></tr>';
            echo '<tr><td><b>Supporter Hyperlink</b><br><input type="text" name="companyHyperlink" value="' . $companyHyperlink . '" size=40 placeholder="Supporter Hyperlink"></td><td></td></tr>';
            echo '<tr><td><b>FrontPage Description</b><br><textarea name="supportDescription" placeholder="FrontPage Description" style="width: 740px; height: 300px;">' . $companyDescription . '</textarea></td><td valign=top width=40%>Select image to upload:<br><input type="file" name="fileToUpload" id="fileToUpload"></td></tr>';
            echo '<tr><td colpsan=2><input type="submit" name="submit" value="Update"></td></tr>';

            echo '</table>';
            echo '</form>';

            $stmt->close();
        }
    }

    public function updateSupporter() {

        $target_dir = "../Images/Logos/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }


        $companyName = filter_input(INPUT_POST, 'companyName');
        $companyHyperlink = filter_input(INPUT_POST, 'companyHyperlink');


        $companyLogo = basename($_FILES["fileToUpload"]["name"]);


        if (!$companyLogo)
            $companyLogo = "na.png";

        $companyDescription = filter_input(INPUT_POST, 'supportDescription');
        $getID = filter_input(INPUT_POST, 'supporterID');


        $stmt = $this->dbConnection->prepare("UPDATE supporters SET companyName=?, companyLogo=?, companyHyperlink=?, companyDescription=? WHERE supporterID=?");
        $stmt->bind_param('ssssi', $companyName, $companyLogo, $companyHyperlink, $companyDescription, $getID);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Supporter Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Supporters">';
    }

    public function supporters() {
        
        echo '<table cellpadding=10>';
        echo '<tr><td>Supporters <a href="?id=Supporters&&moduleID=AddSupporter"><button>Add Supporter</button></a></td></tr>';

        echo '<tr class="tableTop"><td>Company Logo</td><td>Company Name</td><td>Company Hyperlink</td></tr>';
        $stmt = $this->dbConnection->prepare("SELECT supporterID, companyName, companyLogo, companyHyperlink FROM supporters ORDER BY supporterID ");
        $stmt->execute();

        $stmt->bind_result($supporterID, $companyName, $companyLogo, $companyHyperlink);

        while ($checkRow = $stmt->fetch()) {

            echo '<tr id="supporterRow" onclick="window.document.location=\'?id=Supporters&&moduleID=editSupporter&&supporterID=' . $supporterID . '\';" style="cursor: pointer"><td><img src="../Images/Logos/' . $companyLogo . '" height=50></td><td>' . $companyName . '</td><td><a href="' . $companyHyperlink . '" target="_blank">' . $companyHyperlink . '</a></td></tr>';
        }

        echo '</table>';
    }

}
?>

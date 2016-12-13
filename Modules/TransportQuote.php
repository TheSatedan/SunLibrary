<?php
/**
 * Transport queue module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:34:05 SM:  Uses database.
 */

try
{
    $dbTriConnection = Database::GetDBConnection();
}
catch(Exception $objException)
{
    die($objException);
}

/*
 * The Following Snippet is to insert the module table into the mysqli table. 
 */

$val = mysqli_query($dbTriConnection, 'select 1 from `TransportQuote` LIMIT 1');

if ($val !== FALSE) {
    
} else {
    $createTable = $dbTriConnection->prepare("CREATE TABLE TransportQuote (sliderID INT(11) AUTO_INCREMENT PRIMARY KEY, imageToSlide VARCHAR(100) NOT NULL, sliderOrder DECIMAL(3,0) NOT NULL)");
    $createTable->execute();
    $createTable->close();
}

class TransportQuote {

    protected $dbConnection;

    function __construct(mysqli $dbConnection) {

        $this->dbConnection = $dbConnection;
    }

    public function TransportQuote() {

        /*
         * This is prometheus Administrator output.
         */
    }

    public function editContent() {

        $contentCode = filter_input(INPUT_GET, "ContentID");

        $query = "SELECT $contentCode FROM teampanel WHERE teampanelID=1 ";

        echo '<form method="POST" action="?id=team&&moduleID=UpdateContent">';
        echo '<input type="hidden" name="contentCode" value="' . $contentCode . '">';

        if ($stmt = $this->dbConnection->prepare($query)) {

            $stmt->execute();
            $stmt->bind_result($contentCode);
            $stmt->fetch();

            echo '<table border=0 cellpadding=20>';
            echo '<tr><td><h1>Content: </h1></td></tr>';
            echo '<tr><td><textarea cols=100 rows=10 name="contentMatter">' . $contentCode . '</textarea></td></tr>';
            echo '<tr><td><input type="submit" name="submit" value="Update"></td></tr>';
        }
        echo '</form>';
    }

    public function editImage() {

        $contentCode = filter_input(INPUT_GET, "ContentID");

        $query = "SELECT $contentCode FROM teampanel WHERE teampanelID=1 ";

        echo '<form action="?id=team&&moduleID=UpdateImage" method="post" enctype="multipart/form-data">';
        echo '<input type="hidden" name="contentCode" value="' . $contentCode . '">';

        if ($stmt = $this->dbConnection->prepare($query)) {

            $stmt->execute();
            $stmt->bind_result($contentCode);
            $stmt->fetch();

            echo '<table border=0 cellpadding=20>';
            echo '<tr><td><h1>Image Information: </h1></td></tr>';
            echo '<tr><td><img src="../Images/' . $contentCode . '"></td></tr>';
            echo '<tr><td><input type="hidden" name="MAX_FILE_SIZE" value="100000" /></td></tr>';
            echo '<tr><td>Choose a replacement image to upload: <br> <input type="file" name="fileToUpload" id="fileToUpload"></td></tr>';
            echo '<tr><td><input type="submit" name="submit" value="Update"></td></tr>';
        }
        echo '</form>';
    }

    public function updateImage() {

        $target_dir = "../Images/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $target_filename = basename($_FILES["fileToUpload"]["name"]);

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

        /*
         * Update Teampanel Database with the new Image information.
         */

        $contentImageName = $target_filename;
        $contentCode = filter_input(INPUT_POST, 'contentCode');

        $stmt = $this->dbConnection->prepare("UPDATE quotes SET $contentCode=? WHERE quoteID=1");
        $stmt->bind_param('s', $contentImageName);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Image Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=TransportQuote">';
    }

    public function updateContent() {

        $contentDescription = filter_input(INPUT_POST, 'contentMatter');
        $contentCode = filter_input(INPUT_POST, 'contentCode');

        $stmt = $this->dbConnection->prepare("UPDATE quotes SET $contentCode=? WHERE quoteID=1");
        $stmt->bind_param('s', $contentDescription);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=Team">';
    }

    public function callToFunction() {
        ?>
<script type="text/javascript">

function yesnoCheck() {
    if (document.getElementById('yesCheck').checked) {
        document.getElementById('ifYes').style.display = 'block';
    }
    else document.getElementById('ifYes').style.display = 'none';

}

</script>

        <div id="quote-background">
            <div class="body-content">
                <div><br><br><h2>Request a Quote</h2></div>

                <div class="section">
                    <div class="section1">
                        <div class="next2">Name</div><div class="padder"><input type="text" name="quoteName"></div>
                        <div class="next2">Account Name</div><div class="padder"><input type="text" name="quoteName"></div>
                        <div class="next2">Email</div><div class="padder"><input type="text" name="quoteName"></div>
                        <div class="next2">Make of vehicle: (i.e. Ford, Holden)</div><div class="padder"><input type="text" name="quoteName"></div>
                        <div class="next2">Model of vehicle: (i.e. Falcon, Commodore)</div><div class="padder"><input type="text" name="quoteName"></div>
                        <div class="next2">Type of vehicle</div><div class="padder"><select name="quoteCarType"><option value="">[ select ]</option><option value="Sedan">Sedan</option><option value="Wagon">Wagon</option><option value="Ute">Ute</option><option value="Coupe">Coupe</option><option value="Hatch">Hatch</option><option value="Other">Other</option></select></div>
                    </div>
                    <div class="section2">
                        <div>Pickup Details</div>
                        <div class="next2">Suburb</div><div class="padder"><input type="text" name="quoteName"></div>
                        <div class="next2">State</div><div class="padder"><select name="quotePickUpState"><option value="">[ select ]</option><option value="1">Queensland</option><option value="2">New South Wales</option><option value="3">Victoria</option><option value="4">South Australia</option><option value="5">Western Australia</option><option value="7">Northern Territory</option><option value="8">Australian Capital Territory</option></select></div>
                        <div class="next2">Depot Location</div><div class="padder"><input type="text" name="quoteName"></div>
                        <div>Delivery Details</div>
                        <div class="next2">Suburb</div><div class="padder"><input type="text" name="quoteName"></div>
                        <div class="next2">State</div><div class="padder"><select name="quoteDropOffState"><option value="">[ select ]</option><option value="1">Queensland</option><option value="2">New South Wales</option><option value="3">Victoria</option><option value="4">South Australia</option><option value="5">Western Australia</option><option value="7">Northern Territory</option><option value="8">Australian Capital Territory</option></select></div>
                        <div class="next2">Depot Location</div><div class="padder"><input type="text" name="quoteName"></div>
                    </div>
                </div>
                <div class="secondSection">
                    <div class="next2">Is the vehicle drivable:</div><div class="padder"><input type="radio" name="drivable" value="Yes" onclick="javascript:yesnoCheck();" id="yesCheck"> Yes <input type="radio" name="drivable" value="No" onclick="javascript:yesnoCheck();" id="noCheck"> No </div>
                    <div  id="ifYes" style="display:none">
                    <div class="next2">If so, please provide details including dimensions: (height, length, width)</div><div class="padder"><input type="text" name="quoteName"></div>
                    </div>
                    <div class="next2">Lift Kit</div><div class="padder"><input type="text" name="quoteName"></div>
                    <div class="next2">Larger Tyres</div><div class="padder"><input type="text" name="quoteName"></div>
                    <div class="next2">If vehicle is lowered: Ride height</div><div class="padder"><input type="text" name="quoteName"></div>
                    <div class="next2">Will there be personal effects stored in your vehicle *</div><div class="padder"><input type="radio" name="effects" value="Yes"> Yes <input type="radio" name="effects" value="No"> No </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>

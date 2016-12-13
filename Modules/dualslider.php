<?php
/**
 * Dual slider module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM: Prototype
 * @version         1.0.1               2016-12-13 16:20:27 SM: Uses database.
 */
try
{
    $dbTriConnection=Database::GetDBConnection();
}
catch(Exception $objException)
{
    die($objException);
}
$val = mysqli_query($dbTriConnection, 'select 1 from `dualslider` LIMIT 1');

if ($val !== FALSE) {

} else {

    $createTable = $dbTriConnection->prepare("CREATE TABLE dualslider (sliderID INT(11) AUTO_INCREMENT PRIMARY KEY, sliderSide VARCHAR(100) NOT NULL, sliderImage VARCHAR(100) NOT NULL, sliderOrder DECIMAL(1,0) NOT NULL)");
    $createTable->execute();
    $createTable->close();
}

class dualslider {

    protected $dbConnection;
    const ModuleDescription = 'Dual Slider Display. <br><br> Side bye Side Sliders displaying different slideshows.';
    const ModuleAuthor = 'Sunsetcoders Development Team.';
    const ModuleVersion = '0.1';
    
    function __construct($dbConnection) {

        $this->dbConnection = $dbConnection;
    }

    public function dualslider() {

        echo '<table border=1 cellpadding=10>';
        echo '<tr><td>Left</td><td>Right</td></tr>';
        echo '<tr><td></td><td></td></tr>';
        echo '<tr><td></td><td></td></tr>';
        echo '<tr><td></td><td></td></tr>';
        echo '<tr><td></td><td></td></tr>';
        echo '</table>';
    }

    public function editImage() {

        $contentCode = filter_input(INPUT_GET, "ImageID");

        $query = "SELECT $contentCode FROM dualslider WHERE sliderID=? ";

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

        $stmt = $this->dbConnection->prepare("UPDATE dualslider SET $contentCode=? WHERE sliderID=?");
        $stmt->bind_param('s', $contentImageName);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Image Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=dualslider">';
    }

    public function editContent() {

        $contentCode = filter_input(INPUT_GET, "ContentID");

        $query = "SELECT $contentCode FROM services WHERE serviceID=1 ";

        echo '<form method="POST" action="?id=Services&&moduleID=UpdateContent">';
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

    public function updateContent() {

        $contentDescription = filter_input(INPUT_POST, 'contentMatter');
        $contentCode = filter_input(INPUT_POST, 'contentCode');

        $stmt = $this->dbConnection->prepare("UPDATE services SET $contentCode=? WHERE serviceID=1");
        $stmt->bind_param('s', $contentDescription);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=dualslider">';
    }

    public function callToFunction() {
?>
        <script>
            $("#slideshow > div:gt(0)").hide();

            setInterval(function () {
                $('#slideshow > div:first')
                .fadeOut(1000)
                .next()
                .fadeIn(1000)
                .end()
                .appendTo('#slideshow');
                }, 3000);
            $("#slideshow1 > div:gt(0)").hide();

            setInterval(function () {
                $('#slideshow1 > div:first')
                .fadeOut(1000)
                .next()
                .fadeIn(1000)
                .end()
                .appendTo('#slideshow1');
                }, 3000);
        </script>

        <div id="slider-content">
            <div class="body-content"> 
            <a name="The Studios"></a>
                <div class="slider-cover"><img src="Images/slidercover.png" width="100%"></div>
                <div class="slider-left">
                    <div id="slideshow">

                        <?php
                        $leftRef = $this->dbConnection->prepare("SELECT sliderImage FROM dualslider WHERE sliderSide='Left' ORDER BY sliderOrder");
                        $leftRef->execute();

                        $leftRef->bind_result($sliderImage);

                        while ($checkRow = $leftRef->fetch()) {

                            echo '<div><img src="Images/' . $sliderImage . '" width=100%></div>';
                        }
                        $leftRef->close();
                        ?>
                    </div>
                </div>
                <div class="slider-right">
                    <div id="slideshow1">

                        <?php
                        $rightRef = $this->dbConnection->prepare("SELECT sliderImage FROM dualslider WHERE sliderSide='Right' ORDER BY sliderOrder");
                        $rightRef->execute();

                        $rightRef->bind_result($sliderImage);

                        while ($checkRow = $rightRef->fetch()) {

                            echo '<div><img src="Images/' . $sliderImage . '" width=100%></div>';
                        }
                        ?>
                    </div>
                </div>
            </div> 
        </div>
        <?php
    }

}
?>

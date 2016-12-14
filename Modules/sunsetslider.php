<?php
/**
 * Flatbed slider page.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 * @version         1.1.0               2016-12-14 16:51:36 SM:  Uses SunLibraryModule.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

class flatbedslider extends SunLibraryModule
{
    function __construct(mysqli $dbConnection)
    {
        parent::__construct($dbConnection);
    }

    public function flatbedslider()
    {
?>
        <div id="faderTable">
            <div id="slider">
                <?php
                $stmt = $this->objDB->prepare("SELECT imageToSlide FROM flatbedslider");
                $stmt->execute();
                $stmt->bind_result($imageToSlide);

                while ($checkRow = $stmt->fetch()) {

                    echo '<figure><img class="sliderImageSize" src="Images/' . $imgToSlide . '" ></figure>';
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function uploadFile() {

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

        $stmt = $this->objDB->prepare("UPDATE teampanel SET $contentCode=? WHERE teampanelID=1");
        $stmt->bind_param('s', $contentImageName);

        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Image Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=Team">';
    }

    public function callToFunction() {
        ?>
        <div id="faderTable">
            <div id="slider">

        <?php
        $stmt = $this->objDB->prepare("SELECT pageID, pageName, pagePublish FROM pages");
        $stmt->execute();

        $stmt->bind_result($pageID, $pageName, $pagePublish);

        while ($checkRow = $stmt->fetch()) {

            echo '<figure><img class="sliderImageSize" src="' . $upload_dir['baseurl'] . '/' . $row->imageName . '" ></figure>';
        }
        ?>
            </div>
        </div>
        <?php
    }

    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}
?>

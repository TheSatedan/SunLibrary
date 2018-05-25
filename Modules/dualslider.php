<?php
/**
 * Dual slider module.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:20:27 SM:  Uses database.
 * @version         1.1.0               2016-12-15 14:56:07 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2018-05-25 14:38:00 CST SM:  PSR fixes and bug where a variable was not defined in this class.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * Class dualslider
 */
class dualslider extends SunLibraryModule
{
    /**
     * dualslider constructor.
     * @param mysqli $dbConnection
     */
    function __construct(\mysqli $dbConnection)
    {
        parent::__construct($dbConnection);
    }

    /**
     * Render the dual slider.
     */
    public function dualslider():void
    {
        ?>
        <table border="1" cellpadding="10">
            <tbody>
            <tr>
                <td>Left</td>
                <td>Right</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * Render image edit.
     */
    public function editImage(): void
    {
        $contentCode = filter_input(INPUT_GET, "ImageID");
        $query = "SELECT $contentCode FROM dualslider WHERE sliderID=? ";
        ?>
        <form action="?id=team&&moduleID=UpdateImage" method="post" enctype="multipart/form-data">
            <input type="hidden" name="contentCode" value="<?= $contentCode; ?>">
            <?php
            if ($stmt = $this->objDB->prepare($query)) {
                $stmt->execute();
                $stmt->bind_result($contentCode);
                $stmt->fetch();
                ?>
                <table border="0" cellpadding="20">
                    <tbody>
                    <tr>
                        <td>
                            <h1>Image Information: </h1>
                        </td>
                    </tr>
                    ';
                    <tr>
                        <td>
                            <img src="../Images/<?= $contentCode; ?>">
                        </td>
                    </tr>
                    ';
                    <tr>
                        <td>
                            <input type="hidden" name="MAX_FILE_SIZE" value="100000"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Choose a replacement image to upload: <br> <input type="file" name="fileToUpload"
                                                                              id="fileToUpload">
                        </td>
                    </tr>
                    ';
                    <tr>
                        <td>
                            <input type="submit" name="submit" value="Update">
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </form>
        <?php
    }

    /**
     * Update image.
     * @return void
     */
    public function updateImage(): void
    {
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

        $stmt = $this->objDB->prepare("UPDATE dualslider SET $contentCode=? WHERE sliderID=?");
        $stmt->bind_param('s', $contentImageName);

        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Image Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=dualslider">';
    }

    /**
     * Edit content render
     * @return void
     */
    public function editContent(): void
    {
        $contentCode = filter_input(INPUT_GET, "ContentID");

        $query = "SELECT $contentCode FROM services WHERE serviceID=1 ";

        echo '<form method="POST" action="?id=Services&&moduleID=UpdateContent">';
        echo '<input type="hidden" name="contentCode" value="' . $contentCode . '">';

        if ($stmt = $this->objDB->prepare($query)) {

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

    /**
     * Update content render.
     * @return void
     */
    public function updateContent(): void
    {

        $contentDescription = filter_input(INPUT_POST, 'contentMatter');
        $contentCode = filter_input(INPUT_POST, 'contentCode');

        $stmt = $this->dbConnection->prepare("UPDATE services SET $contentCode=? WHERE serviceID=1");
        $stmt->bind_param('s', $contentDescription);

        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=dualslider">';
    }

    /**
     * Entry point.
     * @return void
     */
    public function callToFunction(): void
    {
        ?>
        <div id="slider-content">
            <div class="body-content">
                <a name="The Studios"></a>
                <div class="slider-cover"><img src="Images/slidercover.png" width="100%"></div>
                <div class="slider-left">
                    <div id="slideshow">

                        <?php
                        $leftRef = $this->objDB->prepare("SELECT sliderImage FROM dualslider WHERE sliderSide='Left' ORDER BY sliderOrder");
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
                        $rightRef = $this->objDB->prepare("SELECT sliderImage FROM dualslider WHERE sliderSide='Right' ORDER BY sliderOrder");
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

    /**
     * Assets that the dualslider table exists.
     * @return void
     */
    protected function assertTablesExist(): void
    {
        $objResult = $this->objDB->query('select 1 from `dualslider` LIMIT 1');
        if ($objResult === false) {
            $createTable = $this->objDB->prepare("CREATE TABLE dualslider (sliderID INT(11) AUTO_INCREMENT PRIMARY KEY, sliderSide VARCHAR(100) NOT NULL, sliderImage VARCHAR(100) NOT NULL, sliderOrder DECIMAL(1,0) NOT NULL)");
            $createTable->execute();
            $createTable->close();
        } else {
            $objResult->free();
        }
    }

    /**
     * Renders the Document ready init JS code for the slider.
     * @return void
     */
    public function documentReadyJavaScript(): void
    {
        ?>
        $("#slideshow > div:gt(0)").hide();
        setInterval(function ()
        {
        $('#slideshow > div:first')
        .fadeOut(1000)
        .next()
        .fadeIn(1000)
        .end()
        .appendTo('#slideshow');
        },3000);

        $("#slideshow1 > div:gt(0)").hide();
        setInterval(function ()
        {
        $('#slideshow1 > div:first')
        .fadeOut(1000)
        .next()
        .fadeIn(1000)
        .end()
        .appendTo('#slideshow1');
        },3000);
        <?php
    }

    /**
     * @return string The most recent version number from this particular file.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}

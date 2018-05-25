<?php
/**
 * Terms and conditions page.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:33:19 SM:  Uses database.
 * @version         1.1.0               2016-12-14 16:46:32 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 14:07:53 SM:  Fixed some tags, adjusted indentation, fixed bad DB refs, added doco.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * Terms and conditions
 */
class termsConditions extends SunLibraryModule
{
    /**
     * {@inheritdoc}
     *
     * @param mysqli $dbConnection Connection to the database.
     * @return void
     */
    function __construct(mysqli $dbConnection)
    {
        parent::__construct($dbConnection);
    }

    /**
     * Render terms and conditions.  THIS IS NOT THE CONSTRUCTOR.
     *
     * @return void
     */
    public function termsConditions()
    {
        $contentCode = filter_input(INPUT_GET, "ContentID");
        $query = "SELECT $contentCode FROM terms WHERE termID=1 ";
        ?>
        <form method="POST" action="?id=Terms&&moduleID=UpdateContent">
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
                            <h1>Content: </h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <textarea cols=100 rows=10 name="contentMatter"><?= $contentCode; ?></textarea>
                        </td>
                    </tr>
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
     * Edit image
     *
     * @return void
     */
    public function editImage()
    {
        $contentCode = filter_input(INPUT_GET, "ContentID");
        $query = "SELECT $contentCode FROM terms WHERE termID=1 ";
        ?>
        <form action="?id=team&&moduleID=UpdateImage" method="post" enctype="multipart/form-data">
            <input type="hidden" name="contentCode" value="<?= $contentCode; ?>">
            <input type="hidden" name="MAX_FILE_SIZE" value="100000">
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
                    <tr>
                        <td>
                            <img src="../Images/<?= $contentCode; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Choose a replacement image to upload: <br> <input type="file" name="fileToUpload"
                                                                              id="fileToUpload">
                        </td>
                    </tr>
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
     * Update image
     *
     * @return void
     */
    public function updateImage()
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

        $stmt = $this->objDB->prepare("UPDATE teampanel SET $contentCode=? WHERE teampanelID=1");
        $stmt->bind_param('s', $contentImageName);

        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        ?>
        <font color="black">
            <b>Content Image Information Updated <br><br> Please Wait!!!!<br>
                <meta http-equiv="refresh" content="1;url=?id=Team">
        </font>
        <?php
    }

    /**
     * Update the content
     *
     * @return void
     */
    public function updateContent()
    {
        $contentDescription = filter_input(INPUT_POST, 'contentMatter');
        $contentCode = filter_input(INPUT_POST, 'contentCode');
        $stmt = $this->objDB->prepare("UPDATE terms SET $contentCode=? WHERE termsID=1");
        $stmt->bind_param('s', $contentDescription);
        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }
        $status = $stmt->execute();
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        ?>
        <font color="black">
            <b>Content Information Updated <br><br> Please Wait!!!!<br>
                <meta http-equiv="refresh" content="1;url=?id=terms">
        </font>
        <?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function callToFunction()
    {
        if ($stmt = $this->objDB->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($contentCode);
            $stmt->fetch();
            ?>
            <div class="terms-background">
                <div class="body-content">
                    <?= nl2br($contentCode); ?>
                </div>
            </div>
            <?php
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function assertTablesExist()
    {
        $objResult = $this->objDB->query('select 1 from `terms` LIMIT 1');
        if ($objResult === false) {
            $createTable = $this->objDB->prepare("CREATE TABLE terms (termID INT(11) AUTO_INCREMENT PRIMARY KEY, termsContent VARCHAR(20000) NOT NULL)");
            $createTable->execute();
            $createTable->close();
        } else {
            $objResult->free();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string Full version number as read from the file's docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}

?>

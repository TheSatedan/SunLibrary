<?php
/**
 * Gallery - version 1.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @todo            Class uses a global variable for the DB, but also takes a DB connection in contructor args.  Should probably just use the constructor arg.
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype.
 * @version         1.0.1               2016-12-13 16:25:11 SM:  Uses database.
 * @version         1.1.0               2016-12-15 08:42:00 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 16:23:48 SM:  Fixed indents, minor spelling mistake fix, added doco.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * Gallery module.
 */
class gallery extends SunLibraryModule
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
     * Add image
     *
     * @return void
     */
    public function addimage()
    {
        ?>
        <form action="web-settings.php?id=Gallery&&moduleID=UploadImage" method="post" enctype="multipart/form-data">
            <table>
                <tbody>
                <tr>
                    <td>Add Image</td>
                </tr>
                <tr>
                    <td>
                        Select image to upload: <input type="file" name="fileToUpload" id="fileToUpload">
                    </td>
                </tr>
                <tr>
                    <td>Add Supporter</td>
                </tr>
                <tr>
                    <td>Add Supporter</td>
                <tr>
                    <td>Add Supporter</td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="Upload Image" name="submit">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php
    }

    /**
     * Upload image.
     *
     * @return void
     */
    public function uploadImage()
    {
        $target_dir = "../Images/kidscorner/";
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
        } // if everything is ok, try to upload file
        else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    /**
     * Edit Image
     *
     * @return void
     */
    public function editImage()
    {
        //
    }

    /**
     * update image
     *
     * @return void
     */
    public function updateImage()
    {
        //
    }

    /**
     * Render the gallery.
     *
     * @return void
     */
    public function gallery()
    {
        $x = 1;
        ?>
        <table cellpadding="5">
            <tbody>
            <tr>
                <td>
                    Gallery Images <a href="web-settings.php?id=Gallery&&moduleID=AddImage">
                        <button>Add Gallery Image</button>
                    </a>
                </td>
            </tr>
            <tr>
                <td height="70"></td>
            </tr>
            ';
            <tr>
                <?php
                if ($handle = opendir('../Images/kidscorner/'))
                {
                while (false !== ($entry = readdir($handle)))
                {
                if ($entry != "." && $entry != "..")
                {
                if ($x >= 5)
                {
                ?>
            </tr>
            <tr>
                <?php
                $x = 1;
                }
                ?>
                <td><img src="../Images/kidscorner/<?= $entry; ?>" width="250"> &nbsp;</td>
                <?php
                $x++;
                }
                }
                closedir($handle);
                }
                ?>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * {@inheritdoc}
     *
     * @return string The full version for this module, as read form the file's docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}

?>

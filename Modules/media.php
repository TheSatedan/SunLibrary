<?php
/**
 * Media module.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:26:23 SM:  Uses database.
 * @version         1.1.0               2016-12-15 08:38:08 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 16:13:17 SM:  Added documentation, tidied indentation.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * Specials module.
 */
class specials extends SunLibraryModule
{
    /** @var string $setPostID Module ID sent via $_POST. */
    private $setPostID;
    /** @var string $setGetID Module ID sent via $_GET. */
    private $setGetID;

    /**
     * {@inheritdoc}
     *
     * @param mysqli $dbConnection Connection to the database.
     * @retur void
     */
    function __construct(mysqli $dbConnection)
    {
        $this->setPostID = filter_input(INPUT_POST, 'moduleID');
        $this->setGetID = filter_input(INPUT_GET, 'moduleID');
        parent::__construct($dbConnection);
    }

    /**
     * Show media files.
     *
     * @return void
     */
    public function showMediaFiles()
    {
        // SM:  Used to split rows in the table.
        $x = 2;
        ?>
        <table width="100%" border="1">
            <tbody>
            <tr>
                <td class="sidebar-header">
                    Insert Media
                </td>
            </tr>
            <tr>
                <td>
                    <a href="web-settings.php?id=Media&&moduleID=AddMedia">Upload Media</a>
                    <a href="web-settings.php?id=Media">Media Library</a>
                </td>
            </tr>
            <tr>
                <td width=70%>
                    <table>
                        <tbody>
                        <tr>
                            <?php
                            if ($handle = opendir('../Images'))
                            {
                            while (false !== ($entry = readdir($handle)))
                            {
                            if ($entry != "." && $entry != "..")
                            {
                            ?>
                            <td>
                                <a href="web-settings.php?id=Media&&moduleID=showMedia&&mediaName=<?= $entry; ?>"><img
                                            class="mediaImages" src="../Images/<?= $entry; ?>" height="75">
                            </td>
                            <?php
                            if ($x >= 5)
                            {
                            ?>
                        </tr>
                        <tr>
                            <?php
                            $x = 1;
                            }
                            $x++;
                            }
                            }
                            closedir($handle);
                            }
                            ?>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * Delete media.
     *
     * @return void
     */
    private function deleteMedia()
    {
        $getImageName = filter_input(INPUT_GET, 'mediaName');
        unlink('../Images/' . $getImageName);
        ?>
        <font color="black">
            <b>Please Wait!!!!</b><br>
            <meta http-equiv="refresh" content="1;url=web-settings.php?id=Media">
        </font>
        <?php
    }

    /**
     * Show media
     *
     * @return void
     */
    public function showMedia()
    {
        $getMediaName = filter_input(INPUT_GET, 'mediaName');
        ?>
        <table width="100%" border="1">
            <tbody>
            <tr>
                <td rowspan="2">
                    <img src="../Images/<?= $getMediaName; ?>">
                </td>
                <td>
                    <?php
                    list($width, $height, $type, $attr) = getimagesize("../Images/$getMediaName");
                    $imgsize = filesize("../Images/$getMediaName");
                    $file_size = $imgsize / 1024;
                    ?>
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                Image Details:
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Width:
                            </td>
                            <td>
                                <?= $width; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Height:
                            </td>
                            <td>
                                <?= $height; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Size:
                            </td>
                            <td>
                                <?= number_format($file_size, 2); ?>k
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <a class="redFont"
                       href="web-settings.php?id=Media&&moduleID=DeleteMedia&&mediaName=<?= $getMediaName; ?>">Delete
                        Image</a>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * Upload media
     *
     * @return void
     */
    private function uploadMedia()
    {
        $target_dir = "../Images/";
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
        ?>
        <font color="black">
            <b>Please Wait!!!!</b><br>
            <meta http-equiv="refresh" content="1;url=web-settings.php?id=Media">
        </font>
        <?php
    }

    /**
     * Add media
     *
     * @return void
     */
    public function addMedia()
    {
        ?>
        <form method="POST" action="web-settings.php?id=Media&&moduleID=UploadMedia" enctype="multipart/form-data">
            <table>
                <tbody>
                <tr>
                    <td>
                        Upload Image:
                    </td>
                </tr>
                <tr>
                    <td height="25">
                        <input type="file" name="fileToUpload" id="fileToUpload">
                    </td>
                </tr>
                ';
                <tr>
                    <td>
                        <input type="submit" name="submit" value="Upload">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function switchMode()
    {
        $localAction = null;
        if (isset ($this->setPostID)) {
            $localAction = $this->setPostID;
        } elseif (isset ($this->setGetID)) {
            $localAction = urldecode($this->setGetID);
        }

        switch (strtoupper($localAction)) {
            case "DELETEMEDIA":
                $this->deleteMedia();
                break;
            case "UPLOADMEDIA":
                $this->uploadMedia();
                break;
            case "ADDMEDIA":
                $this->addMedia();
                break;
            case "SHOWMEDIA":
                $this->showMedia();
                break;
            default:
                $this->showMediaFiles();
                break;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string The full version for this file as read from the docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}

?>

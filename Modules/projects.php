<?php
/**
 * Projects module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM: Prototype
 * @version         1.0.1               2016-12-13 16:28:38 SM: Uses database.
 */
try
{
    $dbTriConnection=Database::GetDBConnection();
}
catch(Exception $objException)
{
    die($objException);
}
$val = mysqli_query($dbTriConnection, 'select 1 from `projects` LIMIT 1');

if ($val !== FALSE) {

} else {

    $createTable = $dbTriConnection->prepare("CREATE TABLE projects (projectID INT(11) AUTO_INCREMENT PRIMARY KEY, projectSide VARCHAR(20) NOT NULL, projectContent VARCHAR(200) NOT NULL, projectOrder DECIMAL(1,0) NOT NULL)");
    $createTable->execute();
    $createTable->close();
}

class projects {

    protected $dbConnection;

    function __construct(mysqli $dbConnection) {

        $this->dbConnection = $dbConnection;
    }

    public function projects() {

        echo '<table cellpadding=10 cellspacing=0 width=50%>';
        echo '<tr><td colspan=3><h2>Past Projects</h2></td></tr>';
        echo '<tr><td colspan=3></td></tr>';
        echo '<tr><td colspan=3 bgcolor="262626"><font color="white">Music Studio</td></tr>';
        echo '<tr><td colspan=3><button><a href="?id=Projects&&moduleID=AddMusic">Add Music</a></button></td></tr>';

        $leftRef = $this->dbConnection->prepare("SELECT projectContent FROM projects WHERE projectSide='Left' ORDER BY projectOrder");
        $leftRef->execute();

        $leftRef->bind_result($projectContent);

        while ($checkRow = $leftRef->fetch()) {

            echo '<tr><td bgcolor="white">' . $projectContent . '</td><td bgcolor="white"><a href="#">edit</a></td><td bgcolor="white"><a class="sunsetLink" href="#">delete</a></td></tr>';
        }
        $leftRef->close();

        echo '<tr><td colspan=3></td></tr>';                
        echo '<tr><td colspan=3 bgcolor="262626"><font color="white">Art Studio</td></tr>';
        echo '<tr><td colspan=3><button><a href="?id=Projects&&moduleID=AddImage">Add Artwork</a></button></td></tr>';

        $rightRef = $this->dbConnection->prepare("SELECT projectID, projectContent FROM projects WHERE projectSide='Right' ORDER BY projectOrder");
        $rightRef->execute();

        $rightRef->bind_result($projectID, $projectContent);

        while ($checkRow = $rightRef->fetch()) {

            echo '<tr><td bgcolor="white"><a class="thumbnail" href="#thumb">' . $projectContent . '<span><img src="../../Projects/'.$projectContent.'"></span></a></td><td bgcolor="white"><a href="?id=Projects&&moduleID=editImage&&ImageID='.$projectID.'">edit</a></td><td bgcolor="white"><a class="sunsetLink" href="#">delete</a></td></tr>';
        }
        $rightRef->close();

        echo '</table>';

    }

    public function addImage()
    {
                echo '<form action="?id=Projects&&moduleID=UploadImage" method="post" enctype="multipart/form-data">';

        echo '<table border=0 cellpadding=10>';
        echo '<tr><td><h2>Upload Past Artwork</h2></td></tr>';
        echo '<tr><td><input type="hidden" name="MAX_FILE_SIZE" value="100000" /></td></tr>';
        echo '<tr><td>Choose an image to upload: <br> <input type="file" name="fileToUpload" id="fileToUpload"></td></tr>';
        echo '<tr><td><input type="submit" name="submit" value="Upload"></td></tr>';
        echo '</table>';
        echo '</form>';
    }   

    public function uploadImage()
    {
        $target_dir = "../Projects/";
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

        $contentImageName = $target_filename;
        $contentCode = filter_input(INPUT_POST, 'contentCode');

        $stmt = $this->dbConnection->prepare("INSERT INTO projects (projectSide, projectContent, projectOrder) VALUES ('Right', '$contentImageName', '1')");
  
        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Image Information Uploaded <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=Projects">';

    }

    public function editImage() {

        $contentCode = filter_input(INPUT_GET, "ImageID");

        $query = "SELECT projectContent FROM projects WHERE projectID=? ";

        echo '<form action="?id=Projects&&moduleID=UpdateImage" method="post" enctype="multipart/form-data">';
        echo '<input type="hidden" name="contentCode" value="' . $contentCode . '">';

        if ($stmt = $this->dbConnection->prepare($query)) {

            $stmt->bind_param('i', $contentCode);
            $stmt->execute();
            $stmt->bind_result($projectContent);
            $stmt->fetch();

            echo '<table border=0 cellpadding=20>';
            echo '<tr><td><h2>Image Information: </h2></td></tr>';
            echo '<tr><td><img src="../Images/' . $projectContent . '"></td></tr>';
            echo '<tr><td><input type="hidden" name="MAX_FILE_SIZE" value="100000" /></td></tr>';
            echo '<tr><td>Choose a replacement image to upload: <br> <input type="file" name="fileToUpload" id="fileToUpload"></td></tr>';
            echo '<tr><td><input type="submit" name="submit" value="Update"><button><a href="?id=Projects">Cancel</a></button></td></tr>';
        }
        echo '</form>';
    }

    public function updateImage() {

        $target_dir = "../Projects/";
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

        $stmt = $this->dbConnection->prepare("UPDATE projects SET $contentCode=? WHERE projectID=1");
        $stmt->bind_param('s', $contentImageName);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Image Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=Projects">';
    }


    public function callToFunction() {
?>
        <div id="project-content">
            <div class="body-content"> 
                <a name="Past Projects"></a>
                <div class="project-left">


                    <?php
                    $leftRef = $this->dbConnection->prepare("SELECT projectContent FROM projects WHERE projectSide='Left' ORDER BY projectOrder");
                    $leftRef->execute();

                    $leftRef->bind_result($projectContent);

                    while ($checkRow = $leftRef->fetch()) {

                    ?>
                        <div><?php echo $projectContent; ?></div>
                        <div>
                            <audio controls>
                                <source src="Projects/<?php echo $projectContent; ?>" type="audio/mpeg">
                                Your browser does not support the audio element.';
                            </audio>
                        </div>
                    <?php
                    }
                    $leftRef->close();
                    ?>

                </div>
                <script>
                    $("#projectSlideshow > div:gt(0)").hide();

                    setInterval(function () {
                        $('#projectSlideshow > div:first')
                        .fadeOut(1000)
                        .next()
                        .fadeIn(1000)
                        .end()
                        .appendTo('#projectSlideshow');
                        }, 3000);
                </script>
                <div class="project-right">
                    <div id="projectSlideshow">
                        <?php
                        $rightRef = $this->dbConnection->prepare("SELECT projectContent FROM projects WHERE projectSide='Right' ORDER BY projectOrder");
                        $rightRef->execute();

                        $rightRef->bind_result($sliderImage);

                        while ($checkRow = $rightRef->fetch()) {

                            echo '<div><img src="Projects/' . $sliderImage . '" width=100%></div>';
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

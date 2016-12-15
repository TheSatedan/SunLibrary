<?php
/**
 * Cookbook Display Form Information. Adding, Deleting and editing Recipes, themes and styles.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:18:33 SM:  Uses database.
 * @version         1.1.0               2016-12-15 15:06:52 SM:  Now uses SunLibraryModule.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

class cookbook extends SunLibraryModule
{
    function __construct(mysqli $dbConnection)
    {
        parent::__construct($dbConnection);
    }

    public function cookbook() {
        
        echo '<b>Family Cookbook</b> <a href="?id=Cookbook&&moduleID=AddRecipe"><button>Add New</button></a><br><br><br>';
        echo '<table width=100% cellpadding=10 cellspacing=0 border=0>';
        echo '<tr class="tableTop"><td>Recipe Name</td><td width=260>Recipe Uploader</td></tr>';
        echo '<tr><td colspan=2>&nbsp;</td></tr>';
        $stmt = $this->objDB->prepare("SELECT cookbookID, cookbookName, userFullName FROM cookbook INNER JOIN users ON users.userID=cookbook.userID");
        $stmt->execute();

        $stmt->bind_result($cookbookID, $cookbookName, $userFullName);

        while ($checkRow = $stmt->fetch()) {

            echo '<tr class="rowRef"><td><a href="?id=Cookbook&&moduleID=editRecipe&&cookbookID=' . $cookbookID . '">' . $cookbookName . '</a></td><td>'.$userFullName.'</td></tr>';
        }
        echo '</table>';
    }

    public function updateCookBook() {
        $cookbookID = filter_input(INPUT_POST, 'cookbookID');
        $cookbookName = filter_input(INPUT_POST, 'cookbookName');

        $cookbookIngredient1 = filter_input(INPUT_POST, 'cookbookIngredient1');
        $cookbookIngredient2 = filter_input(INPUT_POST, 'cookbookIngredient2');
        $cookbookIngredient3 = filter_input(INPUT_POST, 'cookbookIngredient3');
        $cookbookIngredient4 = filter_input(INPUT_POST, 'cookbookIngredient4');
        $cookbookIngredient5 = filter_input(INPUT_POST, 'cookbookIngredient5');
        $cookbookIngredient6 = filter_input(INPUT_POST, 'cookbookIngredient6');
        $cookbookIngredient7 = filter_input(INPUT_POST, 'cookbookIngredient7');
        $cookbookIngredient8 = filter_input(INPUT_POST, 'cookbookIngredient8');
        $cookbookIngredient9 = filter_input(INPUT_POST, 'cookbookIngredient9');
        $cookbookIngredient10 = filter_input(INPUT_POST, 'cookbookIngredient10');
        $cookbookIngredient11 = filter_input(INPUT_POST, 'cookbookIngredient11');
        $cookbookDescription = filter_input(INPUT_POST, 'area2');

        if (basename($_FILES["fileToUpload"]["name"])) {
            $target_dir = "../Images/CookBook/";
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
            $cookbookImage = basename($_FILES["fileToUpload"]["name"]);
            $query = "UPDATE cookbook SET cookbookName=?, cookbookImage=?, cookbookIngredient1=?, cookbookIngredient2=?, cookbookIngredient3=?, cookbookIngredient4=?, cookbookIngredient5=?, cookbookIngredient6=?, cookbookIngredient7=?, cookbookIngredient8=?, cookbookIngredient9=?, cookbookIngredient10=?, cookbookIngredient11=?, cookbookDescription=? WHERE cookbookID=?";
            $parm = bind_param('ssssssssssssssi', $cookbookName, $cookbookImage, $cookbookIngredient1, $cookbookIngredient2, $cookbookIngredient3, $cookbookIngredient4, $cookbookIngredient5, $cookbookIngredient6, $cookbookIngredient7, $cookbookIngredient8, $cookbookIngredient9, $cookbookIngredient10, $cookbookIngredient11, $cookbookDescription, $cookbookID);
        } else {
            $parm = 2;
            $query = "UPDATE cookbook SET cookbookName=?, cookbookIngredient1=?, cookbookIngredient2=?, cookbookIngredient3=?, cookbookIngredient4=?, cookbookIngredient5=?, cookbookIngredient6=?, cookbookIngredient7=?, cookbookIngredient8=?, cookbookIngredient9=?, cookbookIngredient10=?, cookbookIngredient11=?, cookbookDescription=? WHERE cookbookID=?";
        }

        $stmt = $this->objDB->prepare($query);

        if ($parm == 2)
            $stmt->bind_param('sssssssssssssi', $cookbookName, $cookbookIngredient1, $cookbookIngredient2, $cookbookIngredient3, $cookbookIngredient4, $cookbookIngredient5, $cookbookIngredient6, $cookbookIngredient7, $cookbookIngredient8, $cookbookIngredient9, $cookbookIngredient10, $cookbookIngredient11, $cookbookDescription, $cookbookID);

        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Recipe Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Cookbook">';
    }

    public function editRecipe() {
        $cookbookID = filter_input(INPUT_GET, 'cookbookID');

        if ($stmt = $this->objDB->prepare("SELECT cookbookName, cookbookImage, cookbookIngredient1, cookbookIngredient2, cookbookIngredient3, cookbookIngredient4, cookbookIngredient5, cookbookIngredient6, cookbookIngredient7, cookbookIngredient8, cookbookIngredient9, cookbookIngredient10, cookbookIngredient11, cookbookDescription FROM cookbook WHERE cookbookID=? ")) {

            $stmt->bind_param("i", $cookbookID);
            $stmt->execute();
            $stmt->bind_result($cookbookName, $cookbookImage, $cookbookIngredient1, $cookbookIngredient2, $cookbookIngredient3, $cookbookIngredient4, $cookbookIngredient5, $cookbookIngredient6, $cookbookIngredient7, $cookbookIngredient8, $cookbookIngredient9, $cookbookIngredient10, $cookbookIngredient11, $cookbookDescription);
            $stmt->fetch();

            echo '<form method="POST" action="web-settings.php?id=Cookbook&&moduleID=UpdateCookbook" enctype="multipart/form-data">';
            echo '<input type="hidden" name="cookbookID" value="' . $cookbookID . '">';
            echo '<table>';
            echo '<tr><td colspan=2><h1>Edit Recipe</h1></td><td rowspan=18 valign=top><br><br><br><br><br><br><br><img src="../Images/cookbook/' . $cookbookImage . '"><br><br><br><b>Please Select New Image</b><br><input type="file" name="fileToUpload" id="fileToUpload"></td></tr>';
            echo '<tr><td><b>Recipe Name: </td><td><input type="text" name="cookbookName" value="' . $cookbookName . '" size=80></td></tr>';

            echo '<tr><td><br><br>&nbsp;</td></tr>';

            echo '<tr><td><b>Ingredient1: </td><td><input type="text" name="cookbookIngredient1" value="' . $cookbookIngredient1 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient2: </td><td><input type="text" name="cookbookIngredient2" value="' . $cookbookIngredient2 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient3: </td><td><input type="text" name="cookbookIngredient3" value="' . $cookbookIngredient3 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient4: </td><td><input type="text" name="cookbookIngredient4" value="' . $cookbookIngredient4 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient5: </td><td><input type="text" name="cookbookIngredient5" value="' . $cookbookIngredient5 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient6: </td><td><input type="text" name="cookbookIngredient6" value="' . $cookbookIngredient6 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient7: </td><td><input type="text" name="cookbookIngredient7" value="' . $cookbookIngredient7 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient8: </td><td><input type="text" name="cookbookIngredient8" value="' . $cookbookIngredient8 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient9: </td><td><input type="text" name="cookbookIngredient9" value="' . $cookbookIngredient9 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient10: </td><td><input type="text" name="cookbookIngredient10" value="' . $cookbookIngredient10 . '" size=80></td></tr>';
            echo '<tr><td><b>Ingredient11: </td><td><input type="text" name="cookbookIngredient11" value="' . $cookbookIngredient11 . '" size=80></td></tr>';

            echo '<tr><td><br><br>&nbsp;</td></tr>';

            echo '<tr><td><b>Description: </td></tr>';
            echo '<tr><td colspan=2><textarea rows=10 name="area2" style="width: 740px; background-color: white;">' . $cookbookDescription . '</textarea></td></tr>';
            echo '<tr><td><input type="Submit" name="Submit" value="Update"></td></tr>';
            echo '</table>';
            echo '</form>';
            echo '<a href="?id=Cookbook&&moduleID=DeleteRecipe&&cookbookID='.$cookbookID.'"><button>Delete</button></a>';
        }
    }

    public function uploadResourceFile() {
        define("UPLOAD_DIR", "../files/");

        if (!empty($_FILES["myFile"])) {
            $myFile = $_FILES["myFile"];

            if ($myFile["error"] !== UPLOAD_ERR_OK) {
                echo "<p>An error occurred.</p>";
                exit;
            }

            // ensure a safe filename
            $name = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile["name"]);

            // don't overwrite an existing file
            $i = 0;
            $parts = pathinfo($name);
            while (file_exists(UPLOAD_DIR . $name)) {
                $i++;
                $name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
            }

            // preserve file from temporary directory
            $success = move_uploaded_file($myFile["tmp_name"], UPLOAD_DIR . $name);

            if (!$success) {
                echo "<p>Unable to save file.</p>";
                exit;
            }

            // set proper permissions on the new file
            chmod(UPLOAD_DIR . $name, 0644);
        }
        echo 'You have successfully added a new File. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=CookBook">';
    }

    public function addRecipe() {
        echo '<form method="POST" action="?id=CookBook&&moduleID=UploadRecipe" enctype="multipart/form-data">';
        echo '<table border=0 cellpadding=5 cellspacing=0 width=100%>';
        echo '<tr><td colspan=3><b>Add New Recipe</b></td></tr>';
        echo '<tr><td colspan=3></td></tr>';
        echo '<tr><td colspan=2><b>Recipe Name</b></td><td rowspan=18 valign=top><input type="file" name="fileToUpload" id="fileToUpload"></td></tr>';
        echo '<tr><td colspan=2><input type="text" name="cookbookName" size="50"></td></tr>';

        echo '<tr><td colspan=2><b>Ingredients</b></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient1" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient2" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient3" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient4" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient5" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient6" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient7" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient8" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient9" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient10" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient11" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient12" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient13" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient14" size="70"></td></tr>';
        echo '<tr><td>Ingredient </td><td><input type="text" name="cookbookIngredient15" size="70"></td></tr>';

        echo '<tr><td colspan=3><b>Description</b></td></tr>';
        echo '<tr><td colspan=3><textarea name="cookbookDescription" style="width: 940px; height: 400px; background-color: white;"></textarea></td></tr>';
        echo '<tr><td colspan=3><input type="submit" name="submit" value="Add Recipe"></td></tr>';
        echo '</table>';
    }

    public function uploadRecipe() {
        
        $cookbookName = filter_input(INPUT_POST, 'cookbookName');

        $cookbookIngredient1 = filter_input(INPUT_POST, 'cookbookIngredient1');
        $cookbookIngredient2 = filter_input(INPUT_POST, 'cookbookIngredient2');
        $cookbookIngredient3 = filter_input(INPUT_POST, 'cookbookIngredient3');
        $cookbookIngredient4 = filter_input(INPUT_POST, 'cookbookIngredient4');
        $cookbookIngredient5 = filter_input(INPUT_POST, 'cookbookIngredient5');
        $cookbookIngredient6 = filter_input(INPUT_POST, 'cookbookIngredient6');
        $cookbookIngredient7 = filter_input(INPUT_POST, 'cookbookIngredient7');
        $cookbookIngredient8 = filter_input(INPUT_POST, 'cookbookIngredient8');
        $cookbookIngredient9 = filter_input(INPUT_POST, 'cookbookIngredient9');
        $cookbookIngredient10 = filter_input(INPUT_POST, 'cookbookIngredient10');
        $cookbookIngredient11 = filter_input(INPUT_POST, 'cookbookIngredient11');
        $cookbookIngredient12 = filter_input(INPUT_POST, 'cookbookIngredient12');
        $cookbookIngredient13 = filter_input(INPUT_POST, 'cookbookIngredient13');
        $cookbookIngredient14 = filter_input(INPUT_POST, 'cookbookIngredient14');
        $cookbookIngredient15 = filter_input(INPUT_POST, 'cookbookIngredient15');
        $cookbookDescription = filter_input(INPUT_POST, 'cookbookDescription');

        $target_dir = "../Images/cookbook/";
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

        $userID = $_SESSION['userID'];
        
        $cookbookImage = basename($_FILES["fileToUpload"]["name"]);

        $stmt = $this->objDB->prepare("INSERT INTO cookbook
        (
            userID, 
			cookbookName,
			cookbookImage,
			cookbookDescription,
			cookbookIngredient1,
			cookbookIngredient2,
			cookbookIngredient3,
			cookbookIngredient4,
			cookbookIngredient5,
			cookbookIngredient6,
			cookbookIngredient7,
			cookbookIngredient8,
			cookbookIngredient9,
			cookbookIngredient10,
			cookbookIngredient11,
			cookbookIngredient12,
			cookbookIngredient13,
			cookbookIngredient14,
			cookbookIngredient15				
        )
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param('issssssssssssssssss', $userID, $cookbookName, $cookbookImage, $cookbookDescription, $cookbookIngredient1, $cookbookIngredient2, $cookbookIngredient3, $cookbookIngredient4, $cookbookIngredient5, $cookbookIngredient6, $cookbookIngredient7, $cookbookIngredient8, $cookbookIngredient9, $cookbookIngredient10, $cookbookIngredient11, $cookbookIngredient12, $cookbookIngredient13, $cookbookIngredient14, $cookbookIngredient15);

        $status = $stmt->execute();

        echo 'You have successfully added a new Recipe. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Cookbook">';
    }

    public function updateRecipe() {
        $resourceName = filter_input(INPUT_POST, 'resourceName');
        $resourceLink = filter_input(INPUT_POST, 'resourceLink');
        $cookbookID = filter_input(INPUT_POST, 'cookbookID');

        $stmt = $this->objDB->prepare("UPDATE cookbook SET resourceName=?, resourceLink=? WHERE cookbookID=?");
        $stmt->bind_param('ssi', $resourceName, $resourceLink, $cookbookID);

        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Recipe Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Cookbook">';
    }

    public function deleteRecipe() {
        
        $getID = filter_input(INPUT_GET, 'cookbookID');

        $stmt = $this->objDB->prepare("DELETE FROM cookbook WHERE cookbookID = ?");
        $stmt->bind_param('i', $getID);
        $stmt->execute();
        $stmt->close();

        echo 'You have successfully deleted a Recipe. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Cookbook">';
    }

    protected function assertTablesExist()
    {
        $objResult=$this->objDB->query('select 1 from `cookbook` LIMIT 1');
        if ($objResult===false)
        {
            $createTable = $this->objDB->prepare("CREATE TABLE cookbook (cookbookID INT(11) AUTO_INCREMENT PRIMARY KEY, "
                . "userID INT(11) NOT NULL, "
                . "cookbookName VARCHAR(150) NOT NULL, "
                . "cookbookPrice VARCHAR(100) NOT NULL, "
                . "cookbookImage VARCHAR(255) NOT NULL, "
                . "cookbookDescription VARCHAR(5000) NOT NULL, "
                . "cookbookIngredient1 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient2 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient3 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient4 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient5 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient6 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient7 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient8 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient9 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient10 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient11 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient12 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient13 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient14 VARCHAR(255) NOT NULL, "
                . "cookbookIngredient15 VARCHAR(255) NOT NULL ) ");
            $createTable->execute();
            $createTable->close();
        }
        else
            $objResult->free();
    }

    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }    
}
?>

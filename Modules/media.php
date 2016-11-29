<?php
/**
 * Media module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

try
{
    $dbConnection = databaseConnection();
}
catch(Exception $objException)
{
    die($objException);
}

$specialsClass = new specials($dbConnection);
$specialsClass->switchMode();

class specials
{
	protected $dbConnection;
	private $setPostID;
	private $setGetID;
	
	function __construct($dbConnection) {
	
		$this->dbConnection = $dbConnection;
	
		$this->setPostID = filter_input ( INPUT_POST, 'moduleID' );
		$this->setGetID = filter_input ( INPUT_GET, 'moduleID' );
	}
  
	public function showMediaFiles()
	{
		$x=2;
		
		echo '<table width=100% border=1>';
		echo '<tr><td class="sidebar-header">Insert Media</td></tr>';
		echo '<tr><td><a href="web-settings.php?id=Media&&moduleID=AddMedia">Upload Media</a> <a href="web-settings.php?id=Media">Media Library</td></tr>';
		echo '<tr><td width=70%>';
		
		echo '<table>';
		echo '<tr>';
		if ($handle = opendir('../Images')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					echo '<td><a href="web-settings.php?id=Media&&moduleID=showMedia&&mediaName='.$entry.'"><img class="mediaImages" src="../Images/'.$entry.'" height=75></td>';
					if ($x>=5)
					{
						echo '</tr><tr>';
						$x=1;
					}
					$x++;
				}
			}
			closedir($handle);
		}
		echo '</tr>';
		echo '</table>';
		
		echo '</td></tr>';
		echo '</table>';
		
	}
	
	private function deleteMedia()
	{
		$getImageName = filter_input(INPUT_GET, 'mediaName');
		
		unlink('../Images/'.$getImageName);
		
		echo '<font color=black><b>Please Wait!!!!<br>';
		echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Media">';
		
	}
	
	public function showMedia()
	{
		$getMediaName = filter_input ( INPUT_GET, 'mediaName' );
		
		echo '<table width=100% border=1>';
		echo '<tr><td rowspan=2><img src="../Images/'.$getMediaName.'"></td><td>';
		list($width, $height, $type, $attr) = getimagesize("../Images/$getMediaName");
		$imgsize=filesize("../Images/$getMediaName");
		$file_size = $imgsize / 1024;
		
		echo '<table>';
		echo '<tr><td>Image Details:</td></tr>';
		echo '<tr><td>Width:</td><td>'.$width.'</td></tr>';
		echo '<tr><td>Height:</td><td>'.$height.'</td></tr>';
		echo '<tr><td>Size: </td><td>'.number_format($file_size,2).'k</td></tr>';
		echo '</table>';

		echo '</td></tr>';
		echo '<tr><td><a class="redFont" href="web-settings.php?id=Media&&moduleID=DeleteMedia&&mediaName='.$getMediaName.'">Delete Image</a></td></tr>';
		echo '</table>';
		
		
	}
	
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
			echo '<font color=black><b>Please Wait!!!!<br>';
			echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Media">';
			
	}

	public function addMedia()
	{
		echo '<form method="POST" action="web-settings.php?id=Media&&moduleID=UploadMedia" enctype="multipart/form-data">';
		echo '<table>';
		echo '<tr><td>Upload Image:</td></tr>';
		echo '<tr><td height=25><input type="file" name="fileToUpload" id="fileToUpload"></td></tr>';
		echo '<tr><td><input type="submit" name="submit" value="Upload"></td></tr>';
		echo '</table>';
		echo '</form>';
		
	}
	
	public function switchMode() {
	
		$localAction = NULL;
	
		if (isset ( $this->setPostID )) {
			$localAction = $this->setPostID;
		} elseif (isset ( $this->setGetID )) {
			$localAction = urldecode ( $this->setGetID );
		}
	
		Switch (strtoupper ( $localAction )) {
	
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
	
}
<?php
/**
 * Gallery - version 1.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @todo            Class uses a global variable for the DB, but also takes a DB connection in contructor args.  Should probably just use the constructor arg.
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 */

$dbConnection = databaseConnection();
error_reporting(E_ALL);
ini_set('display_errors', '1');

class gallery
{
	#const ModuleDescription = 'Add & Edit Images in gallery.';
	#const ModuleAuthor = 'Sunsetcoders Development Team.';
	#const ModuleVersion = '1.0';
	
	protected $dbConnection;
	
	function __construct($dbConnection)
	{
		global $dbConnection;
		
		$this->dbConnection = $dbConnection;

		
	}
	
	public function addimage()
	{
		echo '<form action="web-settings.php?id=Gallery&&moduleID=UploadImage" method="post" enctype="multipart/form-data">';
		echo '<table>';
		echo '<tr><td>Add Image</td></tr>';
		echo 'Select image to upload:';
		echo '<input type="file" name="fileToUpload" id="fileToUpload">';

		

		echo '<tr><td>Add Supporter</td></tr>';
		echo '<tr><td>Add Supporter</td></tr>';
		echo '<tr><td>Add Supporter</td></tr>';
		echo '<input type="submit" value="Upload Image" name="submit">';
		
		echo '</table>';
		echo '</form>';
	}
	
	public function uploadImage()
	{
		$target_dir = "../Images/kidscorner/";
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
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
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
					echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
					$uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					echo "Sorry, your file was not uploaded.";
					// if everything is ok, try to upload file
				} else {
					if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
						echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
					} else {
						echo "Sorry, there was an error uploading your file.";
					}
				}
				
	}
	
	public function editImage()
	{
		
	}
	
	public function updateImage()
	{
		
	}
	
	public function gallery()
	{
		$x=1;
		echo '<table cellpadding=5>';
		echo '<tr><td>Gallery Images <a href="web-settings.php?id=Gallery&&moduleID=AddImage"><button>Add Gallery Image</button></a></td></tr>';
		echo '<tr><td hieght=70></td></tr>';
		echo '<tr>';
		
		if ($handle = opendir('../Images/kidscorner/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					if ($x>=5)
					{
						echo '</tr><tr>';
						$x=1;
					}
					echo '<td><img src="../Images/kidscorner/'.$entry.'" width=250> &nbsp; </td>';
					$x++;
				}
			}
			closedir($handle);
		}
		
		echo '</tr>';
		echo '</table>';
		
	}
	
}
?>

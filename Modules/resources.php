<?php
/**
 * Resources module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @todo            Change class to use db instance passed to it, rather than a global var.
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 */

try
{
    $dbConnection = databaseConnection();
}
catch(Exception $objException)
{
    die($objException);
}

class resources
{
	#const ModuleDescription = 'Access to add addition Resource Links as well change all Resource information  on pages in a text Editor.';
	#const ModuleAuthor = 'Sunsetcoders Development Team.';
	#const ModuleVersion = '1.0c';
	
	protected $dbConnection;
	
	private $setPostID;
	private $setGetID;
	private $getResourceID;
	private $postResourceID;
	
	
	function __construct($dbConnection)
	{
		global $dbConnection;
		
		$this->dbConnection = $dbConnection;

		$this->setPostID = filter_input(INPUT_POST, 'moduleID');
		$this->setGetID = filter_input(INPUT_GET, 'moduleID');
		
		$this->getResourceID = filter_input(INPUT_GET, 'resourceID');
		$this->postResourceID = filter_input(INPUT_POST, 'resourceID');
	}
	
	public function resources()
	{
		echo '<b>Resources</b> <a href="web-settings.php?id=Resources&&moduleID=AddResource"><button>Add New</button></a><br><br><br>';
		echo '<table width=100% cellpadding=5 cellspacing=0 border=1>';
		echo '<tr bgcolor=Black><td>Resource Name</td><td>Resource Hyperlink</td></tr>';
		$stmt = $this->dbConnection->prepare ( "SELECT resourceID, resourceName, resourceLink FROM resources" );
		$stmt->execute ();
		
		$stmt->bind_result ( $resourceID, $resourceName, $resourceLink );
		
		while ( $checkRow = $stmt->fetch () ) {
			
			echo '<tr bgcolor=white style="cursor: pointer;"  onclick="location.href=\'web-settings.php?id=Resources&&moduleID=EditResource&&resourceID='.$resourceID.'\'"><td width="200">'.$resourceName.'</td><td>'.$resourceLink.'</td></tr>';
			 
		}
		echo '</table>';
		
		echo '<br><br><b>Resources</b>';
		
		echo '<form method="POST" action="?id=Resources&&moduleID=UploadResourceFile" enctype="multipart/form-data">';
		echo '<table>';
		echo '<tr><td><input type="file" name="myFile"></td></tr>';
		echo '<tr><td><input type="submit" name="submit" value="Upload File"></td></tr>';
		echo '</table>';
		echo '</form>';
		echo '<br><br><br><b>Current Files:</b><br><br>';
		
		if ($handle = opendir('../files/')) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					echo '<a href="?id=Resources&&moduleID=DeleteResourceFile&&fileName='.$entry.'">'.$entry.'</a><br>';
				}
			}
			closedir($handle);
		}
		
		echo '<br><br><br>Click File To Delete<br><br>';
		
	}
	
	public function deleteResourceFile()
	{
		$fileName = filter_input(INPUT_GET, 'fileName');

		unlink("../files/".$fileName);
		
		echo 'You have successfully Deleted a new File. <br><br><br>Please Wait.....<br>';
		echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Resources">';
		
		
	}
	
	public function uploadResourceFile()
	{
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
		echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Resources">';
		
	}
	
	public function addResource()
	{
		echo '<form method="POST" action="web-settings.php?id=Resources&&moduleID=UploadResource">';
		echo '<table border=0 cellpadding=5 cellspacing=5>';
		echo '<tr><td colspan=2><b>Add New Resource</b></td></tr>';
		echo '<tr><td colspan=2></td></tr>';
		echo '<tr><td><b>Resource Name</b></td><td><b>Resource Link</b></td></tr>';
		echo '<tr><td><input type="text" name="resourceName" size="40"></td><td><input type="text" name="resourceLink" size="180"></td></tr>';
		echo '<tr><td colspan=2><input type="submit" name="submit" value="Add New"></td></tr>';
		echo '</table>';
	}
	public function uploadResource()
	{
		$resourceName = filter_input ( INPUT_POST, 'resourceName' );
		$resourceLink = filter_input ( INPUT_POST, 'resourceLink' );

		$stmt = $this->dbConnection->prepare ( "INSERT INTO resources (resourceName, resourceLink) VALUES (?,?)" );
		
		$stmt->bind_param ( 'ss', $resourceName, $resourceLink );
		
		$status = $stmt->execute ();
		
		echo 'You have successfully added a new Resource Link. <br><br><br>Please Wait.....<br>';
		echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Resources">';
		
	}
	public function editResource()
	{
		$resourceID = filter_input ( INPUT_GET, 'resourceID' );
		?>
		        <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
		        <script type="text/javascript">
		            bkLib.onDomLoaded(function () {
		                nicEditors.allTextAreas()
		            });
		        </script>
		
		        <?php
		        
		echo '<table cellpadding=5>';
		echo '<tr><td><h1>Resource Editor</h1></td></tr>';
		echo '<form method="POST" action="web-settings.php?id=Resources&&moduleID=updateResource">';
		echo '<input type="hidden" name="resourceID" value="'.$resourceID.'">';
		
		
		if ($stmt = $this->dbConnection->prepare ( "SELECT resourceID, resourceName, resourceLink FROM resources WHERE resourceID=? " )) {
		
			$stmt->bind_param ( "i", $resourceID );
			$stmt->execute ();
		
			$stmt->bind_result ( $resourceID, $resourceName, $resourceLink );
			$stmt->fetch ();

			echo '<tr><td>&nbsp;</td></tr>';
			echo '<tr><td><b>Hyperlink Name</td><td><b>Hyperlink</td></tr>';
			echo '<tr><td><input type="text" name="resourceName" value="'.$resourceName.'" size=40></td><td><input type="text" name="resourceLink" value="'.$resourceLink.'" size=180></td></tr>';
			echo '<tr><td><input type="Submit" name="Submit" value="Update"></form><a href="web-settings.php?id=Resources&&moduleID=DeleteResource&&resourceID='.$this->getResourceID.'"><button>Delete</button></a></td></tr>';
			echo '</table>';
			echo '</form>';
		}
	}
	
	public function updateResource()
	{
		$resourceName = filter_input(INPUT_POST, 'resourceName');
		$resourceLink = filter_input(INPUT_POST, 'resourceLink');
		$resourceID = filter_input(INPUT_POST, 'resourceID');
		
		$stmt = $this->dbConnection->prepare("UPDATE resources SET resourceName=?, resourceLink=? WHERE resourceID=?");
        $stmt->bind_param('ssi', $resourceName, $resourceLink, $resourceID);
        
        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Resource Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Resources">';
	}
	
	public function deleteResource()
	{
		$getID = filter_input(INPUT_GET, 'resourceID');
	
		$stmt = $this->dbConnection->prepare("DELETE FROM resources WHERE resourceID = ?");
		$stmt->bind_param('i', $getID);
		$stmt->execute();
		$stmt->close();
	
		echo 'You have successfully deleted a Resource. <br><br><br>Please Wait.....<br>';
		echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Resources">';
	}
}
?>

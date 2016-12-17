<?php
/**
 * Resources module.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @todo            Change class to use db instance passed to it, rather than a global var.
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:31:24 SM:  Uses database.
 * @version         1.1.0               2016-12-14 16:58:41 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 15:07:33 SM:  Tried to line up some tags, added documentation.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

/**
 * Resources module.
 */
class resources extends SunLibraryModule
{	
    /** @var striong $setPostID The module ID sent via $_POST. */
	private $setPostID;
	/** @var string $setGetID The module ID sent via $_GET. */
	private $setGetID;
	/** @var string $getResourceID The resource ID sent via $_GET. */
	private $getResourceID;
	/** @var string $postResourceID The resource ID sent via $_POST. */
	private $postResourceID;
	
	/**
	 * {@inheritdoc}
	 *
	 * @param mysqli $dbConnection Connection to the database.
	 */
	function __construct(mysqli $dbConnection)
	{
		$this->setPostID = filter_input(INPUT_POST, 'moduleID');
		$this->setGetID = filter_input(INPUT_GET, 'moduleID');
		$this->getResourceID = filter_input(INPUT_GET, 'resourceID');
		$this->postResourceID = filter_input(INPUT_POST, 'resourceID');
		parent::__construct($dbConnection);
	}
	
	/**
	 * Resources.
	 *
	 * @return void
	 */
	public function resources()
	{
?>
		<b>Resources</b>
		<a href="web-settings.php?id=Resources&&moduleID=AddResource"><button>Add New</button></a>
		<br><br><br>
		<table width="100%" cellpadding="5" cellspacing="0" border="1">
		    <tbody>
		        <tr bgcolor=Black>
		            <td>
		                Resource Name
		            </td>
		            <td>
		                Resource Hyperlink
		            </td>
		        </tr>
<?php
                $stmt = $this->objDB->prepare("SELECT resourceID, resourceName, resourceLink FROM resources");
		        $stmt->execute ();
		        $stmt->bind_result ( $resourceID, $resourceName, $resourceLink);		
		        while ($checkRow = $stmt->fetch())
		        {
?>
			        <tr bgcolor=white style="cursor: pointer;"  onclick="location.href='web-settings.php?id=Resources&&moduleID=EditResource&&resourceID=<?=$resourceID;?>'">
			            <td width="200">
			                <?=$resourceName;?>
			            </td>
			            <td>
			                <?=$resourceLink;?>
			            </td>
			        </tr>
<?php
		        }
?>
            </tbody>
		</table>
		<br><br>
		<b>Resources</b>
		<form method="POST" action="?id=Resources&&moduleID=UploadResourceFile" enctype="multipart/form-data">
		    <table>
		        <tbody>
		            <tr>
		                <td>
		                    <input type="file" name="myFile">
		                </td>
		            </tr>
		            <tr>
		                <td>
		                    <input type="submit" name="submit" value="Upload File">
		                </td>
		            </tr>
		        </tbody>
		    </table>
		</form>
		<br>
		<br>
		<br>
		<b>Current Files:</b>
		<br>
		<br>
<?php
		if ($handle = opendir('../files/'))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry != "." && $entry != "..")
				{
?>
					<a href="?id=Resources&&moduleID=DeleteResourceFile&&fileName=<?=$entry;?>"><?=$entry;?></a><br>
<?php
				}
			}
			closedir($handle);
		}
?>
		<br><br><br>
		Click File To Delete
		<br><br>
<?php
	}
	
	/**
	 * Delete resource file.
	 *
	 * @return void
	 */
	public function deleteResourceFile()
	{
		$fileName = filter_input(INPUT_GET, 'fileName');
		unlink("../files/".$fileName);
?>
		You have successfully Deleted a new File.
		<br><br><br>
		Please Wait.....<br>
		<meta http-equiv="refresh" content="3;url=web-settings.php?id=Resources">
<?php	
	}

    /**
     * Upload a resource file.
     *
     * @return void
     */
	public function uploadResourceFile()
	{
		define("UPLOAD_DIR", "../files/");
		if (!empty($_FILES["myFile"]))
		{
            $myFile = $_FILES["myFile"];
    		if ($myFile["error"] !== UPLOAD_ERR_OK)
        		die("<p>An error occurred.</p>");

    		// ensure a safe filename
    		$name = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile["name"]);

    		// don't overwrite an existing file
    		$i = 0;
    		$parts = pathinfo($name);
    		while (file_exists(UPLOAD_DIR . $name))
    		{
        		$i++;
        		$name = $parts["filename"] . "-" . $i . "." . $parts["extension"];
    		}

    		// preserve file from temporary directory
    		$success = move_uploaded_file($myFile["tmp_name"], UPLOAD_DIR . $name);
    		
    		if (!$success)
        		die("<p>Unable to save file.</p>");

   			 // set proper permissions on the new file
    		chmod(UPLOAD_DIR . $name, 0644);
		}
?>
		You have successfully added a new File.
		<br><br><br>
		Please Wait.....
		<br>
		<meta http-equiv="refresh" content="3;url=web-settings.php?id=Resources">
<?php
	}
	
	/**
	 * Add resource
	 *
	 * @return void
	 */
	public function addResource()
	{
?>
		<form method="POST" action="web-settings.php?id=Resources&&moduleID=UploadResource">
		    <table border="0" cellpadding="5" cellspacing="5">
		        <tbody>
		            <tr>
		                <td colspan="2">
		                    <b>Add New Resource</b>
		                </td>
		            </tr>
		            <tr>
		                <td colspan="2">
		                    &nbsp;
		                </td>
		            </tr>';
		            <tr>
		                <td>
		                    <b>Resource Name</b>
		                </td>
		                <td>
		                    <b>Resource Link</b>
		                </td>
		            </tr>
		            <tr>
		                <td>
		                    <input type="text" name="resourceName" size="40">
		                </td>
		                <td>
		                    <input type="text" name="resourceLink" size="180">
		                </td>
		            </tr>
		            <tr>
		                <td colspan="2">
		                    <input type="submit" name="submit" value="Add New">
		                </td>
		            </tr>
		        </tbody>
		    </table>
		</form>
<?php
	}
	
	/**
	 * Upload resource.
	 *
	 * @return void
	 */
	public function uploadResource()
	{
		$resourceName = filter_input ( INPUT_POST, 'resourceName' );
		$resourceLink = filter_input ( INPUT_POST, 'resourceLink' );
		$stmt = $this->objDB->prepare ( "INSERT INTO resources (resourceName, resourceLink) VALUES (?,?)" );
		$stmt->bind_param ( 'ss', $resourceName, $resourceLink );
		$status = $stmt->execute ();
?>
		You have successfully added a new Resource Link.
		<br><br><br>
		Please Wait.....
		<br>
		<meta http-equiv="refresh" content="3;url=web-settings.php?id=Resources">
<?php	
	}

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function documentReadyJavaScript()
    {
?>
        bkLib.onDomLoaded(function () {
            nicEditors.allTextAreas()
        });
<?php
    }
	
	/**
	 * Edit resource
	 *
	 * @return void
	 */
	public function editResource()
	{
		$resourceID = filter_input ( INPUT_GET, 'resourceID');
	    if ($stmt = $this->objDB->prepare ( "SELECT resourceID, resourceName, resourceLink FROM resources WHERE resourceID=? " ))
	    {
			$stmt->bind_param ("i", $resourceID );
			$stmt->execute();
			$stmt->bind_result ( $resourceID, $resourceName, $resourceLink );
			$stmt->fetch();
		}
?>
        <form method="POST" action="web-settings.php?id=Resources&&moduleID=updateResource">
            <input type="hidden" name="resourceID" value="<?=$resourceID;?>">
    		<table cellpadding="5">
    		    <thead>
    		        <tr>
    		            <td colspan="2">
    		                <h1>Resource Editor</h1>
    		            </td>
    		        </tr>
			        <tr>
			            <td>
			                <b>Hyperlink Name</b>
			            </td>
			            <td>
			                <b>Hyperlink</b>
			            </td>
			        </tr>
			    </thead>
			    <tbody>
			        <tr>
			            <td>
    			            <input type="text" name="resourceName" value="<?=$resourceName;?>" size="40">
	    		        </td>
		    	        <td>
			                <input type="text" name="resourceLink" value="<?=$resourceLink;?>" size="180">
			            </td>
			        </tr>
			        <tr>
			            <td>
			                <input type="Submit" name="Submit" value="Update">
			            </td>
			        </tr>
			        <tr>
			            <td>
			                <a href="web-settings.php?id=Resources&&moduleID=DeleteResource&&resourceID=<?=$this->getResourceID;?>">
			                    <button>Delete</button>
			                </a>
			            </td>
			        </tr>
			    </tbody>
			</table>
        </form>
<?php        
	}
	
	/**
	 * Update resource
	 *
	 * @return void
	 */
	public function updateResource()
	{
		$resourceName = filter_input(INPUT_POST, 'resourceName');
		$resourceLink = filter_input(INPUT_POST, 'resourceLink');
		$resourceID = filter_input(INPUT_POST, 'resourceID');
		$stmt = $this->objDB->prepare("UPDATE resources SET resourceName=?, resourceLink=? WHERE resourceID=?");
        $stmt->bind_param('ssi', $resourceName, $resourceLink, $resourceID);
        if ($stmt === false) 
            trigger_error($this->objDB->error, E_USER_ERROR);
        $status = $stmt->execute();
        if ($status === false) 
            trigger_error($stmt->error, E_USER_ERROR);
?>
        <font color="black">
            <b>Resource Updated <br><br> Please Wait!!!!<br>
            <meta http-equiv="refresh" content="1;url=web-settings.php?id=Resources">
        </font>
<?php
	}
	
	/**
	 * Delete a resource
	 *
	 * @return void
	 */
	public function deleteResource()
	{
		$getID = filter_input(INPUT_GET, 'resourceID');
		$stmt = $this->objDB->prepare("DELETE FROM resources WHERE resourceID = ?");
		$stmt->bind_param('i', $getID);
		$stmt->execute();
		$stmt->close();
?>
		You have successfully deleted a Resource. 
		<br><br><br>Please Wait.....<br>
		<meta http-equiv="refresh" content="3;url=web-settings.php?id=Resources">
<?php
	}

    /**
     * {@inheritdoc}
     *
     * @return string The full version number as read from this file's docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }	
}
?>

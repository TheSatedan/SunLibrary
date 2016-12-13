<?php
/**
 * Menu module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:12:47 SM:  Uses Database class.
 */
error_reporting ( E_ALL );
ini_set ( 'display_errors', '1' );

try
{
    $dbConnection=Database::GetDBConnection();
}
catch(Exception $objException)
{
    die($objException);
}

$specialsClass = new menu ($dbConnection);
$specialsClass->switchMode ();

class menu {
	protected $dbConnection;
	private $setPostID;
	private $setGetID;
	public static $moduleDescription = 'Manage Menus Throughout the website.';
	public static $moduleAuthor = 'Andrew Jeffries';
	public static $moduleVersion = '0.1';
	
	function __construct(mysqli $dbConnection) {
		$this->dbConnection = $dbConnection;
		
		$this->setPostID = filter_input ( INPUT_POST, 'moduleID' );
		$this->setGetID = filter_input ( INPUT_GET, 'moduleID' );
	}
	
	public function menu()
	{
		$getMenuLocation = filter_input ( INPUT_GET, 'menuLocation' );
		
		echo '<table border=1 width=100%>';
		echo '<tr><td colspan=2>';
		
		echo '<table cellpadding=5>';
		$stmt = $this->dbConnection->prepare("SELECT DISTINCT menuLocation FROM menu ORDER BY menuLocation DESC");
        $stmt->execute();

        $stmt->bind_result($menuLocation);

        while ($checkRow = $stmt->fetch()) {


            echo '<td width="100"><a href="web-settings.php?id=Menu&&menuLocation=' . $menuLocation . '">'.$menuLocation.'</td>';
        }
        echo '</table>';
              
        echo '</td></tr>';
		echo '<tr><td width=15%>';
		
		echo '<table>';
		$stmt = $this->dbConnection->prepare("SELECT pageName FROM pages");
        $stmt->execute();

        $stmt->bind_result($pageName);

        while ($checkRow = $stmt->fetch()) {


            echo '<tr><td width="100"> <input type="checkbox" name="'. $pageName.'" value="Yes"> ' . $pageName . '</td></tr>';
        }
		echo '</table>';
        
        echo '</td><td>';

        echo '<table>';
        $stmt = $this->dbConnection->prepare("SELECT menuName FROM menu WHERE menuLocation='$getMenuLocation' ORDER BY menuOrder");
        $stmt->execute();
        
        $stmt->bind_result($pageName);
        
        while ($checkRow = $stmt->fetch()) {
        
        
        	echo '<tr><td width="100"> <input type="checkbox" name="'. $pageName.'" value="Yes"> ' . $pageName . '</td></tr>';
        }
        echo '</table>';
        
        
        
        echo '</td></tr>';
		echo '</table>';
		
	}
	
	public function switchMode() {
	
		$localAction = NULL;
	
		if (isset ( $this->setPostID )) {
			$localAction = $this->setPostID;
		} elseif (isset ( $this->setGetID )) {
			$localAction = urldecode ( $this->setGetID );
		}
	
		Switch (strtoupper ( $localAction )) {
			case "DELUSER":
				$this->delUser();
				break;
			case "DELETEUSER":
				$this->deleteUser();
				break;
			case "ADDUSER" :
				$this->addUser ();
				break;
			case "UPLOADUSER" :
				$this->uploadUser ();
				break;
			case "EDITUSER" :
				$this->editUser ();
				break;
			case "UPDATEUSER" :
				$this->updateUser ();
				break;
			case "MENU" :
				$this->menu ();
				break;
		}
	}
}
?>

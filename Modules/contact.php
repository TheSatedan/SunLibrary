<?php
/**
 * Contact module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM: Prototype
 * @version         1.0.1               2016-12-13 16:17:20 SM: Uses database.
 */

try
{
    $dbConnection=Database::GetDBConnection();
}
catch(Exception $objException)
{
    die($objException);
}

echo '<link rel="stylesheet" type="text/css" href="style.css">';

// SM:  This will always return '1' in a reuslt set, as long as there is a CONTACT table in the current schema - is that what it should be doing?
$val=$dbConnection->query('select 1 from `contact` LIMIT 1');

if ($val !== FALSE) {
    
} else {

    $createTable = $dbConnection->prepare("CREATE TABLE contact (contactID INT(11) AUTO_INCREMENT PRIMARY KEY, contactSubject VARCHAR(10000) NOT NULL, contactOrder DECIMAL(4,2) NOT NULL ) ");
    $createTable->execute();
    $createTable->close();
}

// $bodyContent = add_snippet_connection ( '[ShowPortfolio]', 'showPortfolio', $pageContent);

class contact {

    protected $dbConnection;
    const ModuleDescription = 'Creating and Editing Contact Form Information.';
    const ModuleAuthor = 'Sunsetcoders Development Team.';
    const ModuleVersion = '0.1';
    
    function __construct($dbConnection) {

        global $dbConnection;

        $this->dbConnection = $dbConnection;
    }

    function contact() {

        echo '<form method="POST" action="?id=Contact&&moduleID=uploadContact">';
        echo '<table width=100% border=0 cellpadding=10>';
        echo '<tr><td colspan=2><h1>Contact Form Subject Information<h1></td></tr>';

        echo '<tr><td><b>Add Subject</b></td><td><b>Current Subjects</b></td></tr>';
        echo '<tr><td valign=top><input type="text" name="subjectName" size=40></td><td>';

        echo '<table border=1 width=100% cellpadding=10>';

        $stmt = $this->dbConnection->prepare("SELECT contactID, contactSubject FROM contact ORDER BY contactOrder ");
        $stmt->execute();

        $stmt->bind_result($contactID, $contactSubject);

        while ($checkRow = $stmt->fetch()) {

            echo '<tr bgcolor=white><td><a href="?id=Contact&&moduleID=EditSubject&&contactID=' . $contactID . '">' . $contactSubject . '</a></td></tr>';
        }
        echo '</table>';

        echo '</td></tr>';
        echo '<tr><td><input type="submit" name="submit" value="Upload"></td></tr>';
        echo '</table></form>';
    }

    public function uploadContact() {

        $setName = filter_input(INPUT_POST, 'subjectName');
        $setOrder = 1;

        $stmt = $this->dbConnection->prepare("INSERT INTO contact (contactSubject, contactOrder) VALUES (?,?)");

        $stmt->bind_param('si', $setName, $setOrder);

        $status = $stmt->execute();

        echo 'You have successfully added a new Contact Subject. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Contact">';
    }

    public function editSubject() {

        $getID = filter_input(INPUT_GET, 'contactID');

        if ($stmt = $this->dbConnection->prepare("SELECT contactSubject FROM contact WHERE contactID=? ")) {

            $stmt->bind_param("i", $getID);
            $stmt->execute();
            $stmt->bind_result($contactSubject);
            $stmt->fetch();

            echo '<table>';
            echo '<tr><td><h1>Edit Contact Subject</h1></td></tr>';
            
            echo '<tr><td><a href="?id=Contact&&moduleID=deleteContact&&contactID='.$getID.'"><button>Delete</button></a></td></tr>';
            
            echo '<form method="POST" action="?id=Contact&&moduleID=updateContact">';
            echo '<input type="hidden" name="contactID" value="'.$getID.'">';
            
            echo '<tr><td>Contact Subject </td><td><input type="text" name="contactName" value="' . $contactSubject . '" size=40></td></tr>';
            echo '<tr><td><input type="submit" name="submit" value="Update"></form></td></tr>';
            echo '</table>';
            echo '</form>';
            
            $stmt->close();
        }
    }

    public function deleteContact()
    {
        $getID = filter_input(INPUT_GET, 'contactID');

        $stmt = $this->dbConnection->prepare("DELETE FROM contact WHERE contactID = ?");
        $stmt->bind_param('i', $getID);
        $stmt->execute();
        $stmt->close();

        echo 'You have successfully Delete a ContactSubject. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Contact">';
    }
    
    public function updateContact() {
        
        $getName = filter_input(INPUT_POST, 'contactName');
        $getID = filter_input(INPUT_POST, 'contactID');

        $stmt = $this->dbConnection->prepare("UPDATE contact SET contactSubject=? WHERE contactID=?");
        $stmt->bind_param('si', $getName, $getID);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Contact Subject Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Contact">';
    }

}
?>

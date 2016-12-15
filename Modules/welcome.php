<?php
/**
 * Welcome page
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:34:51 SM:  Uses database.
 * @version         1.1.0               2016-12-14 15:46:10 SM:  Extends the SunLibraryModule.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

class welcome extends SunLibraryModule
{
    function __construct(mysqli $dbConnection)
    {
        parent::__construct($dbConnection);
    }

    public function welcome()
    {
        //
    }

    public function callToFunction()
    {
        if ($stmt = $this->objDB->prepare("SELECT phoneNumber, mobileNumber, emailOne, emailTwo, AddressOne, AddressTwo, welcomeContent FROM welcome WHERE welcomeID=1"))
        {
            $stmt->execute();
            $stmt->bind_result($phoneNumber, $mobileNumber, $emailOne, $emailTwo, $AddressOne, $AddressTwo, $welcomeContent);
            $stmt->fetch();
            ?>
            <div id="welcome-content">
                <div class="body-content">  
                    <div class="welcome-left">
                        <div class="welcome-one"><?php echo $phoneNumber . '<br>' . $mobileNumber; ?></div>
                        <div class="welcome-two"><?php echo $emailOne . '<br>' . $emailTwo; ?></div>
                        <div class="welcome-three"><?php echo $AddressOne . '<br>' . $AddressTwo; ?></div>
                    </div>
                    <div class="welcome-content"><?php echo nl2br($welcomeContent); ?></div>
                </div> 
            </div>
            <?php
        }
    }
    
    public function assertTablesExist()
    {
        $objResult=$this->objDB->query('select 1 from `welcome` LIMIT 1');
        if ($objResult===false)
        {
            $createTable = $this->objDB->prepare("CREATE TABLE welcome (welcomeID INT(11) AUTO_INCREMENT PRIMARY KEY, phoneNumber VARCHAR(100) NOT NULL, mobileNumber VARCHAR(100) NOT NULL, emailOne VARCHAR(100) NOT NULL, emailTwo VARCHAR(100) NOT NULL, AddressOne VARCHAR(100) NOT NULL, AddressTwo VARCHAR(2000) NOT NULL, welcomeContent VARCHAR(4000) NOT NULL)");
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
    
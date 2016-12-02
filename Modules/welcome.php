<?php
/**
 * Welcome page
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 */

try
{
    $dbTriConnection = databaseConnection();
}
catch(Exception $objException)
{
    die($objException);
}

$val = mysqli_query($dbTriConnection, 'select 1 from `welcome` LIMIT 1');

if ($val !== FALSE) {
    
} else {

    $createTable = $dbTriConnection->prepare("CREATE TABLE welcome (welcomeID INT(11) AUTO_INCREMENT PRIMARY KEY, phoneNumber VARCHAR(100) NOT NULL, mobileNumber VARCHAR(100) NOT NULL, emailOne VARCHAR(100) NOT NULL, emailTwo VARCHAR(100) NOT NULL, AddressOne VARCHAR(100) NOT NULL, AddressTwo VARCHAR(2000) NOT NULL, welcomeContent VARCHAR(4000) NOT NULL)");
    $createTable->execute();
    $createTable->close();
}

class welcome {

    protected $dbConnection;

    function __construct(mysqli $dbConnection) {

        $this->dbConnection = $dbConnection;
    }

    public function welcome() {
        
    }

    public function callToFunction() {

        if ($stmt = $this->dbConnection->prepare("SELECT phoneNumber, mobileNumber, emailOne, emailTwo, AddressOne, AddressTwo, welcomeContent FROM welcome WHERE welcomeID=1")) {

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

}
?>

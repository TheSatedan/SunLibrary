<?php
/**
 * Team page.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:46:13 SM: Prototype
 * @version         1.0.1               2016-12-13 16:32:49 SM: Uses database.
 */

try
{
    $dbTriConnection = Database::GetDBConnection();
}
catch(Exception $objException)
{
    die($objException);
}

$val = mysqli_query($dbTriConnection, 'select 1 from `teampanel` LIMIT 1');

if ($val !== FALSE) {
    
} else {
    echo 'Table Doesnt Exist....';

    $createTable = $dbTriConnection->prepare("CREATE TABLE teampanel (teampanelID INT(11) AUTO_INCREMENT PRIMARY KEY, iconSetOne VARCHAR(100) NOT NULL, pointSetOne VARCHAR(2000) NOT NULL, iconSetTwo VARCHAR(100) NOT NULL, pointSetTwo VARCHAR(2000) NOT NULL, iconSetThree VARCHAR(100) NOT NULL, pointSetThree VARCHAR(2000) NOT NULL, teamImage VARCHAR(100) NOT NULL, teamContent VARCHAR(2000) NOT NULL)");
    $createTable->execute();
    $createTable->close();

    echo 'Table Created.';
}

class team
{
    protected $dbConnection;
    
    function __construct($dbConnection) {
        
        $this->dbConnection = $dbConnection;
    }
    
    public function callToFunction()
    {
        ?>
        <div id="ourTeamPanel">
            <div class="photoImage"></div><div class="photoContent"></div>
            <div class="triWindow">
                <?php
                      
                    $oneRef = $this->dbConnection->prepare("SELECT iconSetOne, pointSetOne  FROM teampanel WHERE teampanelID=1");
                    $oneRef->execute();
                    $oneRef->bind_result($iconSetOne, $pointSetOne);

                    while ($checkRow = $oneRef->fetch()) {
                        echo '<div><img src="Images/'.$iconSetOne.'"><br>'.$pointSetOne.'</div>';
                    }
           
                    ?>
            </div><div class="triWindow">
                <?php
                      
                    $twoRef = $this->dbConnection->prepare("SELECT iconSetTwo, pointSetTwo  FROM teampanel WHERE teampanelID=1");
                    $twoRef->execute();
                    $twoRef->bind_result($iconSetTwo, $pointSetTwo);

                    while ($checkRow = $twoRef->fetch()) {
                        echo '<div><img src="Images/'.$iconSetTwo.'"><br>'.$pointSetTwo.'</div>';
                    }
           
                    ?>
            </div><div class="triWindow">
               <?php
                      
                    $threeRef = $this->dbConnection->prepare("SELECT iconSetThree, pointSetThree  FROM teampanel WHERE teampanelID=1");
                    $threeRef->execute();
                    $threeRef->bind_result($iconSetThree, $pointSetThree);

                    while ($checkRow = $threeRef->fetch()) {
                        echo '<div><img src="Images/'.$iconSetThree.'"><br>'.$pointSetThree.'</div>';
                    }
           
                    ?>
            </div>
            
        </div>
        <?php
    }
}
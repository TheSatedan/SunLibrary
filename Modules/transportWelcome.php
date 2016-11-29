<?php
/**
 * Transport Welcome page
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

/*
 * The Following Snippet is to insert the module table into the mysqli table. 
 */

$val = mysqli_query($dbTriConnection, 'select 1 from `welcome` LIMIT 1');

if ($val !== FALSE) {
    
} else {
    $createTable = $dbTriConnection->prepare("CREATE TABLE welcome (sliderID INT(11) AUTO_INCREMENT PRIMARY KEY, section1 VARCHAR(1000) NOT NULL, section2 VARCHAR(2000) NOT NULL)");
    $createTable->execute();
    $createTable->close();
}

class transportWelcome {

    protected $dbConnection;

    function __construct($dbConnection) {

        $this->dbConnection = $dbConnection;
    }

    public function transportWelcome() {

        /*
         * This is prometheus Administrator output.
         */
    }

    public function editContent() {

        $contentCode = filter_input(INPUT_GET, "ContentID");

        $query = "SELECT $contentCode FROM teampanel WHERE teampanelID=1 ";

        echo '<form method="POST" action="?id=team&&moduleID=UpdateContent">';
        echo '<input type="hidden" name="contentCode" value="' . $contentCode . '">';

        if ($stmt = $this->dbConnection->prepare($query)) {

            $stmt->execute();
            $stmt->bind_result($contentCode);
            $stmt->fetch();

            echo '<table border=0 cellpadding=20>';
            echo '<tr><td><h1>Content: </h1></td></tr>';
            echo '<tr><td><textarea cols=100 rows=10 name="contentMatter">' . $contentCode . '</textarea></td></tr>';
            echo '<tr><td><input type="submit" name="submit" value="Update"></td></tr>';
        }
        echo '</form>';
    }

    public function updateContent() {

        $contentDescription = filter_input(INPUT_POST, 'contentMatter');
        $contentCode = filter_input(INPUT_POST, 'contentCode');

        $stmt = $this->dbConnection->prepare("UPDATE teampanel SET $contentCode=? WHERE teampanelID=1");
        $stmt->bind_param('s', $contentDescription);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=Team">';
    }

    public function callToFunction() {
        ?>
        <br><br><div id="transport-background">
            <div class="body-content">
                <div class="transport-content">
                    <?php
                    if ($stmt = $this->dbConnection->prepare("SELECT section1, section2 FROM welcome WHERE welcomeID=1 ")) {

                        $stmt->execute();
                        $stmt->bind_result($section1, $section2);
                        $stmt->fetch();

                        echo '<div class="leftBank">' . nl2br($section1) . '</div><div class="rightBank">' . nl2br($section2) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

}
?>

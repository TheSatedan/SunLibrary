<?php
/**
 * Pricing module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 */

$dbTriConnection = databaseConnection();

$val = mysqli_query($dbTriConnection, 'select 1 from `pricing` LIMIT 1');

if ($val !== FALSE) {

} else {

    $createTable = $dbTriConnection->prepare("CREATE TABLE pricing (pricingID INT(11) AUTO_INCREMENT PRIMARY KEY, pricingContent VARCHAR(4000) NOT NULL)");
    $createTable->execute();
    $createTable->close();
}

class pricing {

    protected $dbConnection;

    function __construct($dbConnection) {

        $this->dbConnection = $dbConnection;
    }

    public function pricing() {

       $query = "SELECT pricingContent FROM pricing WHERE pricingID=1 ";

        echo '<form method="POST" action="?id=Pricing&&moduleID=UpdateContent">';

        if ($stmt = $this->dbConnection->prepare($query)) {

            $stmt->execute();
            $stmt->bind_result($pricingContent);
            $stmt->fetch();

            echo '<table border=0 cellpadding=10>';
            echo '<tr><td><h2>Pricing Description</h2></td></tr>';
            echo '<tr><td><textarea cols=150 rows=15 name="contentMatter">' . $pricingContent . '</textarea></td></tr>';
            echo '<tr><td><input type="submit" name="submit" value="Update"></td></tr>';
        }
        echo '</form>';
    }

    public function updateContent() {

        $contentDescription = filter_input(INPUT_POST, 'contentMatter');

        $stmt = $this->dbConnection->prepare("UPDATE pricing SET pricingContent=? WHERE pricingID=1");
        $stmt->bind_param('s', $contentDescription);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Content Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=?id=Pricing">';
    }


    public function callToFunction() {
?>
        <div id="pricing-content">
            <div class="body-content">
                <a name="Pricing"></a> 
                <div class="pricing-text">
                    <?php
                    $contentRef = $this->dbConnection->prepare("SELECT pricingContent FROM pricing WHERE pricingID=1");
                    $contentRef->execute();

                    $contentRef->bind_result($pricingContent);

                    while ($checkRow = $contentRef->fetch()) {

                        echo nl2br($pricingContent);
                    }
                    $contentRef->close();
                    ?>
                </div>
            </div>
        </div>

        <?php
    }

}

<?php
/**
 * Transport Welcome page
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:34:22 SM:  Uses database.
 * @version         1.1.0               2016-12-14 15:51:07 SM:  Uses SunLibraryModule
 ( @VERSION         1.1.1               2016-12-16 11:58:18 SM:  Now calcultes what aircraft are around, based on the radar.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

/**
 * The sunlibrary moule.
 */
class transportWelcome extends SunLibraryModule
{
    /**
     * {@inheritdoc}
     * Constructor.
     *
     * @para mysqli $dbConn3ection
     * @return void
     */
    function __construct(mysqli $dbConnection)
    {
        parent::__construct($dbConnection);
    }

    /**
     * Render the transport welcom.
     *
     * @param void
     * @return nothing
     *
    public function transportWelcome()
    {
    /*
     * This is prometheus Administrator output.
     */

    /**
     * {@inheritDoc}
     * Edit contact info.
     *
     * @return void
     */
    public function editContent()
    {
        $contentCode = filter_input(INPUT_GET, "ContentID");
        $query = "SELECT $contentCode FROM teampanel WHERE teampanelID=1 ";
?>
        <form method="POST" action="?id=team&&moduleID=UpdateContent">
            <tbody>
                <input type="hidden" name="contentCode" value="<?=$contentCode;?>">
<?php
                if ($stmt = $this->dbConnection->prepare($query))
                {
                    $stmt->execute();
                    $stmt->bind_result($contentCode);
                    $stmt->fetch();
?>
                    <table border="0" cellpadding="20">
                        <tr><td><h1>Content: </h1></td></tr>
                        <tr><td><textarea cols=100 rows=10 name="contentMatter">' . $contentCode . '</textarea></td></tr>
                        <tr><td><input type="submit" name="submit" value="Update"></td></tr>
<?php
        }
?>
            </tbody>
        </form>'
<?php
    }

    /**
     * Update contact page rendering.
     *
     * @return void
     */
    public function updateContent()
    {
        $contentDescription = filter_input(INPUT_POST, 'contentMatter');
        $contentCode = filter_input(INPUT_POST, 'contentCode');

        $stmt = $this->dbConnection->prepare("UPDATE teampanel SET $contentCode=? WHERE teampanelID=1");
        $stmt->bind_param('s', $contentDescription);

        if ($stmt === false)
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        $status = $stmt->execute();
        if ($status === false) 
            trigger_error($stmt->error, E_USER_ERROR);
?>
        <font color="black">
            <b>Content Information Updated 
            <br><br> Please Wait!!!!<br>';
            echo '<meta http-equiv="refresh" content="1;url=?id=Team">';
    }
<?php
    /**
     * Call to main redering function.
     *
     * @return void.
     */
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
?>
                        <div class="leftBank">
                            <?=nl2br($section1);?>
                        </div>
                        <div class="rightBank">
                            <?=nl2br($section2);?>
                        </div>
<?php
                    }
?>
                </div>
            </div>
        </div>
<?php
    }

    /**
     * Assert that the existing tables are here.  
     *
     * @return void
     */
    public function assertTablesExist()
    {
        $objResult=$this->objDB->query('select 1 from `welcome` LIMIT 1');
        if ($objResult===false)
        {
            $createTable = $this->objDB->prepare("CREATE TABLE welcome (sliderID INT(11) AUTO_INCREMENT PRIMARY KEY, section1 VARCHAR(1000) NOT NULL, section2 VARCHAR(2000) NOT NULL)");
            $createTable->execute();
            $createTable->close();
        }
        else
            $objResult->free();
    }
    
    /**
     * Get the version number, calculated from the docblock.
     *
     * @return string.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}
?>

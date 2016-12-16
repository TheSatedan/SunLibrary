<?php
/**
 * Pricing module.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype.
 * @version         1.0.1               2016-12-13 16:28:15 SM:  Uses databas.
 * @version         1.1.0               2016-12-15 08:28:42 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 15:40:44 SM:  Code tidy, fixing tags, added doco.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

/**
 * Pricing module
 */
class pricing extends SunLibraryModule
{
    /**
     * {@inheritdoc}
     *
     * @param mysqli $dbConnection Database connection
     */
    function __construct(mysqli $dbConnection)
    {
        parent::__construct($dbConnection);
    }

    /**
     * Pricing.
     *
     * @return void
     */
    public function pricing()
    {
?>
        <form method="POST" action="?id=Pricing&&moduleID=UpdateContent">
<?php
           $query = "SELECT pricingContent FROM pricing WHERE pricingID=1 ";
            if ($stmt = $this->objDB->prepare($query))
            {
                $stmt->execute();
                $stmt->bind_result($pricingContent);
                $stmt->fetch();
?>
                <table border="0" cellpadding="10">
                    <thead>
                        <tr>
                            <td>
                                <h2>Pricing Description</h2>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <textarea cols=150 rows=15 name="contentMatter"><?=$pricingContent;?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="submit" name="submit" value="Update">
                            </td>
                        </tr>
                    </tbody>
                </table>
<?php
            }
?>
        </form>
<?php
    }

    /**
     * Update content.
     *
     * @return void
     */
    public function updateContent()
    {
        $contentDescription = filter_input(INPUT_POST, 'contentMatter');
        $stmt = $this->objDB->prepare("UPDATE pricing SET pricingContent=? WHERE pricingID=1");
        $stmt->bind_param('s', $contentDescription);
        if ($stmt === false)
            trigger_error($this->objDB->error, E_USER_ERROR);
        $status = $stmt->execute();
        if ($status === false)
            trigger_error($stmt->error, E_USER_ERROR);
?>
        <font color="black">
            <b>Content Information Updated <br><br> Please Wait!!!!</b><br>
            <meta http-equiv="refresh" content="1;url=?id=Pricing">
        </font>
<?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function callToFunction() {
?>
        <div id="pricing-content">
            <div class="body-content">
                <a name="Pricing"></a> 
                <div class="pricing-text">
<?php
                    $contentRef = $this->objDB->prepare("SELECT pricingContent FROM pricing WHERE pricingID=1");
                    $contentRef->execute();
                    $contentRef->bind_result($pricingContent);
                    while ($checkRow = $contentRef->fetch())
                    {
?>
                        <?=nl2br($pricingContent);?>
<?php
                    }
                    $contentRef->close();
?>
                </div>
            </div>
        </div>
<?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function assertTablesExist()
    {
        $objResult=$this->objDB->query('select 1 from `pricing` LIMIT 1');
        if ($objResult===false)
        {
            $createTable = $this->objDB->prepare("CREATE TABLE pricing (pricingID INT(11) AUTO_INCREMENT PRIMARY KEY, pricingContent VARCHAR(4000) NOT NULL)");
            $createTable->execute();
            $createTable->close();
        }
        else
            $objResult->free();
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

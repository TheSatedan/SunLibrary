<?php
/**
 * Banners.
 * Add and edit banner of all types and assignment of bannercode.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @todo            Replace use of "document.getElementById" by including the jQuery library.
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:14:36 SM:  Uses Database.
 * @version         1.1.0               2016-12-15 15:50:43 SM:  Uses SunLibraryModule.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

class banners extends SunLibraryModule
{
    function __construct($dbConnection)
    {
        parent::__construct($dbConnection);
    }

    public function banners()
    {
        ?>
        <table border="0" cellpadding="15" cellspacing="5" width="50%">
            <tbody>
            <tr>
                <td colspan="3">
                    <h2>Banner Panel</h2>
                </td>
            </tr>
            <tr>
                <td height="40">
                    <button><a href="?id=Banners&&moduleID=AddBanner">Add New</a></button>
                </td>
            </tr>
            <tr>
                <td class="headerMenu" colspan="3">
                    Banner No.
                </td>
            </tr>
            <?php
            $stmt = $this->objDB->prepare("SELECT bannerID, bannerCode, bannerContent, bannerImageOne, bannerImageTwo FROM banners ");
            $stmt->execute();
            $stmt->bind_result($bannerID, $bannerCode, $bannerContent, $bannerImageOne, $bannerImageTwo);
            while ($checkRow = $stmt->fetch()) {
                ?>
                <tr>
                    <td>
                        Banner<?= $bannerID; ?>
                    </td>
                    <td width="150">
                        <?= $bannerCode; ?>
                    </td>
                    <td width="25">
                        <a href="#">edit</a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }

    public function renderCustomJavaScript()
    {
        ?>
        function yesnoCheck()
        {
        if (document.getElementById('onePanel').checked)
        {
        document.getElementById('twoPanel').unchecked;
        document.getElementById('threePanel').unchecked;
        document.getElementById('panelOne').style.display = 'block';
        document.getElementById('panelTwo').style.display = 'none';
        document.getElementById('panelThree').style.display = 'none';
        }
        else
        document.getElementById('panelOne').style.display = 'none';

        if (document.getElementById('twoPanel').checked)
        {
        document.getElementById('onePanel').unchecked;
        document.getElementById('threePanel').unchecked;
        document.getElementById('panelTwo').style.display = 'block';
        document.getElementById('panelOne').style.display = 'none';
        document.getElementById('panelThree').style.display = 'none';
        }
        else
        document.getElementById('panelTwo').style.display = 'none';

        if (document.getElementById('threePanel').checked)
        {
        document.getElementById('onePanel').unchecked;
        document.getElementById('twoPanel').unchecked;
        document.getElementById('panelThree').style.display = 'block';
        document.getElementById('panelOne').style.display = 'none';
        document.getElementById('panelTwo').style.display = 'none';
        }
        else
        document.getElementById('panelThree').style.display = 'none';
        }
        <?php
    }

    public function addBanner()
    {
        ?>
        <table border="1" cellpadding="10" cellspacing="5" width="50%">
            <tbody>
            <tr>
                <td>
                    Banner Panel
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="headerMenu">
                    ShowCode
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="showCode">
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="headerMenu">
                    Image Background
                </td>
            </tr>
            <tr>
                <td>
                    <input type="file" name="imageToUpload">
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td class="headerMenu">
                    Banner Setup
                </td>
            </tr>
            <tr>
                <td>
                    One Panel: <input type="radio" name="onePanel" value="Yes" onclick="javascript:yesnoCheck();"
                                      id="onePanel">
                    Two Panel: <input type="radio" name="twoPanel" value="Yes" onclick="javascript:yesnoCheck();"
                                      id="twoPanel">
                    Three Panel: <input type="radio" name="threePanel" value="Yes" onclick="javascript:yesnoCheck();"
                                        id="threePanel">
                </td>
            </tr>
            <tr>
                <td id="panelOne" style="display:none">
                    One Panel
                </td>
            </tr>
            <tr>
                <td id="panelTwo" style="display:none">
                    <table Border="1" width="100%"
                    " height="50">\
            <tbody>
            <tr>
                <td>
                    <input type="text" name="showCode">
                </td>
                <td>
                    <input type="text" name="showCode">
                </td>
            </tr>
            </tbody>
        </table>
        </td>
        </tr>
        <tr>
            <td id="panelThree" style="display:none">
                <table Border="1" width="100%" height="50">
                    <tbody>
                    <tr>
                        <td>
                            <input type="text" name="showCode">
                        </td>
                        <td>
                            <input type="text" name="showCode">
                        </td>
                        <td>
                            <input type="text" name="showCode">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
        </table>
        <?php
    }

    public function callToFunction()
    {
        ?>
        <div id="banner-background">
            <div class="body-content">
                <?php
                if ($stmt = $this->objDB->prepare("SELECT bannerContent, bannerImageOne, bannerImageTwo FROM banners WHERE bannerID=1 ")) {
                    $stmt->execute();
                    $stmt->bind_result($bannerContent, $bannerImageOne, $bannerImageTwo);
                    $stmt->fetch();
                    ?>
                    <div class="banner-content">
                        <div class="banner-content-left">
                            <?= nl2br($bannerContent); ?>
                        </div>
                        <div class="banner-content-right">
                            <img class="banner-image" src="<?= IMAGE_PATH; ?>/<?= $bannerImageOne; ?>">
                            <img class="banner-image" src="<?= IMAGE_PATH; ?>/<?= $bannerImageTwo; ?>">
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function renderHeaderLinks()
    {
        ?>
        <style>
            #banner-background {
                height: 150px;
                width: 100%;
            }

            .banner-content {
                background-image: url("../Images/Banner Background.png");
                height: 150px;

            }

            .banner-content-left {
                padding-left: 30px;
                padding-top: 35px;
                float: left;
                width: 60%;
                text-shadow: 2px 2px 4px #000;
            }

            .banner-content-right {
                vertical-align: middle;
                float: right;
                width: 32%;
                padding-top: 50px;
            }

            img.banner-image {
                vertical-align: middle;
            }
        </style>
        <?php
    }

    protected function assertTablesExist()
    {
        $objResult = $this->objDB->query('select 1 from `banners` LIMIT 1');
        if ($objResult === false) {
            $createTable = $this->objDB->prepare("CREATE TABLE banners (bannerID INT(11) AUTO_INCREMENT PRIMARY KEY, bannerCode VARCHAR(100) NOT NULL, bannerContent VARCHAR(100) NOT NULL, bannerImageOne VARCHAR(100) NOT NULL, bannerImageTwo VARCHAR(100) NOT NULL)");
            $createTable->execute();
            $createTable->close();
        } else {
            $objResult->free();
        }
    }

    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}

?>

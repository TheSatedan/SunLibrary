<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
$dbTriConnection = databaseConnection();

$val = mysqli_query($dbTriConnection, 'select 1 from `banners` LIMIT 1');

if ($val !== FALSE) {
    
} else {
    $createTable = $dbTriConnection->prepare("CREATE TABLE banners (bannerID INT(11) AUTO_INCREMENT PRIMARY KEY, bannerCode VARCHAR(100) NOT NULL, bannerContent VARCHAR(100) NOT NULL, bannerImageOne VARCHAR(100) NOT NULL, bannerImageTwo VARCHAR(100) NOT NULL)");
    $createTable->execute();
    $createTable->close();
}

class banners {

    protected $dbConnection;

    const ModuleDescription = 'Creating and Displaying Banners of all sizes.';
    const ModuleAuthor = 'Sunsetcoders Development Team.';
    const ModuleVersion = '0.1';

    function __construct($dbConnection) {

        $this->dbConnection = $dbConnection;
    }

    public function banners() {

        echo '<table border=0 cellpadding=15 cellspacing=5 width=50%>';
        echo '<tr><td colspan=3><h2>Banner Panel</h2></td></tr>';
        echo '<tr><td height=40><button><a href="?id=Banners&&moduleID=AddBanner">Add New</a></button></td></tr>';
        echo '<tr><td class="headerMenu" colspan=3>Banner No.</td></tr>';
        $stmt = $this->dbConnection->prepare("SELECT bannerID, bannerCode, bannerContent, bannerImageOne, bannerImageTwo FROM banners ");
        $stmt->execute();

        $stmt->bind_result($bannerID, $bannerCode, $bannerContent, $bannerImageOne, $bannerImageTwo);

        while ($checkRow = $stmt->fetch()) {

            echo '<tr><td>Banner' . $bannerID . '</td><td width=150>' . $bannerCode . '</td><td width=25><a href="#">edit</a></td></tr>';
        }
        echo '</table>';
    }

    public function addBanner() {
        ?>
        <script type="text/javascript">

            function yesnoCheck() {
                if (document.getElementById('onePanel').checked) {
                    document.getElementById('twoPanel').unchecked;
                    document.getElementById('threePanel').unchecked;
                    document.getElementById('panelOne').style.display = 'block';
                    document.getElementById('panelTwo').style.display = 'none';
                    document.getElementById('panelThree').style.display = 'none';
                }
                else
                    document.getElementById('panelOne').style.display = 'none';

                if (document.getElementById('twoPanel').checked) {
                    document.getElementById('onePanel').unchecked;
                    document.getElementById('threePanel').unchecked;
                    document.getElementById('panelTwo').style.display = 'block';
                    document.getElementById('panelOne').style.display = 'none';
                    document.getElementById('panelThree').style.display = 'none';
                }
                else
                    document.getElementById('panelTwo').style.display = 'none';

                if (document.getElementById('threePanel').checked) {
                    document.getElementById('onePanel').unchecked;
                    document.getElementById('twoPanel').unchecked;
                    document.getElementById('panelThree').style.display = 'block';
                    document.getElementById('panelOne').style.display = 'none';
                    document.getElementById('panelTwo').style.display = 'none';
                }
                else
                    document.getElementById('panelThree').style.display = 'none';
            }

        </script>
        <?php
        echo '<table border=1 cellpadding=10 cellspacing=5 width=50%>';
        echo '<tr><td>Banner Panel</td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td class="headerMenu">ShowCode</td></tr>';
        echo '<tr><td><input type="text" name="showCode"></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td class="headerMenu">Image Background</td></tr>';
        echo '<tr><td><input type="file" name="imageToUpload"></td></tr>';
        echo '<tr><td></td></tr>';
        echo '<tr><td class="headerMenu">Banner Setup</td></tr>';
        echo '<tr><td>';
        ?>
        One Panel: <input type="radio" name="onePanel" value="Yes" onclick="javascript:yesnoCheck();" id="onePanel"> 
        Two Panel: <input type="radio" name="twoPanel" value="Yes" onclick="javascript:yesnoCheck();" id="twoPanel"> 
        Three Panel: <input type="radio" name="threePanel" value="Yes" onclick="javascript:yesnoCheck();" id="threePanel">

        <?php
        echo '</td></tr>';
        echo '<tr><td  id="panelOne" style="display:none">One Panel</td></tr>';
        echo '<tr><td  id="panelTwo" style="display:none">';

        echo '<table Border=1 width=100% height=50>';
        echo '<tr><td><input type="text" name="showCode"></td><td><input type="text" name="showCode"></td></tr>';
        echo '</table>';

        echo '</td></tr>';
        echo '<tr><td  id="panelThree" style="display:none">';

        echo '<table Border=1 width=100% height=50>';
        echo '<tr>'
        . '<td><input type="text" name="showCode"></td>'
        . '<td><input type="text" name="showCode"></td>'
        . '<td><input type="text" name="showCode"></td></tr>';
        echo '</table>';

        echo '</td></tr>';
        echo '</table>';
    }

    public function callToFunction() {
        ?>
        <div id="banner-background">
            <div class="body-content">
                <?php
                if ($stmt = $this->dbConnection->prepare("SELECT bannerContent, bannerImageOne, bannerImageTwo FROM banners WHERE bannerID=1 ")) {

                    $stmt->execute();

                    $stmt->bind_result($bannerContent, $bannerImageOne, $bannerImageTwo);
                    $stmt->fetch();
                    ?>

                    <div class="banner-content">
                        <div class="floatLeft"><?php echo $bannerContent; ?></div><div class="floatRight"><img src="<?php echo IMAGE_PATH . '/' . $bannerImageOne; ?>"><img src="<?php echo IMAGE_PATH . '/' . $bannerImageTwo; ?>"></div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

}

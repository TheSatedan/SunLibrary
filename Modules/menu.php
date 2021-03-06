<?php
/**
 * Manage Menus Throughout the website.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype.
 * @version         1.0.1               2016-12-13 16:12:47 SM:  Uses Database class.
 * @version         1.1.0               2016-12-15 08:34:56 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 16:15:10 SM:  Added doco, tidied the indents.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * Extends module
 */
class menu extends SunLibraryModule
{
    /** @var string $setPostID The module ID sent via $_POST. */
    private $setPostID;
    /** @var string $setGetID The module ID sent via $_GET. */
    private $setGetID;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    function __construct(mysqli $dbConnection)
    {
        $this->setPostID = filter_input(INPUT_POST, 'moduleID');
        $this->setGetID = filter_input(INPUT_GET, 'moduleID');
        parent::__construct($dbConnection);
    }

    /**
     * Menu
     *
     * @return void
     */
    public function menu()
    {
        $getMenuLocation = filter_input(INPUT_GET, 'menuLocation');
        ?>
        <table border="1" width="100%">
            <tbody>
            <tr>
                <td colspan="2">
                    <table cellpadding=5>
                        <tbody>
                        <?php
                        $stmt = $this->objDB->prepare("SELECT DISTINCT menuLocation FROM menu ORDER BY menuLocation DESC");
                        $stmt->execute();
                        $stmt->bind_result($menuLocation);
                        while ($checkRow = $stmt->fetch()) {
                            ?>
                            <tr>
                                <td width="100">
                                    <a href="web-settings.php?id=Menu&&menuLocation=<?= $menuLocation; ?>"><?= $menuLocation; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="15%">
                    <table>
                        <tbody>
                        <?php
                        $stmt = $this->objDB->prepare("SELECT pageName FROM pages");
                        $stmt->execute();
                        $stmt->bind_result($pageName);
                        while ($checkRow = $stmt->fetch()) {
                            ?>
                            <tr>
                                <td width="100">
                                    <input type="checkbox" name="<?= $pageName; ?>" value="Yes"><?= $pageName; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table>
                        <tbody>
                        <?php
                        $stmt = $this->objDB->prepare("SELECT menuName FROM menu WHERE menuLocation='$getMenuLocation' ORDER BY menuOrder");
                        $stmt->execute();
                        $stmt->bind_result($pageName);
                        while ($checkRow = $stmt->fetch()) {
                            ?>
                            <tr>
                                <td width="100">
                                    <input type="checkbox" name="<?= $pageName; ?>" value="Yes"><?= $pageName; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function switchMode()
    {
        $localAction = null;

        if (isset ($this->setPostID)) {
            $localAction = $this->setPostID;
        } elseif (isset ($this->setGetID)) {
            $localAction = urldecode($this->setGetID);
        }

        Switch (strtoupper($localAction)) {
            case "DELUSER":
                $this->delUser();
                break;
            case "DELETEUSER":
                $this->deleteUser();
                break;
            case "ADDUSER" :
                $this->addUser();
                break;
            case "UPLOADUSER" :
                $this->uploadUser();
                break;
            case "EDITUSER" :
                $this->editUser();
                break;
            case "UPDATEUSER" :
                $this->updateUser();
                break;
            case "MENU" :
                $this->menu();
                break;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string The full version for this module as read from the file's docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}

?>

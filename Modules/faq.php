<?php
/**
 * Frequently Asked Questions, Shows how to add them, modify and Delete them.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype.
 * @version         1.0.1               2016-12-13 16:23:52 SM:  Uses database.
 * @version         1.1.0               2016-12-15 08:50:58 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 16:43:15 SM:  Added doco.
 */
require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * FAQ module.
 */
class faq extends SunLibraryModule
{

    /** @var string $setPostID Module ID as sent via $_POST. */
    private $setPostID;

    /** @var string $setGetID Module ID as sent via $_GET. */
    private $setGetID;

    /** @var string $getFaqID FAQ ID as sent via $_GET. */
    private $getFaqID;

    /** @var string $postFaqID FAQ ID as sent via $_POST. */
    private $postFaqID;

    /**
     * {@inheritdoc}
     *
     * @param mysqli $dbConnection Connection to the database.
     * @return void
     */
    function __construct(mysqli $dbConnection)
    {
        $this->setPostID = filter_input(INPUT_POST, 'moduleID');
        $this->setGetID = filter_input(INPUT_GET, 'moduleID');
        $this->getFaqID = filter_input(INPUT_GET, 'faqID');
        $this->postFaqID = filter_input(INPUT_POST, 'faqID');
        parent::__construct($dbConnection);
    }

    /**
     * FAQ.  This is NOT a constructor.
     *
     * @return void
     */
    public function faq()
    {
        ?>
        <b>Frequently Asked Questions</b> <a href="web-settings.php?id=Faq&&moduleID=AddFaq">
        <button>Add New</button>
    </a>
        <br><br><br>
        <table width="100%" cellpadding="10" cellspacing="0" border="0">
            <tbody>
            <tr>
                <td class="tableTop">Question</td>
            </tr>
            <?php
            $stmt = $this->objDB->prepare("SELECT faqID, faqQuestion, faqAnswer FROM faq ORDER BY faqOrder");
            $stmt->execute();
            $stmt->bind_result($faqID, $faqQuestion, $faqAnswer);
            while ($checkRow = $stmt->fetch()) {
                ?>
                <tr class="rowRef" style=""
                    onclick="location.href = 'web-settings.php?id=Faq&&moduleID=EditFaq&&faqID=<?= $faqID; ?>'">
                    <td width="40%">
                        <?= $faqQuestion; ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Add FAW
     *
     * @return void
     */
    public function addFaq()
    {
        ?>
        <form method="POST" action="web-settings.php?id=Faq&&moduleID=UploadFaq">
            <table border="0" cellpadding="5" cellspacing="5">
                <thead>
                <tr>
                    <td colspan=2>
                        <b>Add Frequently Asked Question</b>
                    </td>
                </tr>
                ';
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                ';
                <tr>
                    <td>
                        <b>Question</b>
                    </td>
                    <td>
                        <b>Answer</b>
                    </td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <input type="text" name="newQuestion" size="100">
                    </td>
                </tr>
                <tr>
                    <td>
                        <textarea name="newAnswer" style="width: 740px; background-color: white;"></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <input type="submit" name="submit" value="Add New">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <?php
    }

    /**
     * Upload FAQ
     *
     * @return void
     */
    public function uploadFaq()
    {
        $newQuestion = filter_input(INPUT_POST, 'newQuestion');
        $newAnswer = filter_input(INPUT_POST, 'newAnswer');
        $newOrder = 1;
        $stmt = $this->objDB->prepare("INSERT INTO faq (faqQuestion, faqAnswer, faqOrder) VALUES (?,?,?)");
        $stmt->bind_param('ssi', $newQuestion, $newAnswer, $newOrder);
        $status = $stmt->execute();
        ?>
        You have successfully added a new Frequently Asked Question
        <br><br><br>Please Wait.....<br>
        <meta http-equiv="refresh" content="3;url=web-settings.php?id=Faq">
        <?php
    }

    /**
     * Edit FAQ
     *
     * @return void
     */
    public function editFaq()
    {
        $faqID = filter_input(INPUT_GET, 'faqID');
        ?>
        <form method="POST" action="?id=Faq&&moduleID=updateFaq">';
            <table cellpadding="10" width="100%" border="0">
                <tbody>
                <tr>
                    <td>
                        <h1>Question Editor</h1>
                    </td>
                </tr>
                <?php
                if ($stmt = $this->objDB->prepare("SELECT faqID, faqQuestion, faqAnswer, faqOrder FROM faq WHERE faqID=? ")) {
                    $stmt->bind_param("i", $faqID);
                    $stmt->execute();

                    $stmt->bind_result($faqID, $faqQuestion, $faqAnswer, $faqOrder);
                    $stmt->fetch();
                    ?>
                    <tr>
                        <td>
                            <input type="hidden" name="faqID" value="<?= $faqID; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="tableTop">
                            <b>Question</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" name="newQuestion" value="<?= $faqQuestion; ?>" size="100">
                        </td>
                    </tr>
                    <tr>
                        <td class="tableTop">
                            <b>Answer:</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <textarea rows=15 name="area2"
                                      style="width: 940px; background-color: white;"><?= $faqAnswer; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="Submit" name="Submit" value="Update">
                            <a href="?id=Faq&&moduleID=DeleteFaq&&faqID='<?= $faqID; ?>">
                                <button>Delete</button>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </form>
        <?php
    }

    /**
     * Update faq
     *
     * @return void
     */
    public function updateFaq()
    {
        $getQuestion = filter_input(INPUT_POST, 'newQuestion');
        $getAnswer = filter_input(INPUT_POST, 'area2');
        $getID = filter_input(INPUT_POST, 'faqID');
        ?>
        Checking: <br><br>
        Question: <br><br>
        <?= $getQuestion; ?><br><br>
        Answer: <br><br>
        <?= $getAnswer; ?><br><br>
        <?= $getID; ?><br>
        <?php
        $stmt = $this->objDB->prepare("UPDATE faq SET faqQuestion=?, faqAnswer=? WHERE faqID=?");
        $stmt->bind_param('ssi', $getQuestion, $getAnswer, $getID);

        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }
        $status = $stmt->execute();
        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        ?>
        <font color="black">
            <b>Question Updated <br><br> Please Wait!!!!<br></b>
            <meta http-equiv="refresh" content="1;url=web-settings.php?id=Faq">
        </font>
        <?php
    }

    /**
     * Delete FAQ
     *
     * @return void
     */
    public function deleteFaq()
    {
        $getID = filter_input(INPUT_GET, 'faqID');
        $stmt = $this->objDB->prepare("DELETE FROM faq WHERE faqID = ?");
        $stmt->bind_param('i', $getID);
        $stmt->execute();
        $stmt->close();
        ?>
        You have successfully Delete a Frequently Asked Question. <br><br><br>Please Wait.....<br>
        <meta http-equiv="refresh" content="3;url=web-settings.php?id=Faq">
        <?php
    }

    /**
     * {@inheritdoc}
     *
     * @return string The full version number for this module, as read from this files docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }

}

?>

<?php
/**
 * Frequently Asked Questions, Shows how to add them, modify and Delete them.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype.
 * @version         1.0.1               2016-12-13 16:23:52 SM:  Uses database.
 * @version         1.1.0               2016-12-15 08:50:58 SM:  Uses SunLibraryModule.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

class faq extends SunLibraryModule
{
	private $setPostID;
	private $setGetID;
	private $getFaqID;
	private $postFaqID;
	
	function __construct(mysqli $dbConnection)
	{
		$this->setPostID = filter_input(INPUT_POST, 'moduleID');
		$this->setGetID = filter_input(INPUT_GET, 'moduleID');		
		$this->getFaqID = filter_input(INPUT_GET, 'faqID');
		$this->postFaqID = filter_input(INPUT_POST, 'faqID');
		parent::__construct($dbConnection);
	}
	
	// SM:  Be careful with this of thing.  As faq is the class name, if __construct doesn't exist, it will treat THIS method as a construct instead.
	public function faq()
	{
		echo '<b>Frequently Asked Questions</b> <a href="web-settings.php?id=Faq&&moduleID=AddFaq"><button>Add New</button></a><br><br><br>';
		echo '<table width=100% cellpadding=10 cellspacing=0 border=0>';
		echo '<tr><td class="tableTop">Question</td></tr>';
		$stmt = $this->objDB->prepare ( "SELECT faqID, faqQuestion, faqAnswer FROM faq ORDER BY faqOrder" );
		$stmt->execute ();
		
		$stmt->bind_result ( $faqID, $faqQuestion, $faqAnswer );
		
		while ( $checkRow = $stmt->fetch () ) {
			
			echo '<tr class="rowRef" style=""  onclick="location.href=\'web-settings.php?id=Faq&&moduleID=EditFaq&&faqID='.$faqID.'\'"><td width="40%">'.$faqQuestion.'</td></tr>';
			 
		}
		echo '</table>';
		
	}
	
	public function addFaq()
	{
		echo '<form method="POST" action="web-settings.php?id=Faq&&moduleID=UploadFaq">';
		echo '<table border=0 cellpadding=5 cellspacing=5>';
		echo '<tr><td colspan=2><b>Add Frequently Asked Question</b></td></tr>';
		echo '<tr><td colspan=2></td></tr>';
		echo '<tr><td><b>Question</b></td><td><b>Answer</b></td></tr>';
		echo '<tr><td><input type="text" name="newQuestion" size="100"></td></tr>';
		echo '<tr><td><textarea name="newAnswer" style="width: 740px; background-color: white;">></textarea></td></tr>';
		echo '<tr><td colspan=2><input type="submit" name="submit" value="Add New"></td></tr>';
		echo '</table>';
        echo '</form>';
	}
	
	public function uploadFaq()
	{
		$newQuestion = filter_input ( INPUT_POST, 'newQuestion' );
		$newAnswer = filter_input ( INPUT_POST, 'newAnswer' );
		$newOrder = 1;

		$stmt = $this->objDB->prepare ( "INSERT INTO faq (faqQuestion, faqAnswer, faqOrder) VALUES (?,?,?)" );
		
		$stmt->bind_param ( 'ssi', $newQuestion, $newAnswer, $newOrder );
		
		$status = $stmt->execute ();
		
		echo 'You have successfully added a new Frequently Asked Question. <br><br><br>Please Wait.....<br>';
		echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Faq">';
		
	}
	public function editFaq()
	{
		$faqID = filter_input ( INPUT_GET, 'faqID' );
		
		echo '<table cellpadding=10 width=100% border=0>';
		echo '<tr><td><h1>Question Editor</h1></td></tr>';
		echo '<form method="POST" action="?id=Faq&&moduleID=updateFaq">';
		
		if ($stmt = $this->objDB->prepare ( "SELECT faqID, faqQuestion, faqAnswer, faqOrder FROM faq WHERE faqID=? " )) {
		
			$stmt->bind_param ( "i", $faqID );
			$stmt->execute ();
		
			$stmt->bind_result ( $faqID, $faqQuestion, $faqAnswer, $faqOrder );
			$stmt->fetch ();
			
			echo '<input type="hidden" name="faqID" value="'.$faqID.'">';
			echo '<tr><td>&nbsp;</td></tr>';
			echo '<tr><td class="tableTop"><b>Question</td></tr>';
			echo '<tr><td><input type="text" name="newQuestion" value="'.$faqQuestion.'" size=100></td></tr>';
			echo '<tr><td class="tableTop"><b>Answer:</b></td></tr>';
			echo '<tr><td><textarea rows=15 name="area2" style="width: 940px; background-color: white;">' . $faqAnswer . '</textarea></td></tr>';
			echo '<tr><td><input type="Submit" name="Submit" value="Update"></form><a href="?id=Faq&&moduleID=DeleteFaq&&faqID='.$faqID.'"><button>Delete</button></a></td></tr>';
			echo '</table>';
			
		}
	}
	

	
	public function updateFaq()
	{
		$getQuestion = filter_input(INPUT_POST, 'newQuestion');
		$getAnswer = filter_input(INPUT_POST, 'area2');
		$getID = filter_input(INPUT_POST, 'faqID');
		
		echo 'Checking: <br><br>';
		
		echo 'Question: <br><br>';
		echo $getQuestion.'<br><br>';
		
		echo 'Answer: <br><br>';
		echo $getAnswer.'<br><br>';
		
		echo $getID.'<br>';
		
		$stmt = $this->objDB->prepare("UPDATE faq SET faqQuestion=?, faqAnswer=? WHERE faqID=?");
        $stmt->bind_param('ssi', $getQuestion, $getAnswer, $getID);
        
        if ($stmt === false) {
            trigger_error($this->objDB->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Question Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Faq">';
	}
	
    public function deleteFaq()
    {
        $getID = filter_input(INPUT_GET, 'faqID');

        $stmt = $this->objDB->prepare("DELETE FROM faq WHERE faqID = ?");
        $stmt->bind_param('i', $getID);
        $stmt->execute();
        $stmt->close();

        echo 'You have successfully Delete a Frequently Asked Question. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Faq">';
	}

    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}
?>

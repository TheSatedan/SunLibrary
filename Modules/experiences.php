<?php
/**
 * Blog System. <br><br> Displaying blog entries in different formats.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:22:52 SM:  Uses database.
 * @version         1.1.0               2016-12-15 08:55:27 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 17:02:25 SM:  Added doco.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * Experiences module.
 */
class experiences extends SunLibraryModule
{  
    /**
     * {@inheritdoc}
     *
     * @param mysqli $dbConnection Connection to the database.
     * @return void
     */
    function __construct(mysqli $dbConnection)
    {
        parent::__construct($dbConnection);
    }

    /**
     * Experiences.  This is NOT a constructor.
     *
     * @return void
     */
    public function experiences()
    {
?>     
        <table width="100%" cellpadding="10" cellspacing="0" border="0">
            <tbody>
                <tr>
                    <td>Experience Information <a href="?id=Experiences&&moduleID=AddExperience"><button>Add New</button></td>
                </tr>
                <tr>
                    <td colspan=3>&nbsp;</td>
                </tr>
                <tr class="tableTop">
                    <td>Identifer</td>
                    <td>Member Name</td>
                    <td>Date</td>
                </tr>
<?php
                $stmt = $this->objDB->prepare("SELECT blogID, blogSubject, blogDate, userFullName FROM blog INNER JOIN users ON blog.userID=users.userID");
                $stmt->execute();
                $stmt->bind_result($blogID, $blogSubject, $blogDate, $userFullName);
                while ($checkRow = $stmt->fetch())
                {
?>
                    <tr class="rowRef">
                        <td>
                            <a href="?id=Experiences&&moduleID=editExperience&&blogID=<?=$blogID;?>"><?=$blogSubject;?></a>
                        </td>
                        <td>
                            <?=$userFullName;?>
                        </td>
                        <td>
                            <?=datChange($blogDate);?>
                        </td>
                    </tr>
<?php
                }
?>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
<?php
    }

    /**
     * Add experience
     *
     * @return void
     */
    public function addExperience()
    {
?>
        <form method="POST" action="?id=Experiences&&moduleID=UploadExperience">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <h1>Add New Experience</h1>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Experince Identifier
                        </td>
                        <td>
                            <input type="text" name="blogSubject" placeholder="enter Experience Identifer" required size="100">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Experience User
                        </td>
                        <td>
                            <select name="userID">
<?php
                                $stmt = $this->objDB->prepare("SELECT userID, userFullName FROM users");
                                $stmt->execute();
                                $stmt->bind_result($userID, $userFullName);
                                while ($checkRow = $stmt->fetch())
                                {
?>
                                    <option value="<?=$userID;?>"><?=$userFullName;?></option>
<?php
                                }
?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Experince Date
                        </td>
                        <td>
                            <input type="text" name="blogDate" placeholder="enter Experience Date" required size="100"> Format: 15/10/2015
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>Experience Content</b>
                            <br><br>
                            <textarea name="area2" style="width: 1040px;  height: 400px; background-color: white;" required placeholder="Experience Content"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>Show Users Name</td>
                        <td>
                            <input type="radio" name="blogAnonymous" value="Yes">Yes<input type="radio" name="blogAnonymous" value="No">No
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" name="submit" value="Add New">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
<?php
    }

    /**
     * Delete experience
     *
     * @return void
     */
    public function deleteExperience()
    {
        $getID = filter_input(INPUT_GET, 'blogID');
        $stmt = $this->objDB->prepare("DELETE FROM blog WHERE blogID = ?");
        $stmt->bind_param('i', $getID);
        $stmt->execute();
        $stmt->close();
?>
        You have successfully Delete a Frequently Asked Question. <br><br><br>Please Wait.....<br>
        <meta http-equiv="refresh" content="3;url=web-settings.php?id=Faq">
<?php
    }

    /**
     * Upload experience
     *
     * @return void
     */
    public function uploadExperience()
    {    
        $userID = filter_input(INPUT_POST, "userID");
        $getDate = filter_input(INPUT_POST, "blogDate");
        $blogBody = filter_input(INPUT_POST, "area2");
        $blogSubject = filter_input(INPUT_POST, "blogSubject");
        $blogAnonymous = filter_input(INPUT_POST, "blogAnonymous");
        $blogDate = datReturn($getDate);
        $stmt = $this->objDB->prepare("INSERT INTO blog (userID, blogSubject, blogDate, blogBody, blogAnonymous) VALUES (?,?,?,?,?)");
        $stmt->bind_param('issss', $userID, $blogSubject, $blogDate, $blogBody, $blogAnonymous);
        $status = $stmt->execute();
?>
        <br><br>You have successfully added a New Experience. <br><br><br>Please Wait.....<br>
        <meta http-equiv="refresh" content="3;url=web-settings.php?id=Experiences">
<?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function renderHeaderLinks()
    {
?>
        <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
<?php
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function documentReadyJavaScript()
    {
?>
        nicEditors.allTextAreas()
<?php
    }

    /**
     * Edit experience
     *
     * @return void
     */
    public function editExperience()
    {
        $blogID = filter_input(INPUT_GET, 'blogID');
        $setServiceID = filter_input(INPUT_GET, 'serviceID');
?>
        <h1>Experience Information Content</h1>
        <br>
        <form method="POST" action="?id=Experiences&&moduleID=updateExperience">
            <input type="hidden" name="blogID" value="<?=$blogID;?>">
<?php
            if ($stmt = $this->objDB->prepare("SELECT blogID, userFullName, blogSubject, blogDate, blogBody, blogAnonymous FROM blog INNER JOIN users ON blog.userID=users.userID WHERE blogID=? "))
            {
                $stmt->bind_param("i", $blogID);
                $stmt->execute();
                $stmt->bind_result($blogID, $userFullName, $blogSubject, $blogDate, $blogBody, $blogAnonymous);
                $stmt->fetch();
?>
                <table width="100%" cellpadding="10">
                    <tbody>
                        <tr>
                            <td width="200">
                                <b>Experience from User
                            </td>
                            <td>
                                <?=$userFullName;?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Experience Identifer</b>
                            </td>
                            <td>
                                <input type="text" name="blogSubject" value="<?=$blogSubject;?>" required size="100">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Experience Date></b>
                            </td>
                            <td>
                                <?=datChange($blogDate);?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>Experience Content</b>
                                <br><br>
                                <textarea rows="10" name="area2" style="width: 1040px;  height: 400px; background-color: white;"><?=mb_convert_encoding(nl2br($blogBody), 'UTF-8', 'UTF-8');?></textarea>
                            </td>
                        </tr>
<?php
                        if ($blogAnonymous=='Yes')
                        {
?>
                            <tr>
                                <td>
                                    <b>Show Users Name</b>
                                </td>
                                <td>
                                    Yes<input type="checkbox" name="blogAnonymous" value="Yes" checked> No <input type="checkbox" name="blogAnonymous" value="No">
                                </td>
                            </tr>
<?php
                        }
                        else
                        {
?>
                            <tr>
                                <td>
                                    <b>Show Users Name</b>
                                </td>
                                <td>
                                    Yes <input type="radio" name="blogAnonymous" value="Yes"> No <input type="radio" name="blogAnonymous" value="No" checked>
                                </td>
                            </tr>
<?php
                        }
?>
                        <tr>
                            <td>
                                <input type="Submit" name="Submit" value="Update">
                            </td>
                        </tr>
                    </tbody>
                </table>
<?php
            }
?>
        </form>
        <a href="?id=Experiences&&moduleID=deleteExperience&&blogID=<?=$blogID;?>><button>Delete</button></a>
<?php           
    }

    /**
     * Update experience
     *
     * @return void
     */
    public function updateExperience()
    {    
        $blogBody = filter_input(INPUT_POST, 'area2');
        $blogID = filter_input(INPUT_POST, 'blogID');
        $blogSubject= filter_input(INPUT_POST, 'blogSubject');
        $showName = filter_input(INPUT_POST, 'blogAnonymous');        
        $stmt = $this->objDB->prepare("UPDATE blog SET blogBody=?,blogSubject=?, blogAnonymous=? WHERE blogID=?");
        $stmt->bind_param('sssi', $blogBody, $blogSubject, $showName, $blogID);
        if ($stmt === false)
            {trigger_error($this->objDB->error, E_USER_ERROR);}
        $status = $stmt->execute();
        if ($status === false)
            {trigger_error($stmt->error, E_USER_ERROR);}
?>
        <font color="black">
            <b>Experience Information Updated <br><br> Please Wait!!!!<br></b>
            <meta http-equiv="refresh" content="1;url=web-settings.php?id=Experiences">
        </font>
<?php
    }
    
    /**
     * {@inheritdoc}
     *
     * @return string The full version number for this module as determined by the file's docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }    
}

?>

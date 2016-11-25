<?php

$dbConnection = databaseConnection();

class experiences {

    protected $dbConnection;
    const ModuleDescription = 'Blog System. <br><br> Displaying blog entries in different formats.';
    const ModuleAuthor = 'Sunsetcoders Development Team.';
    const ModuleVersion = '0.1';
    
    
    function __construct($dbConnection) {
       
        $this->dbConnection = $dbConnection;
    }

    public function experiences() {
        
        echo '<table width=100% cellpadding=10 cellspacing=0 border=0>';
        echo '<tr><td>Experience Information <a href="?id=Experiences&&moduleID=AddExperience"><button>Add New</button></td></tr>';
        echo '<tr><td colspan=3>&nbsp;</td></tr>';
        echo '<tr class="tableTop"><td>Identifer</td><td>Member Name</td><td>Date</td></tr>';
        $stmt = $this->dbConnection->prepare("SELECT blogID, blogSubject, blogDate, userFullName FROM blog INNER JOIN users ON blog.userID=users.userID");
        $stmt->execute();

        $stmt->bind_result($blogID, $blogSubject, $blogDate, $userFullName);

        while ($checkRow = $stmt->fetch()) {

            echo '<tr class="rowRef"><td><a href="?id=Experiences&&moduleID=editExperience&&blogID=' . $blogID . '">' . $blogSubject . '</a></td><td>'.$userFullName.'</td><td>'.datChange($blogDate).'</td></tr>';
        }
        echo '<tr><td>&nbsp;</td></tr>';
        echo '</table>';
    }

    public function addExperience() {

        echo '<form method="POST" action="?id=Experiences&&moduleID=UploadExperience">';
        echo '<table>';
        echo '<tr><td><h1>Add New Experience</h1></td></tr>';
        echo '<tr><td>Experince Identifier </td><td><input type="text" name="blogSubject" placeholder="enter Experience Identifer" required size=100></td></tr>';
        
        echo '<tr><td>Experience User </td><td>';
        
        echo '<select name="userID">';
        $stmt = $this->dbConnection->prepare("SELECT userID, userFullName FROM users");
        $stmt->execute();

        $stmt->bind_result($userID, $userFullName);

        while ($checkRow = $stmt->fetch()) {

            echo '<option value="'.$userID.'">'.$userFullName.'</option>';
        }
        echo '</select>';
        
        echo '</td></tr>';
        echo '<tr><td>Experince Date </td><td><input type="text" name="blogDate" placeholder="enter Experience Date" required size=100> Format: 15/10/2015</td></tr>';
        echo '<tr><td colspan=2><b>Experience Content</b><br><br><textarea name="area2" style="width: 1040px;  height: 400px; background-color: white;" required placeholder="Experience Content"></textarea></td></tr>';
        echo '<tr><td>Show Users Name </td><td><input type="radio" name="blogAnonymous" value="Yes"> Yes <input type="radio" name="blogAnonymous" value="No"> No </td></tr>';
        echo '<tr><td colspan=2><input type="submit" name="submit" value="Add New"></td></tr>';
        echo '</table>';
        echo '</form>';
    }

        public function deleteExperience() {
        
        $getID = filter_input(INPUT_GET, 'blogID');

        $stmt = $this->dbConnection->prepare("DELETE FROM blog WHERE blogID = ?");
        $stmt->bind_param('i', $getID);
        $stmt->execute();
        $stmt->close();

        echo 'You have successfully Delete a Frequently Asked Question. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Faq">';
    }
    
    public function uploadExperience() {
        
        $userID = filter_input(INPUT_POST, "userID");
        $getDate = filter_input(INPUT_POST, "blogDate");
        $blogBody = filter_input(INPUT_POST, "area2");
        $blogSubject = filter_input(INPUT_POST, "blogSubject");
        $blogAnonymous = filter_input(INPUT_POST, "blogAnonymous");

        $blogDate = datReturn($getDate);
        
        $stmt = $this->dbConnection->prepare("INSERT INTO blog (userID, blogSubject, blogDate, blogBody, blogAnonymous) VALUES (?,?,?,?,?)");

        $stmt->bind_param('issss', $userID, $blogSubject, $blogDate, $blogBody, $blogAnonymous);

        $status = $stmt->execute();

        echo '<br><br>You have successfully added a New Experience. <br><br><br>Please Wait.....<br>';
        echo '<meta http-equiv="refresh" content="3;url=web-settings.php?id=Experiences">';
    }

    public function editExperience() {
        ?>
        <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
        <script type="text/javascript"> bkLib.onDomLoaded(function () {
                nicEditors.allTextAreas()
            });</script>
        <?php

        $blogID = filter_input(INPUT_GET, 'blogID');
        $setServiceID = filter_input(INPUT_GET, 'serviceID');

        echo '<h1>Experience Information Content</h1><br>';
        echo '<form method="POST" action="?id=Experiences&&moduleID=updateExperience">';
        echo '<input type="hidden" name="blogID" value="' . $blogID . '">';


        if ($stmt = $this->dbConnection->prepare("SELECT blogID, userFullName, blogSubject, blogDate, blogBody, blogAnonymous FROM blog INNER JOIN users ON blog.userID=users.userID WHERE blogID=? ")) {

            $stmt->bind_param("i", $blogID);
            $stmt->execute();

            $stmt->bind_result($blogID, $userFullName, $blogSubject, $blogDate, $blogBody, $blogAnonymous);
            $stmt->fetch();
            echo '<table width=100% cellpadding=10>';
            echo '<tr><td width=200><b>Experience from User</td><td>'.$userFullName.'</td></tr>';

            echo '<tr><td><b>Experience Identifer</td><td><input type="text" name="blogSubject" value="'.$blogSubject.'" required size=100></td></tr>';
            echo '<tr><td><b>Experience Date</td><td>'.datChange($blogDate).'</td></tr>';
            
            echo '<tr><td colspan=2><b>Experience Content</b><br><br><textarea rows=10 name="area2" style="width: 1040px;  height: 400px; background-color: white;">' . mb_convert_encoding(nl2br($blogBody), 'UTF-8', 'UTF-8') . '</textarea></td></tr>';
            
            if ($blogAnonymous=='Yes')
            echo '<tr><td><b>Show Users Name</td><td>Yes <input type="checkbox" name="blogAnonymous" value="Yes" checked> No <input type="checkbox" name="blogAnonymous" value="No"></td></tr>';
        else {
            echo '<tr><td><b>Show Users Name</td><td>Yes <input type="radio" name="blogAnonymous" value="Yes"> No <input type="radio" name="blogAnonymous" value="No" checked></td></tr>';
        }
            echo '<tr><td><input type="Submit" name="Submit" value="Update"></td></tr>';
            echo '</table>';
            
        }
        echo '</form>';
        echo '<a href="?id=Experiences&&moduleID=deleteExperience&&blogID='.$blogID.'><button>Delete</button></a>';
                
    }

    public function updateExperience() {
        
        $blogBody = filter_input(INPUT_POST, 'area2');

        $blogID = filter_input(INPUT_POST, 'blogID');
        $blogSubject= filter_input(INPUT_POST, 'blogSubject');
        $showName = filter_input(INPUT_POST, 'blogAnonymous');
        
        $stmt = $this->dbConnection->prepare("UPDATE blog SET blogBody=?,blogSubject=?, blogAnonymous=? WHERE blogID=?");
        $stmt->bind_param('sssi', $blogBody, $blogSubject, $showName, $blogID);

        if ($stmt === false) {
            trigger_error($this->dbConnection->error, E_USER_ERROR);
        }

        $status = $stmt->execute();

        if ($status === false) {
            trigger_error($stmt->error, E_USER_ERROR);
        }
        echo '<font color=black><b>Experience Information Updated <br><br> Please Wait!!!!<br>';
        echo '<meta http-equiv="refresh" content="1;url=web-settings.php?id=Experiences">';
    }

}

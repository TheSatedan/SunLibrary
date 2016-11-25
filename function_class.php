<style>
    body
    {
        background-color: #eee;
        padding: 0;
        margin: 0;
        font-family: century gothic;
    }
    .body-content
    {
        margin: 0 auto;
        max-width: 1024px;
    }

    .module-display
    {
        float: left;
        width: 400px;
        border: 1px #333 solid;
        background-color: #fff;
        height: 350px; 
        padding: 10px;
        margin: 15px;
    }
    h1
    {
        color: darkcyan;
    }
</style>

<?php

$dbConnection = databaseConnection();

function sunlibrary() {

    echo '<div class="body-content">';
    if ($handle = opendir('Modules')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $moduleName = substr($entry, 0, -4);
                echo '<div class="module-display"><h1>' . strtoupper($moduleName) . '</h1>';
                
                include ('Modules/'.$entry);
                echo '<b>Version No.: </b><br>'.$moduleName::ModuleVersion.'<br><br>';  
                echo '<b>Author: </b><br>'.$moduleName::ModuleAuthor.'<br><br>';  
                echo '<b>Description: </b><br>'.$moduleName::ModuleDescription.'<br><br>';  
                echo '<br><a href="">View Module</a></div>';
            }
        }
        closedir($handle);
    }
    echo '</div>';
}

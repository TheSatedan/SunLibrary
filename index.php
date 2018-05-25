<?php
/**
 * Index page.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:12:35 SM:  Prototype
 * @version         1.1.0               2016-12-15 16:17:53 SM:  Made use of the ModuleManager.
 * @version         1.1.1               2018-05-25 14:32:00 CST SM:  PSR fixes
 */
include 'auth.php';
include 'function_class.php';

try
{
    $objDB=Database::GetDBConnection();
    $objModules=new ModuleManager($objDB);
}
catch(Exception $objException)
{
    die($objException);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?=PROJECT_TITLE;?></title>
        <meta name="viewport" content="width=device-width">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script type="text/javascript">
            $(function() {
<?php
                foreach($objModules as $objModule)
                {
                    $objModule->documentReadyJavaScript();
                }
?>
            });
        </script>
        <script type="text/javascript">
<?php
            foreach($objModules as $objModule)
            {
                $objModule->renderCustomJavaScript();
            }
?>
        </script>
<?php
        foreach($objModules as $objModule)
        {
            $objModule->renderHeaderLinks();
        }
?>        
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
                height: 370px; 
                padding: 10px;
                margin: 15px;
                position: relative;
            }
            h2
            {
                color: darkcyan;
            }

            a.bottomView
            {
                position: absolute; 
                bottom: 0;
                text-decoration: none;
                color: darkcyan;
                font-weight: bolder;
            }
        </style>
    </head>
    <body>
<?php
        try
        {
            sunlibrary($objModules);
        }
        catch(Exception $objException)
        {
            $objDB->close();
            die($objException);
        }
?>
    </body>
</html>
<?php
@$objDB->close();

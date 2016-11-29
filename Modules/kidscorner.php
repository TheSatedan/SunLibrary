<?php
/**
 * Kids Corner module.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 */

try
{
    $dbConnection = databaseConnection();
}
catch(Exception $objException)
{
    die($objException);
}

class kidscorner
{
	const ModuleDescription = 'This Module Allows access to change all Images and Text in a text Editor.';
	const ModuleAuthor = 'Sunsetcoders Development Team.';
	const ModuleVersion = '1.0';
	
	function __construct()
	{
		
	}
	
	public function landingPage()
	{
		echo 'Hello World';
	}
}
?>

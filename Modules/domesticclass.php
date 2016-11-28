<?php
/**
 * Domestic class.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 */

$dbConnection = databaseConnection();


class domesticClass
{
	const ModuleDescription = 'This Module Allows Modify Access levels for the Domestic Abuse Database.';
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
<?php
/**
 * Domestic class.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @version         1.0.0               2016-11-28 08:48:35 SM: Prototype
 * @version         1.0.1               2016-12-13 16:19:19 SM: Uses database.
 */

// SM:  We create this but it isn't used?  I'm guessing we would use it in any derived class - if so, then the constructor should be 
//      accepting this as a parameter and the class marked abstract - I think that's the idea, from the other modules?
try
{
    $dbConnection=Database::GetDBConnection();
}
catch(Exception $objException)
{
    die($objException);
}

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

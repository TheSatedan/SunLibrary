<?php
/**
 * This Module Allows Modify Access levels for the Domestic Abuse Database.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:19:19 SM:  Uses database.
 * @version         1.1.0               2016-12-15 15:01:57 SM:  Uses SunLibraryModule.
 */

require_once dirname(dirname(__FILE__)).'/SunLibraryModule.php';

class domesticClass extends SunLibraryModule
{
	function __construct(mysqli $objDB)
	{
		parent::__construct($objDB);
	}
	
	public function landingPage()
	{
		echo 'Hello World';
	}
	
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}
?>

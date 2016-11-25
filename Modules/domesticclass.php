<?php

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
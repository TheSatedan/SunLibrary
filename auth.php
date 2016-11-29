<?php
/**
 * Database utilies?
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @todo            Maybe turn this into a DB factory - a class that has a static method to return a DB instance.
 * @version         1.0.0               2016-11-28 08:23:21 SM:  Prototype
 * @version         1.1.0               2016-11-29 07:30:26 SM:  Function headings, adding PDO function support.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('DB_HOST','host',true);
define('DB_USER','root',true);
define('DB_PASS','Aort101ms',true);
define('DB_CATALOGUE','sunlibrary',true);

/**
 * Returns a handle to a mysqli connection.
 *
 * @return          mysqli              Connection to MySQLI database.
 * @throws          Exception           Unable to connect to the database.
 */
function databaseConnection()
{
    $objDB=new mysqli(DB_HOST, DB_USER, DB_PASS, DB_CATALOGUE);
    if ($objDB->connect_error)
        throw new Exception(__FUNCTION__." failed to connect to the database.  DB error ({$objDB->connect_errno}) - {$objDB->connect_error}");
    return $objDB;
}

/**
 * Returns a PDO connection object to the database.
 *
 * @return          PDO             PDO DB connection.
 * @throws          Exception       PDO extension not loaded.
 * @throws          PDOException    PDO could not connect to the database.
 */
function getDatabasePDO()
{
    if (!extension_loaded('pdo'))
        throw new Exception(__FUNCTION__.' failed as PDO extension was not loaded.');
    try
    {
        $txtDSN='mysql:dbname='.DB_CATALOGUE.';host='.DB_HOST;
        $arrOptions=array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',PDO::ATTR_PERSISTENT => true);
        $objDB=new PDO($txtDSN, DB_USER, DB_PASS, $arrOptions);        
        $objDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $objException)
    {
        throw $objException;
    }
    return $objDB;
}
?>

<?php
/**
 * This Module Allows access to change all Images and Text in a text Editor.
 *
 * @author          Andrew Jeffries <andrew@sunsetcoders.com.au>
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-11-28 08:48:35 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:25:58 SM:  Uses database.
 * @version         1.1.0               2016-12-15 08:39:46 SM:  Uses SunLibraryModule.
 * @version         1.1.1               2016-12-16 16:16:35 SM:  Added doco.
 */

require_once dirname(dirname(__FILE__)) . '/SunLibraryModule.php';

/**
 * Kidscorner module.
 */
class kidscorner extends SunLibraryModule
{
    /**
     * {@inheritdoc}
     *
     * @param mysqli $objDB Connection to the database.
     * @return void
     */
    function __construct(mysqli $objDB)
    {
        parent::__construct($objDB);
    }

    /**
     * Landing page.
     *
     * @return void
     */
    public function landingPage()
    {
        echo 'Hello World';
    }

    /**
     * {@inheritdoc}
     *
     * @return string The full version number of this module, as read from the file's docblock.
     */
    public function getVersion()
    {
        return $this->readVersionFromFile(__FILE__);
    }
}

?>

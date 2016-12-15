<?php
/**
 * The function class.
 *
 * @author          Andrew Jeffries <andrew.jeffries@sunsetcoders.com.au>
 * @todo            Pass the db connection instance to each module when rendered, instead of keeping it global.
 * @todo            Turn modules into classes that extend an abstract parent class, so they can be loaded and executed cleanly and securely.
 * @version         1.0.0               2016-11-28 08:14:46 SM:  Prototype
 * @version         1.0.1               2016-12-13 16:38:42 SM:  Uses database.
 * @version         1.1.0               2016-12-15 14:46:44 SM:  Has access to FileAttributeTools to obtain version number details.
 */

require_once 'FileAttributeTools.php';
use \FileAttributeTools;

/**
 * Loads and handles modules for a given site.
 * Modules are dynamically loaded from the 'modules' folder, and then rendered from here.
 *
 * @return          void
 */
function sunlibrary(ModuleManager $objModules) 
{
    try
    {
?>
        <h1>All Loaded Modules</h1>
        <div class="module-display">
            <ul>
<?php
                foreach($objModules as $txtName=>$objModule)
                {
                    $objFile=FileAttributeTools\FileAttributeTools("Modules\{$objModules[$txtName]}");
?>
                    <li><code><?=get_class($objModule); ?></code></li>
                    <ul>
                        <li>Version: <?=$objFile->txtVersion;?></li>
                        <li>Version Date: <?=$objFile->txtModifiedDate;?></li>
                        <li>Version Comments: <?=$objFile->txtModifiedComments;?></li>
                        <li>Description: <?=$objFile->txtDescription;?></li>
                        <li>Authors</li>
                        <ul>
<?php
                            foreach($objFile->arrAuthor as $txtEmail=>$txtAuthor)
                            {
                                if (is_integer($txtEmail))
                                {
?>
                                    <li><?=$txtAuthor;?></li>
<?php
                                }
                                else
                                {
?>
                                    <li><?=$txtAuthor;?> - Email: <?=$txtEmail;?></li>
<?php
                                }
                            }
?>
                        </ul>
                    </ul>
<?php
                }
?>
            </ul>
        </div>
        <p></p>
        <h1>Rendering All Modules</h1>
        <div class="module-display">
<?php
            $objModules->RenderAll();
?>
        </div>
<?php
        $objModules->RenderAll();
    }
    catch(Exception $objException)
    {
        die($objException);
    }   
}

final class ModuleManager implements Iterator,ArrayAccess,Countable
{
    /** @var array $arrModules A collection of module names that we have loaded. */
    protected $arrModules=array();
    
    /** @var int $lngPosition A pointer variable for use when travering this object with foreach.  Part of the Iterator inter */
    protected $lngPosition;
    
    /**
     * Construct a new module manager.
     *
     * @param mysqli $objDB Connection to the database.
     * @return void
     * @throws Exception If the loading and instantiating of any particular module failed.
     */
    public function __construct(mysqli $objDB)
    {
        $this->lngPosition=0;
        if ($handle = opendir('Modules')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {

                    //--------------------------------------------------------------------------------------                
                    // SM:  Include the module and then render the details.
                    //--------------------------------------------------------------------------------------                

                    require_once 'Modules/' . $entry;
                    
                    try
                    {
                        $objModule=new $entry($dbConnection);
                        $arrModules[$entry]=$objModule;
                    }
                    catch(Exception $objException)
                    {
                        throw $objException;
                    }                                        
                }
            }
            closedir($handle);
        }
    }
    
    /**
     * Render all of the modules we have loaded.
     *
     * @return void
     * @throws Exception Thrown if the callToFunction of any SunLibrary module has failed.
     */
    public function RenderAll()
    {
        foreach($this as $objModule)
        {
            try
            {
                $objModule->callToFunction();
            }
            catch(Exception $objException)
            {
                throw $objException;
            }
        }
    }
    
    /**
     * Iterator interface
     */
    public function rewind()
    {
        $this->lngPosition = 0;
    }
    
    /**
     * Iterator interface
     */
    public function key()
    {
        return $this->lngPosition;
    }
    
    /**
     * Iterator interface
     */
    public function next()
    {
        ++$this->lngPosition;
    }
    
    /**
     * Iterator interface
     */
    public function valid()
    {
        return isset($this->arrModules[$this->lngPosition]);
    }

    /**
     * ArrayAccess interface
     */
    public function offsetSet($mixOffset, SunLibraryModule $objModule)
    {
        if (is_null($mixOffset))
            $this->arrModules[] = $objModule;
        else
            $this->arrModules[$mixOffset] = $objModule;
    }
    
    /**
     * ArrayAccess interface
     */
    public function offsetExists($mixOffset)
    {
        return isset($this->arrModules[$mixOffset]);
    }    

    /**
     * ArrayAccess interface
     */
    public function offsetUnset($mixOffset)
    {
        unset($this->arrModules[$mixOffset]);
    }
    
    /**
     * ArrayAccess interface
     */
    public function offsetGet($mixOffset)
    {
        return isset($this->arrModules[$mixOffset]) ? $this->arrModules[$mixOffset] : null;
    }
    
    /**
     * Countable interface
     */
    public function count() 
    { 
        return count($this->arrModules);
    }
}
?>

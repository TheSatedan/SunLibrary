<?php
/**
 * Sun Library abstract module.  Any module loaded dynamically should be an instance of this.
 *
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-12-14 15:14:53 SM:  Prototype
 */

abstract class SunLibraryModule
{
    protected $objDB;
    
    public function __construct(mysqli $objDB)
    {
        $this->objDB=$objDB;
        $this->assertTablesExist();
    }
    
    public function renderHeaderLinks()
    {
        //
    }
    
    public function renderCustomJavaScript()
    {
        //
    }
    
    public function callToFunction()
    {
        //
    }
    
    protected function assertTablesExist()
    {
        //
    }
    
    public function getVersion()
    {
        return 'unknown';
    }
    
    protected function readVersionFromFile($txtFile)
    {
        $txtVersion='unknown';
        $hdlFile=@fopen($txtFile,r);
        if ($hdlFile===false)
            return $txtVersion;
        $blnProcessing=true;
        $lngMaxVersion=0;
        while($blnProcessing)
        {
            $txtLine=fgets($hdlFile, 4096);
            if ($txtLine===false)
                $blnProcessing=false;
            elseif (stripos($txtLine,'*/') > 0)
                $blnProcessing=false;
            else
            {
                //--------------------------------------------------------------------------------------------------------------------------
                // SM:  Look for anything in the line from the file with a version number in the form 99.99.99 - this is the version number
                //      from the docblock.  Track which version is largest and return that.
                //--------------------------------------------------------------------------------------------------------------------------

                $arrMatches=array();
                if (preg_match ('/(\d+)\.(\d+)\.(\d+)/', $txtLine, $arrMatches))
                {
                    $lngVersion=1000000*intval($arrMatches[1])+1000*intval($arrMatches[2]) + intval($arrMatches[3]);
                    if ($lngVersion > $lngMaxVersion)
                    {
                        $txtVersion=$arrMatches[0];
                        $lngMaxVersion=$lngVersion;
                    }
                }
            }
        }
        fclose($hdlFile);
        return $txtVersion;
    }
}
?>

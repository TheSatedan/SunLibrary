<?php
/**
 * Sun Library abstract module.  Any module loaded dynamically should be an instance of this.
 *
 * @author          Simon Mitchell <kartano@gmail.com>
 * @version         1.0.0               2016-12-14 15:14:53 SM:  Prototype
 * @version         1.1.0               2016-12-15 08:32:18 SM:  switchMode is now part of the footprint.
 */

abstract class SunLibraryModule
{
    protected $objDB;
    
    public function __construct(mysqli $objDB)
    {
        $this->objDB=$objDB;
        $this->assertTablesExist();
        $this->switchMode();
    }
    
    public function renderHeaderLinks()
    {
        //
    }
    
    public function renderCustomJavaScript()
    {
        //
    }
    
    public function documentReadyJavaScript()
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
    
    public function getFileAttributes()
    {
        return null;
    }
    
    public function switchMode()
    {
        // SM:  The default action is to do nothing.  If a class needs a switch mode, it should override this method
        //      and put the necessary code there.
    }    
    protected function readVersionFromFile($txtFile)
    {
    }
}

final class FileAttributes
{
    public $txtVersionNumber;
    public $txtAuthor;
    public $txtVersion;
    public $txtVersionNotes;
    
    public function __construc($txtFile)
    {
        $this->txtVersion='unknown';
        $hdlFile=@fopen($txtFile,r);
        if ($hdlFile===false)
            throw new InvalidArgumentException("The module file $txtFile could not be opened for reading.");
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

                //--------------------------------------------------------------------------------------------------------------------------
                // SM:  Look for the description.  This should be an asterix, following by random chars.
                //      A description can take several lines.  We read it until we hit a line with an @tag indicated we are
                //      Looking at other attributes.
                //      Descripion lines should start with an asterix, and aside from the description body, there should be NO phpdoc tags here.
                //--------------------------------------------------------------------------------------------------------------------------
                if (stripos('*',$txtLine)>0 && stripos('@',$txtLine,4)==0)
                {
                    while (true)
                    {
                        $txtLine=fgets($hdlFile, 4096);
                        if ($txtLine===false)
                            $blnProcessing=false;
                        elseif (stripos($txtLine,'*/') > 0)
                            $blnProcessing=false;
                        $lngStart=strpos('*',$txtLine);
                        [^*]+[a-zA-Z0-9\(\)]*
                        $this->txtDescription+=trim(substr());
                }
            }
        }
        fclose($hdlFile);
        return $txtVersion;
        
    }
}
?>
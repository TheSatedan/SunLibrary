<?php
/**
 * File attributes reader.
 * Scans the top docblock of a script and attempts to gather the version information and such about a particular file.
 *
 * Any line with no specific docblock tag will just get appended to the description field.
 *
 * This library is a SEPARATE repo from the SunLibrary.
 * @see             https://github.com/kartano/FileAttributeTools
 *
 * @author          Simon Mitchell <kartano@gmail.com>
 * @author          Author with no email
 * @author          Another author with an email <siastro@gmail.com>
 * @package         FileAttributeTools
 * @version         1.0.0               2016-11-28 08:46:13 SM:  Prototype
 * @version         1.1.0               2016-12-15 14:01:45 SM:  Now returns a DOMDocument format for the values.
 */

namespace FileAttributeTools;

// SM:  Testing only.
/*
$objFile=new FileAttributes(__FILE__);
echo "<pre>";
print_r($objFile);
print_r($objFile->ToXML()->saveXML());
echo "</pre>";
*/

/**
 * File Attributes class.
 */
final class FileAttributes
{
    /** @var string $txtFilename The filename that was sent to use for verisioning. */
    private $txtFilename;
    /** @var string $txtAuthor The value from the Author tag in the header. */
    public $arrAuthor;
    /** @var string $txtVersion The full x.x.x version number. */
    public $txtVersion;
    /** @var string $txtVersionMajor The major version ID. */
    public $txtVersionMajor;
    /** @var string $txtVersionMinor The minor version ID. */
    public $txtVersionMinor;
    /** @var string $txtVersionPatch The patch number of the ID. */
    public $txtVersionPatch;
    /** @var string $txtDescription The description for this file, from the top blockdoc. */
    public $txtDescription;
    /** @var string $txtModifiedDate ISO8601 date and time format for the current version. */
    public $txtModifiedDate;
    /** @var string $txtModifiedComments The comments assocated with the current version. */
    public $txtModifiedComments;
    /** @var string $txtNamespace The name space to which the file belongs - if there is one.  It reads this by looking for a NAMESPACE statement, not through the docblock. */
    public $txtNamespace;
    /** @var array $arrUses An arrqay of names of other namespaces this file uses. */
    public $arrUses;
    /** @var array $arrSee Any of the SEE markups in the document. */
    public $arrSee;
    
    /**
     * Constructor
     *
     * @param string $txtFile The FQN of the file to be parsed.
     * @return void
     */
    public function __construct($txtFile)
    {
        $this->txtFilename=$txtFile;
        $this->txtVersion='';
        $this->arrAuthor=array();
        $this->txtDescription='';
        $this->txtVersionMajor='';
        $this->txtVersionMinor='';
        $this->txtVersionPatch='';
        $this->txtDescription='';
        $this->txtModifiedDate='';
        $this->txtModifiedComments='';
        $this->arrUses=array();
        $this->arrSee=array();

        $hdlFile=@fopen($txtFile,r);
        if ($hdlFile===false)
            throw new InvalidArgumentException("The module file $txtFile could not be opened for reading.");
        $blnProcessing=true;
        $blnWeAreInDockblock=true;
        $lngMaxVersion=0;
        while($blnProcessing)
        {
            $txtLine=fgets($hdlFile, 4096);
            
            //------------------------------------------------------------------------------------------------
            // SM:  If we hit the end of the file or we find an open brace that is commencing code, we stop.
            //------------------------------------------------------------------------------------------------

            if ($txtLine===false)
                $blnProcessing=false;
            elseif (stripos($txtLine,'*/'))
                $blnWeAreInDockblock=false;
            elseif (stripos($txtLine,'{') )
                $blnProcessing=false;

            //------------------------------------------------------------------------------------------------
            // SM:  All of this code assumes we are still reading the lines from the file's docblock.
            //------------------------------------------------------------------------------------------------

            elseif ($blnWeAreInDockblock)
            {
                
                //------------------------------------------------------------------------------------------------
                // SM:  Ignore the opening comment, signally the start of a docblock.  Consume it - move to next line.
                //------------------------------------------------------------------------------------------------
                
                if (stripos($txtLine,'/**')===false)
                {
                    $arrMatches=array();
                    
                    //--------------------------------------------------------------------------------------------------------------------------
                    // SM:  Look for anything in the line from the file with a version number in the form 99.99.99 - this is the version number
                    //      from the docblock.  Track which version is largest and return that.  Grab the version comments, if there are any.
                    //--------------------------------------------------------------------------------------------------------------------------
            
                    if (preg_match('/(\d+)\.(\d+)\.(\d+)/', $txtLine, $arrMatches) && stripos($txtLine,'@version'))
                    {      
                        $lngVersion=10000000*intval($arrMatches[1])+10000*intval($arrMatches[2]) + intval($arrMatches[3]);
                        if ($lngVersion > $lngMaxVersion)
                        {
                            $this->txtVersion=$arrMatches[0];
                            $this->txtVersionMajor=$arrMatches[1];
                            $this->txtVersionMinor=$arrMatches[2];
                            $this->txtVersionPatch=$arrMatches[3];
                            $lngMaxVersion=$lngVersion;
                            preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2}) (.*)/',$txtLine,$arrMatches);
                            $this->txtModifiedDate="{$arrMatches[1]}-{$arrMatches[2]}-{$arrMatches[3]} {$arrMatches{4}}:{$arrMatches[5]}:{$arrMatches[6]}";
                            $this->txtModifiedComments=$arrMatches[7];
                        }
                    }
                    
                    //--------------------------------------------------------------------------------------------------------------------------
                    // SM:  Look for a line that has no tag, just text after the asterix.  Consider this a description.
                    //--------------------------------------------------------------------------------------------------------------------------
                    
                    if (stripos($txtLine,'*') && !stripos($txtLine,'@'))
                    {
                        $lngStart=strpos('*',$txtLine);
                        $txtDescription=substr($txtLine,$lngStart+2);
                        if ($txtDescription!='')
                            $this->txtDescription.=' '.trim($txtDescription);
                    }

                    //--------------------------------------------------------------------------------------------------------------------------
                    // SM:  If we've found an author tag, find their name.  We need to be wary of the inclusion of email addresses <....>
                    //      We will filter those out, if they are there.
                    //--------------------------------------------------------------------------------------------------------------------------
                    
                    if (stripos($txtLine,'@author'))
                    {
                        if (stripos($txtLine,'<'))
                        {
                            $lngStart=stripos($txtLine,'@author');
                            $lngEnd=stripos($txtLine,'<');
                            // SM:  Grab the email contents.
                            preg_match('/<(.*?)>/',$txtLine, $arrMatches);
                            $txtAuthorKey=$arrMatches[1];
                            if ($lngStart>=0)
                                $this->arrAuthor[$txtAuthorKey]=trim(substr($txtLine, $lngStart+7,($lngEnd-$lngStart-7)));
                        }
                        else
                        {
                            $lngStart=stripos($txtLine,'@author');
                            $lngKey=count($this->arrAuthor)+1;
                            $this->arrAuthor[$lngKey]=str_replace('@author','',trim(substr($txtLine, $lngStart+7)));
                        }
                    } // End if we found an author.
                    
                    if (stripos($txtLine,'@see'))
                    {
                        $lngStart=stripos($txtLine,'@see');
                        $this->arrSee[]=str_replace('@see','',trim(substr($txtLine, $lngStart+4)));                        
                    }
                } // End if we are in the document section of the docblock.
            }// End if we are in a docblock.
            else
            {
                $arrMatches=array();
                // SM: Look for namespace declarations.
                if (preg_match('/namespace( )+(\w+)+(\w+)*/',$txtLine,$arrMatches))
                    $this->txtNamespace=$arrMatches[2];
                else
                // SM:  Look for use declarations.
                if (preg_match('/use( )+(\w+)+(\w+)*/',$txtLine,$arrMatches))
                    $this->arrUses[]=$arrMatches[2];
            }
        } // End processing file.
        
        fclose($hdlFile);

        if ($this->txtVersion==='')
            $this->txtVersion='unknown';
        if ($this->txtDescription==='')
            $this->txtDescription='unknown';
        if ($this->txtVersionMajor=='')
            $this->txtVersionMajor='unknown';
        if ($this->txtVersionMinor=='')
            $this->txtVersionMinor='unknown';
        if ($this->txtVersionPatch=='')
            $this->txtVersionPatch='unknown';
        if ($this->txtDescription=='')
            $this->txtDescription='unknown';
        if ($this->txtModifiedDate=='')
            $this->txtModifiedDate='unknown';
        if ($this->txtModifiedComments=='')
            $this->txtModifiedComments='unknown';
        if ($this->txtNamespace=='')
            $this->txtNamespace='unknown';
    }
    
    /**
     * Convers the data to an xml format.
     *
     * @return \DOMDocument XML object.
     */
    public function ToXML()
    {
        $objDOM=new \DOMDocument('1.0', 'iso-8859-1');

        $objRoot=$objDOM->createElement('versions');
        $objRoot->setAttribute('filename',$this->txtFilename);
        
        $objVersion=$objDOM->createElement('version');
        $objVersion->setAttribute('value',$this->txtVersion);
        $objVersion->setAttribute('major',$this->txtVersionMajor);
        $objVersion->setAttribute('minor',$this->txtVersionMinor);
        $objVersion->setAttribute('patch',$this->txtVersionPatch);
        $objVersion->setAttribute('modified',$this->txtModifiedDate);
        $objVersion->setAttribute('comments',$this->txtModifiedComments);
        $objRoot->appendChild($objVersion);

        $objAuthors=$objDOM->createElement('authors');
        foreach($this->arrAuthor as $txtKey=>$txtAuthor)
        {
            $objAuthor=$objDOM->createElement('author');
            $objAuthor->setAttribute('name',$txtAuthor);
            if (is_integer($txtKey))
                $objAuthor->setAttribute('email','');
            else
                $objAuthor->setAttribute('email',$txtKey);
            $objAuthors->appendChild($objAuthor);
        }
        $objRoot->appendChild($objAuthors);

        $objDescription=$objDOM->createElement('description');
        $objDescription->setAttribute('value',$this->txtDescription);
        $objRoot->appendChild($objDescription);

        $objNamespace=$objDOM->createElement('namespace');
        $objNamespace->setAttribute('value',$this->txtNamespace);
        $objRoot->appendChild($objNamespace);

        $objUses=$objDOM->createElement('uses');
        foreach($this->arrUses as $txtUse)
        {
            $objUse=$objDOM->createElement('use');
            $objUse->setAttribute('name',$txtUse);
            $objUses->appendChild($objUse);
        }
        $objRoot->appendChild($objUses);
        
        $objSees=$objDOM->createElement('sees');
        foreach($this->arrSee as $txtSee)
        {
            $objSee=$objDOM->createElement('see');
            $objSee->setAttribute('value',$txtSee);
            $objSees->appendChild($objSee);
        }
        $objRoot->appendChild($objSees);
        
        $objDOM->appendChild($objRoot);
        return $objDOM;
    }
}
?>

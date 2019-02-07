<?php

class CreateCodeGenerator
{
    /** The indentation used in the output code. */
    protected $indentation = '    ';
    /** Just out of convenience so we don't we need to type this all the time */
    protected $strPrivStatic = 'private static final String ';
    
    public $tableVariables = [];
    public $errors = [];
    
    /**
     * Multi dimensional array structured as follows:
     * 'table_name' => 
     *  'column_name_1' => 'variablename'
     *  'column_name_2' => 'variablename'
     *
     * For the variableName of the table itself @see $tableVariables
    */
    public $tableColumnVariables = [];
    
    /**
     * Please note that splitting the inserts statements is done in a very rough manor. 
     * This can easily be broken.
    */
    public function CreateCodeGenerator($sql, $className = 'MyAppDbHelper', $identation = '    ')
    {
        $this->className = $className;
        $this->identation = $identation;
        $this->strPrivStatic = $identation.$this->strPrivStatic;
        
        //Single 'quotes' in comments seem to throw the parser off 
        //Who knows what else does - we don't need them, so remove them.
        $sql = preg_replace('~/\*.*?\*\/~s', '', $sql);

        //Split the create queries up @TODO refine this... it's very brute force, could possibly use the parser itself.
        $createParts = explode(";", $sql);
        foreach($createParts as $sqlCreate)
        {
            if(!empty($sqlCreate))
            {
                //<workaround>
                //The whole idea behind all of this code is injecting a foreign key in the SQL.
                //This is due to the used PHPSQLParser library not treating the first foreign key as a foreign key
                //but as an index.
                $fk = 'FOREIGN KEY';
                $fkPos = strpos($sqlCreate, $fk);
                $strLen = strlen($fk);
                $fakeKey = "FOREIGN KEY (BUGMENOT) REFERENCES BUGMENOT(BUGMENOT), \n  ";
                $sqlCreate = substr_replace($sqlCreate, $fakeKey, $fkPos, 0);
                //parent::__construct();
                //</workaround>
                $sqlParser = new PHPSQLParser\PHPSQLParser($sqlCreate);
                //echo '<pre style="display: block; background: #555; clear: both;">'.print_r($sqlParser, true).'</pre>';
                //die();
                //        die();
                $this->parseTableInfo($sqlParser->parsed);
            }
        }
    }
}

?>
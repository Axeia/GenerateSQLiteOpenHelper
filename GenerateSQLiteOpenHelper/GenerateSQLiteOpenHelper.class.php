<?php
/**
 * Turns a list of SQL statements into a SQLiteOpenHelper class to be used in Android developement.
 * Greatly simplifies 
 */
include_once(__DIR__.'/../'.'CreateCodeGenerator.class.php');
class GenerateSQLiteOpenHelper extends CreateCodeGenerator
{
    /** Name of the generated class */
    protected $className = ''; 
    /** The indentation used in the output code. */
    protected $indentation = '    ';
    /** Just out of convenience so we don't we need to type this all the time */
    protected $strPrivStatic = 'private static final String ';
    /** */
    protected $tableNamesCode = '';
    protected $columnNamesCode = '';
    
    protected $tableCreation = '';
    
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
     * Please note that splitting the Table statements is done in a very rough manor. 
     * This can easily be broken.
     */
    public function parseTableInfo($parsed)
    {
        //echo '<pre style="display: block; background: #555;">'.print_r($parsed, true).'</pre>';
        //die();
        $tableName = $parsed['TABLE']['name'];
        $this->tableColumnVariables[$tableName] = [];
        $tableAsPrefix = 'TABLE_'.strtoupper($tableName);
        $tableVariable = $this->strPrivStatic.$tableAsPrefix;   
        $this->tableVariables[$tableName] = $tableAsPrefix;
        
        $this->tableNamesCode .= "\n".$tableVariable.' = "'.$tableName."\";";
        
        $this->tableCreation .= sprintf("\n"
            .$this->strPrivStatic
            .'CREATE_'
            .$tableAsPrefix." = \""."CREATE TABLE \"+%s ".$this->indentation,
            $tableAsPrefix.'+"(\\n"');
        
        $this->columnNamesCode .= "\n".$this->indentation.'//'.$tableName." table columns\n";
        foreach($parsed['TABLE']['create-def']['sub_tree'] as $columnInfo)
        {
            $this->generateColumnInfo($tableName, $columnInfo);            
        }
        $this->tableCreation = substr($this->tableCreation, 0, -4).'"';
        $this->tableCreation .= "\n".$this->indentation."+\");\";";
    }
    
    private function generateColumnInfo($tableName, array $columnInfo)
    {
        $tableAsPrefix = 'TABLE_'.strtoupper($tableName);
        //echo '<pre>'.print_r($columnInfo['sub_tree'], true)."</pre>";
        
        if($columnInfo['sub_tree'][0]['expr_type'] === 'colref'
        && !empty($columnInfo['sub_tree'][0]['base_expr']) )
        {
            $columnName = $columnInfo['sub_tree'][0]['base_expr'];
            $variableName = $tableAsPrefix.'_'.strtoupper($columnName);
            $this->tableColumnVariables[$tableName][$columnName] = $variableName;
            
            $this->columnNamesCode .= $this->strPrivStatic
                .$variableName.' = "'.$columnName."\";\n";
            
            $this->tableCreation .= "\n".str_repeat($this->identation, 2);
            $this->tableCreation .= '+'.$variableName."+\" ";
            $this->tableCreation .= preg_replace('/\s+/', ' ', $columnInfo['sub_tree'][1]['base_expr']);
            $this->tableCreation .= ",\\n\"";
            
        }
        else
        {
            $keyCode = rtrim($this->parseKeyCode($columnInfo, $tableName), ' ');
            //The first foreign key due to a bug in PHPSQLParser is returned as an index rather
            //than foreign key. This filters out the emtpy result our own code gives
            if($keyCode !== "+\"") 
            {
                $this->tableCreation .= "\n".str_repeat($this->identation, 2);
                $this->tableCreation .= $keyCode.',\\n"';
            }
        }
    }
    
    private function parseKeyCode(array $subTree, $tableName)
    {
        $keyCode = "+\"";
        if($subTree['expr_type'] === 'primary-key')
        {
            $keyCode .= $this->parsePrimaryKeyCode($subTree, $tableName);
        }
        else if($subTree['expr_type'] === 'foreign-key')
        {
            $keyCode .= $this->parseForeignKeyCode($subTree, $tableName);
        }

        return $keyCode;
    }
    /**
     * 
     */
    private function parsePrimaryKeyCode(array $subTree, $tableName)
    {
        $priKeyCode = "";
        foreach($subTree['sub_tree'] as $subSubTree)
        {
            if($subSubTree['expr_type'] === 'column-list')
            {
                $priKeyCode .= $this->parseColumnList($subSubTree, $tableName);
            }
            else
            {
                $priKeyCode .= $subSubTree['base_expr'].' ';
            }
        }
        return $priKeyCode;
    }
    /**
     * Will recursively call itself until the entire foreign key line is rendered.
     */
    private function parseForeignKeyCode(array $subTree, $tableName)
    {
        $fkCode = "";
        if(isset($subTree['table']))
        {
            if(!isset($this->tableVariables[$subTree['table']]))
            {
                $errors[] = 'Could not find table "'.$subTree['table'].', please note the script is case sensitive. 
                Also if you use backticks be consistent in using them."';
            }
            $fkCode .= '"+'.$this->tableVariables[$subTree['table']].'+"';
        }
        else if(isset($subTree['expr_type']) && $subTree['expr_type'] === 'column-list')
        {
            $fkCode .= $this->parseColumnList($subTree, $tableName);
        }
        else if(isset($subTree['sub_tree']))
        {
            $fkCode .= $this->parseForeignKeyCode($subTree['sub_tree'], $tableName);
        }        
        else if(isset($subTree['base_expr']))
        {            
            $fkCode .= $subTree['base_expr'].' ';
        }
        else
        {
            foreach($subTree as $subSubTree)
            {               
                if(isset($subSubTree['table']))
                {
                    $tableName = $subSubTree['table'];
                }
                $fkCode .= $this->parseForeignKeyCode($subSubTree, $tableName);
            }
        }
        
        return $fkCode;
    }
    
    /**
     * Used when a list of columns is encountered, such as is the case with 
     * for example a key reference.
     */
    private function parseColumnList(array $subTree, $tableName)
    {
        $keyCode = "(";
        //echo $tableName."<hr>";
        
        foreach($subTree['sub_tree'] as $subSubTree)
        {
            
            $columnName = $subSubTree['name'];
            if(isset($this->tableColumnVariables[$tableName]) && isset($this->tableColumnVariables[$tableName][$columnName]))
            {
                $keyCode .= '"+'.$this->tableColumnVariables[$tableName][$columnName].'+", ';
            }
            else
            {
                echo $tableName.'=> '.$columnName.'<hr/>';
            }
        }
        $keyCode = rtrim($keyCode, ', ');
        $keyCode .= ") ";
        
        return $keyCode;
    }
    
    /**
     * @return string
     */
    public function getJavaString()
    {        
        $javaTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'SQLiteOpenHelperTemplate.java');
        $javaString = str_replace('class MyAppDbHelper', 'class '.$this->className, $javaTemplate);
        $javaString = str_replace('DATABASE_NAME = "main.sqlite"', 'DATABASE_NAME = "'.$this->databaseName.'"', $javaString);
        $javaString = str_replace('//Table names', "//Table names".$this->tableNamesCode, $javaString);
        $javaString = str_replace($this->indentation.'//Column names', $this->columnNamesCode, $javaString);
        $javaString = str_replace('//Table create statements', "//Table create statements".$this->tableCreation, $javaString);
        
        $codeCreateSQL = '//Create tables';
        $codeUpgradeSQL = '//Upgrading, dropping tables';
        foreach($this->tableVariables as $tableVariable)
        {
            $codeCreateSQL .= "\n".str_repeat($this->identation, 2).'db.execSQL('
            .str_replace('TABLE_', 'CREATE_TABLE_', $tableVariable)
            .');';
            $codeUpgradeSQL .= "\n".str_repeat($this->identation, 2)
                .'db.execSQL("DROP TABLE IF EXISTS "+'.$tableVariable.');';
        }
        $javaString = str_replace("//Create tables", $codeCreateSQL, $javaString);
        $javaString = str_replace("//Upgrading, dropping tables", $codeUpgradeSQL, $javaString);
        
        return $javaString;
    }
}

?>
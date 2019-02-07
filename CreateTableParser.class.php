<?php
include_once(__DIR__.'/CreateCodeGenerator.class.php');
/**
 * Turns a list of SQL statements into a SQLiteOpenHelper class to be used in Android developement.
 * Greatly simplifies 
 */
class CreateTableParser extends CreateCodeGenerator
{
    /**
     * Array where the index is the table name and the value is a
     * subArray with the column info (just the bits we need). Not all
     * the stuff PHPSQLParser supplies us with.
     *
     * @var array
     */
    public $simplifiedParsedTable = [];

    /**
     * Iterates through the results of PHPSQLParser to generate
     * the code needed 
     */
    public function parseTableInfo($parsed)
    {
        $tableName = $parsed['TABLE']['name'];
        
        $this->simplifiedParsedTable[$tableName] = [
            'fields' => [],
            'foreign_keys' => [], //['field' => , 'foreign_table' =>, 'foreign_field' ].
            'primary_keys' => [],
        ];
                
        foreach($parsed['TABLE']['create-def']['sub_tree'] as $columnInfo)
        {
            $this->generateColumnInfo($tableName, $columnInfo);            
        }
    }
    
    private function generateColumnInfo($tableName, array $columnInfo)
    {
        if($columnInfo['sub_tree'][0]['expr_type'] === 'colref'
        && !empty($columnInfo['sub_tree'][0]['base_expr']) )
        {            
            $columnName = $columnInfo['sub_tree'][0]['base_expr'];
            $columnInfoSimplified = [
                'type' => $columnInfo['sub_tree'][1]['sub_tree'][0]['base_expr']
            ];
            if($columnInfo['sub_tree'][1]['sub_tree'][0]['length'] !== false)
            {
                $columnInfoSimplified['length'] = $columnInfo['sub_tree'][1]['sub_tree'][0]['length'];
            }
            if(isset($columnInfo['sub_tree'][1]['sub_tree'][0]['decimals']))
            {
                $columnInfoSimplified['precision'] = $columnInfo['sub_tree'][1]['sub_tree'][0]['decimals'];                
            }

            //inline primary key
            if(isset($columnInfo['sub_tree'][1]['sub_tree'][2]['base_expr']) 
            && $columnInfo['sub_tree'][1]['sub_tree'][2]['base_expr'] === 'PRIMARY')
            {
                $this->setPrimaryKey($tableName, $columnName);
            }

            $columnInfoSimplified['nullable'] = 
                !(isset($columnInfo['sub_tree'][1]['base_expr']) 
                && stripos($columnInfo['sub_tree'][1]['base_expr'], 'NOT NULL') !== false);

            //print_r($columnInfo);
            //echo '_____________________________________________________';

            $this->simplifiedParsedTable[$tableName]['fields'][$columnName] = $columnInfoSimplified;
        }
        else
        {
            $this->parseKeyCode($columnInfo, $tableName);
        }
    }

    private function dbTypeToLaravel($name, $column)
    {
        switch($column['type'])
        {
            case 'VARCHAR':
                return '$table->string(\''.$name.'\', '.$column['length'].');';
            case 'INT':
                return '$table->integer(\''.$name.'\');'; 
            case 'DOUBLE':
                return '$table->double(\''.$name.'\');';
            case 'DECIMAL':
                return '$table->decimal(\''.$name.'\', '.$column['length'].', '.$column['precision'].');';
            
        }
    }
    
    private function parseKeyCode(array $subTree, $tableName)
    {
        if($subTree['expr_type'] === 'primary-key')
        {
            $this->parsePrimaryKeyCode($subTree, $tableName);
        }
        else if($subTree['expr_type'] === 'foreign-key')
        {
            $this->parseForeignKeyCode($subTree, $tableName);
        }
    }
    
    /**
     * 
     */
    private function parsePrimaryKeyCode(array $subTree, $tableName)
    {
        foreach($subTree['sub_tree'] as $subSubTree)
        {            
            if($subSubTree['expr_type'] === 'column-list')
            {
                foreach($subSubTree['sub_tree'] as $subsubSubTree)
                {                    
                    $this->setPrimaryKey($tableName, $subsubSubTree['base_expr']);        
                }
                
            }
            else
            {
                //$priKeyCode .= $subSubTree['base_expr'].' ';
            }
        }
    }

    /**
     * Sets the primary key for a table.
     *
     * @param string $table
     * @param string $field
     * @return void
     */
    private function setPrimaryKey($table, $field)
    {
        $this->simplifiedParsedTable[$table]['primary_keys'][] = $field;
    }


    /**
     * Will recursively call itself until the entire foreign key line is rendered.
     */
    private function parseForeignKeyCode(array $subTree, $tableName)
    {
        $this->simplifiedParsedTable[$tableName]['foreign_keys'][] = [
            'field' => $subTree['sub_tree'][2]['sub_tree'][0]['name'], 
            'ref_table' => $subTree['sub_tree'][3]['sub_tree'][1]['table'],
            'ref_field' => $subTree['sub_tree'][3]['sub_tree'][2]['sub_tree'][0]['name']
        ];      
    }
    
    /**
     * Used when a list of columns is encountered, such as is the case with 
     * for example a key reference.
     */
    private function parseColumnList(array $subTree, $tableName)
    {
        $keyCode = "(";

        $keyCode = rtrim($keyCode, ', ');
        $keyCode .= ") ";
        
        return $keyCode;
    }
}

?>
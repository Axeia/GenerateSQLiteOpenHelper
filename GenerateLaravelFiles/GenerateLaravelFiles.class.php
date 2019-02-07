<?php
include_once(__DIR__.'/../'.'CreateTableParser.class.php');
/**
 * Turns a list of SQL statements into corrosponding laravel files
 */
class GenerateLaravelFiles extends CreateTableParser
{
    /**
     * Turns underscored naming scheme into a camelcase naming scheme.
     *
     * @param string $str
     * @param boolean $capitalizeFirstCharacter
     * @return void
     * @link https://stackoverflow.com/questions/2791998/convert-dashes-to-camelcase-in-php borrowed code from here
     */
    public static function underscoreToCamelCase($str, $capitalizeFirstCharacter = true)
    {
        $str = str_replace('_', '', ucwords($str, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }
    
        return $str;
    }

    private function dbTypeToLaravel($name, $column)
    {
        $strField = '$table->%s(\'%s\'%s)';
        $return = '';
        switch($column['type'])
        {
            case 'CHAR':
                $return = sprintf($strField, 'char', $name, ', '.$column['length']);
                break;
            case 'VARCHAR':
                $return = sprintf($strField, 'string', $name, ', '.$column['length']);
                break;
            case 'INT':
                $return = sprintf($strField, 'integer', $name, '');
                break;
            case 'DOUBLE':
                $return = sprintf($strField, 'double', $name);
                break;
            case 'DECIMAL':
                $return = sprintf($strField, 'decimal', $name, ', '.$column['length'].', '.$column['precision']);
                break;
            case 'TINYINT':
                $return = $column['length'] === '1' 
                    ? sprintf($strField, 'boolean', $name, '')
                    : sprintf($strField, 'tinyInteger', $name, '');
                break;
            default:
                $return .= print_r($column, true);
        }

        if($column['nullable']===true)
            $return .= '->nullable()';

        return $return .= ';';
    }

    /**
     * @return string HTML
    */
    public function getMigrationString()
    {
        $html = '<div id="laravel-creates"></div><div id="laravel-inserts"></div>';

        foreach($this->simplifiedParsedTable as $tableName => $info)
        {
            $className = 'Create'.GenerateLaravelFiles::underscoreToCamelCase($tableName).'Table';
            $date = new DateTime();
            $documentName = $date->format('Y_m_d_Hisu_').'create_'.strtolower($tableName).'_table.php';
            $html .= '<small class="tab-header" id="tab-title-creates-'.$tableName.'">'.$documentName.'</small>';
            $html .= '<textarea id="tab-content-creates-'.$tableName.'" class="aced-tabs" name="'.str_replace('.php', '_php', $documentName).'">';
            $laravelTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'laravel_migration_template.php');
            $templateString = '';

            $strTableFields = '';
            foreach($info['fields'] as $fieldName => $fieldDefinition)
            {
                $strTableFields .= str_repeat($this->indentation, 3).$this->dbTypeToLaravel($fieldName, $fieldDefinition)."\n";
            }   

            $strKeys = '';
            $primKeyCount = count($info['primary_keys']);
            if($primKeyCount > 0)
            {
                $strKeys = str_repeat($this->indentation, 3).'$table->primary(\'';
                $strKeys .= $primKeyCount === 1 //Singular
                    ? $info['primary_keys'][0]
                    : '['.implode(', ', $info['primary_keys'])."]";
                $strKeys .= "');\n";
            }

            foreach($info['foreign_keys'] as $foreignKeyData)
            {
                $strKeys .= str_repeat($this->indentation, 3).'$table->foreign(\''.$foreignKeyData['field'].'\')';
                $strKeys .= '->references(\''.$foreignKeyData['ref_field'].'\')';
                $strKeys .= '->on(\''.$foreignKeyData['ref_table'].'\');'."\n";
            }

            //Assign classname
            $templateString = str_replace('LaravelMigrationTemplate', $className, $laravelTemplate);
            //Set table name inside the template
            $templateString = str_replace('laravel_migration_template', strtolower($tableName), $templateString);
            //Add columns
            $templateString = str_replace(str_repeat($this->indentation, 3).'//Generated contents', $strTableFields, $templateString);
            //Add foreign keys
            $templateString = str_replace(str_repeat($this->indentation, 3).'//keys', $strKeys, $templateString);

            $html .= $templateString;
            $html .= '</textarea>';
        }

        return $html;
    }
}

?>
<?php
include_once(__DIR__.'/CreateCodeGenerator.class.php');
/**
 * Turns a list of SQL statements into a SQLiteOpenHelper class to be used in Android developement.
 * Greatly simplifies 
 */
class LaravelInsertParser
{
    private $indentation;

    /**
     * ['tablename'] => [
     *  'fields' => []
     *  'values' => [[], [], []]
     * ]
     *
     * @var array
     */
    public $allInfo = [];

    public function LaravelInsertParser($sqlInserts, $indentation = '    ')
    {
        $this->indentation = $indentation;
        
        //Single 'quotes' in comments seem to throw the parser off 
        //Who knows what else does - we don't need them, so remove them.
        $sqlInserts = preg_replace('~/\*.*?\*\/~s', '', $sqlInserts);

        //Split the create queries up @TODO refine this... it's very brute force, could possibly use the parser itself.
        $insertParts = explode(";", $sqlInserts);
        foreach($insertParts as $sqlInsert)
        {
            if(!empty($sqlInsert))
            {
                $sqlParser = new PHPSQLParser\PHPSQLParser($sqlInsert);
                //echo '<pre style="display: block; background: #555; clear: both;">'.print_r($sqlParser, true).'</pre>';
                $this->parseInsertInfo($sqlParser->parsed);
            }
        }
    }

    /**
     * Iterates through the results of PHPSQLParser to generate
     * the code needed 
     */
    public function parseInsertInfo($parsed)
    {
        if(isset($parsed['INSERT'][0]['base_expr']) && $parsed['INSERT'][0]['base_expr'] === 'INTO')
        {
            $fields = [];

            if(isset($parsed['INSERT'][2]['sub_tree']))
            {
                foreach($parsed['INSERT'][2]['sub_tree'] as $parsedFields)
                {
                    $fields[] = $parsedFields['no_quotes']['parts'][0];
                }
            }

            $allValues = [];
            if(isset($parsed['VALUES']))
            {
                foreach($parsed['VALUES'] as $parsedRow)
                {
                    $values = [];
                    foreach($parsedRow['data'] as $parsedFields)
                    {
                        $values[] = $parsedFields['base_expr'];
                    }
                    $allValues[] = $values;
                }
            }

            $this->allInfo[$parsed['INSERT'][1]['table']] = [
                'fields' => $fields,
                'values' => $allValues
            ];

        }
    }
    
    /**
     * Adds something like this into the template.
     * 
     * DB::table('users')->insert([
     *       ['email' => 'taylor@example.com', 'votes' => 0],
     *       ['email' => 'dayle@example.com', 'votes' => 0]
     * ]);
     *
     * @return void
     */
    public function getSeederString()
    {
        $html = '';
        foreach($this->allInfo as $tableName => $fieldsAndValues)
        {            
            $id = strtolower($tableName);
            $className = GenerateLaravelFiles::underscoreToCamelCase($tableName).'TableSeeder';
            $filename = $className.'.php';
            $html .= '<small class="tabbed tab-footer" id="tab-title-inserts-'.$id.'">'.$filename.'</small>';
            $html .= '<textarea name="'.str_replace('.php', '_php', $filename).'" id="tab-content-inserts-'.$id.'">';

            $insertCode = "\DB::table('";
            $insertCode .= $tableName;
            $insertCode .= "')->insert([";

            foreach($fieldsAndValues['values'] as $values)
            {
                $insertCode .= "\n".str_repeat($this->indentation, 3).'[';
                for($i = 0; $i < count($values); $i++)
                {
                    $insertCode .= sprintf('"%s" => %s', $fieldsAndValues['fields'][$i], $values[$i]).', ';
                }
                $insertCode = substr($insertCode, 0, -2);
                $insertCode .= '],';

            }
            $insertCode .= "\n".str_repeat($this->indentation, 2)."]);";

            $laravelTemplate = file_get_contents(__DIR__.'/GenerateLaravelFiles'.DIRECTORY_SEPARATOR.'laravel_seeder_template.php');

            $laravelTemplate = str_replace('LaravelSeeder', $className, $laravelTemplate);
            $laravelTemplate = str_replace('//inserts', $insertCode, $laravelTemplate);

            $html .= $laravelTemplate."\n</textarea>";
        }
        return $html;
    }
}

?>
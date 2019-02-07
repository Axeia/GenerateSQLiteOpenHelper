<?php
include_once(__DIR__.'/../'.'CreateTableParser.class.php');
/**
 * Turns a list of SQL statements into a SQLiteOpenHelper class to be used in Android developement.
 * Greatly simplifies 
 */
class GenerateLaravelSeeder
{
    public static function getHTML($insertQuery, $filename)
    {
        $id = strtolower($filename);
        $className = ucfirst($filename).'TableSeeder';
        $filename = $className.'.php';
        $html = '<small class="tabbed tab-footer" id="tab-title-inserts-'.$id.'">'.$filename.'</small>';
        $html .= '<textarea name="'.str_replace('.php', '_php', $filename).'" id="tab-content-inserts-'.$id.'">';
        $insertCode = "\DB::raw('";
        $insertCode .= $insertQuery;
        $insertCode .= "');";

        $laravelTemplate = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'laravel_seeder_template.php');

        $laravelTemplate = str_replace('LaravelSeeder', $className, $laravelTemplate);
        $laravelTemplate = str_replace('//inserts', $insertCode, $laravelTemplate);

        $html .= $laravelTemplate."\n</textarea>";

        return $html;
    }
}

?>
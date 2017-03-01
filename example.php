<?php
$currentFolder = __DIR__.'/';
spl_autoload_register(function ($className) {
    global $currentFolder;
    include $currentFolder.str_replace('\\', '/', $className).'.php';
});
include($currentFolder.'GenerateSQLiteOpenHelper.class.php');

$sql = '/** Sample SQL. **/
CREATE TABLE Albums(
   id           INT     NOT NULL,
   title        TEXT    NOT NULL,
   releaseYear  INT     NOT NULL,
   PRIMARY KEY(id)
);

CREATE TABLE Songs(
   id           INT     NOT NULL,
   artist       VARCHAR(255) NOT NULL,
   title        VARCHAR(255) NOT NULL,
   releaseYear  INT     NOT NULL,
   length       DOUBLE,
   PRIMARY KEY(id)
);

CREATE TABLE AlbumTracks(
    album_id    INT     NOT NULL,
    song_id     INT     NOT NULL,
    PRIMARY KEY(album_id, song_id),
    FOREIGN KEY(album_id) REFERENCES Albums(id),
    FOREIGN KEY(song_id)  REFERENCES Songs(id)
);';
$className = isset($_POST['class_name']) ?  trim($_POST['class_name']) : 'MyAppDbHelper';
$dbName = isset($_POST['db_name']) ? trim($_POST['db_name']) : 'main.sqlite';
if(isset($_POST['ddl-sql']))
{
    $sql = $_POST['ddl-sql'];
}

$creates = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'creates.sql');
$uniqueTypes = [];

$sqliteOpenHelper = new GenerateSQLiteOpenHelper($sql, $className, $dbName);


?><!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>SQLiteOpenHelper helper</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            html, body{
                margin: 0; 
                padding: 0;
            }
            body {
              background-color: rgb(32, 32, 32);
              background-image: linear-gradient(45deg, black 25%, transparent 25%, transparent 75%, black 75%, black), 
                linear-gradient(45deg, black 25%, transparent 25%, transparent 75%, black 75%, black), 
                linear-gradient(to bottom, rgb(8, 8, 8), rgb(32, 32, 32));
              background-size: 10px 10px, 10px 10px, 10px 5px;
              background-position: 0px 0px, 5px 5px, 0px 0px;
              color: #ddd;
              font-family: "SF Pro Text","SF Pro Icons","Helvetica Neue","Helvetica","Arial",sans-serif;
              padding: 1% 2%;
            }
            h1, h2{
                letter-spacing: 1px;
                text-shadow: 0px 0px 3px rgba(255, 255, 255, 0.5);
                margin: 0;
                padding: 0;
            }
            h1{
                padding: 0;
            }            
            pre{
                width: 100%;
                min-height: 500px;
            }            
            .sql{
                overflow: auto;
                border: 1px #cecece solid ;
            }
            .creates{
            }
            abbr{
                cursor: help;
            }
            input, textarea{
                background: #272822;
                border: 0;
            }
            a{
                color: #e6db74;
            }
            table {
                margin-bottom: 3em;
            }
            table th{                
                white-space: nowrap;
                text-align: right;
            }
            table td{
                width: 90%;
            }
            table td[rowspan="2"]{
                width: 100px;
                height: 2.4em;
            }
            table button{
                display: inline-block;
                height: 2.8em;
                width: 100%;
                height: 100%;
                margin-top: -1px;
                border: 1px solid #fff;
                font-weight: bold;
                background: #559A0D;
                color: #fff;
            }
            table input[type="text"]{
                width: 100%;
                height: 1.4em;
                border: 1px solid #2F3129;
                color: #fff;
            }
            p{
                margin: 0.2em 0;
                padding: 0.4em;
                font-size: 0.9em;
            }
            #credits{
                clear: both;
                bottom: 0.2em;
                margin: 0 auto;
                text-align: center;
            }
            @media (min-width:1700px) { 
                #ddl, #output{ width: 48%; float: left; }
                #output{ float: right; }
                .ace_editor{
                    height: calc(100vh - 250px);
                }
            } 
            #footnote{
                background: #A4DDED;
                color: #000;
            }
        </style>
    </head>
    <body>
        <h1>Generated SQLiteOpenHelper Helper</h1>
        <hr/>
        
        <form id="ddl" method="POST">
            <h2>Create Statements <abbr title="Data Definition Language">(DDL)</h2>
            <p>Please note that no validating/correcting is done, so please make sure to enter valid SQL.</p>
            <textarea class="sql creates" id="ddl-sql" name="ddl-sql"><?php echo $sql; ?></textarea>
            <table>
                <tr>
                    <th><label for="class_name">Class name:</th>
                    <td><input type="text" placeholder="MyAppDbHelper" name="class_name" value="<?php echo $className ?>" /></label></td>
                    <td rowspan="2">
                        <button>
                        Generate SQLiteOpenHelper
                        </button>
                    </td>
                </tr>
                <tr>
                    <th><label for="db_name">Database name:</th>
                    <td><input type="text" name="db_name" value="<?php echo $dbName ?>" /></td>
                </tr>
            </table>
        </form>
        
        <div id="output">
            <h2>Android SQLiteOpenHelper</h2>
            <p>Just copy &amp; paste into Android Studio's file tree</p>
            <textarea id="sqlitehelper"><?php
echo $sqliteOpenHelper->getJavaString();?>
            </textarea>        
        </div>
        <p id="credits">
            Makes use of <a href='https://ace.c9.io/'>Ace embeddable editor</a> for syntax highlighting and the <a href='https://github.com/greenlion/PHP-SQL-Parser'>PHP-SQL-Parser</a> to generate the SQLite classes.
            Enjoy!
        </p>
        <div id="footnote">
            <p>
                Please note that the code is not very robust and far from perfect to handle a lot of different cases. Things to keep in mind that have the biggest chance of not breaking it and ending up with a usable android class are:
            </p>
            <ul>
                <li>Table and column names <strong>are case-sensitive</strong>. The code will start throwing error if you refer to column `id` as `ID`.</li>
                <li>Be consistent in backtick usage, either use them or don't when refering to the same table and or columns.</li>
                <li>Define foreign and composite primary keys after the columns within the same create table statement.</li>
                <li>Splitting of the tables is done on semi-colons, so don't use those anywhere else</li>
                <li>Indexes haven't been tested.</li>
            </ul>
        </div>
        <script src="https://cdn.jsdelivr.net/ace/1.2.6/min/ace.js"></script>
        <script>
            var editorDDL = ace.edit("ddl-sql");
            editorDDL.setTheme("ace/theme/monokai");
            editorDDL.getSession().setMode("ace/mode/sql");
            
            var editorSQLiteHelper = ace.edit("sqlitehelper");
            editorSQLiteHelper.setTheme("ace/theme/monokai");
            editorSQLiteHelper.getSession().setMode("ace/mode/java");            
        </script>
    </body>
</html>
# Live Demo
If all you're interested in is generating your Java class based off SQL create statement then just click the live Demo link and you're off to the races :)
#[live demo](http://monsterhunters.duckdns.org:9998/example.php)

![Screenshot of the example file to clarify what it does](https://puu.sh/upZn7/8cdc74509c.png)
#How to run this yourself?
Please note that this project uses the [PHPSQLParser](https://github.com/greenlion/PHP-SQL-Parser) which is not included if you click the download zip button on github.
In order to obtain it:
* Go to [its own github page](https://github.com/greenlion/PHP-SQL-Parser) and download it and place the `PHP-SQL-Parser` folder inside the  :file_folder: folder of wherever you have unzipped this project.

or
* Download this project via git like so (the recurisve parameter does the magic of including sub-repositories): 
```cmd
git clone --recursive https://github.com/Axeia/GenerateSQLiteOpenHelper
```

#Who is it for?
Anyone that plans on creating a database, just paste your SQL create statements, run the code and you're presented with a class you copy&paste into your Android project.

#Bugs 
As I whipped this up mostly for myself but then figured I might as well share it and save other folks some time this is not the most polished product.
* :bug: Creating an index inside the create statement not part of the initial column definition will most likely get ignored.
* :beetle: Case sensitivity, please note that defining a column as for example `id INT NOT NULL` (lowercase 'id') and then later on trying to do `PRIMARY KEY(ID)` (uppercase 'ID') will fail to produce a proper class.
* :bug: Quotes/backtick usage, consistency is the name of the game. If you choose to use these then always use them or don't use them at all or the code won't parse.
  * Quotes might actually fail to produce proper Java-code as they're probably not escaped
* :beetle: Don't have semicolons in the SQL Create statements anywhere but at the end of the createstatement. Splitting of the table code is done extremely simple by considering the semicolon as the seperator character. 

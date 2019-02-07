<?php
//Use at your own risk, thought it would improve performance
//But went from a 5.5s rendertime to 11s - results may vary based on server hardware.
//ob_start("ob_gzhandler");
$timeStart = microtime(true);

if(isset($_POST['Download']))
{
    $zip = new ZipArchive();
    $filename = '/tmp/xxx.zip';
    if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) 
    {
        throw new Exception();
    }

    foreach($_POST as $types => $post)
    {
        //Skip these.
        if($types==='output' || $types==='Download')
            continue;

        $zip->addFromString(str_replace('_php', '.php', $types), $post);
    }

    $zip->close();
    $fileString = file_get_contents($filename);
    unlink($filename);

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename=laravel_db_stuff.zip');
    die($fileString);   

    echo '<h1>'.$types.'</h1>';
    echo '<pre style="background: #333; color: #cecece; margin: 1em;">'.print_r(htmlentities($post), true).'</pre>';
}

$currentFolder = __DIR__.'/';
spl_autoload_register(function ($className) 
{
    global $currentFolder;
    $sqlParserFolder = $currentFolder;
    if(strpos($className, '\\') !== false)  
    {
        $classPath = str_replace('PHPSQLParser\\', 'PHP-SQL-Parser/src/PHPSQLParser/', $className);
        $classPath = $sqlParserFolder.str_replace('\\', '/', $classPath);
        include $classPath.'.php';
    }
});

include($currentFolder.'GenerateSQLiteOpenHelper/GenerateSQLiteOpenHelper.class.php');
include($currentFolder.'GenerateLaravelFiles/GenerateLaravelFiles.class.php');
include($currentFolder.'GenerateLaravelFiles/GenerateLaravelSeeder.class.php');


function isShiny($name)
{
    $shinyPokemonNames = [
        //Gen 1
        'Bulbasaur'     => '0001',
        'Ivysaur'       => '0002',
        'Venusaur'      => '0003',
        'Charmander'    => '0004',
        'Charmeleon'    => '0005',
        'Charizard'     => '0006',
        'Squirtle'      => '0007',
        'Wartortle'     => '0008',
        'Blastoise'     => '0009',
        'Caterpie'      => '0010',
        'Metapod'       => '0011',
        'Butterfree'    => '0012',
        'Pichu'         => '0013',
        'Pikachu'       => '0025',
        'Raichu'        => '0026',
        'Alolan Raichu' => '0026',
        'Pichu'         => '0172',
        'Nidoran♀'      => '0029',
        'Nidorina'      => '0030',
        'Nidoqueen'     => '0031',
        'Growlithe'     => '0058',
        'Arcanine'      => '0059',
        'Geodude'       => '0074',
        'Graveler'      => '0075',
        'Golem'         => '0076',
        'Ponyta'        => '0077',
        'Rapidash'      => '0078',
        'Magnemite'     => '0081',
        'Magneton'      => '0082',
        'Grimer'        => '0088',
        'Muk'           => '0089',
        'Shellder'      => '0090',
        'Cloyster'      => '0091',
        'Gastly'        => '0092',
        'Haunter'       => '0093',
        'Gengar'        => '0094',
        'Drowzee'       => '0096',
        'Hypno'         => '0097',
        'Krabby'        => '0098',
        'Kingler'       => '0099',
        'Cubone'        => '0104',
        'Marowak'       => '0105',
        'Alolan Marowak'=> '0105',
        'Electabuzz'    => '0125',
        'Magmar'        => '0126',
        'Pinsir'        => '0127',
        'Magikarp'      => '0129',
        'Gyarados'      => '0130',
        'Eevee'         => '0133',
        'Vaporeon'      => '0134',
        'Jolteon'       => '0135',
        'Flareon'       => '0136',
        'Espeon'        => '0196',
        'Umbreon'       => '0197',
        'Omanyte'       => '0138',
        'Omastar'       => '0139',
        'Kabuto'        => '0140',
        'Kabutops'      => '0141',
        'Aerodactyl'    => '0142',
        'Articuno'      => '0144',
        'Zapdos'        => '0145',
        'Moltres'       => '0146',
        'Dratini'       => '0147',
        'Dragonair'     => '0148',
        'Dragonite'     => '0149',

        //Gen 2
        'Chikorita'     => '0152',
        'Bayleef'       => '0153',
        'Meganium'      => '0154',
        'Cyndaquil'     => '0155',
        'Quilava'       => '0156',
        'Typhlosion'    => '0157',
        'Togepi'        => '0175',
        'Togetic'       => '0176',
        'Natu'          => '0177',
        'Xatu'          => '0178',
        'Mareep'        => '0179',
        'Flaaffy'       => '0180',
        'Ampharos'      => '0181',
        'Marill'        => '0183',
        'Azumarill'     => '0184',
        'Sunkern'       => '0191',
        'Sunflora'      => '0192',
        'Murkrow'       => '0198',
        'Wynaut'        => '0167',
        'Wobbuffet'     => '0202',
        'Pineco'        => '0204',
        'Forretress'    => '0205',
        'Snubbull'      => '0209',
        'Granbull'      => '0210',
        'Delibird'      => '0225',
        'Houndour'      => '0228',
        'Houndoom'      => '0229',
        'Elekid'        => '0239',
        'Magby'         => '0240',
        'Larvitar'      => '0246',
        'Pupitar'       => '0247',
        'Tyranitar'     => '0248',
        'Lugia'         => '0249',
        'Ho-Oh'         => '0250',

        //Gen 3
        'Poochyena'     => '0261',
        'Mightyena'     => '0262',
        'Wingull'       => '0278',
        'Pelipper'      => '0279',
        'Azurill'       => '0298',
        'Makuhita'      => '0296',
        'Hariyama'      => '0297',
        'Sableye'       => '0302',
        'Mawile'        => '0303',
        'Aron'          => '0304',
        'Lairon'        => '0305',
        'Aggron'        => '0306',
        'Meditite'      => '0307',
        'Medicham'      => '0308',
        'Plusle'        => '0311',
        'Minun'         => '0312',
        'Roselia'       => '0315',
        'Wailmer'       => '0320',
        'Wailord'       => '0321',
        'Swablu'        => '0333',
        'Altaria'       => '0334',
        'Shuppet'       => '0353',
        'Banette'       => '0354',
        'Duskull'       => '0355',
        'Dusclops'      => '0356',
        'Absol'         => '0359',
        'Snorunt'       => '0361',
        'Glalie'        => '0362',
        'Luvdisc'       => '0370',
        'Beldum'        => '0374',
        'Metang'        => '0375',
        'Metagross'     => '0376',
        'Kyogre'        => '0382',

        //Gen 4
        'Shinx'         => '0403',
        'Luxio'         => '0404',
        'Luxray'        => '0405',
        'Budew'         => '0406',
        'Roserade'      => '0407',
        'Drifloon'      => '0425',
        'Drifblim'      => '0426',
        'Honchkrow'     => '0430',
        'Electivire'    => '0466',
        'Electivire'    => '0466',
        'Magmortar'     => '0467',
        'Togekiss'      => '0468',
        'Dusknoir'      => '0477',
    ];

    return intval(isset($shinyPokemonNames[$name]));
}

function isReleased($name)
{
    $notReleased = [
        //Gen 2 Johto
        'Smeargle' => '',

        //Gen 3 Hoehn
        'Kecleon' => '',
        'Clamperl' => '',
        'Gorebyss' => '',
        'Jirachi' => '',

        //Gen 4
        'Burmy' => '',
        'Wormadam (plant)' => '',
        'Wormadam (sandy)' => '',
        'Wormadam (trash)' => '',
        'Cherubi' => '',
        'Cherrim (overcast)' => '',
        'Cherrim (sunny)' => '',
        'Shellos (east sea)' => '',
        'Shellos (west sea)' => '',
        'Gastrodon (east sea)' => '',
        'Gastrodon (west sea)' => '',
        'Gible' => '',
        'Gabite' => '',
        'Garchomp' => '',
        'Hippopotas' => '',
        'Hippowdon' => '',
        'Magnezone' => '',
        'Leafeon' => '',
        'Glaceon' => '',
        'Probopass' => '',
        'Rotom (fan)' => '',
        'Rotom (frost)' => '',
        'Rotom (heat)' => '',
        'Rotom (mow)' => '',
        'Rotom (normal)' => '',
        'Rotom (wash)' => '',
        'Rotom (sky)' => '',
        'Uxie' => '',
        'Mesprit' => '',
        'Azelf' => '',
        'Dialga' => '',
        'Regigigas' => '',
        'Giratina (origin)' => '',
        'Phione'            => '',
        'Manaphy'           => '',
        'Darkrai'           => '',
        'Shaymin (land)' => '',
        'Shaymin (sky)' => '',
        'Arceus (poison)' => '',
        'Arceus (fire)' => '',
        'Arceus (flying)' => '',
        'Arceus (water)' => '',
        'Arceus (bug)' => '',
        'Arceus (normal)' => '',
        'Arceus (dark)' => '',
        'Arceus (electric)' => '',
        'Arceus (psychic)' => '',
        'Arceus (ground)' => '',
        'Arceus (ice)' => '',
        'Arceus (steel)' => '',
        'Arceus (fairy)' => '',
        'Arceus (fighting)' => '',
        'Arceus (rock)' => '',
        'Arceus (ghost)' => '',
        'Arceus (grass)' => '',
        'Arceus (dragon)' => '',
    ];    
    return intval(!isset($notReleased[$name]));
}
function pokemonHasNoUse($protobuffId)
{
    /**
     * These are the pokémon we'll ignore. They're duplicates for what seems like some use within the game code.
     * We aren't trying to be the game so we'll just stick to unique Pokémon and ignore duplicates.
     */
    $unusedPokemon = [
        /* For some reason Pokémon that have an Alolan form come in 3 flavours.
        * 1) Original form (e.g. RATTATA)
        * 2) Alolan form (e.g. RATTATA_ALOLA)
        * 3)'Normal' form (e.g. RATTATA_NORMAL)
        * For our database we really only need #2 and either #1 or #3. Since we don't want "normal" displaying in the name.
        * we'll ignore #3 */
        'V0019_POKEMON_RATTATA_NORMAL'      => '',
        'V0020_POKEMON_RATICATE_NORMAL'     => '',
        'V0026_POKEMON_RAICHU_NORMAL'       => '',
        'V0027_POKEMON_SANDSHREW_NORMAL'    => '',
        'V0028_POKEMON_SANDSLASH_NORMAL'    => '',
        'V0037_POKEMON_VULPIX_NORMAL'       => '',
        'V0038_POKEMON_NINETALES_NORMAL'    => '',
        'V0050_POKEMON_DIGLETT_NORMAL'      => '',
        'V0051_POKEMON_DUGTRIO_NORMAL'      => '',
        'V0052_POKEMON_MEOWTH_NORMAL'       => '',
        'V0053_POKEMON_PERSIAN_NORMAL'      => '',
        'V0074_POKEMON_GEODUDE_NORMAL'      => '',
        'V0075_POKEMON_GRAVELER_NORMAL'     => '',
        'V0076_POKEMON_GOLEM_NORMAL'        => '',
        'V0088_POKEMON_GRIMER_NORMAL'       => '',
        'V0089_POKEMON_MUK_NORMAL'          => '',
        'V0103_POKEMON_EXEGGUTOR_NORMAL'    => '',
        'V0105_POKEMON_MAROWAK_NORMAL'      => '',

        //All of the following pbIds belong to a Pokémon that have multiple forms, one of the forms
        //is equal to the baseform (usually postfixed by _NORMAL) - We'll keep the more descriptive variant instead of the baseform.
        'V0351_POKEMON_CASTFORM' => '',
        'V0386_POKEMON_DEOXYS' => '',
        'V0413_POKEMON_WORMADAM' => '',
        'V0421_POKEMON_CHERRIM' => '',
        'V0422_POKEMON_SHELLOS' => '',
        'V0423_POKEMON_GASTRODON' => '',
        'V0479_POKEMON_ROTOM' => '',
        'V0487_POKEMON_GIRATINA' => '',
        'V0492_POKEMON_SHAYMIN'  => '',
        'V0493_POKEMON_ARCEUS' => '',

    ];
    return isset($unusedPokemon[$protobuffId]);
}
$PokemonIdPbIdMap = [];
$creates = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'sql_creates.sql');
$insertPokemonQuery = '/** If we want to update instead of insert, 
create a new table called updated_pokemons and insert the below values. 
Then run:
UPDATE    pokemons p,
          updated_pokemons u
SET       p.pokedex_number       = u.pokedex_number,
          p.type1                = u.type1,
          p.type2                = u.type2,
          p.base_attack          = u.base_attack,
          p.base_defense         = u.base_defense,
          p.base_stamina         = u.base_stamina,
          p.base_capture_rate    = u.base_capture_rate,
          p.base_flee_rate       = u.base_flee_rate,
          p.evolves_from         = u.evolves_from,
          p.buddy_distance       = u.buddy_distance,
          p.candy_to_evolve      = u.candy_to_evolve,
          p.released             = u.released,
          p.shiny                = u.shiny,
          p.male_ratio           = u.male_ratio,
          p.female_ratio         = u.female_ratio
WHERE     p.name = u.name;
DROP TABLE updated_pokemons;
 **/
INSERT INTO pokemons(name, pokedex_number, type1, type2, 
base_attack, base_defense, base_stamina, 
base_capture_rate, base_flee_rate, 
evolves_from, buddy_distance, candy_to_evolve, released, shiny, male_ratio, female_ratio) 
VALUES ';
$insertTypeQuery = 'INSERT INTO types(typing)
VALUES';
$insertPokemonImagesQuery = '/** If we want to update instead of insert,
create a new table called updated_pokemon_images and insert the below values.
Then run:
UPDATE    pokemon_images pi,
          updated_pokemon_images ui
SET       pokemons.pokedex_number       = updated_pokemon_images.male,
          pokemons.type1                = updated_pokemon_images.female,
          pokemons.type2                = updated_pokemon_images.alt_form,
          pokemons.base_attack          = updated_pokemon_images.shiny,
          pokemons.base_defense         = updated_pokemon_images.file_name
WHERE     pi.pokemons_name = ui.pokemons_name;
DROP TABLE updated_pokemon_images;
*/
INSERT INTO pokemon_images(pokemons_name, male, female, alt_form, shiny, file_name) VALUES';

$stringsForAndroid = '';
//We use the array indexes so we end up with unique values for the types.
$uniqueTypes = [];
/**  
 * The protobuff id is the arrays 'key'
 **/
$uniquePokemon = [];

/** We store the gender separately as it's parsed before the Pokémon themselves due
 * to the order of the protobuff document - then merge them in once all Pokémon have been parsed. 
 * 
 * The protobuff id is the arrays 'key'
 **/
$genderRatios = []; 

$rawData = file_get_contents($currentFolder.'decoded_master.proto');
$dataParts = explode("item_templates",$rawData);
//The data used is in the protobuf format which sadly cannot be parsed easily in PHP.
//However - using a few regular expressions it can be converted into valid JSON.
for($i = 1; $i < count($dataParts)-1; $i++) //First and last items are of no interest.
{
    $part = $dataParts[$i];
    //Quotes all 'keys' of objects and values that aren't booleans or numbers
    $jsonPart = preg_replace_callback(
        '~([a-z_0-9]+): ([a-zA-Z0-9\._\- :/"]+)~', 
        function($matches)
        {
            $escapedValues = '"'.$matches[1].'": ';
            if(is_numeric($matches[2]) || $matches[2] === 'true' || $matches[2] === 'false')
                $escapedValues .= $matches[2];
            else if(strpos($matches[2], '"') === 0) //Don't quote already quoted values
                $escapedValues .= $matches[2];
            else
                $escapedValues .= '"'.$matches[2].'"';
            
            return $escapedValues.',';
        },
        $part);
    
    //Quote all keys that come before an object and add the needed colon,  e.g.
    //avatar_customization { } becomes "avatar_customization": {} 
    $jsonPart = preg_replace('~([a-z_]+) {~', '"$1": {', $jsonPart);
    //Filter out commas after the last value in an object as that isn't valid JSON.
    $jsonPart = preg_replace('~(,)(\s+})~', '$2', $jsonPart); 
    //Add comma's between object values
    $jsonPart = preg_replace('~}(\s+"[a-z_0-9]+")~', '},$1', $jsonPart);    

    $json = json_decode($jsonPart);
    if($json===null)
    {
        echo '<h1 style="color: red;">Error: '.json_last_error_msg().'</h1>';
        echo '<h2>Whilst parsing input:</h2>';
        echo '<pre>'.$part.'</pre>';
        echo '<h2>as JSONified: </h2>';
        echo '<pre>'.$jsonPart.'</pre>';
        echo '<hr/>';
    }
    else
    {
        //TODO: Use FORMS_$pbId to figure out pokémon images based off form_settings?
        /*if(isset($json->form_settings))
        {
            echo $json->template_id.'<br/>';
            print_r($json->form_settings);
            echo '<hr/>';
        }*/

        //If it's a Pokémon.
        if(isset($json->pokemon_settings))
        {
            //Hit a dud - next.
            if(pokemonHasNoUse($json->template_id))
                continue;
            
            $normalPos = strpos($json->template_id, '_NORMAL');
            $pbId = $json->template_id;

            $pokemon = [];            
            $pokemonSettings = $json->pokemon_settings;
            $stats           = $pokemonSettings->stats;        
            $encounter       = $pokemonSettings->encounter;
            $name = extractPokemonNameFromTemplateId($pbId);            

            $pokemonIdPbIdMap[$json->pokemon_settings->pokemon_id] = $pbId;
            //echo '<pre>'.print_r($json, true).'</pre>';
            //echo extractPokedexNumberFromId($template->templateId).' '.$pokemonSettings->pokemonId."\n";
            $pokemon['name'] = '"'.$name.'"';
            $pokemon['pokedex_number'] = extractPokedexNumberFromId($json->template_id);

            $uniqueTypes[$pokemonSettings->type]= '';
            if(isset($pokemonSettings->type_2))
            {
                $uniqueTypes[$pokemonSettings->type_2]= '';
            }
            
            $pokemon['type1'] = '"'.extractPokemonType($pokemonSettings->type).'"';
            //For some reason Cranidos and Rampardos have a double rock type
            $pokemon['type2'] = isset($pokemonSettings->type_2) && $pokemonSettings->type_2 !== $pokemonSettings->type
                ? '"'.extractPokemonType($pokemonSettings->type_2).'"'
                : $pokemon['type2'] = 'null';
            $pokemon['base_attack']  = $stats->base_attack;
            $pokemon['base_defense'] = $stats->base_defense;
            $pokemon['base_stamina'] = $stats->base_stamina;
            $pokemon['base_capture_rate'] = isset($encounter->base_capture_rate) 
                ? $encounter->base_capture_rate 
                : 'null';
            $pokemon['base_flee_rate'] = isset($encounter->base_flee_rate)
                ? $encounter->base_flee_rate
                : 'null';
            //We can't always write the value we want to this yet as some baby forms 
            //were added in a generation after their post-baby form was added.
            //We'll have to iterate over all the evolves_from names after we know
            //all the names. 
            //For now we'll just store the pbId so we can retrieve the names based on that later.
            $pokemon['evolves_from'] = isset($pokemonSettings->parent_pokemon_id)
                ? $pokemonSettings->parent_pokemon_id
                : 'null';    
            $pokemon['buddy_distance'] = $pokemonSettings->km_buddy_distance;
            $pokemon['candy_to_evolve'] = isset($pokemonSettings->candy_to_evolve)
                ? $pokemonSettings->candy_to_evolve
                : $pokemon['candy_to_evolve'] = 'null';
            $pokemon['released'] = isReleased($name);
            $pokemon['shiny'] = isShiny($name);
            $pokemon['female'] = 

            $uniquePokemon[$pbId] = $pokemon;
            
            //echo '<pre>'.print_r($json, true).'</pre>';
            if(isset($_GET['output']))
            {
                switch($_GET['output'])
                {
                    case 'Android':
                        include(__DIR__.'/GenerateSQLiteOpenHelper/code.php');
                    break;
                }
            }
        }

        else if(isset($json->gender_settings))
        {
            $genderSettings = $json->gender_settings;
            $pbId = substr($json->template_id, 6);

            if(pokemonHasNoUse($pbId))
                continue;
            
            //If both genders are set then assign the ratios to our pokémon
            if(isset($genderSettings->gender->male_percent) && isset($genderSettings->gender->female_percent))
            {
                $genderRatios[$pbId]['male_ratio'] = $genderSettings->gender->male_percent;
                $genderRatios[$pbId]['female_ratio'] = $genderSettings->gender->female_percent;
            }
            //If a genderless species (like unown)
            else if(isset($genderSettings->gender->genderless_percent))
            {
                $genderRatios[$pbId]['male_ratio'] = 'null';
                $genderRatios[$pbId]['female_ratio'] = 'null'; 
            }
            //If male exclusive species (such as Nidoran♂)
            else if(isset($genderSettings->gender->male_percent) && !isset($genderSettings->gender->female_percent))
            {
                $genderRatios[$pbId]['male_ratio'] = $genderSettings->gender->male_percent;
                $genderRatios[$pbId]['female_ratio'] = 0;
            }
            //if female exclusive species (such as Nidoran♀)
            else if(!isset($genderSettings->gender->male_percent) && isset($genderSettings->gender->female_percent))
            {
                $genderRatios[$pbId]['female_ratio'] = $genderSettings->gender->female_percent;
                $genderRatios[$pbId]['male_ratio'] = 0;
            }
            else
            {
                die('<h1 style="color: red">Found a Pokémon that is not genderless, not male and not female.</h1>'.print_r($json,true));
            }
        }
    }
}

/**
 * Annoyingly the parent_id field on pokémon is not set to the protobuff id 
 * that seems to be used everywhere else. 
 * 
 * This function finds the pbId for the given $pokemonId and then finds the
 * pokémon name for that pbId.
 * 
 * @param string $pokemonId
 */
function getNameForParentId($ownPbId, $pokemonId)
{
    global $pokemonIdPbIdMap, $uniquePokemon;

    $pbId = $pokemonIdPbIdMap[$pokemonId];
    $evolvesFromName = $uniquePokemon[$pbId]['name'];

    /*Alolan Pokémon actually come with the POKEMON_ID of their non-alolan counterpart
    * e.g. 
    * <code>template_id: "V0028_POKEMON_SANDSLASH_ALOLA"
    * pokemon_settings {
    *  pokemon_id: SANDSLASH</code>
    *
    * Alolan pokémon definitely don't evolve from their non-alolan forms thus this get
    * corrrected in the return;
    */
    return strpos($ownPbId, 'ALOLA') !== false
        ? $evolvesFromName
        : str_replace('Alolan ', '', $evolvesFromName);
}

foreach($uniquePokemon as $pbId => $pokemon)
{
    //First fix the parent_pokemon field now that we know all the post-baby names.  
    if($pokemon['evolves_from'] !== 'null')
    {
        $pokemon['evolves_from'] = getNameForParentId($pbId, $pokemon['evolves_from']);
    }

    //Secondly now add the gender ratio
    $pokemon['male_ratio']   = $genderRatios[$pbId]['male_ratio'];
    $pokemon['female_ratio'] = $genderRatios[$pbId]['female_ratio'];

    //Third - Pokémon data complete. Add it to the insert query.
    $insertPokemonQuery .= "\n(".implode($pokemon, ', ').'),';

    //Fourth and finally - fill in the images query.
    createImagesQuery($pbId, $pokemon);    
}

function createImagesQuery($pbId, $pokemon)
{
    global $insertPokemonImagesQuery;

    /* These Pokémon are a special case, due to having multiple forms
     *  
     */
    $specialCases = [
        'V0351_POKEMON_CASTFORM_NORMAL'=> '11',
        'V0351_POKEMON_CASTFORM_RAINY' => '13',
        'V0351_POKEMON_CASTFORM_SNOWY' => '14',
        'V0351_POKEMON_CASTFORM_SUNNY' => '12',

        'V0386_POKEMON_DEOXYS_NORMAL'  => '11',
        'V0386_POKEMON_DEOXYS_ATTACK'  => '12',
        'V0386_POKEMON_DEOXYS_DEFENSE' => '13',
        'V0386_POKEMON_DEOXYS_SPEED'   => '14',
        
        'V0421_POKEMON_CHERRIM'        => '11',

        'V0487_POKEMON_GIRATINA_ALTERED' => '11',
        'V0487_POKEMON_GIRATINA_ORIGIN'  => '12',

        'V0492_POKEMON_SHAYMIN_LAND'   => '11',
    ];

    /*
     * These pokémon we'll just skip for the image part as they don't have an image yet.
     * This is due to them having multiple form and niantic probably not having figured out yet how to implement that.
     * 
     * If an image does show up for them move them to $specialCases
     */
    $overwriteCase = [
        'V0413_POKEMON_WORMADAM'       => '',
        'V0413_POKEMON_WORMADAM_PLANT' => '',
        'V0413_POKEMON_WORMADAM_SANDY' => '',
        'V0413_POKEMON_WORMADAM_TRASH' => '',

        'V0421_POKEMON_CHERRIM_OVERCAST' => '',
        'V0421_POKEMON_CHERRIM_SUNNY'    => '',

        'V0422_POKEMON_SHELLOS'            => '',
        'V0422_POKEMON_SHELLOS_EAST_SEA'   => '',
        'V0422_POKEMON_SHELLOS_WEST_SEA'   => '',
        'V0423_POKEMON_GASTRODON'          => '',
        'V0423_POKEMON_GASTRODON_EAST_SEA' => '',
        'V0423_POKEMON_GASTRODON_WEST_SEA' => '',

        'V0479_POKEMON_ROTOM'       => '',
        'V0479_POKEMON_ROTOM_FAN'   => '',
        'V0479_POKEMON_ROTOM_FROST' => '',
        'V0479_POKEMON_ROTOM_HEAT'  => '',
        'V0479_POKEMON_ROTOM_MOW'   => '',
        'V0479_POKEMON_ROTOM_WASH'  => '',

        //Equal to SPAWN_V0487_POKEMON_GIRATINA_ALTERED which is more accurate.
        'V0487_POKEMON_GIRATINA'    => '',

        //Equal to V0492_POKEMON_SHAYMIN_LAND which is more accurate
        'V0492_POKEMON_SHAYMIN' => '',
        //Doesn't have an image yet
        'V0492_POKEMON_SHAYMIN_SKY' => '',

        'V0493_POKEMON_ARCEUS'          => '',
        'V0493_POKEMON_ARCEUS_BUG'      => '',
        'V0493_POKEMON_ARCEUS_DARK'     => '',
        'V0493_POKEMON_ARCEUS_DRAGON'   => '',
        'V0493_POKEMON_ARCEUS_ELECTRIC' => '',
        'V0493_POKEMON_ARCEUS_FAIRY'    => '',
        'V0493_POKEMON_ARCEUS_FIGHTING' => '',
        'V0493_POKEMON_ARCEUS_FIRE'     => '',
        'V0493_POKEMON_ARCEUS_FLYING'   => '',
        'V0493_POKEMON_ARCEUS_GHOST'    => '',
        'V0493_POKEMON_ARCEUS_GRASS'    => '',
        'V0493_POKEMON_ARCEUS_GROUND'   => '',
        'V0493_POKEMON_ARCEUS_ICE'      => '',
        'V0493_POKEMON_ARCEUS_POISON'   => '',
        'V0493_POKEMON_ARCEUS_PSYCHIC'  => '',
        'V0493_POKEMON_ARCEUS_ROCK'     => '',
        'V0493_POKEMON_ARCEUS_STEEL'    => '',
        'V0493_POKEMON_ARCEUS_WATER'    => '',
    ];
 
    $pokeIconsDir = dirname(__FILE__).'/../eggfriends/public/images/pokemon_icons/';
    $isAlolan     = strpos($pbId, 'ALOLA') !== false;
    $filenameTemplate = 'pokemon_icon_'.substr($pbId, 2, 3);

    $files = null;
    if(!isset($specialCases[$pbId]) && !isset($overwriteCase[$pbId]))
        $files = glob($pokeIconsDir.$filenameTemplate.'*');
    else if(isset($specialCases[$pbId]) )
        $files = glob($pokeIconsDir.$filenameTemplate.'_'.$specialCases[$pbId].'*');
    else //Cause the foreach to do nothing.
        $files = [];

    foreach($files as $filePath)
    {
        //TODO: if file matches one below - just ignore it. Alolan Raichu has the same image multiple times.
        //pokemon_icon_026_61_01.png
        //pokemon_icon_026_61_01_shiny.png
        //pokemon_icon_026_61_02.png
        //pokemon_icon_026_61_02_shiny.png
        //pokemon_icon_026_61_03.png
        //pokemon_icon_026_61_03_shiny.png
        //pokemon_icon_026_61_04.png
        //pokemon_icon_026_61_04_shiny.png
        //pokemon_icon_026_61_05.png
        //pokemon_icon_026_61_05_shiny.png

        $filename = basename($filePath);
        $filenameParts = explode('_', $filename);
        $strGender = strtok($filenameParts[3], '.png');
        $isGenderless = $pokemon['male_ratio'] === 'null' && $pokemon['female_ratio'] === 'null';
        $isAltForm = strpos($filename, 'shiny')!==false
            ? count($filenameParts) > 5
            : count($filenameParts) > 4;
        //Since alolan pokémon share the same pokedex number make sure they're not added to their baseform
        //or vice versa.
        if(!$isAlolan && intval($strGender) < 61 || $isAlolan && intval($strGender)>= 61)
        {
            $insertPokemonImagesQuery .= sprintf(
                "\n(%s, %s, %s, %s, %s, %s),",
                //Name
                $pokemon['name'],
                //Male - if the species can even be male then 00 = male, Alolan Pokémon do have a gender but no gender differences
                intval($isAlolan || !$isGenderless && $strGender === '00' && $pokemon['male_ratio'] != 1), 
                //Female - Females are typically '01' but for female exclusive species it can be 00.
                //If there's no female specific form  it's the same as the male form.
                intval(
                    !$isGenderless && $strGender === '01' 
                    || $pokemon['female_ratio'] == 1
                    || ($pokemon['female_ratio'] > 0 && !checkIfFemaleVariantExists($pokeIconsDir, $filenameParts))
                    || $isAlolan //Alolan Pokémon do have a gender but no gender differences
                ),
                $isAltForm ? '"'.strtok($filenameParts[4], '.png').'"' : 'null',
                intval(strpos($filename, 'shiny')!==false),
                '"'.$filename.'"'
            );
        }
    }
}

function checkIfFemaleVariantExists($dir, $maleForm)
{
    if(isset($maleForm[3]))
    {
        $maleForm[3] = str_replace('00', '01', $maleForm[3]);
        return file_exists($dir.implode('_', $maleForm));
    }
    return false;
}

$insertPokemonQuery = rtrim($insertPokemonQuery, ',').';';
$insertPokemonImagesQuery = rtrim($insertPokemonImagesQuery, ',').';';

/** @param String */
function extractPokedexNumberFromId($str)
{
    return intval(substr($str, 1, 4), 10);
}

function extractPokemonNameFromTemplateId($str)
{
    $pokedexId = intval(substr($str, 1, 4));
    $name = substr($str, 14);
    $parts = explode('_', $name);

    //Fix unique cases
    //Do note that you may need to manipulate $parts[0] instead of $name
    switch($pokedexId)
    {
        case 29:
            $name .= '♀';
            break;
        case 32:
            $name .= '♂';
            break;
    }

    if(count($parts)>1 && $parts[1] === 'ALOLA')
    {
        return 'Alolan '.getEnglishPokemonName($parts[0]);
    }
    else if(count($parts) > 1)
    {
        if(strpos($name, '_') !== false)
        {
            $partsArrayObject = new ArrayObject($parts);
            // create a copy of the array
            $nonNameParts = $partsArrayObject->getArrayCopy();
            // Get rid of the name
            array_shift($nonNameParts);
            
            return getEnglishPokemonName($parts[0].' ('.implode(' ', $nonNameParts).')');
        }

    }
    return getEnglishPokemonName($name);
}

function getEnglishPokemonName($idName)
{
    //echo $idName.'<hr>';
    switch($idName)
    {
        case 'MR (MIME)':
            return 'Mr. Mime';
        case 'HO (OH)':
            return 'Ho-Oh';
        case 'MIME (JR)':
            return 'Mime jr.';
        case 'PORYGON (Z)':
            return 'Porygon-Z';
        default:
                return ucfirst(strtolower($idName));
    }
}

function extractPokemonType($str)
{
    return strtolower(substr($str, 13));
}

function getPokemonNameByPokedex($pokedex)
{    
    global $uniquePokemon;
    $id = intval($pokedex);
    foreach($uniquePokemon as $pokemonName => $pokemon)
    {
        if($pokemon['pokedex_number'] === $pokedex)
        {
            return $pokemon['name'];
        }
    }
    return "OOPS Something went wrong";
}

$uniqueTypesToImplode = [];
foreach($uniqueTypes as $uniqueType => $nonsense)
{
    $uniqueTypesToImplode[] = extractPokemonType($uniqueType);
}
$insertTypeQuery .= "\n(\"" 
    .implode($uniqueTypesToImplode, "\"),\n(\"")
    .'");';
?><!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>SQLiteOpenHelper helper</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <h1>Pokémon Go Database</h1>
        <form action="/" method="GET" id="output-for">
            <label for="output">Output for: </label>
            <select name="output" id="select-output">
                <option></option>
                <option value="Android" <?php echo isset($_GET['output']) && $_GET['output'] === 'Android' ? 'SELECTED' : '' ?>>Android</option>
                <option value="Laravel" <?php echo isset($_GET['output']) && $_GET['output'] === 'Laravel' ? 'SELECTED' : '' ?>>Laravel</option>
            </select>
        </form>
        <script type="text/javascript">
        document.getElementById('select-output').addEventListener("change", function(e){
            var form = this;
            while(form.nodeName != 'FORM'){
                form = form.parentNode;
            }
            form.submit();
        });
        </script>
        
        <div class="splitter-container">
            <small class="tab-header tabbed">Create Statements <abbr title="Data Definition Language">(DDL)</abbr></small>
            <textarea class="sql creates" id="ddl-sql" name="ddl-sql"><?php echo $creates ?></textarea>
        </div>
        
        <div class="splitter-container">
            <small class="tab-header tabbed">Insert Statements <abbr title="Data Manipulation Language">(DML)</abbr></small>
            <textarea id="dml-sql">
<?php 
echo $insertTypeQuery."\n\n"; 
echo $insertPokemonQuery."\n\n";
echo $insertPokemonImagesQuery."\n\n";
?>
            </textarea>        
        </div>
        
        <script src="https://cdn.jsdelivr.net/ace/1.2.6/min/ace.js"></script>
        <script>
            var editorDDL = ace.edit("ddl-sql");
            editorDDL.setTheme("ace/theme/monokai");
            editorDDL.getSession().setMode("ace/mode/sql");
            
            var editorDDL = ace.edit("dml-sql");
            editorDDL.setTheme("ace/theme/monokai");
            editorDDL.getSession().setMode("ace/mode/sql");
        </script>
<?php
    if(isset($_GET['output']))
    {
        if(isset($_GET['output']))
        {
            switch($_GET['output'])
            {
                case 'Android':
                    include(__DIR__.'/GenerateSQLiteOpenHelper/output.html');
                break;
                case 'Laravel':
                    include(__DIR__.'/GenerateLaravelFiles/output.html');
                    break;
            }
        } 
    }

$timeEnd = microtime(true);
$time = $timeEnd - $timeStart;
echo '<!--'.$time.' seconds -->';
    ?>
    </body>
</html>
<?php
$stringsForAndroid .= htmlspecialchars( 
                sprintf('<string name="%s">%s</string>'."\n", 
                    $pokemonSettings->pokemon_id, 
                    getEnglishPokemonName($pokemonSettings->pokemon_id)
                )
            );
?>
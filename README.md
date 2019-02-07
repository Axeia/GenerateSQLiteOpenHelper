
# Project is dead

As a follow up to having revived this project I shall now put an end to this particular one. The code became a mess trying to make it do 2 things whilst I only use it for one.
Also the parsing of the SQL is quite slow so I'm going to start afresh for my Laravel (and thus not Android) needs.

## PokemonGoDB
PokemonGoDB is an attempt at turning the gamedata of interest from Pokémon Go into a Database. Previously I had some interest in doing this for the purposes of an Android app
but due to Niantic cracking down on map apps and thus the use of my app I stopped development on that.
I am however now working on a Laravel based website for which I'll need the same data and thus have revived this project.
## What's the aim of this project?
For my own use for now the aim is to have a database with the data of the Pokémon in it. Possibly in the future this may be expanded upon to include calculating moveset dps etc etc.
## How does it work?
The [pogo-game-master-decoder](https://github.com/apavlinovic/pogo-game-master-decoder) project is at the base of the project although that's a completely seperate project not made or maintained by me. It takes the games master file and converts it into the [protobuf format](https://developers.google.com/protocol-buffers/)
  1. A datafile as output by the pogo-game-master-decoder is converted to JSON using several regular expressions.
  2. SQL insert statements are created based on the JSON file generated in step 1.
  3. Here we branch off between the new and old usage.
     1. Laravel
        - A laravel migration file is generated. 
        - A laravel seeder file is generated.
     2. Android (old and basically abandoned by me)
        - An Android SQLiteOpenHelper class is generated based of the create statements.
        - * The [GenerateSQLiteOpenHelper](https://github.com/Axeia/GenerateSQLiteOpenHelper) project is used for this, that project is maintained (well not really anymore) and created by me. 
            It's simply split off from this one as I thought more people have an use for it.
            XML string elements are generated based off the JSON to be used in your Android projects strings file containing the names of the Pokémon.
           

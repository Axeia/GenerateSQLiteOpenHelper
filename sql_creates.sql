
/**
 * As type is a reserved keyword in SQL we're trying not to use it. 
 */
CREATE TABLE types
(
  typing VARCHAR(255) PRIMARY KEY NOT NULL
);


/**
 * Left out FOREIGN KEY (evolves_from) REFERENCES pokemons (name),
 * As later generations added baby forms for pokémon that were already
 * in the game. 
 * So even though Pikachu evolves from Pichu, 
 * Pikachu is a gen 1 pokémon and thus comes first without Pichu being known yet.
 * - Reversing the logic is not an option as the value wouldn't be unique, e.g. 
 * Jolteon has always evolved from Eevee, But Eevee doesn't always evolve into Joleon
 * (also into Flareon, Vaporeon, Leafeon etc)
 *
 *
 * The female_ratio field isn't properly normalised as it's a calculated value
 * but it's added for convience
 * The following should net us the same value as the female_ratio field:
 *
 * SELECT CASE WHEN male_ratio IS NOT NULL 
 *             THEN 1 - male_ratio 
 *             ELSE null
 *              END 
               AS female_ratio
 */
CREATE TABLE pokemons
(
  name              VARCHAR(255)  COLLATE utf8mb4_unicode_ci NOT NULL,
  pokedex_number    INT           NOT NULL,
  type1             VARCHAR(255)  COLLATE utf8mb4_unicode_ci NOT NULL,
  type2             VARCHAR(255)  COLLATE utf8mb4_unicode_ci NULL,
  base_attack       INT           NOT NULL,
  base_defense      INT           NOT NULL,
  base_stamina      INT           NOT NULL,
  base_capture_rate DECIMAL(7, 3) NULL,
  base_flee_rate    DECIMAL(7, 3) NULL,
  evolves_from      VARCHAR(255)  COLLATE utf8mb4_unicode_ci NULL,
  buddy_distance    INT           NOT NULL,
  candy_to_evolve   INT           NULL,
  released          TINYINT(1)    NOT NULL,
  shiny             TINYINT(1)    NOT NULL,
  male_ratio        DECIMAL(4, 3) NULL,
  female_ratio      DECIMAL(4, 3) NULL,

  PRIMARY KEY (name),
  FOREIGN KEY (type1) REFERENCES types (typing),
  FOREIGN KEY (type2) REFERENCES types (typing)
);

/**
 * All the image names with some additional information
 * about the pictured pokémon.
 *
 * You could possibly consider all fields beside name and file_name
 * to be calculated values as they are but the logic behind it is quite
 * complicated so we're probably better off simply doing this once in PHP during
 * the generation phase rather than every single time we want to queue this table.
 */
CREATE TABLE pokemon_images
(
  pokemons_name   VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  male            TINYINT(1)   NOT NULL,
  female          TINYINT(1)   NOT NULL,
  alt_form        CHAR(2)      NULL,
  shiny           TINYINT(1)   NOT NULL,
  file_name       VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,

  PRIMARY KEY(file_name),
  FOREIGN KEY(pokemons_name) REFERENCES pokemons (name)
)

CREATE TABLE evolution_branches
(
  pokemons_name   VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  evolved_name    VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL, --could be a special form
  evolution_item  VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL, --Bag_Sinnoh_Stone_Sprite.png
  buddy_distance  DECIMAL(4,1) NULL, --20.0 for feebas
  gender          TINYINT(1) NULL
)
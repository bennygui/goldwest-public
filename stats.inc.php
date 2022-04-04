<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * goldwest implementation : © Guillaume Benny bennygui@gmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * stats.inc.php
 *
 * goldwest game statistics description
 *
 */

/*
    In this file, you are describing game statistics, that will be displayed at the end of the
    game.
    
    !! After modifying this file, you must use "Reload  statistics configuration" in BGA Studio backoffice
    ("Control Panel" / "Manage Game" / "Your Game")
    
    There are 2 types of statistics:
    _ table statistics, that are not associated to a specific player (ie: 1 value for each game).
    _ player statistics, that are associated to each players (ie: 1 value for each player in the game).

    Statistics types can be "int" for integer, "float" for floating point values, and "bool" for boolean
    
    Once you defined your statistics there, you can start using "initStat", "setStat" and "incStat" method
    in your game logic, using statistics names defined below.
    
    !! It is not a good idea to modify this file when a game is running !!

    If your game is already public on BGA, please read the following before any change:
    http://en.doc.boardgamearena.com/Post-release_phase#Changes_that_breaks_the_games_in_progress
    
    Notes:
    * Statistic index is the reference used in setStat/incStat/initStat PHP method
    * Statistic index must contains alphanumerical characters and no space. Example: 'turn_played'
    * Statistics IDs must be >=10
    * Two table statistics can't share the same ID, two player statistics can't share the same ID
    * A table statistic can have the same ID than a player statistics
    * Statistics ID is the reference used by BGA website. If you change the ID, you lost all historical statistic data. Do NOT re-use an ID of a deleted statistic
    * Statistic name is the English description of the statistic as shown to players
    
*/

require_once("modules/GWGlobals.inc.php");

$stats_type = [
    // Statistics global to table
    "table" => [/*
        "turns_number" => [
            "id" => 10,
            "name" => totranslate("Number of turns"),
            "type" => "int"
        ],
    */],

    // Statistics existing for each player
    "player" => [
        STATS_PLAYER_TURN_ORDER => [
            "id" => 10,
            "name" => totranslate("Player turn order"),
            "type" => "int"
        ],

        STATS_PLAYER_SCORE_SHIPPING_TRACK => [
            "id" => 25,
            "name" => totranslate("Score from Shipping Track"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_BOOMTOWN => [
            "id" => 26,
            "name" => totranslate("Score from BoomTown"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_INVESTMENTS => [
            "id" => 27,
            "name" => totranslate("Score from Investments"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_LARGEST_GROUP => [
            "id" => 28,
            "name" => totranslate("Score from largest building group"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_INFLUENCE => [
            "id" => 29,
            "name" => totranslate("Score from Influence"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_WANTED => [
            "id" => 30,
            "name" => totranslate("Score lost from being Wanted"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_SUPPLY_TRACK => [
            "id" => 31,
            "name" => totranslate("Score from Supply Track"),
            "type" => "int"
        ],

        STATS_PLAYER_NB_GAINED_RESOURCE_TOTAL => [
            "id" => 11,
            "name" => totranslate("Nb gained resources (total)"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_GAINED_RESOURCE_GO => [
            "id" => 12,
            "name" => totranslate("Nb gained Gold"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_GAINED_RESOURCE_SI => [
            "id" => 13,
            "name" => totranslate("Nb gained Silver"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_GAINED_RESOURCE_CO => [
            "id" => 14,
            "name" => totranslate("Nb gained Copper"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_GAINED_RESOURCE_WO => [
            "id" => 15,
            "name" => totranslate("Nb gained Wood"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_GAINED_RESOURCE_ST => [
            "id" => 16,
            "name" => totranslate("Nb gained Stone"),
            "type" => "int"
        ],

        STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_0 => [
            "id" => 17,
            "name" => totranslate("Nb resources added to supply track 0"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_1 => [
            "id" => 18,
            "name" => totranslate("Nb resources added to supply track 1"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_2 => [
            "id" => 19,
            "name" => totranslate("Nb resources added to supply track 2"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_3 => [
            "id" => 20,
            "name" => totranslate("Nb resources added to supply track 3"),
            "type" => "int"
        ],

        STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_0 => [
            "id" => 21,
            "name" => totranslate("Nb activation of supply track 0"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_1 => [
            "id" => 22,
            "name" => totranslate("Nb activation of supply track 1"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_2 => [
            "id" => 23,
            "name" => totranslate("Nb activation of supply track 2"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_3 => [
            "id" => 24,
            "name" => totranslate("Nb activation of supply track 3"),
            "type" => "int"
        ],

        STATS_PLAYER_SHIPPING_DISTANCE_GO => [
            "id" => 32,
            "name" => totranslate("Shipping distance of Gold"),
            "type" => "int"
        ],
        STATS_PLAYER_SHIPPING_DISTANCE_SI => [
            "id" => 33,
            "name" => totranslate("Shipping distance of Silver"),
            "type" => "int"
        ],
        STATS_PLAYER_SHIPPING_DISTANCE_CO => [
            "id" => 34,
            "name" => totranslate("Shipping distance of Copper"),
            "type" => "int"
        ],

        STATS_PLAYER_NB_SHIPPING_BONUS => [
            "id" => 35,
            "name" => totranslate("Nb of Shipping bonus tiles"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_INVESTMENT_BONUS => [
            "id" => 36,
            "name" => totranslate("Nb of Investment bonus tiles"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_INVESTMENT => [
            "id" => 37,
            "name" => totranslate("Nb of Investment cards"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_BOOMTOWN_INFLUENCE => [
            "id" => 38,
            "name" => totranslate("Nb of influence placed in BoomTown"),
            "type" => "int"
        ],

        STATS_PLAYER_NB_BUILT_CAMP => [
            "id" => 39,
            "name" => totranslate("Nb of built Camps"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_BUILT_SETTLEMENT => [
            "id" => 40,
            "name" => totranslate("Nb of built Settlements"),
            "type" => "int"
        ],
        STATS_PLAYER_NB_LOOT => [
            "id" => 41,
            "name" => totranslate("Nb of Camp in Wanted area"),
            "type" => "int"
        ],

        STATS_PLAYER_INFLUENCE_WO => [
            "id" => 42,
            "name" => totranslate("Total influence for Wood"),
            "type" => "int"
        ],
        STATS_PLAYER_INFLUENCE_GO => [
            "id" => 43,
            "name" => totranslate("Total influence for Gold"),
            "type" => "int"
        ],
        STATS_PLAYER_INFLUENCE_SI => [
            "id" => 44,
            "name" => totranslate("Total influence for Silver"),
            "type" => "int"
        ],
        STATS_PLAYER_INFLUENCE_CO => [
            "id" => 45,
            "name" => totranslate("Total influence for Copper"),
            "type" => "int"
        ],

        STATS_PLAYER_SCORE_OFFICE_0 => [
            "id" => 46,
            "name" => totranslate("Total score for BoomTown office: Docks"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_1 => [
            "id" => 47,
            "name" => totranslate("Total score for BoomTown office: Frontier Office"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_2 => [
            "id" => 48,
            "name" => totranslate("Total score for BoomTown office: Homestead Office"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_3 => [
            "id" => 49,
            "name" => totranslate("Total score for BoomTown office: Courthouse"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_4 => [
            "id" => 50,
            "name" => totranslate("Total score for BoomTown office: Deeds office"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_5 => [
            "id" => 51,
            "name" => totranslate("Total score for BoomTown office: Depot"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_6 => [
            "id" => 52,
            "name" => totranslate("Total score for BoomTown office: Mayor's Office"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_7 => [
            "id" => 53,
            "name" => totranslate("Total score for BoomTown office: Saloon"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_8 => [
            "id" => 54,
            "name" => totranslate("Total score for BoomTown office: Sheriff's Office"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_9 => [
            "id" => 55,
            "name" => totranslate("Total score for BoomTown office: Shipping Office"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_10 => [
            "id" => 56,
            "name" => totranslate("Total score for BoomTown office: Surveyor's Office"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_11 => [
            "id" => 57,
            "name" => totranslate("Total score for BoomTown office: Town Hall"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_12 => [
            "id" => 58,
            "name" => totranslate("Total score for BoomTown office: Insurance Office"),
            "type" => "int"
        ],
        STATS_PLAYER_SCORE_OFFICE_13 => [
            "id" => 59,
            "name" => totranslate("Total score for BoomTown office: Hoosegow"),
            "type" => "int"
        ],
    ]
];

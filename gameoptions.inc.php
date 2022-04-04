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
 * gameoptions.inc.php
 *
 * goldwest game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in goldwest.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

require_once("modules/GWGlobals.inc.php");

$game_options = [

    GAME_OPTION_EXPANSION_BANDITS => [
        'name' => totranslate('Bandits mini-expansion'),
        'values' => [
            GAME_OPTION_EXPANSION_BANDITS_VALUE_OFF => [
                'name' => totranslate('Exclude this expansion'),
                'tmdisplay' => totranslate('Exclude Bandits mini-expansion'),
            ],
            GAME_OPTION_EXPANSION_BANDITS_VALUE_ON => [
                'name' => totranslate('Include this expansion'),
                'tmdisplay' => totranslate('Include Bandits mini-expansion'),
                'description' => totranslate('Add two new BoomTown offices: Insurance Office and Hoosegow'),
            ],
        ],
    ],

    GAME_OPTION_EXPANSION_TRADING_POST => [
        'name' => totranslate('Trading Post mini-expansion'),
        'values' => [
            GAME_OPTION_EXPANSION_TRADING_POST_VALUE_OFF => [
                'name' => totranslate('Exclude this expansion'),
                'tmdisplay' => totranslate('Exclude Trading Post mini-expansion'),
            ],
            GAME_OPTION_EXPANSION_TRADING_POST_VALUE_ONLY_EXPANSION => [
                'name' => totranslate('Only tiles from this expansion'),
                'tmdisplay' => totranslate('Only use tiles from the Trading Post mini-expansion'),
                'description' => totranslate('Randomly choose from the 4 new resources tiles for BoomTown instead of the 4 points tile.'),
            ],
            GAME_OPTION_EXPANSION_TRADING_POST_VALUE_WITH_BASE => [
                'name' => totranslate('Tiles from this expansion or the base tile'),
                'tmdisplay' => totranslate('Use tiles from the Trading Post mini-expansion or the base tile'),
                'description' => totranslate('Randomly choose from the base 4 points tile and the 4 new resources tiles for BoomTown.'),
            ],
        ],
    ],

    /*
    
    // note: game variant ID should start at 100 (ie: 100, 101, 102, ...). The maximum is 199.
    100 => array(
                'name' => totranslate('my game option'),    
                'values' => array(

                            // A simple value for this option:
                            1 => array( 'name' => totranslate('option 1') )

                            // A simple value for this option.
                            // If this value is chosen, the value of "tmdisplay" is displayed in the game lobby
                            2 => array( 'name' => totranslate('option 2'), 'tmdisplay' => totranslate('option 2') ),

                            // Another value, with other options:
                            //  description => this text will be displayed underneath the option when this value is selected to explain what it does
                            //  beta=true => this option is in beta version right now (there will be a warning)
                            //  alpha=true => this option is in alpha version right now (there will be a warning, and starting the game will be allowed only in training mode except for the developer)
                            //  nobeginner=true  =>  this option is not recommended for beginners
                            3 => array( 'name' => totranslate('option 3'), 'description' => totranslate('this option does X'), 'beta' => true, 'nobeginner' => true )
                        ),
                'default' => 1
            ),

    */

];

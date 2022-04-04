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
 * states.inc.php
 *
 * goldwest game states description
 *
 */

//    !! It is not a good idea to modify this file when a game is running !!

require_once("modules/GWGlobals.inc.php");

$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array("" => STATE_START_OF_TURN_SETUP_ID)
    ),

    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    ),

    // States related to "Activate Supply Track"
    STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID => [
        "name" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE,
        "description" => clienttranslate('${actplayer} must choose a supply track to activate'),
        "descriptionmyturn" => clienttranslate('${you} must choose a supply track to activate'),
        "type" => "activeplayer",
        "args" => "argGenericOnlyGlobals",
        "possibleactions" => [
            "chooseSupplyTrackToActivate",
            //"chooseDebugGotoLastTurn", // DEBUG! Comment for production
        ],
        "transitions" => [
            "toLeaveResource" => STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE_ID,
            "zombiePass" => STATE_NEXT_PLAYER_OR_GAME_END_ID,
            //"toDebugGotoLastTurn" => STATE_START_OF_TURN_SETUP_ID, // DEBUG! Comment for production
        ],
    ],
    STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE_ID => array(
        "name" => STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE,
        "description" => '',
        "type" => "game",
        "action" => "stHasSupplyTrackResourcesToLeave",
        "transitions" => [
            "toUseMetal" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
            "toLeaveResource" => STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE_ID,
        ],
    ),
    STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE_ID => [
        "name" => STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE,
        "description" => clienttranslate('${actplayer} must choose a resource to leave in supply track ${STG_CHOSEN_SUPPLY_TRACK_SECTION}'),
        "descriptionmyturn" => clienttranslate('${you} must choose a resource to leave in supply track ${STG_CHOSEN_SUPPLY_TRACK_SECTION}'),
        "type" => "activeplayer",
        "args" => "argChooseSupplyTrackResourceToLeave",
        "possibleactions" => ["chooseSupplyTrackResourceToLeave", "cancelTurn"],
        "transitions" => [
            "next" => STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],

    // States related to "Use Metal"
    STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID => array(
        "name" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE,
        "description" => '',
        "type" => "game",
        "action" => "stHasMetalToUseAndPlaceToUse",
        "transitions" => [
            "toUseMetal" => STATE_CHOOSE_METAL_USE_ID,
            "toNoMetal" => STATE_CHECK_IS_LAST_TURN_ID,
        ],
    ),
    STATE_CHOOSE_METAL_USE_ID => [
        "name" => STATE_CHOOSE_METAL_USE,
        "description" => clienttranslate('${actplayer} must use the activated metals'),
        "descriptionmyturn" => clienttranslate('${you} must use the activated metals'),
        "type" => "activeplayer",
        "args" => "argPossibleMetalUse",
        "possibleactions" => ["chooseBoomTown", "chooseInvestment", "chooseShippingTrack", "cancelTurn"],
        "transitions" => [
            "toUsedBoomTown" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
            "toUsedBoomTownTradingPostMetal" => STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK_ID,
            "toUsedBoomTownTradingPostWoodOrStone" => STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE_ID,
            "toUsedShippingTrack" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
            "toUsedBasicInvestment" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
            // These are for the special investment cards that requires more actions
            "toUsedInvestmentFreeShippingTrack" => STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK_ID,
            "toUsedInvestmentFreeUnoccupiedBoomTown" => STATE_INVESTMENT_HAS_FREE_UNOCCUPIED_BOOMTOWN_ID,
            "toUsedInvestmentFreeOccupiedBoomTown" => STATE_INVESTMENT_HAS_FREE_OCCUPIED_BOOMTOWN_ID,
            "toUsedInvestmentMiningTokenToView" => STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW_ID,
            "toUsedInvestmentNewResources" => STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES_ID,
            "toUsedInvestmentCampUpgrade" => STATE_INVESTMENT_HAS_CAMP_TO_UPGRADE_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],

    // State related to Buy/Loot
    STATE_CHECK_IS_LAST_TURN_ID => array(
        "name" => STATE_CHECK_IS_LAST_TURN,
        "description" => '',
        "type" => "game",
        "action" => "stCheckIsLastTurn",
        "transitions" => [
            "toNotLastTurn" => STATE_HAS_WOOD_OR_STONE_ID,
            "toIsLastTurn" => STATE_DISCARD_REMAINING_RESOURCES_LAST_TURN_ID,
        ],
    ),
    STATE_HAS_WOOD_OR_STONE_ID => array(
        "name" => STATE_HAS_WOOD_OR_STONE,
        "description" => '',
        "type" => "game",
        "action" => "stHasWoodOrStone",
        "transitions" => [
            "toLoot" => STATE_CHOOSE_TOKEN_LOOT_ID,
            "toCamp" => STATE_CHOOSE_TOKEN_BUILD_CAMP_ID,
            "toSettlement" => STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT_ID,
        ],
    ),
    STATE_CHOOSE_TOKEN_LOOT_ID => [
        "name" => STATE_CHOOSE_TOKEN_LOOT,
        "description" => clienttranslate('${actplayer} must choose a mining token to loot'),
        "descriptionmyturn" => clienttranslate('${you} must choose a mining token to loot'),
        "type" => "activeplayer",
        "args" => "argChooseToken",
        "possibleactions" => ["chooseMiningToken", "cancelTurn"],
        "transitions" => [
            "next" => STATE_DISCARD_REMAINING_RESOURCES_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_CHOOSE_TOKEN_BUILD_CAMP_ID => [
        "name" => STATE_CHOOSE_TOKEN_BUILD_CAMP,
        "description" => clienttranslate('${actplayer} must choose a mining token to build a camp'),
        "descriptionmyturn" => clienttranslate('${you} must choose a mining token to build a camp'),
        "type" => "activeplayer",
        "args" => "argChooseToken",
        "possibleactions" => ["chooseMiningToken", "cancelTurn"],
        "transitions" => [
            "next" => STATE_DISCARD_REMAINING_RESOURCES_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT_ID => [
        "name" => STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT,
        "description" => clienttranslate('${actplayer} must choose a mining token to build a settlement'),
        "descriptionmyturn" => clienttranslate('${you} must choose a mining token to build a settlement'),
        "type" => "activeplayer",
        "args" => "argChooseToken",
        "possibleactions" => ["chooseMiningToken", "cancelTurn"],
        "transitions" => [
            "next" => STATE_DISCARD_REMAINING_RESOURCES_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_DISCARD_REMAINING_RESOURCES_ID => array(
        "name" => STATE_DISCARD_REMAINING_RESOURCES,
        "description" => '',
        "type" => "game",
        "action" => "stDiscardRemainingResources",
        "transitions" => ["" => STATE_CHOOSE_SUPPLY_TRACK_TO_ADD_ID],
    ),
    STATE_DISCARD_REMAINING_RESOURCES_LAST_TURN_ID => array(
        "name" => STATE_DISCARD_REMAINING_RESOURCES_LAST_TURN,
        "description" => '',
        "type" => "game",
        "action" => "stDiscardRemainingResources",
        "transitions" => ["" => STATE_CHOOSE_TO_CONFIRM_TURN_ID],
    ),
    STATE_CHOOSE_SUPPLY_TRACK_TO_ADD_ID => [
        "name" => STATE_CHOOSE_SUPPLY_TRACK_TO_ADD,
        "description" => clienttranslate('${actplayer} must choose the supply track where to add the resources'),
        "descriptionmyturn" => clienttranslate('${you} must choose the supply track where to add the resources'),
        "type" => "activeplayer",
        "args" => "argChooseSupplyTrackToAdd",
        "possibleactions" => ["chooseSupplyTrackToAdd", "cancelTurn"],
        "transitions" => [
            "next" => STATE_CHOOSE_TO_CONFIRM_TURN_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],

    // State related to End of turn
    STATE_CHOOSE_TO_CONFIRM_TURN_ID => [
        "name" => STATE_CHOOSE_TO_CONFIRM_TURN,
        "description" => clienttranslate('${actplayer} must confirm their turn'),
        "descriptionmyturn" => clienttranslate('${you} must confirm your turn'),
        "type" => "activeplayer",
        "args" => "argChooseToConfirmTurn",
        "possibleactions" => ["confirmTurn", "cancelTurn"],
        "transitions" => [
            "next" => STATE_NEXT_PLAYER_OR_GAME_END_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_INFORM_AFTER_CANCEL_VIEW_ID => [
        "name" => STATE_INFORM_AFTER_CANCEL_VIEW,
        "description" => clienttranslate('${actplayer} is restarting their turn after the investment card action'),
        "descriptionmyturn" => clienttranslate('${you} are restarting your turn after your investment card action'),
        "type" => "activeplayer",
        "args" => "argGenericOnlyGlobals",
        "possibleactions" => ["confirmAfterCancelView"],
        "transitions" => ["" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID],
    ],
    STATE_NEXT_PLAYER_OR_GAME_END_ID => array(
        "name" => STATE_NEXT_PLAYER_OR_GAME_END,
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayerOrGameEnd",
        "updateGameProgression" => true,
        "transitions" => [
            "nextPlayer" => STATE_SWITCH_TO_NEXT_PLAYER_ID,
            "gameEnd" => STATE_END_SCORING_ID,
        ],
    ),
    STATE_SWITCH_TO_NEXT_PLAYER_ID => array(
        "name" => STATE_SWITCH_TO_NEXT_PLAYER,
        "description" => '',
        "type" => "game",
        "action" => "stSwitchToNextPlayer",
        "updateGameProgression" => true,
        "transitions" => ["" => STATE_START_OF_TURN_SETUP_ID],
    ),
    STATE_START_OF_TURN_SETUP_ID => array(
        "name" => STATE_START_OF_TURN_SETUP,
        "description" => '',
        "type" => "game",
        "action" => "stStartOfTurnSetup",
        "transitions" => ["" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID],
    ),

    // State related to Investment cards
    STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK_ID => array(
        "name" => STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK,
        "description" => '',
        "type" => "game",
        "action" => "stInvestmentHasFreeShippingTrack",
        "transitions" => [
            "toChooseShippingTrack" => STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK_ID,
            "toNoShippingTrack" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
        ],
    ),
    STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK_ID => [
        "name" => STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK,
        "description" => clienttranslate('${actplayer} must choose a shipping track to advance for free'),
        "descriptionmyturn" => clienttranslate('${you} must choose a shipping track to advance for free'),
        "type" => "activeplayer",
        "args" => "argInvestmentShippingTrack",
        "possibleactions" => ["chooseFreeShippingTrack", "cancelTurn"],
        "transitions" => [
            "next" => STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_INVESTMENT_HAS_FREE_UNOCCUPIED_BOOMTOWN_ID => array(
        "name" => STATE_INVESTMENT_HAS_FREE_UNOCCUPIED_BOOMTOWN,
        "description" => '',
        "type" => "game",
        "action" => "stInvestmentHasUnoccupiedBoomTown",
        "transitions" => [
            "toChooseBoomTown" => STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN_ID,
            "toTownFull" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
        ],
    ),
    STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN_ID => [
        "name" => STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN,
        "description" => clienttranslate('${actplayer} must choose a BoomTown office for free'),
        "descriptionmyturn" => clienttranslate('${you} must choose a BoomTown office for free'),
        "type" => "activeplayer",
        "args" => "argInvestmentUnoccupiedBoomTown",
        "possibleactions" => ["chooseFreeBoomTown", "cancelTurn"],
        "transitions" => [
            "next" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
            "toUsedBoomTownTradingPostMetal" => STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK_ID,
            "toUsedBoomTownTradingPostWoodOrStone" => STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_INVESTMENT_HAS_FREE_OCCUPIED_BOOMTOWN_ID => array(
        "name" => STATE_INVESTMENT_HAS_FREE_OCCUPIED_BOOMTOWN,
        "description" => '',
        "type" => "game",
        "action" => "stInvestmentHasOccupiedBoomTown",
        "transitions" => [
            "toChooseBoomTown" => STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN_ID,
            "toNotOccupied" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
        ],
    ),
    STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN_ID => [
        "name" => STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN,
        "description" => clienttranslate('${actplayer} must choose an occupied BoomTown office for free'),
        "descriptionmyturn" => clienttranslate('${you} must choose an occupied BoomTown office for free'),
        "type" => "activeplayer",
        "args" => "argInvestmentOccupiedBoomTown",
        "possibleactions" => ["chooseFreeOccupiedBoomTown", "cancelTurn"],
        "transitions" => [
            "next" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
            "toUsedBoomTownTradingPostMetal" => STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK_ID,
            "toUsedBoomTownTradingPostWoodOrStone" => STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW_ID => array(
        "name" => STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW,
        "description" => '',
        "type" => "game",
        "action" => "stHasRemainMiningTokenToView",
        "transitions" => [
            "toHasTokenToView" => STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW_ID,
            "toNoTokenToView" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
        ],
    ),
    STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW_ID => [
        "name" => STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW,
        "description" => clienttranslate('${actplayer} must choose a mining token to view'),
        "descriptionmyturn" => clienttranslate('${you} must choose a mining token to view'),
        "type" => "activeplayer",
        "args" => "argMiningTokenToView",
        "possibleactions" => ["chooseMiningTokenToView", "cancelTurn"],
        "transitions" => [
            "toViewedToken" => STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_INVESTMENT_HAS_CAMP_TO_UPGRADE_ID => array(
        "name" => STATE_INVESTMENT_HAS_CAMP_TO_UPGRADE,
        "description" => '',
        "type" => "game",
        "action" => "stHasCampToUpgrade",
        "transitions" => [
            "toHasCamp" => STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE_ID,
            "toNoCamp" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
        ],
    ),
    STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE_ID => [
        "name" => STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE,
        "description" => clienttranslate('${actplayer} must choose a camp to upgrade to a settlement'),
        "descriptionmyturn" => clienttranslate('${you} must choose a camp to upgrade to a settlement'),
        "type" => "activeplayer",
        "args" => "argInvestmentCampToUpgrade",
        "possibleactions" => ["chooseCampToUpgrade", "cancelTurn"],
        "transitions" => [
            "next" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES_ID => array(
        "name" => STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES,
        "description" => '',
        "type" => "game",
        "action" => "stHasRemainFreeResources",
        "transitions" => [
            "toHasResources" => STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_ID,
            "toNoResources" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
        ],
    ),
    STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_ID => [
        "name" => STATE_INVESTMENT_CHOOSE_FREE_RESOURCE,
        "description" => clienttranslate('${actplayer} must choose a free resource'),
        "descriptionmyturn" => clienttranslate('${you} must choose a free resource'),
        "type" => "activeplayer",
        "args" => "argGenericOnlyGlobals",
        "possibleactions" => ["chooseFreeResource", "cancelTurn"],
        "transitions" => [
            "next" => STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK_ID => [
        "name" => STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK,
        "description" => clienttranslate('${actplayer} must choose a supply track to add ${resource_type_name}'),
        "descriptionmyturn" => clienttranslate('${you} must choose a supply track to add ${resource_type_name}'),
        "type" => "activeplayer",
        "args" => "argInvestmentChooseFreeResourceTrack",
        "possibleactions" => ["chooseFreeResourceTrack", "cancelTurn"],
        "transitions" => [
            "next" => STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],

    // States related to BoomTown Trading Post
    STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE_ID => [
        "name" => STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE,
        "description" => clienttranslate('${actplayer} must choose a resource'),
        "descriptionmyturn" => clienttranslate('${you} must choose a resource'),
        "type" => "activeplayer",
        "args" => "argGenericOnlyGlobals",
        "possibleactions" => [
            "chooseTradingPostResource",
            "cancelTurn",
        ],
        "transitions" => [
            "next" => STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],
    STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK_ID => [
        "name" => STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK,
        "description" => clienttranslate('${actplayer} must choose to keep the resource or add it in the supply track'),
        "descriptionmyturn" => clienttranslate('${you} must choose to keep the resource or add it in your supply track'),
        "type" => "activeplayer",
        "args" => "argGenericOnlyGlobals",
        "possibleactions" => [
            "tradingPostKeepResource",
            "tradingPostSupplyTrackAdd",
            "cancelTurn",
        ],
        "transitions" => [
            "next" => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID,
            "restartTurn" => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID,
            "restartTurnHasMetalToUse" => STATE_INFORM_AFTER_CANCEL_VIEW_ID,
        ],
    ],

    // End scoring
    STATE_END_SCORING_ID => array(
        "name" => STATE_END_SCORING,
        "description" => clienttranslate('Calculating final score'),
        "type" => "game",
        "action" => "stEndScoring",
        "transitions" => ["" => STATE_GAME_END_ID],
    ),
);

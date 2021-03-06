<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * goldwest implementation : © Guillaume Benny bennygui@gmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 */

// Game related

const GOLDWEST_COLORS = ['f07f16', '982fff', '0000ff', 'ffffff'];
const GOLDWEST_COLOR_NAMES = ['orange', 'purple', 'blue', 'white'];

const RESOURCE_TYPE_GOLD = 'GO';
const RESOURCE_TYPE_SILVER = 'SI';
const RESOURCE_TYPE_COPPER = 'CO';
const RESOURCE_TYPE_WOOD = 'WO';
const RESOURCE_TYPE_STONE = 'ST';

const RESOURCE_TYPE_GOLD_ID = 1;
const RESOURCE_TYPE_SILVER_ID = 2;
const RESOURCE_TYPE_COPPER_ID = 3;
const RESOURCE_TYPE_WOOD_ID = 4;
const RESOURCE_TYPE_STONE_ID = 5;

const RESOURCE_TYPES_TO_ID = [
    RESOURCE_TYPE_GOLD => RESOURCE_TYPE_GOLD_ID,
    RESOURCE_TYPE_SILVER => RESOURCE_TYPE_SILVER_ID,
    RESOURCE_TYPE_COPPER => RESOURCE_TYPE_COPPER_ID,
    RESOURCE_TYPE_WOOD => RESOURCE_TYPE_WOOD_ID,
    RESOURCE_TYPE_STONE => RESOURCE_TYPE_STONE_ID,
];
const RESOURCE_IDS_TO_TYPE = [
    RESOURCE_TYPE_GOLD_ID => RESOURCE_TYPE_GOLD,
    RESOURCE_TYPE_SILVER_ID => RESOURCE_TYPE_SILVER,
    RESOURCE_TYPE_COPPER_ID => RESOURCE_TYPE_COPPER,
    RESOURCE_TYPE_WOOD_ID => RESOURCE_TYPE_WOOD,
    RESOURCE_TYPE_STONE_ID => RESOURCE_TYPE_STONE,
];

const RESOURCE_TYPES_ALL = [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_WOOD, RESOURCE_TYPE_STONE];
const RESOURCE_TYPES_METALS = [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_GOLD];
const RESOURCE_TYPES_BUILD = [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_STONE];

const TERRAIN_TYPE_GOLD = 'GO';
const TERRAIN_TYPE_SILVER = 'SI';
const TERRAIN_TYPE_COPPER = 'CO';
const TERRAIN_TYPE_WOOD = 'WO';
const TERRAIN_TYPE_WATER = 'WA';

const TERRAIN_TYPE_GOLD_ID = 1;
const TERRAIN_TYPE_SILVER_ID = 2;
const TERRAIN_TYPE_COPPER_ID = 3;
const TERRAIN_TYPE_WOOD_ID = 4;

const TERRAIN_TYPES_BUILDABLE = [TERRAIN_TYPE_WOOD, TERRAIN_TYPE_GOLD, TERRAIN_TYPE_SILVER, TERRAIN_TYPE_COPPER];
const TERRAIN_TYPES_TO_ID = [
    TERRAIN_TYPE_GOLD => TERRAIN_TYPE_GOLD_ID,
    TERRAIN_TYPE_SILVER => TERRAIN_TYPE_SILVER_ID,
    TERRAIN_TYPE_COPPER => TERRAIN_TYPE_COPPER_ID,
    TERRAIN_TYPE_WOOD => TERRAIN_TYPE_WOOD_ID,
];
const TERRAIN_IDS_TO_TYPE = [
    TERRAIN_TYPE_GOLD_ID => TERRAIN_TYPE_GOLD,
    TERRAIN_TYPE_SILVER_ID => TERRAIN_TYPE_SILVER,
    TERRAIN_TYPE_COPPER_ID => TERRAIN_TYPE_COPPER,
    TERRAIN_TYPE_WOOD_ID => TERRAIN_TYPE_WOOD,
];

const INVESTMENTS_CARD_EFFECT_BASIC = "INVESTMENTS_CARD_EFFECT_BASIC";
const INVESTMENTS_CARD_EFFECT_INFLUENCE_SI = "INVESTMENTS_CARD_EFFECT_INFLUENCE_SI";
const INVESTMENTS_CARD_EFFECT_INFLUENCE_WO = "INVESTMENTS_CARD_EFFECT_INFLUENCE_WO";
const INVESTMENTS_CARD_EFFECT_INFLUENCE_CO = "INVESTMENTS_CARD_EFFECT_INFLUENCE_CO";
const INVESTMENTS_CARD_EFFECT_INFLUENCE_GO = "INVESTMENTS_CARD_EFFECT_INFLUENCE_GO";
const INVESTMENTS_CARD_EFFECT_FREE_SHIPPING_TRACK_1 = "INVESTMENTS_CARD_EFFECT_FREE_SHIPPING_TRACK_1";
const INVESTMENTS_CARD_EFFECT_FREE_SHIPPING_TRACK_2 = "INVESTMENTS_CARD_EFFECT_FREE_SHIPPING_TRACK_2";
const INVESTMENTS_CARD_EFFECT_FREE_UNOCCUPIED_BOOM_TOWN = "INVESTMENTS_CARD_EFFECT_FREE_UNOCCUPIED_BOOM_TOWN";
const INVESTMENTS_CARD_EFFECT_FREE_OCCUPIED_BOOM_TOWN = "INVESTMENTS_CARD_EFFECT_FREE_OCCUPIED_BOOM_TOWN";
const INVESTMENTS_CARD_EFFECT_MINING_TOKEN_LOOK_2 = "INVESTMENTS_CARD_EFFECT_MINING_TOKEN_LOOK_2";
const INVESTMENTS_CARD_EFFECT_TAKE_RESOURCE_2 = "INVESTMENTS_CARD_EFFECT_TAKE_RESOURCE_2";
const INVESTMENTS_CARD_EFFECT_UPGRADE_CAMP = "INVESTMENTS_CARD_EFFECT_UPGRADE_CAMP";

// Score related
const SCORE_POINTS_PER_BUILDING = 2;
const SCORE_INFLUENCE_BY_TERRAIN_TYPE = [
    TERRAIN_TYPE_WOOD => [8, 4],
    TERRAIN_TYPE_GOLD => [9, 5],
    TERRAIN_TYPE_SILVER => [10, 6],
    TERRAIN_TYPE_COPPER => [11, 7],
];

// State Globals

const STG_LAST_TURN_COUNT = "STG_LAST_TURN_COUNT";
const STG_CANCEL_ALLOWED = "STG_CANCEL_ALLOWED";
const STG_CANCEL_GOTO_STATE = "STG_CANCEL_GOTO_STATE";

const STG_NB_RESOURCES_ = "STG_NB_RESOURCES_";
const STG_NB_RESOURCES_GO = "STG_NB_RESOURCES_GO";
const STG_NB_RESOURCES_SI = "STG_NB_RESOURCES_SI";
const STG_NB_RESOURCES_CO = "STG_NB_RESOURCES_CO";
const STG_NB_RESOURCES_WO = "STG_NB_RESOURCES_WO";
const STG_NB_RESOURCES_ST = "STG_NB_RESOURCES_ST";

const STG_CHOSEN_SUPPLY_TRACK_SECTION = "STG_CHOSEN_SUPPLY_TRACK_SECTION";

const STG_CHOSEN_MINING_TOKEN_ID = "STG_CHOSEN_MINING_TOKEN_ID";
const STG_CHOSEN_MINING_TOKEN_TERRAIN_ID = "STG_CHOSEN_MINING_TOKEN_TERRAIN_ID";

const STG_USE_METAL_BOOMTOWN_USED = "STG_USE_METAL_BOOMTOWN_USED";
const STG_USE_METAL_INVESTMENT_USED = "STG_USE_METAL_INVESTMENT_USED";

// State Globals related to investment cards
const STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT = "STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT";
const STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT = "STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT";
const STG_INVESTMENT_FREE_RESOURCE_COUNT = "STG_INVESTMENT_FREE_RESOURCE_COUNT";
const STG_INVESTMENT_FREE_RESOURCE_ID = "STG_INVESTMENT_FREE_RESOURCE_ID";

// State Globals related to BoomTown Trading Post
const STG_TRADING_POST_RESOURCE_ID = "STG_TRADING_POST_RESOURCE_ID";

const STG_ALL = [
    STG_LAST_TURN_COUNT => STG_LAST_TURN_COUNT,
    STG_CANCEL_ALLOWED => STG_CANCEL_ALLOWED,
    STG_CANCEL_GOTO_STATE => STG_CANCEL_GOTO_STATE,
    STG_NB_RESOURCES_GO => STG_NB_RESOURCES_GO,
    STG_NB_RESOURCES_SI => STG_NB_RESOURCES_SI,
    STG_NB_RESOURCES_CO => STG_NB_RESOURCES_CO,
    STG_NB_RESOURCES_WO => STG_NB_RESOURCES_WO,
    STG_NB_RESOURCES_ST => STG_NB_RESOURCES_ST,
    STG_CHOSEN_SUPPLY_TRACK_SECTION => STG_CHOSEN_SUPPLY_TRACK_SECTION,
    STG_CHOSEN_MINING_TOKEN_ID => STG_CHOSEN_MINING_TOKEN_ID,
    STG_CHOSEN_MINING_TOKEN_TERRAIN_ID => STG_CHOSEN_MINING_TOKEN_TERRAIN_ID,
    STG_USE_METAL_BOOMTOWN_USED => STG_USE_METAL_BOOMTOWN_USED,
    STG_USE_METAL_INVESTMENT_USED => STG_USE_METAL_INVESTMENT_USED,
    STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT => STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT,
    STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT => STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT,
    STG_INVESTMENT_FREE_RESOURCE_COUNT => STG_INVESTMENT_FREE_RESOURCE_COUNT,
    STG_INVESTMENT_FREE_RESOURCE_ID => STG_INVESTMENT_FREE_RESOURCE_ID,
    STG_TRADING_POST_RESOURCE_ID => STG_TRADING_POST_RESOURCE_ID,
];

// States
const STATE_GAME_END = "gameEnd";
const STATE_GAME_END_ID = 99;

const STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE = "STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE";
const STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID = 100;

const STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE = "STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE";
const STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE_ID = 101;

const STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE = "STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE";
const STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE_ID = 102;

const STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE = "STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE";
const STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID = 103;

const STATE_CHOOSE_METAL_USE = "STATE_CHOOSE_METAL_USE";
const STATE_CHOOSE_METAL_USE_ID = 104;

const STATE_CHECK_IS_LAST_TURN = "STATE_CHECK_IS_LAST_TURN";
const STATE_CHECK_IS_LAST_TURN_ID = 105;

const STATE_HAS_WOOD_OR_STONE = "STATE_HAS_WOOD_OR_STONE";
const STATE_HAS_WOOD_OR_STONE_ID = 106;

const STATE_CHOOSE_TOKEN_LOOT = "STATE_CHOOSE_TOKEN_LOOT";
const STATE_CHOOSE_TOKEN_LOOT_ID = 107;
const STATE_CHOOSE_TOKEN_BUILD_CAMP = "STATE_CHOOSE_TOKEN_BUILD_CAMP";
const STATE_CHOOSE_TOKEN_BUILD_CAMP_ID = 108;
const STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT = "STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT";
const STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT_ID = 109;

const STATE_DISCARD_REMAINING_RESOURCES = "STATE_DISCARD_REMAINING_RESOURCES";
const STATE_DISCARD_REMAINING_RESOURCES_ID = 110;
const STATE_DISCARD_REMAINING_RESOURCES_LAST_TURN = "STATE_DISCARD_REMAINING_RESOURCES_LAST_TURN";
const STATE_DISCARD_REMAINING_RESOURCES_LAST_TURN_ID = 111;

const STATE_CHOOSE_SUPPLY_TRACK_TO_ADD = "STATE_CHOOSE_SUPPLY_TRACK_TO_ADD";
const STATE_CHOOSE_SUPPLY_TRACK_TO_ADD_ID = 112;
const STATE_CHOOSE_TO_CONFIRM_TURN = "STATE_CHOOSE_TO_CONFIRM_TURN";
const STATE_CHOOSE_TO_CONFIRM_TURN_ID = 113;
const STATE_NEXT_PLAYER_OR_GAME_END = "STATE_NEXT_PLAYER_OR_GAME_END";
const STATE_NEXT_PLAYER_OR_GAME_END_ID = 129;

const STATE_SWITCH_TO_NEXT_PLAYER = "STATE_SWITCH_TO_NEXT_PLAYER";
const STATE_SWITCH_TO_NEXT_PLAYER_ID = 114;

const STATE_START_OF_TURN_SETUP = "STATE_START_OF_TURN_SETUP";
const STATE_START_OF_TURN_SETUP_ID = 115;

const STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK = "STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK";
const STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK_ID = 116;
const STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK = "STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK";
const STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK_ID = 117;
const STATE_INVESTMENT_HAS_FREE_UNOCCUPIED_BOOMTOWN = "STATE_INVESTMENT_HAS_FREE_UNOCCUPIED_BOOMTOWN";
const STATE_INVESTMENT_HAS_FREE_UNOCCUPIED_BOOMTOWN_ID = 118;
const STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN = "STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN";
const STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN_ID = 119;
const STATE_INVESTMENT_HAS_FREE_OCCUPIED_BOOMTOWN = "STATE_INVESTMENT_HAS_FREE_OCCUPIED_BOOMTOWN";
const STATE_INVESTMENT_HAS_FREE_OCCUPIED_BOOMTOWN_ID = 120;
const STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN = "STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN";
const STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN_ID = 121;
const STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW = "STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW";
const STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW_ID = 122;
const STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW = "STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW";
const STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW_ID = 123;
const STATE_INVESTMENT_HAS_CAMP_TO_UPGRADE = "STATE_INVESTMENT_HAS_CAMP_TO_UPGRADE";
const STATE_INVESTMENT_HAS_CAMP_TO_UPGRADE_ID = 124;
const STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE = "STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE";
const STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE_ID = 125;
const STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES = "STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES";
const STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES_ID = 126;
const STATE_INVESTMENT_CHOOSE_FREE_RESOURCE = "STATE_INVESTMENT_CHOOSE_FREE_RESOURCE";
const STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_ID = 127;
const STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK = "STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK";
const STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK_ID = 128;

const STATE_END_SCORING = "STATE_START_OF_TURN_SETUP";
const STATE_END_SCORING_ID = 130;

const STATE_INFORM_AFTER_CANCEL_VIEW = "STATE_INFORM_AFTER_CANCEL_VIEW";
const STATE_INFORM_AFTER_CANCEL_VIEW_ID = 131;

const STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE = "STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE";
const STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE_ID = 132;
const STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK = "STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK";
const STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK_ID = 133;

const STATE_ALL = [
    STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE => STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE,
    STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE => STATE_HAS_SUPPLY_TRACK_RESOURCES_TO_LEAVE,
    STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE => STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE,
    STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE => STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE,
    STATE_CHOOSE_METAL_USE => STATE_CHOOSE_METAL_USE,
    STATE_CHECK_IS_LAST_TURN => STATE_CHECK_IS_LAST_TURN,
    STATE_HAS_WOOD_OR_STONE => STATE_HAS_WOOD_OR_STONE,
    STATE_CHOOSE_TOKEN_LOOT => STATE_CHOOSE_TOKEN_LOOT,
    STATE_CHOOSE_TOKEN_BUILD_CAMP => STATE_CHOOSE_TOKEN_BUILD_CAMP,
    STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT => STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT,
    STATE_DISCARD_REMAINING_RESOURCES => STATE_DISCARD_REMAINING_RESOURCES,
    STATE_CHOOSE_SUPPLY_TRACK_TO_ADD => STATE_CHOOSE_SUPPLY_TRACK_TO_ADD,
    STATE_CHOOSE_TO_CONFIRM_TURN => STATE_CHOOSE_TO_CONFIRM_TURN,
    STATE_NEXT_PLAYER_OR_GAME_END => STATE_NEXT_PLAYER_OR_GAME_END,
    STATE_SWITCH_TO_NEXT_PLAYER => STATE_SWITCH_TO_NEXT_PLAYER,
    STATE_START_OF_TURN_SETUP => STATE_START_OF_TURN_SETUP,
    STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK => STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK,
    STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK => STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK,
    STATE_INVESTMENT_HAS_FREE_UNOCCUPIED_BOOMTOWN => STATE_INVESTMENT_HAS_FREE_UNOCCUPIED_BOOMTOWN,
    STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN => STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN,
    STATE_INVESTMENT_HAS_FREE_OCCUPIED_BOOMTOWN => STATE_INVESTMENT_HAS_FREE_OCCUPIED_BOOMTOWN,
    STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN => STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN,
    STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW => STATE_INVESTMENT_HAS_REMAIN_MINING_TOKEN_TO_VIEW,
    STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW => STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW,
    STATE_INVESTMENT_HAS_CAMP_TO_UPGRADE => STATE_INVESTMENT_HAS_CAMP_TO_UPGRADE,
    STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE => STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE,
    STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES => STATE_INVESTMENT_HAS_REMAIN_FREE_RESOURCES,
    STATE_INVESTMENT_CHOOSE_FREE_RESOURCE => STATE_INVESTMENT_CHOOSE_FREE_RESOURCE,
    STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK => STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK,
    STATE_END_SCORING => STATE_END_SCORING,
    STATE_INFORM_AFTER_CANCEL_VIEW => STATE_INFORM_AFTER_CANCEL_VIEW,
    STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE => STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE,
    STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK => STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK,
];

// Notifications

const NTF_ACTIVATE_SUPPLY_TRACK = "NTF_ACTIVATE_SUPPLY_TRACK";
const NTF_LEAVE_IN_SUPPLY_TRACK = "NTF_LEAVE_IN_SUPPLY_TRACK";
const NTF_UPDATE_BOOMTOWN = "NTF_UPDATE_BOOMTOWN";
const NTF_UPDATE_SHIPPING_TRACK = "NTF_UPDATE_SHIPPING_TRACK";
const NTF_UPDATE_SHIPPING_BONUS = "NTF_UPDATE_SHIPPING_BONUS";
const NTF_UPDATE_SCORE = "NTF_UPDATE_SCORE";
const NTF_UPDATE_INVESTMENT_CARD = "NTF_UPDATE_INVESTMENT_CARD";
const NTF_UPDATE_INVESTMENT_CARD_BONUS = "NTF_UPDATE_INVESTMENT_CARD_BONUS";
const NTF_UPDATE_GET_MINING_TOKEN = "NTF_UPDATE_GET_MINING_TOKEN";
const NTF_UPDATE_ADD_RESOURCES_TO_TRACK = "NTF_UPDATE_ADD_RESOURCES_TO_TRACK";
const NTF_UPDATE_REVEAL_TOKEN = "NTF_UPDATE_REVEAL_TOKEN";
const NTF_UPDATE_INVESTMENT_INFLUENCE = "NTF_UPDATE_INVESTMENT_INFLUENCE";
const NTF_UPDATE_VIEW_MINING_TOKEN = "NTF_UPDATE_VIEW_MINING_TOKEN";
const NTF_UPDATE_UPGRADE_TO_SETTLEMENT = "NTF_UPDATE_UPGRADE_TO_SETTLEMENT";
const NTF_UPDATE_TERRAIN_HIGHLIGHT = "NTF_UPDATE_TERRAIN_HIGHLIGHT";
const NTF_UPDATE_SCORE_WANTED = "NTF_UPDATE_SCORE_WANTED";
const NTF_UPDATE_SCORE_INFLUENCE = "NTF_UPDATE_SCORE_INFLUENCE";
const NTF_UPDATE_SCORE_BOOMTOWN = "NTF_UPDATE_SCORE_BOOMTOWN";
const NTF_UPDATE_ADD_RESOURCE_TO_HAND = "NTF_UPDATE_ADD_RESOURCE_TO_HAND";
const NTF_ALL = [
    NTF_ACTIVATE_SUPPLY_TRACK => NTF_ACTIVATE_SUPPLY_TRACK,
    NTF_LEAVE_IN_SUPPLY_TRACK => NTF_LEAVE_IN_SUPPLY_TRACK,
    NTF_UPDATE_BOOMTOWN => NTF_UPDATE_BOOMTOWN,
    NTF_UPDATE_SHIPPING_TRACK => NTF_UPDATE_SHIPPING_TRACK,
    NTF_UPDATE_SHIPPING_BONUS => NTF_UPDATE_SHIPPING_BONUS,
    NTF_UPDATE_SCORE => NTF_UPDATE_SCORE,
    NTF_UPDATE_INVESTMENT_CARD => NTF_UPDATE_INVESTMENT_CARD,
    NTF_UPDATE_INVESTMENT_CARD_BONUS => NTF_UPDATE_INVESTMENT_CARD_BONUS,
    NTF_UPDATE_GET_MINING_TOKEN => NTF_UPDATE_GET_MINING_TOKEN,
    NTF_UPDATE_ADD_RESOURCES_TO_TRACK => NTF_UPDATE_ADD_RESOURCES_TO_TRACK,
    NTF_UPDATE_REVEAL_TOKEN => NTF_UPDATE_REVEAL_TOKEN,
    NTF_UPDATE_INVESTMENT_INFLUENCE => NTF_UPDATE_INVESTMENT_INFLUENCE,
    NTF_UPDATE_VIEW_MINING_TOKEN => NTF_UPDATE_VIEW_MINING_TOKEN,
    NTF_UPDATE_UPGRADE_TO_SETTLEMENT => NTF_UPDATE_UPGRADE_TO_SETTLEMENT,
    NTF_UPDATE_TERRAIN_HIGHLIGHT => NTF_UPDATE_TERRAIN_HIGHLIGHT,
    NTF_UPDATE_SCORE_WANTED => NTF_UPDATE_SCORE_WANTED,
    NTF_UPDATE_SCORE_INFLUENCE => NTF_UPDATE_SCORE_INFLUENCE,
    NTF_UPDATE_SCORE_BOOMTOWN => NTF_UPDATE_SCORE_BOOMTOWN,
    NTF_UPDATE_ADD_RESOURCE_TO_HAND => NTF_UPDATE_ADD_RESOURCE_TO_HAND,
];

// Game Statistics
const STATS_PLAYER_TURN_ORDER = 'STATS_PLAYER_TURN_ORDER';

const STATS_PLAYER_NB_GAINED_RESOURCE_TOTAL = 'STATS_PLAYER_NB_GAINED_RESOURCE_TOTAL';
const STATS_PLAYER_NB_GAINED_RESOURCE_ =      'STATS_PLAYER_NB_GAINED_RESOURCE_';
const STATS_PLAYER_NB_GAINED_RESOURCE_GO =    'STATS_PLAYER_NB_GAINED_RESOURCE_GO';
const STATS_PLAYER_NB_GAINED_RESOURCE_SI =    'STATS_PLAYER_NB_GAINED_RESOURCE_SI';
const STATS_PLAYER_NB_GAINED_RESOURCE_CO =    'STATS_PLAYER_NB_GAINED_RESOURCE_CO';
const STATS_PLAYER_NB_GAINED_RESOURCE_WO =    'STATS_PLAYER_NB_GAINED_RESOURCE_WO';
const STATS_PLAYER_NB_GAINED_RESOURCE_ST =    'STATS_PLAYER_NB_GAINED_RESOURCE_ST';

const STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_ = 'STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_';
const STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_0 = 'STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_0';
const STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_1 = 'STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_1';
const STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_2 = 'STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_2';
const STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_3 = 'STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_3';

const STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_ = 'STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_';
const STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_0 = 'STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_0';
const STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_1 = 'STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_1';
const STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_2 = 'STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_2';
const STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_3 = 'STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_3';

const STATS_PLAYER_SCORE_SHIPPING_TRACK = 'STATS_PLAYER_SCORE_SHIPPING_TRACK';
const STATS_PLAYER_SCORE_BOOMTOWN = 'STATS_PLAYER_SCORE_BOOMTOWN';
const STATS_PLAYER_SCORE_INVESTMENTS = 'STATS_PLAYER_SCORE_INVESTMENTS';
const STATS_PLAYER_SCORE_LARGEST_GROUP = 'STATS_PLAYER_SCORE_LARGEST_GROUP';
const STATS_PLAYER_SCORE_INFLUENCE = 'STATS_PLAYER_SCORE_INFLUENCE';
const STATS_PLAYER_SCORE_WANTED = 'STATS_PLAYER_SCORE_WANTED';
const STATS_PLAYER_SCORE_SUPPLY_TRACK = 'STATS_PLAYER_SCORE_SUPPLY_TRACK';

const STATS_PLAYER_SCORE_OFFICE_ = 'STATS_PLAYER_SCORE_OFFICE_';
const STATS_PLAYER_SCORE_OFFICE_0 = 'STATS_PLAYER_SCORE_OFFICE_0';
const STATS_PLAYER_SCORE_OFFICE_1 = 'STATS_PLAYER_SCORE_OFFICE_1';
const STATS_PLAYER_SCORE_OFFICE_2 = 'STATS_PLAYER_SCORE_OFFICE_2';
const STATS_PLAYER_SCORE_OFFICE_3 = 'STATS_PLAYER_SCORE_OFFICE_3';
const STATS_PLAYER_SCORE_OFFICE_4 = 'STATS_PLAYER_SCORE_OFFICE_4';
const STATS_PLAYER_SCORE_OFFICE_5 = 'STATS_PLAYER_SCORE_OFFICE_5';
const STATS_PLAYER_SCORE_OFFICE_6 = 'STATS_PLAYER_SCORE_OFFICE_6';
const STATS_PLAYER_SCORE_OFFICE_7 = 'STATS_PLAYER_SCORE_OFFICE_7';
const STATS_PLAYER_SCORE_OFFICE_8 = 'STATS_PLAYER_SCORE_OFFICE_8';
const STATS_PLAYER_SCORE_OFFICE_9 = 'STATS_PLAYER_SCORE_OFFICE_9';
const STATS_PLAYER_SCORE_OFFICE_10 = 'STATS_PLAYER_SCORE_OFFICE_10';
const STATS_PLAYER_SCORE_OFFICE_11 = 'STATS_PLAYER_SCORE_OFFICE_11';
const STATS_PLAYER_SCORE_OFFICE_12 = 'STATS_PLAYER_SCORE_OFFICE_12';
const STATS_PLAYER_SCORE_OFFICE_13 = 'STATS_PLAYER_SCORE_OFFICE_13';

const STATS_PLAYER_SHIPPING_DISTANCE_ = 'STATS_PLAYER_SHIPPING_DISTANCE_';
const STATS_PLAYER_SHIPPING_DISTANCE_GO = 'STATS_PLAYER_SHIPPING_DISTANCE_GO';
const STATS_PLAYER_SHIPPING_DISTANCE_SI = 'STATS_PLAYER_SHIPPING_DISTANCE_SI';
const STATS_PLAYER_SHIPPING_DISTANCE_CO = 'STATS_PLAYER_SHIPPING_DISTANCE_CO';

const STATS_PLAYER_NB_SHIPPING_BONUS = 'STATS_PLAYER_NB_SHIPPING_BONUS';
const STATS_PLAYER_NB_INVESTMENT_BONUS = 'STATS_PLAYER_NB_INVESTMENT_BONUS';
const STATS_PLAYER_NB_INVESTMENT = 'STATS_PLAYER_NB_INVESTMENT';
const STATS_PLAYER_NB_BOOMTOWN_INFLUENCE = 'STATS_PLAYER_NB_BOOMTOWN_INFLUENCE';

const STATS_PLAYER_NB_BUILT_CAMP = 'STATS_PLAYER_NB_BUILT_CAMP';
const STATS_PLAYER_NB_BUILT_SETTLEMENT = 'STATS_PLAYER_NB_BUILT_SETTLEMENT';
const STATS_PLAYER_NB_LOOT = 'STATS_PLAYER_NB_LOOT';

const STATS_PLAYER_INFLUENCE_ = 'STATS_PLAYER_INFLUENCE_';
const STATS_PLAYER_INFLUENCE_WO = 'STATS_PLAYER_INFLUENCE_WO';
const STATS_PLAYER_INFLUENCE_GO = 'STATS_PLAYER_INFLUENCE_GO';
const STATS_PLAYER_INFLUENCE_SI = 'STATS_PLAYER_INFLUENCE_SI';
const STATS_PLAYER_INFLUENCE_CO = 'STATS_PLAYER_INFLUENCE_CO';

// Game Options

const STG_GAME_OPTION_EXPANSION_BANDITS = 'STG_GAME_OPTION_EXPANSION_BANDITS';
const GAME_OPTION_EXPANSION_BANDITS = 100;
const GAME_OPTION_EXPANSION_BANDITS_VALUE_OFF = 0;
const GAME_OPTION_EXPANSION_BANDITS_VALUE_ON = 1;

const STG_GAME_OPTION_EXPANSION_TRADING_POST = 'STG_GAME_OPTION_EXPANSION_TRADING_POST';
const GAME_OPTION_EXPANSION_TRADING_POST = 101;
const GAME_OPTION_EXPANSION_TRADING_POST_VALUE_OFF = 0;
const GAME_OPTION_EXPANSION_TRADING_POST_VALUE_ONLY_EXPANSION = 1;
const GAME_OPTION_EXPANSION_TRADING_POST_VALUE_WITH_BASE = 2;

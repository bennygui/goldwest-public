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
 * goldwest.game.php
 *
 */


require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');
require_once("modules/GWGlobals.inc.php");
require_once("modules/GWBoard.class.php");
require_once("modules/GWBoomTown.class.php");
require_once("modules/GWInvestments.class.php");
require_once("modules/GWShippingTrack.class.php");
require_once("modules/GWPlayerBoard.class.php");

class goldwest extends Table
{
    public $resourceName;
    public $terrainName;
    public $board;
    public $boomtown;
    public $investments;
    public $shipping;
    public $playerBoard;

    function __construct()
    {
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();

        $this->initGameStateLabels([
            STG_LAST_TURN_COUNT => 10,
            STG_NB_RESOURCES_GO => 11,
            STG_NB_RESOURCES_SI => 12,
            STG_NB_RESOURCES_CO => 13,
            STG_NB_RESOURCES_WO => 14,
            STG_NB_RESOURCES_ST => 15,
            STG_CHOSEN_SUPPLY_TRACK_SECTION => 16,
            STG_CHOSEN_MINING_TOKEN_ID => 17,
            STG_CHOSEN_MINING_TOKEN_TERRAIN_ID => 18,
            STG_USE_METAL_BOOMTOWN_USED => 19,
            STG_USE_METAL_INVESTMENT_USED => 20,
            STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT => 21,
            STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT => 22,
            STG_CANCEL_ALLOWED => 23,
            STG_INVESTMENT_FREE_RESOURCE_COUNT => 24,
            STG_INVESTMENT_FREE_RESOURCE_ID => 25,
            STG_CANCEL_GOTO_STATE => 26,
            STG_TRADING_POST_RESOURCE_ID => 27,
            STG_GAME_OPTION_EXPANSION_BANDITS => GAME_OPTION_EXPANSION_BANDITS,
            STG_GAME_OPTION_EXPANSION_TRADING_POST => GAME_OPTION_EXPANSION_TRADING_POST,
        ]);

        $this->resourceName = [
            RESOURCE_TYPE_GOLD => clienttranslate("Gold"),
            RESOURCE_TYPE_SILVER => clienttranslate("Silver"),
            RESOURCE_TYPE_COPPER => clienttranslate("Copper"),
            RESOURCE_TYPE_WOOD => clienttranslate("Wood"),
            RESOURCE_TYPE_STONE => clienttranslate("Stone"),
        ];
        $this->terrainName = [
            TERRAIN_TYPE_GOLD => clienttranslate("Gold"),
            TERRAIN_TYPE_SILVER => clienttranslate("Silver"),
            TERRAIN_TYPE_COPPER => clienttranslate("Copper"),
            TERRAIN_TYPE_WOOD => clienttranslate("Wood"),
        ];

        $this->board = new GWBoard();
        $this->boomtown = new GWBoomTown();
        $this->investments = new GWInvestments(
            self::getNew("module.common.deck"),
            self::getNew("module.common.deck")
        );
        $this->shipping = new GWShippingTrack();
        $this->playerBoard = new GWPlayerBoard($this->board);
    }

    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "goldwest";
    }

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array())
    {
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];
        // Set colors for Gold West
        $goldwest_colors = GOLDWEST_COLORS;
        $goldwest_color_names = GOLDWEST_COLOR_NAMES;
        $default_colors = GOLDWEST_COLORS;

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, player_color_name) VALUES ";
        $values = array();
        foreach ($players as $player_id => $player) {
            $color = array_shift($goldwest_colors);
            $color_name = array_shift($goldwest_color_names);
            $values[] = "('" . $player_id . "','$color','" . $player['player_canal'] . "','" . addslashes($player['player_name']) . "','" . addslashes($player['player_avatar']) . "', '$color_name')";
        }
        $sql .= implode($values, ',');
        self::DbQuery($sql);
        self::reattributeColorsBasedOnPreferences($players, $default_colors);
        self::reloadPlayersBasicInfos();
        // If player colors changed, we need to change the color name to match
        foreach ($this->loadPlayersBasicInfos() as $playerId => $playerInfo) {
            $pos = array_search($playerInfo['player_color'], GOLDWEST_COLORS);
            if ($pos !== false) {
                $color_name = GOLDWEST_COLOR_NAMES[$pos];
                self::DbQuery("UPDATE player SET player_color_name = '${color_name}' WHERE player_id = $playerId");
            }
        }
        self::reloadPlayersBasicInfos();

        /************ Start the game initialization *****/

        // Init global values with their initial values
        self::setGameStateInitialValue(STG_LAST_TURN_COUNT, 0);
        self::setGameStateInitialValue(STG_CANCEL_ALLOWED, 1);
        self::setGameStateInitialValue(STG_NB_RESOURCES_GO, 0);
        self::setGameStateInitialValue(STG_NB_RESOURCES_SI, 0);
        self::setGameStateInitialValue(STG_NB_RESOURCES_CO, 0);
        self::setGameStateInitialValue(STG_NB_RESOURCES_WO, 0);
        self::setGameStateInitialValue(STG_NB_RESOURCES_ST, 0);
        self::setGameStateInitialValue(STG_CHOSEN_SUPPLY_TRACK_SECTION, -1);
        self::setGameStateInitialValue(STG_CHOSEN_MINING_TOKEN_ID, -1);
        self::setGameStateInitialValue(STG_CHOSEN_MINING_TOKEN_TERRAIN_ID, -1);
        self::setGameStateInitialValue(STG_USE_METAL_BOOMTOWN_USED, 0);
        self::setGameStateInitialValue(STG_USE_METAL_INVESTMENT_USED, 0);
        self::setGameStateInitialValue(STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT, 0);
        self::setGameStateInitialValue(STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT, 0);
        self::setGameStateInitialValue(STG_INVESTMENT_FREE_RESOURCE_COUNT, 0);
        self::setGameStateInitialValue(STG_INVESTMENT_FREE_RESOURCE_ID, 0);
        self::setGameStateInitialValue(STG_CANCEL_GOTO_STATE, STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID);
        self::setGameStateInitialValue(STG_TRADING_POST_RESOURCE_ID, -1);

        // Init game statistics
        self::initStat('player', STATS_PLAYER_TURN_ORDER, 0);
        self::initStat('player', STATS_PLAYER_NB_GAINED_RESOURCE_TOTAL, 0);
        self::initStat('player', STATS_PLAYER_NB_GAINED_RESOURCE_GO, 0);
        self::initStat('player', STATS_PLAYER_NB_GAINED_RESOURCE_SI, 0);
        self::initStat('player', STATS_PLAYER_NB_GAINED_RESOURCE_CO, 0);
        self::initStat('player', STATS_PLAYER_NB_GAINED_RESOURCE_WO, 0);
        self::initStat('player', STATS_PLAYER_NB_GAINED_RESOURCE_ST, 0);
        self::initStat('player', STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_0, 0);
        self::initStat('player', STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_1, 0);
        self::initStat('player', STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_2, 0);
        self::initStat('player', STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_3, 0);
        self::initStat('player', STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_0, 0);
        self::initStat('player', STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_1, 0);
        self::initStat('player', STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_2, 0);
        self::initStat('player', STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_3, 0);
        self::initStat('player', STATS_PLAYER_SCORE_SHIPPING_TRACK, 0);
        self::initStat('player', STATS_PLAYER_SCORE_BOOMTOWN, 0);
        self::initStat('player', STATS_PLAYER_SCORE_INVESTMENTS, 0);
        self::initStat('player', STATS_PLAYER_SCORE_LARGEST_GROUP, 0);
        self::initStat('player', STATS_PLAYER_SCORE_INFLUENCE, 0);
        self::initStat('player', STATS_PLAYER_SCORE_WANTED, 0);
        self::initStat('player', STATS_PLAYER_SCORE_SUPPLY_TRACK, 0);
        self::initStat('player', STATS_PLAYER_SHIPPING_DISTANCE_GO, 0);
        self::initStat('player', STATS_PLAYER_SHIPPING_DISTANCE_SI, 0);
        self::initStat('player', STATS_PLAYER_SHIPPING_DISTANCE_CO, 0);
        self::initStat('player', STATS_PLAYER_NB_SHIPPING_BONUS, 0);
        self::initStat('player', STATS_PLAYER_NB_INVESTMENT_BONUS, 0);
        self::initStat('player', STATS_PLAYER_NB_INVESTMENT, 0);
        self::initStat('player', STATS_PLAYER_NB_BOOMTOWN_INFLUENCE, 0);
        self::initStat('player', STATS_PLAYER_NB_BUILT_CAMP, 0);
        self::initStat('player', STATS_PLAYER_NB_BUILT_SETTLEMENT, 0);
        self::initStat('player', STATS_PLAYER_NB_LOOT, 0);
        self::initStat('player', STATS_PLAYER_INFLUENCE_WO, 0);
        self::initStat('player', STATS_PLAYER_INFLUENCE_GO, 0);
        self::initStat('player', STATS_PLAYER_INFLUENCE_SI, 0);
        self::initStat('player', STATS_PLAYER_INFLUENCE_CO, 0);

        $playerCount = count($players);
        $this->board->generate($playerCount);
        $useBanditsExpansion = (self::getGameStateValue(STG_GAME_OPTION_EXPANSION_BANDITS) == GAME_OPTION_EXPANSION_BANDITS_VALUE_ON);
        $tradingPostExpansion = self::getGameStateValue(STG_GAME_OPTION_EXPANSION_TRADING_POST);
        $this->boomtown->generate($useBanditsExpansion, $tradingPostExpansion);
        $this->investments->generate($playerCount);
        $this->shipping->generate(array_keys($players));
        $this->playerBoard->generate($this->loadPlayersBasicInfos());

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        // Set stats for player order
        foreach ($this->loadPlayersBasicInfos() as $playerId => $playerInfo) {
            self::setStat($playerInfo['player_no'], STATS_PLAYER_TURN_ORDER, $playerId);
        }

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array();

        $currentPlayerId = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!

        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score, player_color, player_color_name, player_no FROM player ";
        $result['players'] = self::getCollectionFromDb($sql);

        $result['constants']['STATE_ALL'] = STATE_ALL;
        $result['constants']['NTF_ALL'] = NTF_ALL;
        $result['constants']['STG_ALL'] = STG_ALL;
        $result['constants']['TERRAIN_IDS_TO_TYPE'] = TERRAIN_IDS_TO_TYPE;
        $result['constants']['RESOURCE_IDS_TO_TYPE'] = RESOURCE_IDS_TO_TYPE;

        $result['constants']['investments'] = $this->investments->getConstants();
        $result['constants']['shipping'] = $this->shipping->getConstants();
        $result['constants']['terrains']['buildable'] = TERRAIN_TYPES_BUILDABLE;
        $result['constants']['resources']['all'] = RESOURCE_TYPES_ALL;
        $result['constants']['resources']['metals'] = RESOURCE_TYPES_METALS;
        $result['constants']['resources']['build'] = RESOURCE_TYPES_BUILD;
        $result['constants']['playerboard'] = $this->playerBoard->getConstants($this->getPlayersNumber());
        $result['constants']['boomtown'] = $this->boomtown->getConstants();

        $result['board']['invisible'] = $this->board->getInvisibleTokenClient();
        $result['board']['visible'] = $this->board->getVisibleTokenClient();
        $result['board']['camp'] = $this->board->getCampClient();
        $result['board']['settlement'] = $this->board->getSettlementClient();
        $result['investments']['cards'] = $this->investments->getCardsOnTable();
        $result['investments']['bonus'] = $this->investments->getBonusOnTable();
        $result['investments']['playerCards'] = $this->investments->getCardsByPlayers();
        $result['investments']['playerBonus'] = $this->investments->getBonusByPlayers();
        $result['shipping']['distances'] = $this->shipping->getPlayersDistance();
        $result['shipping']['bonus'] = $this->shipping->getBonus();
        $result['boomtown']['town'] = $this->boomtown->getTown();

        $result['playerboard']['supplytrack'] = $this->playerBoard->getSupplyTrack();
        $result['playerboard']['playerinfo'] = $this->board->getBoardPlayerInfoClient(array_keys($this->loadPlayersBasicInfos()), $this->investments);
        $result['playerboard']['viewedMiningToken'] = $this->board->getViewedMiningTokenClient($currentPlayerId);

        $result['scores'] = [];
        foreach (self::getCollectionFromDb("SELECT player_id, player_score FROM player") as $row) {
            $result['scores'][$row['player_id']] = $row['player_score'];
        }

        $result['STG'] = $this->getAllStateGlobals();

        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        $playerBasicInfo = $this->loadPlayersBasicInfos();
        $playerCount = count($playerBasicInfo);
        $playerInfoArray = $this->board->getBoardPlayerInfo(array_keys($playerBasicInfo), $this->investments);
        $playerZombieTurnCount = $this->getAllPlayersZombieTurnCount();
        $maxCamp = GWPlayerBoard::CAMP_COUNT_PER_PLAYER_COUNT[$playerCount];
        $maximumProgress =
            $playerCount // Last turn
            + $maxCamp * $playerCount;
        $progress = self::getGameStateValue(STG_LAST_TURN_COUNT);
        foreach ($playerInfoArray as $playerId => $info) {
            $progress += $info->usedCamp() + $playerZombieTurnCount[$playerId];
            // Zombie players will not take last turn
            if ($playerZombieTurnCount[$playerId] > 0) {
                $maximumProgress -= 1;
            }
        }

        if ($maximumProgress == 0) {
            return 0;
        } else {
            return min((int)($progress * 100 / $maximumProgress), 100);
        }
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Utility functions
    ////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */
    public function currentPlayerId()
    {
        return self::getCurrentPlayerId();
    }

    public function getAllStateGlobals()
    {
        $values = [];
        foreach (STG_ALL as $stg) {
            $values[$stg] = self::getGameStateValue($stg);
        }
        $values['playerinfo'] = $this->board->getBoardPlayerInfoClient(array_keys($this->loadPlayersBasicInfos()), $this->investments);
        return $values;
    }

    public function mergeAllStateGlobals($array)
    {
        return array_merge($array, $this->getAllStateGlobals());
    }

    public function stateGlobalsToResources()
    {
        $resources = [];
        foreach (RESOURCE_TYPES_ALL as $resourceType) {
            $resources[$resourceType] = self::getGameStateValue(STG_NB_RESOURCES_ . $resourceType);
        }
        return $resources;
    }

    public function addToPlayerScore($playerId, $score, $statsToUpdate)
    {
        self::incStat($score, $statsToUpdate, $playerId);
        self::DbQuery("UPDATE player SET player_score=player_score + $score WHERE player_id = $playerId");
        return self::getUniqueValueFromDB("SELECT player_score FROM player WHERE player_id = $playerId");
    }

    public function substractFromPlayerScore($playerId, $score, $statsToUpdate)
    {
        self::incStat($score * -1, $statsToUpdate, $playerId);
        self::DbQuery("UPDATE player SET player_score=player_score - $score WHERE player_id = $playerId");
        return self::getUniqueValueFromDB("SELECT player_score FROM player WHERE player_id = $playerId");
    }

    public function incPlayerZombieTurnCount($playerId)
    {
        self::DbQuery("UPDATE player SET zombie_turn_count = zombie_turn_count + 1 WHERE player_id = $playerId");
    }

    public function getAllPlayersZombieTurnCount()
    {
        $playerZombieTurn = [];
        $sql = "SELECT player_id, zombie_turn_count FROM player";
        foreach (self::getCollectionFromDb($sql) as $row) {
            $playerZombieTurn[$row['player_id']] = $row['zombie_turn_count'];
        }
        return $playerZombieTurn;
    }

    public function tradingPostSetResourceAndGetNextState($officeId, $defaultNextState)
    {
        switch ($officeId) {
            case OFFICE_ID_RESOURCE_COPPER:
                self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, RESOURCE_TYPE_COPPER_ID);
                return "toUsedBoomTownTradingPostMetal";
            case OFFICE_ID_RESOURCE_SILVER:
                self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, RESOURCE_TYPE_SILVER_ID);
                return "toUsedBoomTownTradingPostMetal";
            case OFFICE_ID_RESOURCE_GOLD:
                self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, RESOURCE_TYPE_GOLD_ID);
                return "toUsedBoomTownTradingPostMetal";
            case OFFICE_ID_RESOURCE_WOOD_OR_STONE:
                self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, -1);
                return "toUsedBoomTownTradingPostWoodOrStone";
        }
        return $defaultNextState;
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in goldwest.action.php)
    */

    public function chooseSupplyTrackToActivate($section)
    {
        $this->checkAction("chooseSupplyTrackToActivate");
        $playerId = $this->getActivePlayerId();
        $supplyTrack = $this->playerBoard->getFilledSupplyTrack($playerId, $section);
        if (count($supplyTrack) == 0)
            throw new BgaUserException(sprintf(self::_('You cannot choose supply track %s'), $section));
        self::setGameStateValue(STG_CHOSEN_SUPPLY_TRACK_SECTION, $section);
        self::incStat(1, STATS_PLAYER_NB_ACTIVATE_SUPPLY_TRACK_ . $section, $playerId);
        foreach ($supplyTrack as $resourceType => $track) {
            self::setGameStateValue(STG_NB_RESOURCES_ . $resourceType, $track->resourceCount);
            $track->resourceCount = 0;
        }
        $this->playerBoard->save();
        $this->notifyAllPlayers(
            NTF_ACTIVATE_SUPPLY_TRACK,
            clienttranslate('${player_name} activates supply track ${section}'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'section' => $section,
            ])
        );
        $this->gamestate->nextState('toLeaveResource');
    }

    public function chooseSupplyTrackResourceToLeave($resourceType)
    {
        $this->checkAction("chooseSupplyTrackResourceToLeave");
        // One less activated resource
        $nbResource = self::getGameStateValue(STG_NB_RESOURCES_ . $resourceType);
        if ($nbResource < 1)
            throw new BgaUserException(self::_('You do not have enough resource'));
        self::setGameStateValue(STG_NB_RESOURCES_ . $resourceType, $nbResource - 1);
        // Add resource in supply track
        $playerId = $this->getActivePlayerId();
        $section = self::getGameStateValue(STG_CHOSEN_SUPPLY_TRACK_SECTION);
        $supplyTrack = $this->playerBoard->getPlayerSupplyTrack($playerId, $section);
        foreach ($supplyTrack as $trackResourceType => $track) {
            if ($trackResourceType == $resourceType) {
                $track->resourceCount += 1;
                break;
            }
        }
        $this->playerBoard->save();

        $this->notifyAllPlayers(
            NTF_LEAVE_IN_SUPPLY_TRACK,
            clienttranslate('${player_name} leaves ${resource_name} in supply track ${section}'),
            $this->mergeAllStateGlobals([
                'i18n' => ['resource_name'],
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'resource_name' => $this->resourceName[$resourceType],
                'section' => $section,
                'resourceType' => $resourceType,
            ])
        );
        $this->gamestate->nextState("next");
    }

    public function chooseBoomTown($x, $y, $freeBownTown)
    {
        if ($freeBownTown) {
            $this->checkAction("chooseFreeBoomTown");
        } else {
            $this->checkAction("chooseBoomTown");
        }
        if ($x >= GWBoomTown::TOWN_SIZE || $y >= GWBoomTown::TOWN_SIZE)
            throw new BgaUserException(self::_('You cannot choose this office'));

        if (!$freeBownTown) {
            if (self::getGameStateValue(STG_USE_METAL_BOOMTOWN_USED) != 0)
                throw new BgaUserException(self::_('You cannot choose BoomTown more than once per turn'));
            self::setGameStateValue(STG_USE_METAL_BOOMTOWN_USED, 1);

            $resourceCost = $this->boomtown->getPriceAtPosition($x, $y);
            foreach ($resourceCost as $resourceType) {
                self::incGameStateValue(STG_NB_RESOURCES_ . $resourceType, -1);
                if (self::getGameStateValue(STG_NB_RESOURCES_ . $resourceType) < 0)
                    throw new BgaUserException(self::_('You do not have enough resources to choose this office'));
            }
        }

        $tile = $this->boomtown->getAtPosition($x, $y);
        if ($tile->playerId !== null)
            throw new BgaUserException(self::_('This office is aleady taken'));
        $playerId = $this->getActivePlayerId();
        $tile->playerId = $playerId;
        $this->boomtown->save();

        self::incStat(1, STATS_PLAYER_NB_BOOMTOWN_INFLUENCE, $playerId);

        $this->notifyAllPlayers(
            NTF_UPDATE_BOOMTOWN,
            clienttranslate('${player_name} places an office in BoomTown'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'x' => $x,
                'y' => $y,
            ])
        );

        $tileScore = $tile->buildTileScore();
        $newScore = null;
        if ($tileScore != 0) {
            $newScore = $this->addToPlayerScore($playerId, $tileScore, STATS_PLAYER_SCORE_BOOMTOWN);
            $this->notifyAllPlayers(
                NTF_UPDATE_SCORE,
                clienttranslate('${player_name} gains ${score} points by placing an office in BoomTown'),
                $this->mergeAllStateGlobals([
                    'player_id' => $playerId,
                    'player_name' => $this->getActivePlayerName(),
                    'score' => $tileScore,
                    'totalScore' => $newScore,
                ])
            );
        }

        $nextState = "next";
        if ($freeBownTown) {
            $nextState = "next";
        } else {
            $nextState = "toUsedBoomTown";
        }
        $nextState = $this->tradingPostSetResourceAndGetNextState($tile->officeId, $nextState);
        $this->gamestate->nextState($nextState);
    }

    public function chooseFreeOccupiedBoomTown($x, $y)
    {
        $this->checkAction("chooseFreeOccupiedBoomTown");
        if ($x >= GWBoomTown::TOWN_SIZE || $y >= GWBoomTown::TOWN_SIZE)
            throw new BgaUserException(self::_('You cannot choose this office'));

        $playerId = $this->getActivePlayerId();
        $tile = $this->boomtown->getAtPosition($x, $y);
        if ($tile->playerId === null || $tile->playerId == $playerId)
            throw new BgaUserException(self::_('You must choose an office owned by another player'));
        if ($tile->investmentPlayerId !== null)
            throw new BgaUserException(self::_('This office is aleady taken'));
        $tile->investmentPlayerId = $playerId;
        $this->boomtown->save();

        self::incStat(1, STATS_PLAYER_NB_BOOMTOWN_INFLUENCE, $playerId);

        $this->notifyAllPlayers(
            NTF_UPDATE_BOOMTOWN,
            clienttranslate('${player_name} places an office in BoomTown'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'x' => $x,
                'y' => $y,
            ])
        );

        $tileScore = $tile->buildTileScore();
        $newScore = null;
        if ($tileScore != 0) {
            $newScore = $this->addToPlayerScore($playerId, $tileScore, STATS_PLAYER_SCORE_BOOMTOWN);
            $this->notifyAllPlayers(
                NTF_UPDATE_SCORE,
                clienttranslate('${player_name} gains ${score} points by placing an office in BoomTown'),
                $this->mergeAllStateGlobals([
                    'player_id' => $playerId,
                    'player_name' => $this->getActivePlayerName(),
                    'score' => $tileScore,
                    'totalScore' => $newScore,
                ])
            );
        }

        $nextState = "next";
        $nextState = $this->tradingPostSetResourceAndGetNextState($tile->officeId, $nextState);
        $this->gamestate->nextState($nextState);
    }

    public function chooseInvestment($cardType)
    {
        if (self::getGameStateValue(STG_USE_METAL_INVESTMENT_USED) != 0)
            throw new BgaUserException(self::_('You cannot buy an investment more than once per turn'));
        self::setGameStateValue(STG_USE_METAL_INVESTMENT_USED, 1);

        $playerBasicInfo = $this->loadPlayersBasicInfos();
        $playerId = $this->getActivePlayerId();
        // Get player info before modifications
        $playerInfo = $this->board->getBoardPlayerInfo(array_keys($playerBasicInfo), $this->investments)[$playerId];

        $resources = $this->stateGlobalsToResources();
        $investmentCards = $this->investments->canBuyCards($resources);
        if (!array_search($cardType, $investmentCards) === false)
            throw new BgaUserException(self::_('You do not have enough resources to buy this investment'));
        $resourceCost = $this->investments->getPriceForCard($cardType);
        foreach ($resourceCost as $resourceType) {
            self::incGameStateValue(STG_NB_RESOURCES_ . $resourceType, -1);
            // This should not happen since "canBuyCards" already checked this
            if (self::getGameStateValue(STG_NB_RESOURCES_ . $resourceType) < 0)
                throw new BgaUserException(self::_('You do not have enough resources to buy this investment'));
        }
        $bonus = $this->investments->giveCardToPlayer($cardType, $playerId);
        $cardScore = $this->investments->getCardScore($cardType);

        self::incStat(1, STATS_PLAYER_NB_INVESTMENT, $playerId);

        $this->notifyAllPlayers(
            NTF_UPDATE_INVESTMENT_CARD,
            clienttranslate('${player_name} buys an investment'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'card' => $cardType,
            ])
        );
        $newScore = $this->addToPlayerScore($playerId, $cardScore, STATS_PLAYER_SCORE_INVESTMENTS);
        $this->notifyAllPlayers(
            NTF_UPDATE_SCORE,
            clienttranslate('${player_name} gains ${score} points by buying an investment'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'score' => $cardScore,
                'totalScore' => $newScore,
            ])
        );
        if ($bonus !== null) {
            self::incStat(1, STATS_PLAYER_NB_INVESTMENT_BONUS, $playerId);
            $this->notifyAllPlayers(
                NTF_UPDATE_INVESTMENT_CARD_BONUS,
                clienttranslate('${player_name} receives a bonus for their investment'),
                $this->mergeAllStateGlobals([
                    'player_id' => $playerId,
                    'player_name' => $this->getActivePlayerName(),
                    'bonus' => $bonus,
                ])
            );
            $newScore = $this->addToPlayerScore($playerId, $bonus, STATS_PLAYER_SCORE_INVESTMENTS);
            $this->notifyAllPlayers(
                NTF_UPDATE_SCORE,
                clienttranslate('${player_name} gains ${score} points with the investment bonus'),
                $this->mergeAllStateGlobals([
                    'player_id' => $playerId,
                    'player_name' => $this->getActivePlayerName(),
                    'score' => $bonus,
                    'totalScore' => $newScore,
                ])
            );
        }

        $nextState = "toUsedBasicInvestment";
        $cardEffect = $this->investments->getCardEffect($cardType);
        switch ($cardEffect) {
            case INVESTMENTS_CARD_EFFECT_BASIC:
                // Nothing more to do
                break;
            case INVESTMENTS_CARD_EFFECT_INFLUENCE_SI:
            case INVESTMENTS_CARD_EFFECT_INFLUENCE_WO:
            case INVESTMENTS_CARD_EFFECT_INFLUENCE_CO:
            case INVESTMENTS_CARD_EFFECT_INFLUENCE_GO:
                $terrainType = null;
                if ($cardEffect == INVESTMENTS_CARD_EFFECT_INFLUENCE_SI)
                    $terrainType = TERRAIN_TYPE_SILVER;
                elseif ($cardEffect == INVESTMENTS_CARD_EFFECT_INFLUENCE_WO)
                    $terrainType = TERRAIN_TYPE_WOOD;
                elseif ($cardEffect == INVESTMENTS_CARD_EFFECT_INFLUENCE_CO)
                    $terrainType = TERRAIN_TYPE_COPPER;
                elseif ($cardEffect == INVESTMENTS_CARD_EFFECT_INFLUENCE_GO)
                    $terrainType = TERRAIN_TYPE_GOLD;
                else
                    throw new BgaUserException(self::_('Invalid investment card terrain type'));
                $this->notifyAllPlayers(
                    NTF_UPDATE_INVESTMENT_INFLUENCE,
                    clienttranslate('${player_name} advances on influence track ${terrain_type_name}'),
                    $this->mergeAllStateGlobals([
                        'i18n' => ['terrain_type_name'],
                        'player_id' => $playerId,
                        'player_name' => $this->getActivePlayerName(),
                        'terrainType' => $terrainType,
                        'terrain_type_name' => $this->terrainName[$terrainType],
                        'influenceBeforeDistance' => $playerInfo->totalInfluence($terrainType),
                    ])
                );
                break;
            case INVESTMENTS_CARD_EFFECT_FREE_SHIPPING_TRACK_1:
                self::setGameStateValue(STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT, 1);
                $nextState = "toUsedInvestmentFreeShippingTrack";
                break;
            case INVESTMENTS_CARD_EFFECT_FREE_SHIPPING_TRACK_2:
                self::setGameStateValue(STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT, 2);
                $nextState = "toUsedInvestmentFreeShippingTrack";
                break;
            case INVESTMENTS_CARD_EFFECT_FREE_UNOCCUPIED_BOOM_TOWN:
                $nextState = "toUsedInvestmentFreeUnoccupiedBoomTown";
                break;
            case INVESTMENTS_CARD_EFFECT_FREE_OCCUPIED_BOOM_TOWN:
                $nextState = "toUsedInvestmentFreeOccupiedBoomTown";
                break;
            case INVESTMENTS_CARD_EFFECT_MINING_TOKEN_LOOK_2:
                self::setGameStateValue(STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT, 2);
                $nextState = "toUsedInvestmentMiningTokenToView";
                break;
            case INVESTMENTS_CARD_EFFECT_TAKE_RESOURCE_2:
                self::setGameStateValue(STG_INVESTMENT_FREE_RESOURCE_COUNT, 2);
                $nextState = "toUsedInvestmentNewResources";
                break;
            case INVESTMENTS_CARD_EFFECT_UPGRADE_CAMP:
                $nextState = "toUsedInvestmentCampUpgrade";
                break;
            default:
                throw new BgaUserException(self::_('Investment card has an invalid effect'));
                break;
        }

        $this->gamestate->nextState($nextState);
    }

    public function chooseShippingTrack($resourceType, $freeShipping)
    {
        if ($freeShipping) {
            $this->checkAction("chooseFreeShippingTrack");
        } else {
            $this->checkAction("chooseShippingTrack");
        }
        $playerId = $this->getActivePlayerId();
        if ($freeShipping) {
            $resources = [
                RESOURCE_TYPE_GOLD => 1,
                RESOURCE_TYPE_SILVER => 1,
                RESOURCE_TYPE_COPPER => 1,
            ];
        } else {
            $resources = $this->stateGlobalsToResources();
        }
        $trackPositions = $this->shipping->canShip($playerId, $resources);
        if (!array_key_exists($resourceType, $trackPositions))
            throw new BgaUserException(self::_('You cannot advance on this track'));
        if ($freeShipping) {
            // This should never happen since state STATE_INVESTMENT_HAS_FREE_SHIPPING_TRACK should check this
            if (self::getGameStateValue(STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT) <= 0)
                throw new BgaUserException(self::_('You cannot advance on this track for free'));
            self::incGameStateValue(STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT, -1);
        } else {
            self::incGameStateValue(STG_NB_RESOURCES_ . $resourceType, -1);
            // This should never happen since "canShip" should check this
            if (self::getGameStateValue(STG_NB_RESOURCES_ . $resourceType) < 0)
                throw new BgaUserException(self::_('You do not have enough resources to advance on this track'));
        }
        $bonus = $this->shipping->advanceTrackAndSave($playerId, $resourceType);
        // This should never happen since "canShip" should check this
        if ($bonus === null)
            throw new BgaUserException(self::_('You cannot advance on this track'));

        self::incStat(1, STATS_PLAYER_SHIPPING_DISTANCE_ . $resourceType, $playerId);

        $newScore = null;
        if ($bonus->score != 0) {
            $newScore = $this->addToPlayerScore($playerId, $bonus->score, STATS_PLAYER_SCORE_SHIPPING_TRACK);
        }

        $this->notifyAllPlayers(
            NTF_UPDATE_SHIPPING_TRACK,
            clienttranslate('${player_name} advances on ${resource_type_name} shipping track'),
            $this->mergeAllStateGlobals([
                'i18n' => ['resource_type_name'],
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'resourceType' => $resourceType,
                'resource_type_name' => $this->resourceName[$resourceType],
                'newDistance' => $bonus->newDistance,
            ])
        );
        if ($bonus->bonus !== null) {
            self::incStat(1, STATS_PLAYER_NB_SHIPPING_BONUS, $playerId);
            $this->notifyAllPlayers(
                NTF_UPDATE_SHIPPING_BONUS,
                clienttranslate('${player_name} gains a bonus token by advancing on the shipping track'),
                $this->mergeAllStateGlobals([
                    'player_id' => $playerId,
                    'player_name' => $this->getActivePlayerName(),
                    'resourceType' => $resourceType,
                    'bonusLevel' => $bonus->bonus->level,
                    'bonusPos' => $bonus->bonus->pos,
                ])
            );
        }
        if ($bonus->score != 0) {
            $this->notifyAllPlayers(
                NTF_UPDATE_SCORE,
                clienttranslate('${player_name} gains ${score} points by advancing on the shipping track'),
                $this->mergeAllStateGlobals([
                    'player_id' => $playerId,
                    'player_name' => $this->getActivePlayerName(),
                    'score' => $bonus->score,
                    'totalScore' => $newScore,
                ])
            );
        }

        if ($freeShipping) {
            $this->gamestate->nextState("next");
        } else {
            $this->gamestate->nextState("toUsedShippingTrack");
        }
    }

    public function chooseMiningToken($tokenId)
    {
        $this->checkAction("chooseMiningToken");
        $coord = GWBoard::idToCoord($tokenId);
        if (!is_array($coord) || count($coord) != 2)
            throw new BgaUserException(self::_('Invalid mining token position'));
        $x = $coord[0];
        $y = $coord[1];
        if (!$this->board->canTokenBeTaken($x, $y))
            throw new BgaUserException(self::_('You cannot take this mining token'));

        $playerBasicInfo = $this->loadPlayersBasicInfos();
        $playerId = $this->getActivePlayerId();
        // Get player info before modifications
        $playerInfo = $this->board->getBoardPlayerInfo(array_keys($playerBasicInfo), $this->investments)[$playerId];

        $wood = self::getGameStateValue(STG_NB_RESOURCES_WO);
        $stone = self::getGameStateValue(STG_NB_RESOURCES_ST);

        $newSpotStatus = null;
        $notifText = null;
        $newScore = null;
        if ($wood >= 1 && $stone >= 1) {
            $newSpotStatus = SPOT_STATUS_SETTLEMENT;
            $notifText = clienttranslate('${player_name} takes a mining token and builds a settlement');
        } else if ($wood == 0 && $stone == 0) {
            $newSpotStatus = SPOT_STATUS_LOOT;
            $notifText = clienttranslate('${player_name} loots a mining token');
            $newScore = $this->substractFromPlayerScore($playerId, 1, STATS_PLAYER_SCORE_WANTED);
        } else {
            $newSpotStatus = SPOT_STATUS_CAMP;
            $notifText = clienttranslate('${player_name} takes a mining token and builds a camp');
        }
        $token = $this->board->giveTokenToPlayer($x, $y, $playerId, $newSpotStatus);
        self::setGameStateValue(STG_CHOSEN_MINING_TOKEN_ID, $token->miningTokenId);
        self::setGameStateValue(STG_CHOSEN_MINING_TOKEN_TERRAIN_ID, $token->miningTokenTerrainId());

        $this->notifyAllPlayers(
            NTF_UPDATE_GET_MINING_TOKEN,
            $notifText,
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'tokenPositionId' => $tokenId,
                'isLoot' => ($newSpotStatus == SPOT_STATUS_LOOT),
                'isSettlement' => ($newSpotStatus == SPOT_STATUS_SETTLEMENT),
                'terrainType' => $token->terrainType,
                'influenceBeforeDistance' => $playerInfo->totalInfluence($token->terrainType),
                'campBefore' => $playerInfo->usedCamp(),
                'settlementBefore' => $playerInfo->usedSettlement(),
            ])
        );
        if ($newScore !== null) {
            $this->notifyAllPlayers(
                NTF_UPDATE_SCORE,
                clienttranslate('${player_name} loses 1 points by looting'),
                $this->mergeAllStateGlobals([
                    'player_id' => $playerId,
                    'player_name' => $this->getActivePlayerName(),
                    'totalScore' => $newScore,
                ])
            );
        }
        $this->gamestate->nextState("next");
    }

    public function chooseMiningTokenToView($tokenId)
    {
        $this->checkAction("chooseMiningTokenToView");
        $coord = GWBoard::idToCoord($tokenId);
        if (!is_array($coord) || count($coord) != 2)
            throw new BgaUserException(self::_('Invalid mining token position'));
        $x = $coord[0];
        $y = $coord[1];

        if (!$this->board->canTokenBeViewed($x, $y))
            throw new BgaUserException(self::_('You cannot view this mining token'));

        self::incGameStateValue(STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT, -1);
        self::setGameStateValue(STG_CANCEL_ALLOWED, 0);
        self::setGameStateValue(STG_CANCEL_GOTO_STATE, STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID);
        $playerId = $this->getActivePlayerId();
        $token = $this->board->setTokenViewedByPlayer($x, $y, $playerId);

        $this->notifyAllPlayers(
            NTF_UPDATE_SCORE,
            clienttranslate('${player_name} secretly looks at a mining token'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
            ])
        );

        $this->notifyPlayer(
            $playerId,
            NTF_UPDATE_VIEW_MINING_TOKEN,
            '',
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'tokenPositionId' => $tokenId,
                'miningTokenId' =>  $token->miningTokenId,
                'terrainType' => $token->terrainType,
            ])
        );

        $this->gamestate->nextState("toViewedToken");
    }

    public function chooseCampToUpgrade($tokenId)
    {
        $this->checkAction("chooseCampToUpgrade");
        $coord = GWBoard::idToCoord($tokenId);
        if (!is_array($coord) || count($coord) != 2)
            throw new BgaUserException(self::_('Invalid camp position'));

        $playerId = $this->getActivePlayerId();
        if (array_search($tokenId, $this->board->getCampByPlayerId($playerId)) === false)
            throw new BgaUserException(self::_('You must select one of your camp to upgrade'));

        $playerBasicInfo = $this->loadPlayersBasicInfos();
        // Get player info before modifications
        $playerInfo = $this->board->getBoardPlayerInfo(array_keys($playerBasicInfo), $this->investments)[$playerId];

        $token = $this->board->upgradeToSettlement($tokenId);

        $this->notifyAllPlayers(
            NTF_UPDATE_UPGRADE_TO_SETTLEMENT,
            clienttranslate('${player_name} upgrades a camp to a settlement with an investment card'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'tokenPositionId' => $tokenId,
                'terrainType' => $token->terrainType,
                'influenceBeforeDistance' => $playerInfo->totalInfluence($token->terrainType),
                'settlementBefore' => $playerInfo->usedSettlement(),
            ])
        );

        $this->gamestate->nextState("next");
    }

    public function chooseSupplyTrackToAdd($section)
    {
        $this->checkAction("chooseSupplyTrackToAdd");
        $playerId = $this->getActivePlayerId();
        if (array_search($section, GWPlayerBoard::SUPPLY_TRACK_SECTIONS) === false)
            throw new BgaUserException(self::_('Invalid supply track section'));
        $miningTokenId = self::getGameStateValue(STG_CHOSEN_MINING_TOKEN_ID);
        $terrainType = TERRAIN_IDS_TO_TYPE[self::getGameStateValue(STG_CHOSEN_MINING_TOKEN_TERRAIN_ID)];
        $resources = MINING_TOKEN_RESOURCES[$terrainType][$miningTokenId];
        $this->playerBoard->addResourcesToSupplyTrack($playerId, $section, $resources);

        self::incStat(1, STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_ . $section, $playerId);
        foreach ($resources as $resourceType) {
            self::incStat(1, STATS_PLAYER_NB_GAINED_RESOURCE_TOTAL, $playerId);
            self::incStat(1, STATS_PLAYER_NB_GAINED_RESOURCE_ . $resourceType, $playerId);
        }

        self::setGameStateValue(STG_CHOSEN_MINING_TOKEN_ID, -1);
        self::setGameStateValue(STG_CHOSEN_MINING_TOKEN_TERRAIN_ID, -1);

        $newScore = $this->addToPlayerScore($playerId, $section, STATS_PLAYER_SCORE_SUPPLY_TRACK);

        $this->notifyAllPlayers(
            NTF_UPDATE_ADD_RESOURCES_TO_TRACK,
            clienttranslate('${player_name} adds resources to section ${section}'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'section' => $section,
                'resources' => $resources,
            ])
        );

        $this->notifyAllPlayers(
            NTF_UPDATE_SCORE,
            clienttranslate('${player_name} gains ${section} points by adding resources to section ${section}'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'totalScore' => $newScore,
                'section' => $section,
            ])
        );
        $this->gamestate->nextState("next");
    }
    
    public function confirmTurn()
    {
        $this->checkAction("confirmTurn");
        // Turn token
        $revealedTerrain = $this->board->revealMiningTokens();
        $revealedInfo = [];
        foreach ($revealedTerrain as $terrain) {
            $revealedInfo[$terrain->id()] = [
                'terrainType' => $terrain->terrainType,
                'miningTokenId' => $terrain->miningTokenId,
            ];
        }
        $this->notifyAllPlayers(
            NTF_UPDATE_REVEAL_TOKEN,
            clienttranslate('${token_revealed_count} mining tokens are revealed'),
            $this->mergeAllStateGlobals([
                'token_revealed_count' => count($revealedTerrain),
                'revealedInfo' => $revealedInfo,
            ])
        );

        // Give time at end of turn
        $playerId = $this->getActivePlayerId();
        $this->giveExtraTime($playerId);

        $this->gamestate->nextState("next");
    }

    public function confirmAfterCancelView()
    {
        $this->checkAction("confirmAfterCancelView");
        $this->gamestate->nextState("");
    }


    public function chooseFreeResource($resourceType)
    {
        $this->checkAction("chooseFreeResource");
        self::incGameStateValue(STG_INVESTMENT_FREE_RESOURCE_COUNT, -1);
        self::setGameStateValue(STG_INVESTMENT_FREE_RESOURCE_ID, RESOURCE_TYPES_TO_ID[$resourceType]);

        $playerId = $this->getActivePlayerId();
        $this->notifyAllPlayers(
            NTF_UPDATE_SCORE,
            clienttranslate('${player_name} chooses ${resource_type_name} as a free resource'),
            $this->mergeAllStateGlobals([
                'i18n' => ['resource_type_name'],
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'resource_type_name' => $this->resourceName[$resourceType],
            ])
        );

        $this->gamestate->nextState("next");
    }

    public function chooseTradingPostResource($resourceType)
    {
        $this->checkAction("chooseTradingPostResource");
        self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, RESOURCE_TYPES_TO_ID[$resourceType]);

        $playerId = $this->getActivePlayerId();
        $this->notifyAllPlayers(
            NTF_UPDATE_SCORE,
            clienttranslate('${player_name} chooses ${resource_type_name} from the Trading Post in BoomTown'),
            $this->mergeAllStateGlobals([
                'i18n' => ['resource_type_name'],
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'resource_type_name' => $this->resourceName[$resourceType],
            ])
        );

        $this->gamestate->nextState("next");
    }

    public function chooseFreeResourceTrack($section)
    {
        $this->checkAction("chooseFreeResourceTrack");
        if (array_search($section, GWPlayerBoard::SUPPLY_TRACK_SECTIONS) === false)
            throw new BgaUserException(sprintf(self::_('Invalid supply track section: %s'), $section));

        $resourceType = RESOURCE_IDS_TO_TYPE[self::getGameStateValue(STG_INVESTMENT_FREE_RESOURCE_ID)];
        self::setGameStateValue(STG_INVESTMENT_FREE_RESOURCE_ID, 0);

        $playerId = $this->getActivePlayerId();
        $this->playerBoard->addResourcesToSupplyTrack($playerId, $section, [$resourceType]);
        self::incStat(1, STATS_PLAYER_NB_GAINED_RESOURCE_TOTAL, $playerId);
        self::incStat(1, STATS_PLAYER_NB_GAINED_RESOURCE_ . $resourceType, $playerId);
        self::incStat(1, STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_ . $section, $playerId);

        $this->notifyAllPlayers(
            NTF_UPDATE_ADD_RESOURCES_TO_TRACK,
            clienttranslate('${player_name} adds ${resource_type_name} to section ${section}'),
            $this->mergeAllStateGlobals([
                'i18n' => ['resource_type_name'],
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'section' => $section,
                'resources' => [$resourceType],
                'resource_type_name' => $this->resourceName[$resourceType],
            ])
        );

        $this->gamestate->nextState('next');
    }

    public function tradingPostKeepResource()
    {
        $this->checkAction("tradingPostKeepResource");

        $resourceType = RESOURCE_IDS_TO_TYPE[self::getGameStateValue(STG_TRADING_POST_RESOURCE_ID)];
        self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, -1);
        self::incGameStateValue(STG_NB_RESOURCES_ . $resourceType, 1);
        
        $playerId = $this->getActivePlayerId();

        self::incStat(1, STATS_PLAYER_NB_GAINED_RESOURCE_TOTAL, $playerId);
        self::incStat(1, STATS_PLAYER_NB_GAINED_RESOURCE_ . $resourceType, $playerId);

        $this->notifyAllPlayers(
            NTF_UPDATE_ADD_RESOURCE_TO_HAND,
            clienttranslate('${player_name} chooses to use ${resource_type_name} in the current turn'),
            $this->mergeAllStateGlobals([
                'i18n' => ['resource_type_name'],
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'resourceType' => $resourceType,
                'resource_type_name' => $this->resourceName[$resourceType],
            ])
        );

        $this->gamestate->nextState('next');
    }

    public function tradingPostSupplyTrackAdd($section)
    {
        $this->checkAction("tradingPostSupplyTrackAdd");
        $playerId = $this->getActivePlayerId();
        if (array_search($section, GWPlayerBoard::SUPPLY_TRACK_SECTIONS) === false)
            throw new BgaUserException(self::_('Invalid supply track section'));

        $resourceType = RESOURCE_IDS_TO_TYPE[self::getGameStateValue(STG_TRADING_POST_RESOURCE_ID)];
        self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, -1);
        $this->playerBoard->addResourcesToSupplyTrack($playerId, $section, [$resourceType]);

        self::incStat(1, STATS_PLAYER_NB_ADD_RESOURCE_SUPPLY_TRACK_ . $section, $playerId);
        self::incStat(1, STATS_PLAYER_NB_GAINED_RESOURCE_TOTAL, $playerId);
        self::incStat(1, STATS_PLAYER_NB_GAINED_RESOURCE_ . $resourceType, $playerId);

        $newScore = $this->addToPlayerScore($playerId, $section, STATS_PLAYER_SCORE_SUPPLY_TRACK);

        $this->notifyAllPlayers(
            NTF_UPDATE_ADD_RESOURCES_TO_TRACK,
            clienttranslate('${player_name} adds resource from Trading Post to section ${section}'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'section' => $section,
                'resources' => [$resourceType],
            ])
        );

        $this->notifyAllPlayers(
            NTF_UPDATE_SCORE,
            clienttranslate('${player_name} gains ${section} points by adding resources to section ${section}'),
            $this->mergeAllStateGlobals([
                'player_id' => $playerId,
                'player_name' => $this->getActivePlayerName(),
                'totalScore' => $newScore,
                'section' => $section,
            ])
        );
        $this->gamestate->nextState("next");
    }

    public function cancelTurn()
    {
        $this->checkAction("cancelTurn");
        if (self::getGameStateValue(STG_CANCEL_ALLOWED) == 0)
            throw new BgaUserException(self::_('Cancelling turn is not allowed'));
        $this->undoRestorePoint();
        $nextState = self::getGameStateValue(STG_CANCEL_GOTO_STATE);
        switch ($nextState) {
            case STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID:
                $this->gamestate->nextState("restartTurn");
                break;
            case STATE_HAS_METAL_TO_USE_AND_PLACE_TO_USE_ID:
                $this->gamestate->nextState("restartTurnHasMetalToUse");
                break;
            default:
                throw new BgaVisibleSystemException(sprintf('Invalid state for undo, STG_CANCEL_GOTO_STATE is invalid: %s', $nextState));
                break;
        }
    }

    // DEBUG! Comment for production
    //public function chooseDebugGotoLastTurn()
    //{
    //    $this->checkAction("chooseDebugGotoLastTurn");
    //    if (self::getGameStateValue(STG_LAST_TURN_COUNT) == 0) {
    //        self::setGameStateValue(STG_LAST_TURN_COUNT, 1);
    //        $playerBasicInfo = $this->loadPlayersBasicInfos();
    //        $playerIdArray = array_keys($playerBasicInfo);
    //        $this->shipping->debugFill($playerIdArray);
    //        $this->investments->debugFill($playerIdArray);
    //        $this->boomtown->debugFill($playerIdArray);
    //        $this->board->debugFill($playerIdArray);
    //    }
    //    $this->gamestate->nextState("toDebugGotoLastTurn");
    //}

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    ////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    public function argChooseSupplyTrackResourceToLeave()
    {
        return $this->getAllStateGlobals();
    }

    public function argPossibleMetalUse()
    {
        // Return what can be bought
        $playerId = $this->getActivePlayerId();
        $resources = $this->stateGlobalsToResources();
        $boomtownPositions = [];
        if (self::getGameStateValue(STG_USE_METAL_BOOMTOWN_USED) == 0)
            $boomtownPositions = $this->boomtown->canBuyPositions($resources);
        $investmentCards = [];
        if (self::getGameStateValue(STG_USE_METAL_INVESTMENT_USED) == 0)
            $investmentCards = $this->investments->canBuyCards($resources);
        $trackPositions = $this->shipping->canShip($playerId, $resources);
        return $this->mergeAllStateGlobals([
            "boomtownPositions" => $boomtownPositions,
            "investmentCards" => $investmentCards,
            "trackPositions" => $trackPositions,
        ]);
    }


    public function argChooseToken()
    {
        return $this->mergeAllStateGlobals([
            'tokens' => $this->board->getVisibleTokenId(),
        ]);
    }

    public function argChooseSupplyTrackToAdd()
    {
        $miningTokenId = self::getGameStateValue(STG_CHOSEN_MINING_TOKEN_ID);
        $terrainType = TERRAIN_IDS_TO_TYPE[self::getGameStateValue(STG_CHOSEN_MINING_TOKEN_TERRAIN_ID)];
        $resources = MINING_TOKEN_RESOURCES[$terrainType][$miningTokenId];
        return $this->mergeAllStateGlobals([
            'resources' => $resources,
        ]);
    }

    public function argInvestmentShippingTrack()
    {
        // Return what can be "bought" for free
        $playerId = $this->getActivePlayerId();
        $trackPositions = $this->shipping->canShip(
            $playerId,
            [
                RESOURCE_TYPE_GOLD => 1,
                RESOURCE_TYPE_SILVER => 1,
                RESOURCE_TYPE_COPPER => 1,
            ]
        );
        return $this->mergeAllStateGlobals([
            "trackPositions" => $trackPositions,
        ]);
    }

    public function argInvestmentUnoccupiedBoomTown()
    {
        $boomtownPositions = $this->boomtown->freeOffices();
        return $this->mergeAllStateGlobals([
            "boomtownPositions" => $boomtownPositions,
        ]);
    }

    public function argInvestmentOccupiedBoomTown()
    {
        $playerId = $this->getActivePlayerId();
        $boomtownPositions = $this->boomtown->occupiedOfficesByOtherPlayers($playerId);
        return $this->mergeAllStateGlobals([
            "boomtownPositions" => $boomtownPositions,
        ]);
    }

    public function argMiningTokenToView()
    {
        $playerId = $this->getActivePlayerId();
        return $this->mergeAllStateGlobals([
            'tokens' => $this->board->getInvisibleTokenIdNotViewedByPlayerId($playerId),
        ]);
    }

    public function argInvestmentCampToUpgrade()
    {
        $playerId = $this->getActivePlayerId();
        return $this->mergeAllStateGlobals([
            'tokens' => $this->board->getCampByPlayerId($playerId),
        ]);
    }

    public function argInvestmentChooseFreeResourceTrack()
    {
        $resourceId = self::getGameStateValue(STG_INVESTMENT_FREE_RESOURCE_ID);
        $resourceType = RESOURCE_IDS_TO_TYPE[$resourceId];
        return $this->mergeAllStateGlobals([
            'i18n' => ['resource_type_name'],
            'resource_type_name' => $this->resourceName[$resourceType],
        ]);
    }

    public function argChooseToConfirmTurn()
    {
        return $this->getAllStateGlobals();
    }

    public function argGenericOnlyGlobals()
    {
        return $this->getAllStateGlobals();
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    public function stHasSupplyTrackResourcesToLeave()
    {
        // Already on section 0, the resources are already out
        $section = self::getGameStateValue(STG_CHOSEN_SUPPLY_TRACK_SECTION);
        if ($section <= 0) {
            self::setGameStateValue(STG_CHOSEN_SUPPLY_TRACK_SECTION, -1);
            $this->gamestate->nextState("toUseMetal");
            return;
        }
        // Leave resources only if possible
        $hasResources = false;
        foreach (RESOURCE_TYPES_ALL as $resourceType) {
            if (self::getGameStateValue(STG_NB_RESOURCES_ . $resourceType) > 0) {
                $hasResources = true;
                break;
            }
        }
        if ($hasResources) {
            self::setGameStateValue(STG_CHOSEN_SUPPLY_TRACK_SECTION, $section - 1);
            $this->gamestate->nextState("toLeaveResource");
        } else {
            self::setGameStateValue(STG_CHOSEN_SUPPLY_TRACK_SECTION, -1);
            $this->gamestate->nextState("toUseMetal");
        }
    }

    public function stHasMetalToUseAndPlaceToUse()
    {
        // Nothing more to check if player has no metal
        $hasMetal = false;
        foreach (RESOURCE_TYPES_METALS as $resourceType) {
            if (self::getGameStateValue(STG_NB_RESOURCES_ . $resourceType) > 0) {
                $hasMetal = true;
                break;
            }
        }
        if (!$hasMetal) {
            $this->gamestate->nextState("toNoMetal");
            return;
        }
        // Player has metal, check if he can use it
        $playerId = $this->getActivePlayerId();
        $resources = $this->stateGlobalsToResources();
        $boomtownPositions = [];
        if (self::getGameStateValue(STG_USE_METAL_BOOMTOWN_USED) == 0)
            $boomtownPositions = $this->boomtown->canBuyPositions($resources);
        $investmentCards = [];
        if (self::getGameStateValue(STG_USE_METAL_INVESTMENT_USED) == 0)
            $investmentCards = $this->investments->canBuyCards($resources);
        $trackPositions = $this->shipping->canShip($playerId, $resources);
        if (
            count($boomtownPositions) == 0 &&
            count($investmentCards) == 0 &&
            count($trackPositions) == 0
        ) {
            $this->gamestate->nextState("toNoMetal");
            return;
        }
        $this->gamestate->nextState("toUseMetal");
    }

    public function stCheckIsLastTurn()
    {
        if (self::getGameStateValue(STG_LAST_TURN_COUNT) == 0) {
            $this->gamestate->nextState("toNotLastTurn");
        } else {
            $this->gamestate->nextState("toIsLastTurn");
        }
    }

    public function stHasWoodOrStone()
    {
        $wood = self::getGameStateValue(STG_NB_RESOURCES_WO);
        $stone = self::getGameStateValue(STG_NB_RESOURCES_ST);
        if ($wood >= 1 && $stone >= 1) {
            $this->gamestate->nextState("toSettlement");
        } else if ($wood == 0 && $stone == 0) {
            $this->gamestate->nextState("toLoot");
        } else {
            $this->gamestate->nextState("toCamp");
        }
    }

    public function stDiscardRemainingResources()
    {
        foreach (RESOURCE_TYPES_ALL as $resourceType) {
            self::setGameStateValue(STG_NB_RESOURCES_ . $resourceType, 0);
        }
        $this->notifyAllPlayers(
            NTF_UPDATE_SCORE,
            '',
            $this->mergeAllStateGlobals([])
        );
        $this->gamestate->nextState();
    }

    public function stSwitchToNextPlayer()
    {
        // Reset globals
        self::setGameStateValue(STG_CANCEL_ALLOWED, 1);
        self::setGameStateValue(STG_CANCEL_GOTO_STATE, STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID);
        self::setGameStateValue(STG_NB_RESOURCES_GO, 0);
        self::setGameStateValue(STG_NB_RESOURCES_SI, 0);
        self::setGameStateValue(STG_NB_RESOURCES_CO, 0);
        self::setGameStateValue(STG_NB_RESOURCES_WO, 0);
        self::setGameStateValue(STG_NB_RESOURCES_ST, 0);
        self::setGameStateValue(STG_CHOSEN_SUPPLY_TRACK_SECTION, -1);
        self::setGameStateValue(STG_CHOSEN_MINING_TOKEN_ID, -1);
        self::setGameStateValue(STG_CHOSEN_MINING_TOKEN_TERRAIN_ID, -1);
        self::setGameStateValue(STG_USE_METAL_BOOMTOWN_USED, 0);
        self::setGameStateValue(STG_USE_METAL_INVESTMENT_USED, 0);
        self::setGameStateValue(STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT, 0);
        self::setGameStateValue(STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT, 0);
        self::setGameStateValue(STG_INVESTMENT_FREE_RESOURCE_COUNT, 0);
        self::setGameStateValue(STG_INVESTMENT_FREE_RESOURCE_ID, 0);
        self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, -1);

        $this->activeNextPlayer();
        $this->gamestate->nextState();
    }

    public function stStartOfTurnSetup()
    {
        $this->undoSavepoint();
        $this->gamestate->nextState();
    }

    public function stInvestmentHasFreeShippingTrack()
    {
        if (self::getGameStateValue(STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT) <= 0) {
            $this->gamestate->nextState("toNoShippingTrack");
            return;
        }
        $playerId = $this->getActivePlayerId();
        // Try shipping but with free resources
        $trackPositions = $this->shipping->canShip(
            $playerId,
            [
                RESOURCE_TYPE_GOLD => 1,
                RESOURCE_TYPE_SILVER => 1,
                RESOURCE_TYPE_COPPER => 1,
            ]
        );
        if (count($trackPositions) == 0) {
            $this->gamestate->nextState("toNoShippingTrack");
            return;
        }
        $this->gamestate->nextState("toChooseShippingTrack");
    }

    public function stInvestmentHasUnoccupiedBoomTown()
    {
        if (count($this->boomtown->freeOffices()) > 0) {
            $this->gamestate->nextState("toChooseBoomTown");
        } else {
            $this->gamestate->nextState("toTownFull");
        }
    }

    public function stInvestmentHasOccupiedBoomTown()
    {
        $playerId = $this->getActivePlayerId();
        if (count($this->boomtown->occupiedOfficesByOtherPlayers($playerId)) > 0) {
            $this->gamestate->nextState("toChooseBoomTown");
        } else {
            $this->gamestate->nextState("toNotOccupied");
        }
    }

    public function stHasRemainMiningTokenToView()
    {
        if (self::getGameStateValue(STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT) <= 0) {
            self::setGameStateValue(STG_CANCEL_ALLOWED, 1);
            $this->undoSavepoint();
            $this->gamestate->nextState("toNoTokenToView");
            return;
        }
        $playerId = $this->getActivePlayerId();
        if (count($this->board->getInvisibleTokenIdNotViewedByPlayerId($playerId)) > 0) {
            $this->gamestate->nextState("toHasTokenToView");
        } else {
            self::setGameStateValue(STG_CANCEL_ALLOWED, 1);
            $this->undoSavepoint();
            $this->gamestate->nextState("toNoTokenToView");
        }
    }

    public function stHasCampToUpgrade()
    {
        $playerId = $this->getActivePlayerId();
        if (count($this->board->getCampByPlayerId($playerId)) > 0) {
            $this->gamestate->nextState("toHasCamp");
        } else {
            $this->gamestate->nextState("toNoCamp");
        }
    }

    public function stHasRemainFreeResources()
    {
        if (self::getGameStateValue(STG_INVESTMENT_FREE_RESOURCE_COUNT) <= 0) {
            $this->gamestate->nextState("toNoResources");
        } else {
            $this->gamestate->nextState("toHasResources");
        }
    }

    public function stNextPlayerOrGameEnd()
    {
        // Check if we must switch into "last turn mode": all camps are used
        $playerBasicInfo = $this->loadPlayersBasicInfos();
        $playerCount = count($playerBasicInfo);
        $playerInfoArray = $this->board->getBoardPlayerInfo(array_keys($playerBasicInfo), $this->investments);
        $playerZombieTurnCount = $this->getAllPlayersZombieTurnCount();
        $nextState = "nextPlayer";
        if (self::getGameStateValue(STG_LAST_TURN_COUNT) == 0) {
            $allCampUsed = true;
            foreach ($playerInfoArray as $playerId => $info) {
                if ($info->usedCamp() + $playerZombieTurnCount[$playerId] != GWPlayerBoard::CAMP_COUNT_PER_PLAYER_COUNT[$playerCount]) {
                    $allCampUsed = false;
                    break;
                }
            }
            if ($allCampUsed === true) {
                self::setGameStateValue(STG_LAST_TURN_COUNT, 1);
                $this->notifyAllPlayers(
                    NTF_UPDATE_SCORE,
                    clienttranslate('This is the last turn for all players'),
                    $this->mergeAllStateGlobals([])
                );
            }
        } else if (self::getGameStateValue(STG_LAST_TURN_COUNT) == $playerCount) {
            $nextState = "gameEnd";
        } else {
            self::incGameStateValue(STG_LAST_TURN_COUNT, 1);
        }

        $this->gamestate->nextState($nextState);
    }

    public function stEndScoring()
    {
        // Reset globals
        self::setGameStateValue(STG_LAST_TURN_COUNT, 0);
        self::setGameStateValue(STG_CANCEL_ALLOWED, 1);
        self::setGameStateValue(STG_CANCEL_GOTO_STATE, STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE_ID);
        self::setGameStateValue(STG_NB_RESOURCES_GO, 0);
        self::setGameStateValue(STG_NB_RESOURCES_SI, 0);
        self::setGameStateValue(STG_NB_RESOURCES_CO, 0);
        self::setGameStateValue(STG_NB_RESOURCES_WO, 0);
        self::setGameStateValue(STG_NB_RESOURCES_ST, 0);
        self::setGameStateValue(STG_CHOSEN_SUPPLY_TRACK_SECTION, -1);
        self::setGameStateValue(STG_CHOSEN_MINING_TOKEN_ID, -1);
        self::setGameStateValue(STG_CHOSEN_MINING_TOKEN_TERRAIN_ID, -1);
        self::setGameStateValue(STG_USE_METAL_BOOMTOWN_USED, 0);
        self::setGameStateValue(STG_USE_METAL_INVESTMENT_USED, 0);
        self::setGameStateValue(STG_INVESTMENT_FREE_SHIPPING_TRACK_COUNT, 0);
        self::setGameStateValue(STG_INVESTMENT_MINING_TOKEN_TO_VIEW_COUNT, 0);
        self::setGameStateValue(STG_INVESTMENT_FREE_RESOURCE_COUNT, 0);
        self::setGameStateValue(STG_INVESTMENT_FREE_RESOURCE_ID, 0);
        self::setGameStateValue(STG_TRADING_POST_RESOURCE_ID, -1);

        $playerBasicInfo = $this->loadPlayersBasicInfos();
        $playerInfoArray = $this->board->getBoardPlayerInfo(array_keys($playerBasicInfo), $this->investments);

        foreach ($playerInfoArray as $playerId => $playerInfo) {
            self::setStat($playerInfoArray[$playerId]->builtCamp(), STATS_PLAYER_NB_BUILT_CAMP, $playerId);
            self::setStat($playerInfoArray[$playerId]->usedSettlement(), STATS_PLAYER_NB_BUILT_SETTLEMENT, $playerId);
            self::setStat($playerInfoArray[$playerId]->usedLootCamp(), STATS_PLAYER_NB_LOOT, $playerId);
        }
        // Send globals to reset display
        $this->notifyAllPlayers(
            NTF_UPDATE_SCORE,
            '',
            $this->mergeAllStateGlobals([])
        );

        // Score building groups
        $buildingGroupPerPlayers = $this->board->getBuildingGroupPerPlayers();
        foreach ($buildingGroupPerPlayers as $playerId => $groups) {
            if (count($groups) < 1) {
                // Should not happen
                continue;
            }
            $score = count($groups[0]) * SCORE_POINTS_PER_BUILDING;
            $totalScore = $this->addToPlayerScore($playerId, $score, STATS_PLAYER_SCORE_LARGEST_GROUP);
            $this->notifyAllPlayers(
                NTF_UPDATE_TERRAIN_HIGHLIGHT,
                clienttranslate('${player_name} gains ${score} points with their largest group of ${building_count} buildings'),
                $this->mergeAllStateGlobals([
                    'player_id' => $playerId,
                    'player_name' => $playerBasicInfo[$playerId]['player_name'],
                    'score' => $score,
                    'totalScore' => $totalScore,
                    'building_count' => count($groups[0]),
                    'ids' => $groups[0],
                ])
            );
        }

        // Score terrain influence
        $playerIdInfluenceCountMap = [];
        foreach (array_keys($playerBasicInfo) as $playerId) {
            $playerIdInfluenceCountMap[$playerId] = 0;
            foreach (TERRAIN_TYPES_BUILDABLE as $terrainType) {
                self::setStat($playerInfoArray[$playerId]->totalInfluence($terrainType), STATS_PLAYER_INFLUENCE_ . $terrainType, $playerId);
            }
        }
        foreach (TERRAIN_TYPES_BUILDABLE as $terrainType) {
            $influenceOrder = array_keys($playerBasicInfo);
            // Terrain influence is won by total influence. Ties are won by the player with the most settlements.
            // If still equal, the sum of the points are splitted, rounded down.
            usort($influenceOrder, function ($a, $b) use (&$playerInfoArray, &$terrainType) {
                return ($playerInfoArray[$b]->endScoreInfluence($terrainType) <=> $playerInfoArray[$a]->endScoreInfluence($terrainType));
            });
            $nbPlayers = count($influenceOrder);
            if ($nbPlayers > 2) {
                $firstInfluence = $playerInfoArray[$influenceOrder[0]]->endScoreInfluence($terrainType);
                if ($firstInfluence[0] == 0) {
                    continue;
                }
                $firstInfluencePlayers = [array_shift($influenceOrder)];
                while (!empty($influenceOrder)) {
                    $currentInfluence = $playerInfoArray[$influenceOrder[0]]->endScoreInfluence($terrainType);
                    if ($currentInfluence[0] == 0) {
                        array_shift($influenceOrder);
                    } else if ($firstInfluence == $currentInfluence) {
                        $firstInfluencePlayers[] = array_shift($influenceOrder);
                    } else {
                        break;
                    }
                }
                $firstInfluenceScore = (count($firstInfluencePlayers) == 1 ?
                    SCORE_INFLUENCE_BY_TERRAIN_TYPE[$terrainType][0] :
                    intdiv(SCORE_INFLUENCE_BY_TERRAIN_TYPE[$terrainType][0] + SCORE_INFLUENCE_BY_TERRAIN_TYPE[$terrainType][1], count($firstInfluencePlayers)));
                foreach ($firstInfluencePlayers as $playerId) {
                    $playerIdInfluenceCountMap[$playerId] += 1;
                    $totalScore = $this->addToPlayerScore($playerId, $firstInfluenceScore, STATS_PLAYER_SCORE_INFLUENCE);
                    $this->notifyAllPlayers(
                        NTF_UPDATE_SCORE_INFLUENCE,
                        clienttranslate('${player_name} gains ${score} points by having the most influence (${influence}) for ${terrain_type_name}'),
                        $this->mergeAllStateGlobals([
                            'i18n' => ['terrain_type_name'],
                            'player_id' => $playerId,
                            'player_name' => $playerBasicInfo[$playerId]['player_name'],
                            'score' => $firstInfluenceScore,
                            'totalScore' => $totalScore,
                            'terrainType' => $terrainType,
                            'terrain_type_name' => $this->terrainName[$terrainType],
                            'influence' => $firstInfluence,
                        ])
                    );
                }
            }
            if (
                ($nbPlayers == 2 || count($firstInfluencePlayers) == 1)
                && count($influenceOrder) > 0
            ) {
                $secondInfluence = $playerInfoArray[$influenceOrder[0]]->endScoreInfluence($terrainType);
                if ($secondInfluence[0] == 0) {
                    continue;
                }
                $secondInfluencePlayers = [array_shift($influenceOrder)];
                while (!empty($influenceOrder)) {
                    $currentInfluence = $playerInfoArray[$influenceOrder[0]]->endScoreInfluence($terrainType);
                    if ($currentInfluence[0] == 0) {
                        array_shift($influenceOrder);
                    } else if ($secondInfluence == $currentInfluence) {
                        $secondInfluencePlayers[] = array_shift($influenceOrder);
                    } else {
                        break;
                    }
                }
                $secondInfluenceScore = intdiv(SCORE_INFLUENCE_BY_TERRAIN_TYPE[$terrainType][1], count($secondInfluencePlayers));
                $text = clienttranslate('${player_name} gains ${score} points by having the second most influence (${influence}) for ${terrain_type_name}');
                if ($nbPlayers == 2) {
                    $text = clienttranslate('${player_name} gains ${score} points by having the most influence (${influence}) for ${terrain_type_name}');
                }
                foreach ($secondInfluencePlayers as $playerId) {
                    $totalScore = $this->addToPlayerScore($playerId, $secondInfluenceScore, STATS_PLAYER_SCORE_INFLUENCE);
                    $playerIdInfluenceCountMap[$playerId] += 1;
                    $this->notifyAllPlayers(
                        NTF_UPDATE_SCORE_INFLUENCE,
                        $text,
                        $this->mergeAllStateGlobals([
                            'i18n' => ['terrain_type_name'],
                            'player_id' => $playerId,
                            'player_name' => $playerBasicInfo[$playerId]['player_name'],
                            'score' => $secondInfluenceScore,
                            'totalScore' => $totalScore,
                            'terrainType' => $terrainType,
                            'terrain_type_name' => $this->terrainName[$terrainType],
                            'influence' => $secondInfluence,
                        ])
                    );
                }
            }
        }

        // Score BoomTown
        $boomTownScoreList = $this->boomtown->getEndGameScoreForAllPlayers(
            $this->board,
            $this->investments,
            $this->shipping,
            $playerInfoArray,
            $playerIdInfluenceCountMap
        );
        $boomTownOfficeStats = [];
        foreach ($boomTownScoreList as $boomTownScore) {
            if (!array_key_exists($boomTownScore->officeId, $boomTownOfficeStats)) {
                $boomTownOfficeStats[$boomTownScore->officeId] = [];
                foreach (array_keys($playerBasicInfo) as $playerId) {
                    $boomTownOfficeStats[$boomTownScore->officeId][$playerId] = 0;
                }
            }
            $boomTownOfficeStats[$boomTownScore->officeId][$boomTownScore->playerId] += $boomTownScore->score;
            $totalScore = $this->addToPlayerScore($boomTownScore->playerId, $boomTownScore->score, STATS_PLAYER_SCORE_BOOMTOWN);
            $this->notifyAllPlayers(
                NTF_UPDATE_SCORE_BOOMTOWN,
                clienttranslate('${player_name} gains ${score} points with BoomTown: ${office_name}'),
                $this->mergeAllStateGlobals([
                    'i18n' => ['office_name'],
                    'player_id' => $boomTownScore->playerId,
                    'player_name' => $playerBasicInfo[$boomTownScore->playerId]['player_name'],
                    'score' => $boomTownScore->score,
                    'totalScore' => $totalScore,
                    'x' => $boomTownScore->x,
                    'y' => $boomTownScore->y,
                    'ids' => $boomTownScore->ids,
                    'office_name' => $boomTownScore->officeName(),
                ])
            );
        }

        foreach ($boomTownOfficeStats as $officeId => $playerStat) {
            foreach ($playerStat as $playerId => $stat) {
                self::setStat($stat, STATS_PLAYER_SCORE_OFFICE_ . $officeId, $playerId);
            }
        }

        // Score looting
        $lootOrder = array_keys($playerBasicInfo);
        usort($lootOrder, function ($a, $b) use (&$playerInfoArray) {
            return ($playerInfoArray[$b]->usedLootCamp() <=> $playerInfoArray[$a]->usedLootCamp());
        });
        $firstLootCount = 0;
        $firstLootScore = $playerInfoArray[$lootOrder[0]]->usedLootCamp();
        $secondLootScore = null;
        if ($firstLootScore > 0) {
            foreach ($lootOrder as $playerId) {
                if ($firstLootScore == $playerInfoArray[$playerId]->usedLootCamp()) {
                    ++$firstLootCount;
                    $totalScore = $this->substractFromPlayerScore($playerId, $firstLootScore, STATS_PLAYER_SCORE_WANTED);
                    $this->notifyAllPlayers(
                        NTF_UPDATE_SCORE_WANTED,
                        clienttranslate('${player_name} loses ${score} points by being the Most Wanted'),
                        $this->mergeAllStateGlobals([
                            'player_id' => $playerId,
                            'player_name' => $playerBasicInfo[$playerId]['player_name'],
                            'score' => $firstLootScore,
                            'totalScore' => $totalScore,
                        ])
                    );
                } else {
                    $secondLootScore = $playerInfoArray[$playerId]->usedLootCamp();
                    break;
                }
            }
            if ($firstLootCount == 1 && $secondLootScore > 0) {
                if ($secondLootScore == $playerInfoArray[$playerId]->usedLootCamp()) {
                    $score = intdiv($secondLootScore, 2);
                    $totalScore = $this->substractFromPlayerScore($playerId, $score, STATS_PLAYER_SCORE_WANTED);
                    $this->notifyAllPlayers(
                        NTF_UPDATE_SCORE_WANTED,
                        clienttranslate('${player_name} loses ${score} points by being the Second Most Wanted'),
                        $this->mergeAllStateGlobals([
                            'player_id' => $playerId,
                            'player_name' => $playerBasicInfo[$playerId]['player_name'],
                            'score' => $score,
                            'totalScore' => $totalScore,
                        ])
                    );
                }
            }
        }

        // Tie breaker:
        //    In the case of a tie, the tied player with the fewest Camp Pieces in the Wanted area is the winner.
        //    If players are still tied, the tied player with the most resources remaining in their Supply Track is the winner.
        // So player_score_aux will be = XXYYY
        //    Where XX = <Total number of camp> - <Number of wanted camp>
        //    Where YYY = <Number of resources in supply track>
        $totalCamp = GWPlayerBoard::CAMP_COUNT_PER_PLAYER_COUNT[count($playerBasicInfo)];
        foreach (array_keys($playerBasicInfo) as $playerId) {
            $auxScore = ($totalCamp - $playerInfoArray[$playerId]->usedLootCamp()) * 1000;
            foreach (GWPlayerBoard::SUPPLY_TRACK_SECTIONS as $section) {
                foreach ($this->playerBoard->getPlayerSupplyTrack($playerId, $section) as $track) {
                    $auxScore += $track->resourceCount;
                }
            }
            self::DbQuery("UPDATE player SET player_score_aux = $auxScore WHERE player_id = $playerId");
        }

        $this->gamestate->nextState();
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Zombie
    ////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn($state, $activePlayer)
    {
        if ($state['name'] == STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE) {
            $this->incPlayerZombieTurnCount($activePlayer);
            $this->gamestate->nextState("zombiePass");
        } else {
            // Undo the player partial turn. After the undo, the state will be
            // STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE and we can "zombie pass" his turn
            $this->undoRestorePoint();
            $this->gamestate->nextState("restartTurn");
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////:
    ////////// DB upgrade
    //////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

    function upgradeTableDb($from_version)
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345

        // Example:
        //        if( $from_version <= 1404301345 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        if( $from_version <= 1405061421 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        // Please add your future database scheme changes here
        //
        //


    }
}

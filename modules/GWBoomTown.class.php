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

require_once("GWGlobals.inc.php");

const OFFICE_ID_4VP = -1;
const OFFICE_ID_DOCKS = 0;
const OFFICE_ID_FRONTIER_OFFICE = 1;
const OFFICE_ID_HOMESTEAD_OFFICE = 2;
const OFFICE_ID_COURTHOUSE = 3;
const OFFICE_ID_DEEDS_OFFICE = 4;
const OFFICE_ID_DEPOT = 5;
const OFFICE_ID_MAYORS_OFFICE = 6;
const OFFICE_ID_SALOON = 7;
const OFFICE_ID_SHERIFFS_OFFICE = 8;
const OFFICE_ID_SHIPPING_OFFICE = 9;
const OFFICE_ID_SURVEYORS_OFFICE = 10;
const OFFICE_ID_TOWN_HALL = 11;
// Expansions
const OFFICE_ID_RESOURCE_COPPER = -2;
const OFFICE_ID_RESOURCE_SILVER = -3;
const OFFICE_ID_RESOURCE_GOLD = -4;
const OFFICE_ID_RESOURCE_WOOD_OR_STONE = -5;
const OFFICE_ID_INSURANCE_OFFICE = 12;
const OFFICE_ID_HOOSEGOW = 13;

const OFFICE_IDS_TRADING_POST_EXPANSION = [
    OFFICE_ID_RESOURCE_COPPER,
    OFFICE_ID_RESOURCE_SILVER,
    OFFICE_ID_RESOURCE_GOLD,
    OFFICE_ID_RESOURCE_WOOD_OR_STONE,
];

class GWBoomTownScore
{
    public $x;
    public $y;
    public $playerId;
    public $officeId;
    public $score;
    public $ids;

    public function __construct()
    {
        $this->x = null;
        $this->y = null;
        $this->playerId = null;
        $this->officeId = null;
        $this->score = 0;
        $this->ids = [];
    }

    public function officeName()
    {
        switch ($this->officeId) {
            case OFFICE_ID_DOCKS:
                return clienttranslate("Docks");
            case OFFICE_ID_FRONTIER_OFFICE:
                return clienttranslate("Frontier Office");
            case OFFICE_ID_HOMESTEAD_OFFICE:
                return clienttranslate("Homestead Office");
            case OFFICE_ID_COURTHOUSE:
                return clienttranslate("Courthouse");
            case OFFICE_ID_DEEDS_OFFICE:
                return clienttranslate("Deeds office");
            case OFFICE_ID_DEPOT:
                return clienttranslate("Depot");
            case OFFICE_ID_MAYORS_OFFICE:
                return clienttranslate("Mayor's Office");
            case OFFICE_ID_SALOON:
                return clienttranslate("Saloon");
            case OFFICE_ID_SHERIFFS_OFFICE:
                return clienttranslate("Sheriff's Office");
            case OFFICE_ID_SHIPPING_OFFICE:
                return clienttranslate("Shipping Office");
            case OFFICE_ID_SURVEYORS_OFFICE:
                return clienttranslate("Surveyor's Office");
            case OFFICE_ID_TOWN_HALL:
                return clienttranslate("Town Hall");
            case OFFICE_ID_INSURANCE_OFFICE:
                return clienttranslate("Insurance Office");
            case OFFICE_ID_HOOSEGOW:
                return clienttranslate("Hoosegow");
        }
        return '';
    }
}

class GWBoomTownTile
{
    public $x;
    public $y;
    public $officeId;
    public $playerId;
    public $investmentPlayerId;

    public function __construct(
        int $x,
        int $y,
        ?int $officeId = null,
        ?int $playerId = null,
        ?int $investmentPlayerId = null
    ) {
        $this->x = $x;
        $this->y = $y;
        $this->officeId = $officeId;
        $this->playerId = $playerId;
        $this->investmentPlayerId = $investmentPlayerId;
    }

    public function toClientTopLeft()
    {
        return [
            $this->x * (GWBoomTown::OFFICE_TILE_SIZE) + GWBoomTown::OFFICE_GLOBAL_LEFT_PADDING,
            $this->y * (GWBoomTown::OFFICE_TILE_SIZE) + GWBoomTown::OFFICE_GLOBAL_TOP_PADDING,
        ];
    }

    public function toClientOfficeId()
    {
        return GWBoomTown::officeIdToClient($this->officeId);
    }

    public function isSingleTile()
    {
        return ($this->officeId < 0);
    }

    public function buildTileScore()
    {
        if ($this->officeId == OFFICE_ID_4VP) {
            return 4;
        } else {
            return 0;
        }
    }
}

class GWBoomTownOffice
{
    public $clientX;
    public $clientY;
    public $clientOfficeId;
    public $isVertical;

    public function __construct(
        string $clientX,
        string $clientY,
        string $clientOfficeId,
        bool $isVertical
    ) {
        $this->clientX = $clientX;
        $this->clientY = $clientY;
        $this->clientOfficeId = $clientOfficeId;
        $this->isVertical = $isVertical;
    }
}

class GWBoomTown extends APP_DbObject
{
    public const TOWN_SIZE = 3;
    private const POSITION_TO_RESOURCE = [
        0 => [
            0 => [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_GOLD],
            1 => [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER],
            2 => [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        ],
        1 => [
            0 => [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_GOLD],
            1 => [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
            2 => [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_COPPER],
        ],
        2 => [
            0 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
            1 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER],
            2 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_COPPER],
        ],
    ];
    // Office related
    private const OFFICE_TILE_COUNT = 12;
    private const OFFICE_TILE_COUNT_EXPANSION_BANDITS = 2;
    private const OFFICE_TILE_CHOOSE_COUNT = 4;
    private const OFFICE_SINGLE_VALID_LOCATIONS_MIDDLE = [1, 1];
    private const OFFICE_SINGLE_VALID_LOCATIONS = [
        [0, 0], [2, 0],
        self::OFFICE_SINGLE_VALID_LOCATIONS_MIDDLE,
        [0, 2], [2, 2],
    ];
    private const OFFICE_SINGLE_MIDDLE_CHOICE = [
        0 => [
            [0,  0, 1],
            [2, -1, 1],
            [2,  3, 3],
        ],
        1 => [
            [0,  1, 1],
            [0, -1, 2],
            [3,  3, 2],
        ],
    ];
    private const MAX_GENERATE_RETRY = 100;

    // Display sizes
    public const OFFICE_TILE_SIZE = 66;
    public const OFFICE_GLOBAL_TOP_PADDING = 563;
    public const OFFICE_GLOBAL_LEFT_PADDING = 9;

    private $town = null;

    public static function officeIdToClient(int $id)
    {
        $preid = $id < 0 ? "m" : "p";
        $aid = abs($id);

        return "${preid}_${aid}";
    }

    public static function clientToOfficeId(string $id)
    {
        $parts = explode("_", $id);
        $officeId = (int)($parts[1]);
        if ($parts[0] == "m") {
            $officeId *= -1;
        }
        return $officeId;
    }

    public function generate($useBanditsExpansion, $tradingPostExpansion)
    {
        $singleLocationIndex = array_rand(self::OFFICE_SINGLE_VALID_LOCATIONS);
        $singleLocation = self::OFFICE_SINGLE_VALID_LOCATIONS[$singleLocationIndex];
        $choice = [];
        if ($singleLocation == self::OFFICE_SINGLE_VALID_LOCATIONS_MIDDLE) {
            // The single tile is in the middle, choose one of the two valid dispositions
            $middleChoiceIndex = array_rand(self::OFFICE_SINGLE_MIDDLE_CHOICE);
            $choice = self::OFFICE_SINGLE_MIDDLE_CHOICE[$middleChoiceIndex];
        } else {
            $globalRetryCount = 0;
            while ($globalRetryCount < self::MAX_GENERATE_RETRY) {
                ++$globalRetryCount;
                $coordList = [];
                for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
                    for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                        if ($x == $singleLocation[0] && $y == $singleLocation[1]) {
                            continue;
                        }
                        $coordList[] = [$x, $y];
                    }
                }
                $choice = [];
                $choice[$singleLocation[0]][$singleLocation[1]] = -1;
                $indexToAssign = range(0, self::OFFICE_TILE_CHOOSE_COUNT - 1);
                $localRetryCount = 0;
                while (
                    count($indexToAssign) > 0
                    && $localRetryCount < self::OFFICE_TILE_CHOOSE_COUNT * self::OFFICE_TILE_CHOOSE_COUNT
                ) {
                    ++$localRetryCount;
                    $coord = array_shift($coordList);
                    $direction = [[0, 1], [0, -1], [-1, 0], [1, 0]];
                    shuffle($direction);
                    $foundDirection = false;
                    foreach ($direction as $dir) {
                        $searchCoord = [$coord[0] + $dir[0], $coord[1] + $dir[1]];
                        $dirIndex = array_search($searchCoord, $coordList);
                        if ($dirIndex !== false) {
                            $foundDirection = true;
                            array_splice($coordList, $dirIndex, 1);
                            $choice[$coord[0]][$coord[1]] = $indexToAssign[0];
                            $choice[$searchCoord[0]][$searchCoord[1]] = $indexToAssign[0];
                            array_shift($indexToAssign);
                            break;
                        }
                    }
                    if ($foundDirection === false) {
                        $coordList[] = $coord;
                    }
                }
                if (count($indexToAssign) == 0) {
                    break;
                }
            }
            if ($globalRetryCount >= self::MAX_GENERATE_RETRY) {
                // Failed to generate, give a default choice
                $choice = self::OFFICE_SINGLE_MIDDLE_CHOICE[0];
            }
        }

        // Assign real office id
        $nbOfficesToUse = self::OFFICE_TILE_COUNT;
        if ($useBanditsExpansion) {
            $nbOfficesToUse += self::OFFICE_TILE_COUNT_EXPANSION_BANDITS;
        }

        $choosenTileIdArray = range(0, $nbOfficesToUse - 1);
        shuffle($choosenTileIdArray);
        $choosenTileIdArray = array_slice($choosenTileIdArray, 0, self::OFFICE_TILE_CHOOSE_COUNT);

        $choosenSingleTileIdArray = [OFFICE_ID_4VP];
        if ($tradingPostExpansion == GAME_OPTION_EXPANSION_TRADING_POST_VALUE_ONLY_EXPANSION) {
            $choosenSingleTileIdArray = OFFICE_IDS_TRADING_POST_EXPANSION;
        } else if ($tradingPostExpansion == GAME_OPTION_EXPANSION_TRADING_POST_VALUE_WITH_BASE) {
            $choosenSingleTileIdArray = array_merge([OFFICE_ID_4VP], OFFICE_IDS_TRADING_POST_EXPANSION);
        }
        shuffle($choosenSingleTileIdArray);

        $this->town = [];
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            $this->town[$x] = [];
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $index = $choice[$x][$y];
                if ($index < 0) {
                    $this->town[$x][$y] = new GWBoomTownTile($x, $y, $choosenSingleTileIdArray[0]);
                } else {
                    $this->town[$x][$y] = new GWBoomTownTile($x, $y, $choosenTileIdArray[$index]);
                }
            }
        }

        $this->save();
    }

    public function load()
    {
        if ($this->town !== null) {
            return;
        }
        $this->town = [];
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            $this->town[$x] = [];
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $this->town[$x][$y] = null;
            }
        }
        $valueArray = self::getObjectListFromDB("SELECT x, y, office_id, player_id, investment_player_id FROM boomtown");
        foreach ($valueArray as $value) {
            $tile = new GWBoomTownTile(
                $value['x'],
                $value['y'],
                $value['office_id'],
                $value['player_id'],
                $value['investment_player_id']
            );
            $this->town[$tile->x][$tile->y] = $tile;
        }
    }

    public function save()
    {
        if ($this->town === null) {
            return;
        }
        self::DbQuery("DELETE FROM boomtown");
        $sql = "INSERT INTO boomtown (x, y, office_id, player_id, investment_player_id) VALUES ";
        $sql_values = [];
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                $playerId = $tile->playerId === null ? 'NULL' : "{$tile->playerId}";
                $investmentPlayerId = $tile->investmentPlayerId === null ? 'NULL' : "{$tile->investmentPlayerId}";
                $sql_values[] = "({$tile->x}, {$tile->y}, {$tile->officeId}, {$playerId}, {$investmentPlayerId})";
            }
        }
        $sql .= implode(',', $sql_values);
        self::DbQuery($sql);
    }

    public function render($page)
    {
        $this->load();
        // Render individual tiles
        $page->begin_block("goldwest_goldwest", "boomtown-tile");
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                $coord = $tile->toClientTopLeft();
                $page->insert_block(
                    "boomtown-tile",
                    array(
                        'POS_X' => $x,
                        'POS_Y' => $y,
                        'TOP' => $coord[1],
                        'LEFT' => $coord[0],
                        'OFFICE_ID' => $tile->toClientOfficeId(),
                    )
                );
            }
        }
        // Build set of double tiles
        $officeSet = [];
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                if ($tile->isSingleTile() || array_key_exists($tile->officeId, $officeSet)) {
                    continue;
                }
                $isVertical = false;
                if (
                    $y + 1 < self::TOWN_SIZE
                    && $tile->officeId == $this->town[$x][$y + 1]->officeId
                ) {
                    $isVertical = true;
                }
                $coord = $tile->toClientTopLeft();
                $office = new GWBoomTownOffice($coord[0], $coord[1], $tile->toClientOfficeId(), $isVertical);
                $officeSet[$tile->officeId] = $office;
            }
        }
        // Render doubles tiles
        $page->begin_block("goldwest_goldwest", "boomtown-office-double");
        foreach ($officeSet as $office) {
            $page->insert_block(
                "boomtown-office-double",
                array(
                    'TOP' => $office->clientY,
                    'LEFT' => $office->clientX,
                    'OFFICE_ID' => $office->clientOfficeId,
                    'VERTICAL_CLASS' => $office->isVertical ? 'vertical' : 'horizontal',
                )
            );
        }
        // Render single tiles
        $page->begin_block("goldwest_goldwest", "boomtown-office-single");
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                if (!$tile->isSingleTile()) {
                    continue;
                }
                $tile = $this->town[$x][$y];
                $coord = $tile->toClientTopLeft();
                $page->insert_block(
                    "boomtown-office-single",
                    array(
                        'TOP' => $coord[1],
                        'LEFT' => $coord[0],
                        'OFFICE_ID' => $tile->toClientOfficeId(),
                    )
                );
            }
        }
    }

    public function getConstants()
    {
        return [
            "TOWN_SIZE" => self::TOWN_SIZE,
        ];
    }

    public function getTown()
    {
        $this->load();
        return $this->town;
    }

    public function canBuyPositions($resources)
    {
        $buyPositions = [];
        $this->load();
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                if ($tile->playerId !== null) {
                    continue;
                }
                $avail = $resources;
                $required = self::POSITION_TO_RESOURCE[$x][$y];
                $avail[$required[0]] -= 1;
                if ($avail[$required[0]] < 0) {
                    continue;
                }
                $avail[$required[1]] -= 1;
                if ($avail[$required[1]] < 0) {
                    continue;
                }
                $buyPositions[] = [$x, $y];
            }
        }
        return $buyPositions;
    }

    public function getAtPosition($x, $y)
    {
        $this->load();
        return $this->town[$x][$y];
    }

    public function getPriceAtPosition($x, $y)
    {
        return self::POSITION_TO_RESOURCE[$x][$y];
    }

    public function freeOffices()
    {
        $freeOffices = [];
        $this->load();
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                if ($tile->playerId === null) {
                    $freeOffices[] = [$x, $y];
                }
            }
        }
        return $freeOffices;
    }

    public function occupiedOfficesByOtherPlayers($playerId)
    {
        $occupiedOffices = [];
        $this->load();
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                if (
                    $tile->playerId !== null
                    && $tile->playerId != $playerId
                    && $tile->investmentPlayerId === null
                ) {
                    $occupiedOffices[] = [$x, $y];
                }
            }
        }
        return $occupiedOffices;
    }

    public function getEndGameScoreForAllPlayers(
        $board,
        $investments,
        $shipping,
        $playerInfoArray,
        $playerIdInfluenceCountMap
    ) {
        $scoreList = [];
        $this->load();
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                foreach ([$tile->playerId, $tile->investmentPlayerId] as $playerId) {
                    $score = $this->getEndGameScore(
                        $board,
                        $investments,
                        $shipping,
                        $playerInfoArray,
                        $playerIdInfluenceCountMap,
                        $tile->officeId,
                        $playerId
                    );
                    if ($score != null) {
                        $score->x = $x;
                        $score->y = $y;
                        $score->playerId = $playerId;
                        $score->officeId = $tile->officeId;
                        $scoreList[] = $score;
                    }
                }
            }
        }
        return $scoreList;
    }

    public function debugFill($playerIdArray)
    {
        $this->load();
        $investX = rand(0, self::TOWN_SIZE - 1);
        $investY = rand(0, self::TOWN_SIZE - 1);
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                $i = array_rand($playerIdArray);
                $playerId = $playerIdArray[$i];
                $tile->playerId = $playerId;
                if ($x == $investX && $y == $investY) {
                    $tile->investmentPlayerId = $playerIdArray[($i + 1) % count($playerIdArray)];
                }
            }
        }
        $this->save();
    }

    private function getEndGameScore(
        $board,
        $investments,
        $shipping,
        $playerInfoArray,
        $playerIdInfluenceCountMap,
        $officeId,
        $playerId
    ) {
        if ($playerId === null) {
            return null;
        }

        switch ($officeId) {
            case OFFICE_ID_DOCKS:
                return $this->getEndGameScoreDocks($board, $playerId);
            case OFFICE_ID_FRONTIER_OFFICE:
                return $this->getEndGameScoreFrontierOffice($board, $playerId);
            case OFFICE_ID_HOMESTEAD_OFFICE:
                return $this->getEndGameScoreHomesteadOffice($playerInfoArray, $playerId);
            case OFFICE_ID_COURTHOUSE:
                return $this->getEndGameScoreCourthouse($playerInfoArray, $playerId);
            case OFFICE_ID_DEEDS_OFFICE:
                return $this->getEndGameScoreDeedsOffice($playerIdInfluenceCountMap, $playerId);
            case OFFICE_ID_DEPOT:
                return $this->getEndGameScoreDepot($board, $playerId);
            case OFFICE_ID_MAYORS_OFFICE:
                return $this->getEndGameScoreMayorsOffice($playerId);
            case OFFICE_ID_SALOON:
                return $this->getEndGameScoreSaloon($investments, $playerId);
            case OFFICE_ID_SHERIFFS_OFFICE:
                return $this->getEndGameScoreSheriffsOffice($board, $playerId);
            case OFFICE_ID_SHIPPING_OFFICE:
                return $this->getEndGameScoreShippingOffice($shipping, $playerId);
            case OFFICE_ID_SURVEYORS_OFFICE:
                return $this->getEndGameScoreSurveyorsOffice($board, $playerId);
            case OFFICE_ID_TOWN_HALL:
                return $this->getEndGameScoreTownHall($board, $playerId);
            case OFFICE_ID_INSURANCE_OFFICE:
                return $this->getEndGameScoreInsuranceOffice($board, $playerId);
            case OFFICE_ID_HOOSEGOW:
                return $this->getEndGameScoreHoosegow($playerInfoArray);
        }
        // This can happen for single tiles
        return null;
    }

    private function getEndGameScoreDocks($board, $playerId)
    {
        $score = new GWBoomTownScore();
        foreach ($board->getTerrainAdjacentWater($playerId) as $terrain) {
            if ($terrain->spotStatus == SPOT_STATUS_CAMP) {
                $score->score += 1;
                $score->ids[] = $terrain->id();
            } else if ($terrain->spotStatus == SPOT_STATUS_SETTLEMENT) {
                $score->score += 2;
                $score->ids[] = $terrain->id();
            }
        }
        return $score;
    }

    private function getEndGameScoreFrontierOffice($board, $playerId)
    {
        $score = new GWBoomTownScore();
        foreach ($board->getTerrainAdjacentBoardEdge($playerId) as $terrain) {
            if ($terrain->spotStatus == SPOT_STATUS_CAMP || $terrain->spotStatus == SPOT_STATUS_SETTLEMENT) {
                $score->score += 1;
                $score->ids[] = $terrain->id();
            }
        }
        return $score;
    }

    private function getEndGameScoreHomesteadOffice($playerInfoArray, $playerId)
    {
        $score = new GWBoomTownScore();
        $minimumInfluence = PHP_INT_MAX;
        foreach (TERRAIN_TYPES_BUILDABLE as $terrainType) {
            $minimumInfluence = min($minimumInfluence, $playerInfoArray[$playerId]->totalInfluence($terrainType));
        }
        if ($minimumInfluence != PHP_INT_MAX) {
            $score->score = $minimumInfluence * 2;
        }
        return $score;
    }

    private function getEndGameScoreCourthouse($playerInfoArray, $playerId)
    {
        $score = new GWBoomTownScore();
        $maximumInfluence = 0;
        $maximumToken = 0;
        foreach (TERRAIN_TYPES_BUILDABLE as $terrainType) {
            $newInfluence = $playerInfoArray[$playerId]->totalInfluence($terrainType);
            if ($newInfluence >= $maximumInfluence) {
                $maximumInfluence = $newInfluence;
                $maximumToken = max($maximumToken, $playerInfoArray[$playerId]->influence($terrainType));
            }
        }
        $score->score = $maximumToken;
        return $score;
    }

    private function getEndGameScoreDeedsOffice($playerIdInfluenceCountMap, $playerId)
    {
        $score = new GWBoomTownScore();
        $score->score = $playerIdInfluenceCountMap[$playerId] * 2;
        return $score;
    }

    private function getEndGameScoreDepot($board, $playerId)
    {
        $score = new GWBoomTownScore();
        $buildingGroupPerPlayers = $board->getBuildingGroupPerPlayers();
        $groups = $buildingGroupPerPlayers[$playerId];
        if (count($groups) >= 2) {
            $score->score = count($groups[1]) * 2;
            $score->ids = $groups[1];
        }
        return $score;
    }

    private function getEndGameScoreMayorsOffice($playerId)
    {
        $score = new GWBoomTownScore();
        $count = 0;
        for ($x = 0; $x < self::TOWN_SIZE; ++$x) {
            for ($y = 0; $y < self::TOWN_SIZE; ++$y) {
                $tile = $this->town[$x][$y];
                if ($tile->playerId !== null && $tile->playerId == $playerId) {
                    ++$count;
                }
                if ($tile->investmentPlayerId !== null && $tile->investmentPlayerId == $playerId) {
                    ++$count;
                }
            }
        }
        $score->score += intdiv($count, 2) * 3;
        return $score;
    }

    private function getEndGameScoreSaloon($investments, $playerId)
    {
        $score = new GWBoomTownScore();
        $cards = $investments->getCardsByPlayers();
        if (array_key_exists($playerId, $cards)) {
            $score->score += count($cards[$playerId]) * 2;
        }
        return $score;
    }

    private function getEndGameScoreSheriffsOffice($board, $playerId)
    {
        $score = new GWBoomTownScore();
        foreach ($board->getTerrainAdjacentToOtherPlayers($playerId) as $terrain) {
            if ($terrain->spotStatus == SPOT_STATUS_CAMP || $terrain->spotStatus == SPOT_STATUS_SETTLEMENT) {
                $score->score += 1;
                $score->ids[] = $terrain->id();
            }
        }
        return $score;
    }

    private function getEndGameScoreShippingOffice($shipping, $playerId)
    {
        $score = new GWBoomTownScore();
        foreach ($shipping->getBonus() as $bonus) {
            if ($bonus->playerId == $playerId) {
                $score->score += 2;
            }
        }
        return $score;
    }

    private function getEndGameScoreTownHall($board, $playerId)
    {
        $score = new GWBoomTownScore();
        foreach ($board->getTerrainForPlayer($playerId) as $terrain) {
            if ($terrain->spotStatus == SPOT_STATUS_SETTLEMENT) {
                $score->score += 1;
                $score->ids[] = $terrain->id();
            }
        }
        return $score;
    }

    private function getEndGameScoreSurveyorsOffice($board, $playerId)
    {
        $score = new GWBoomTownScore();
        foreach ($board->getTerrainLongestLine($playerId) as $terrain) {
            $score->score += 1;
            $score->ids[] = $terrain->id();
        }
        return $score;
    }

    private function getEndGameScoreInsuranceOffice($board, $playerId)
    {
        $score = new GWBoomTownScore();
        foreach ($board->getTerrainAdjacentToLooted($playerId) as $terrain) {
            if ($terrain->spotStatus == SPOT_STATUS_CAMP || $terrain->spotStatus == SPOT_STATUS_SETTLEMENT) {
                $score->score += 1;
                $score->ids[] = $terrain->id();
            }
        }
        return $score;
    }

    private function getEndGameScoreHoosegow($playerInfoArray)
    {
        $score = new GWBoomTownScore();
        foreach ($playerInfoArray as $info) {
            $score->score += $info->usedLootCamp();
        }
        return $score;
    }
}

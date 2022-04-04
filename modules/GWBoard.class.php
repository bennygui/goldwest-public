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
require_once("GWBoardPlayerInfo.class.php");
require_once("GWPlayerBoard.class.php");

const SPOT_STATUS_TOKEN_EXCLUDED = 'EX';
const SPOT_STATUS_TOKEN_INVISIBLE = 'IN';
const SPOT_STATUS_TOKEN_VISIBLE = 'VI';
const SPOT_STATUS_CAMP = 'CA';
const SPOT_STATUS_SETTLEMENT = 'SE';
const SPOT_STATUS_LOOT = 'LO';

const MINING_TOKEN_COUNT_PER_TYPE = 14;
const MINING_TOKEN_RESOURCES = [
    TERRAIN_TYPE_GOLD => [
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_STONE, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_STONE, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_COPPER],
    ],
    TERRAIN_TYPE_SILVER => [
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_STONE, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_STONE, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_STONE],
    ],
    TERRAIN_TYPE_COPPER => [
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_STONE, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_STONE, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_STONE],
    ],
    TERRAIN_TYPE_WOOD => [
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_WOOD, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_WOOD, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_COPPER],
        [RESOURCE_TYPE_STONE, RESOURCE_TYPE_STONE, RESOURCE_TYPE_STONE],
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_SILVER],
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_STONE],
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_WOOD, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_STONE, RESOURCE_TYPE_STONE, RESOURCE_TYPE_STONE],
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_WOOD, RESOURCE_TYPE_STONE],
        [RESOURCE_TYPE_STONE, RESOURCE_TYPE_STONE, RESOURCE_TYPE_STONE],
        [RESOURCE_TYPE_STONE, RESOURCE_TYPE_STONE, RESOURCE_TYPE_WOOD],
        [RESOURCE_TYPE_STONE, RESOURCE_TYPE_GOLD],
        [RESOURCE_TYPE_WOOD, RESOURCE_TYPE_STONE],
    ],
];


class GWBoardLargeTile
{
    public $tile;
    public $rotation;

    public function __construct(int $tile, int $rotation = 0)
    {
        $this->tile = $tile;
        $this->rotation = $rotation;
    }
}

class GWBoardTile
{
    public $x;
    public $y;
    public $terrainType;
    public $spotStatus;
    public $miningTokenId;
    public $playerId;
    public $investmentViewPlayerId;

    public function __construct(
        int $x,
        int $y,
        ?string $terrainType,
        string $spotStatus = SPOT_STATUS_TOKEN_EXCLUDED,
        ?int $miningTokenId = null,
        ?int $playerId = null,
        ?int $investmentViewPlayerId = null
    ) {
        $this->x = $x;
        $this->y = $y;
        $this->terrainType = $terrainType;
        $this->spotStatus = $spotStatus;
        $this->miningTokenId = $miningTokenId;
        $this->playerId = $playerId;
        $this->investmentViewPlayerId = $investmentViewPlayerId;
    }

    public function id()
    {
        return GWBoard::coordToId($this->x, $this->y);
    }

    public function miningTokenTerrainId()
    {
        return TERRAIN_TYPES_TO_ID[$this->terrainType];
    }

    public function toClientTopLeft()
    {
        $top = ($this->y - GWBoard::MAX_BOARD_Y) * -1 * (GWBoard::TERRAIN_TILE_SIZE + GWBoard::TERRAIN_TILE_TOP_PADDING);
        if (($this->x % 2) != 0) {
            $top += GWBoard::TERRAIN_TILE_SIZE / 2;
        } else {
            $top -= 8;
        }
        return [
            (int)($top + GWBoard::TERRAIN_GLOBAL_TOP_PADDING),
            (int)(($this->x - GWBoard::MIN_BOARD_X) * (GWBoard::TERRAIN_TILE_SIZE + GWBoard::TERRAIN_TILE_LEFT_PADDING) + GWBoard::TERRAIN_GLOBAL_LEFT_PADDING),
        ];
    }
}

class GWBoard extends APP_DbObject
{
    // Coordinates
    // Board uses “odd-q” vertical layout (shoves odd columns down)
    // See reference: https://www.redblobgames.com/grids/hexagons/
    public const MIN_BOARD_X = -6;
    public const MAX_BOARD_X = 7;
    public const MIN_BOARD_Y = -3;
    public const MAX_BOARD_Y = 3;
    private const BOARD_RANGES = [
        -6 => [0, 1],
        -5 => [0, 2],
        -4 => [-2, 1],
        -3 => [-2, 2],
        -2 => [-2, 2],
        -1 => [-2, 2],
        0 => [-3, 2],
        1 => [-2, 3],
        2 => [-2, 2],
        3 => [-2, 2],
        4 => [-2, 2],
        5 => [-1, 2],
        6 => [-2, 0],
        7 => [-1, 0],
    ];
    private const BOARD_WATER = [
        -1 => 0,
        0 => 0,
        1 => 0,
        2 => 0,
    ];

    // Large tiles used for generation: each has 6 terrain around and one in the middle (index 6).
    // The middle cannot move when rotated.
    private const LARGE_TILE_TERRAIN_COUNT = 7;
    private const LARGE_TILE_PLAYER_COUNT_EXCLUDE = [
        2 => [0, 4],
        3 =>  [],
        4 =>  [],
    ];
    private const LARGE_TILE_TERRAIN = [
        0 => [
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_SILVER,
        ],
        1 => [
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_WOOD,
        ],
        2 => [
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_GOLD,
        ],
        3 => [
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_COPPER,
        ],
        4 => [
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_COPPER,
        ],
        5 => [
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_SILVER,
        ],
        6 => [
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_WOOD,
            TERRAIN_TYPE_GOLD,
        ],
        7 => [
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_GOLD,
            TERRAIN_TYPE_SILVER,
            TERRAIN_TYPE_COPPER,
            TERRAIN_TYPE_WOOD,
        ],
    ];
    private const LARGE_TILE_COORD = [
        0 => [
            [-4, 1],
            [-5, 2],
            [-6, 1],
            [-6, 0],
            [-5, 0],
            [-4, 0],
            [-5, 1],
        ],
        1 => [
            [-1, 2],
            [-2, 2],
            [-3, 2],
            [-3, 1],
            [-2, 0],
            [-1, 1],
            [-2, 1],
        ],
        2 => [
            [2, 2],
            [1, 3],
            [0, 2],
            [0, 1],
            [1, 1],
            [2, 1],
            [1, 2],
        ],
        3 => [
            [5, 2],
            [4, 2],
            [3, 2],
            [3, 1],
            [4, 0],
            [5, 1],
            [4, 1],
        ],
        4 => [
            [7, 0],
            [6, 0],
            [5, 0],
            [5, -1],
            [6, -2],
            [7, -1],
            [6, -1],
        ],
        5 => [
            [4, -1],
            [3, 0],
            [2, -1],
            [2, -2],
            [3, -2],
            [4, -2],
            [3, -1],
        ],
        6 => [
            [1, -1],
            [0, -1],
            [-1, -1],
            [-1, -2],
            [0, -3],
            [1, -2],
            [0, -2],
        ],
        7 => [
            [-2, -1],
            [-3, 0],
            [-4, -1],
            [-4, -2],
            [-3, -2],
            [-2, -2],
            [-3, -1],
        ],
    ];

    // Display sizes
    public const TERRAIN_TILE_SIZE = 60;
    public const TERRAIN_TILE_TOP_PADDING = 20.1;
    public const TERRAIN_TILE_LEFT_PADDING = 9;
    public const TERRAIN_GLOBAL_TOP_PADDING = 103;
    public const TERRAIN_GLOBAL_LEFT_PADDING = 108;

    private $board = null;
    private $boardLargeTile = null;

    public static function coordToId(int $x, int $y)
    {
        $prex = $x < 0 ? "m" : "p";
        $ax = abs($x);

        $prey = $y < 0 ? "m" : "p";
        $ay = abs($y);
        return "${prex}_${ax}_${prey}_${ay}";
    }

    public static function idToCoord(string $id)
    {
        $parts = explode("_", $id);
        $x = (int)($parts[1]);
        if ($parts[0] == "m") {
            $x *= -1;
        }
        $y = (int)($parts[3]);
        if ($parts[2] == "m") {
            $y *= -1;
        }
        return [$x, $y];
    }

    const HEX_DIRECTION_COUNT = 6;
    const HEX_NEIGHBOR_DIRECTIONS = [
        [
            [+1,  0], [+1, -1], [0, -1], [-1, -1], [-1,  0], [0, +1]
        ],
        [
            [+1, +1], [+1,  0], [0, -1], [-1,  0], [-1, +1], [0, +1]
        ],
    ];
    public static function coordNeighbor(int $x, int $y, int $direction)
    {
        $even = ($x & 1);
        $dir = self::HEX_NEIGHBOR_DIRECTIONS[$even][$direction];
        return [$x + $dir[0], $y - $dir[1]];
    }

    public function __construct()
    {
    }

    public function generate($playerCount)
    {
        $this->board = [];
        // Fill with no terrain, except for water
        for ($x = self::MIN_BOARD_X; $x <= self::MAX_BOARD_X; ++$x) {
            for ($y = self::MIN_BOARD_Y; $y <= self::MAX_BOARD_Y; ++$y) {
                if ($y < self::BOARD_RANGES[$x][0] || $y > self::BOARD_RANGES[$x][1])
                    continue;
                $terrainType = null;
                if (array_key_exists($x, self::BOARD_WATER) && self::BOARD_WATER[$x] == $y) {
                    $terrainType = TERRAIN_TYPE_WATER;
                }
                $terrain = new GWBoardTile($x, $y, $terrainType);
                $this->board[$terrain->id()] = $terrain;
            }
        }
        // Assign terrain type based on the large tiles
        $this->boardLargeTile = [];
        foreach (range(0, count(self::LARGE_TILE_TERRAIN) - 1) as $tile) {
            $this->boardLargeTile[] = new GWBoardLargeTile($tile);
        }
        shuffle($this->boardLargeTile);
        // Remove unused large tiles based on player count
        foreach ($this->boardLargeTile as $pos => $largeTile) {
            if (array_search($pos, self::LARGE_TILE_PLAYER_COUNT_EXCLUDE[$playerCount]) !== false) {
                unset($this->boardLargeTile[$pos]);
            }
        }
        foreach ($this->boardLargeTile as $pos => $largeTile) {
            $largeTile->rotation = random_int(0, self::LARGE_TILE_TERRAIN_COUNT - 2);
            $terrainTypeArray = [];
            // Get terrain type with largeTile->rotation, excluding the middle index
            // which cannot rotate
            for ($typeIndex = 0; $typeIndex <= self::LARGE_TILE_TERRAIN_COUNT - 2; ++$typeIndex) {
                $terrainTypeArray[] = self::LARGE_TILE_TERRAIN[$largeTile->tile][($typeIndex + $largeTile->rotation) % (self::LARGE_TILE_TERRAIN_COUNT - 1)];
            }
            // Center does not rotate
            $terrainTypeArray[] = self::LARGE_TILE_TERRAIN[$largeTile->tile][self::LARGE_TILE_TERRAIN_COUNT - 1];
            foreach ($terrainTypeArray as $index => $type) {
                $coord = self::LARGE_TILE_COORD[$pos][$index];
                $id = self::coordToId($coord[0], $coord[1]);
                $this->board[$id]->terrainType = $type;
                $this->board[$id]->spotStatus = SPOT_STATUS_TOKEN_INVISIBLE;
            }
        }
        // Assign mining token based on terrain type
        $resourceIdArray = [];
        foreach (MINING_TOKEN_RESOURCES as $terrainType => $resouceArray) {
            $resourceIdArray[$terrainType] = range(0, MINING_TOKEN_COUNT_PER_TYPE - 1);
            shuffle($resourceIdArray[$terrainType]);
        }
        foreach ($this->board as $tile) {
            if (!array_key_exists($tile->terrainType, $resourceIdArray)) {
                continue;
            }
            $tile->miningTokenId = array_pop($resourceIdArray[$tile->terrainType]);
        }
        // Flip mining tokens around lake
        foreach (self::BOARD_WATER as $x => $y) {
            for ($dir = 0; $dir < self::HEX_DIRECTION_COUNT; ++$dir) {
                $neighbor = self::coordNeighbor($x, $y, $dir);
                $id = self::coordToId($neighbor[0], $neighbor[1]);
                $tile = $this->board[$id];
                if ($tile->terrainType != TERRAIN_TYPE_WATER) {
                    $tile->spotStatus = SPOT_STATUS_TOKEN_VISIBLE;
                }
            }
        }

        $this->save();
    }

    public function load()
    {
        if ($this->board !== null) {
            return;
        }
        $this->board = [];
        $valueArray = self::getObjectListFromDB("SELECT x, y, terrain_type, spot_status, mining_token_id, player_id, investment_view_player_id FROM board");
        foreach ($valueArray as $value) {
            $terrain = new GWBoardTile(
                $value['x'],
                $value['y'],
                $value['terrain_type'],
                $value['spot_status'],
                $value['mining_token_id'],
                $value['player_id'],
                $value['investment_view_player_id']
            );
            $this->board[$terrain->id()] = $terrain;
        }
        $this->boardLargeTile = [];
        $valueArray = self::getObjectListFromDB("SELECT pos, tile, rotation FROM board_large_tile");
        foreach ($valueArray as $value) {
            $this->boardLargeTile[$value['pos']] = new GWBoardLargeTile(
                $value['tile'],
                $value['rotation']
            );
        }
    }

    public function save()
    {
        if ($this->board === null) {
            return;
        }
        self::DbQuery("DELETE FROM board");
        $sql = "INSERT INTO board (x, y, terrain_type, spot_status, mining_token_id, player_id, investment_view_player_id) VALUES ";
        $sql_values = [];
        foreach ($this->board as $terrain) {
            $tokenId = $terrain->miningTokenId === null ? 'NULL' : "'{$terrain->miningTokenId}'";
            $playerId = $terrain->playerId === null ? 'NULL' : "{$terrain->playerId}";
            $investmentViewPlayerId = $terrain->investmentViewPlayerId === null ? 'NULL' : "{$terrain->investmentViewPlayerId}";
            $sql_values[] = "({$terrain->x}, {$terrain->y}, '{$terrain->terrainType}', '{$terrain->spotStatus}', $tokenId, $playerId, $investmentViewPlayerId)";
        }
        $sql .= implode(',', $sql_values);
        self::DbQuery($sql);

        self::DbQuery("DELETE FROM board_large_tile");
        $sql = "INSERT INTO board_large_tile (pos, tile, rotation) VALUES ";
        $sql_values = [];
        foreach ($this->boardLargeTile as $pos => $largeTile) {
            $sql_values[] = "({$pos}, {$largeTile->tile}, {$largeTile->rotation})";
        }
        $sql .= implode(',', $sql_values);
        self::DbQuery($sql);
    }

    public function render($page)
    {
        $this->load();
        // Large rotatable tiles
        $page->begin_block("goldwest_goldwest", "terrain-large-tile");
        foreach ($this->boardLargeTile as $pos => $largeTile) {
            $page->insert_block(
                "terrain-large-tile",
                array(
                    'POS' => $pos,
                    'TILE' => $largeTile->tile,
                    'ROTATION' => $largeTile->rotation,
                )
            );
        }
        // Small divs over tiles and player camp and settlement
        $page->begin_block("goldwest_goldwest", "terrain-small-tile");
        foreach ($this->board as $id => $terrain) {
            $coord = $terrain->toClientTopLeft();
            $page->insert_block(
                "terrain-small-tile",
                array(
                    'TOP' => $coord[0],
                    'LEFT' => $coord[1],
                    'TERRAIN_ID' => $terrain->id(),
                    'TERRAIN_TYPE' => $terrain->terrainType ?? 'empty',
                )
            );
        }
    }

    public function getInvisibleTokenClient()
    {
        $this->load();
        $tokens = [];
        foreach ($this->board as $id => $terrain) {
            if ($terrain->spotStatus != SPOT_STATUS_TOKEN_INVISIBLE) {
                continue;
            }
            $tokens[] = [
                'terrainId' => $terrain->id(),
                'terrainType' => $terrain->terrainType,
            ];
        }
        return $tokens;
    }

    public function getVisibleTokenClient()
    {
        $this->load();
        $tokens = [];
        foreach ($this->board as $id => $terrain) {
            if ($terrain->spotStatus != SPOT_STATUS_TOKEN_VISIBLE) {
                continue;
            }
            $tokens[] = [
                'terrainId' => $terrain->id(),
                'terrainType' => $terrain->terrainType,
                'miningTokenId' => $terrain->miningTokenId,
            ];
        }
        return $tokens;
    }

    public function getCampClient()
    {
        $this->load();
        $tokens = [];
        foreach ($this->board as $id => $terrain) {
            if ($terrain->spotStatus != SPOT_STATUS_CAMP && $terrain->spotStatus != SPOT_STATUS_SETTLEMENT) {
                continue;
            }
            $tokens[$id] = $terrain->playerId;
        }
        return $tokens;
    }

    public function getSettlementClient()
    {
        $this->load();
        $tokens = [];
        foreach ($this->board as $id => $terrain) {
            if ($terrain->spotStatus != SPOT_STATUS_SETTLEMENT) {
                continue;
            }
            $tokens[$id] = $terrain->playerId;
        }
        return $tokens;
    }

    public function getBoardPlayerInfo($playerIdArray, $investments)
    {
        $this->load();
        $playerInfoArray = [];
        foreach ($playerIdArray as $playerId) {
            $playerInfoArray[$playerId] = new GWBoardPlayerInfo($playerId);
        }
        foreach ($this->board as $id => $terrain) {
            if (
                $terrain->terrainType == TERRAIN_TYPE_WATER
                || $terrain->playerId === null
            ) {
                continue;
            }
            switch ($terrain->spotStatus) {
                case SPOT_STATUS_CAMP:
                    $playerInfoArray[$terrain->playerId]->addCamp($terrain->terrainType);
                    break;
                case SPOT_STATUS_SETTLEMENT:
                    $playerInfoArray[$terrain->playerId]->addSettlement($terrain->terrainType);
                    break;
                case SPOT_STATUS_LOOT:
                    $playerInfoArray[$terrain->playerId]->addLoot();
                    break;
            }
        }
        foreach ($investments->getAddedInfuenceByPlayers($playerIdArray) as $playerId => $terrainArray) {
            foreach ($terrainArray as $terrainType => $influenceCount) {
                for ($i = 0; $i < $influenceCount; ++$i) {
                    $playerInfoArray[$playerId]->addInfluence($terrainType);
                }
            }
        }
        return $playerInfoArray;
    }

    public function getBoardPlayerInfoClient($playerIdArray, $investments)
    {
        $playerInfoArray = [];
        foreach ($this->getBoardPlayerInfo($playerIdArray, $investments) as $playerId => $info) {
            $playerInfoArray[$playerId] = $info->toClient();
        }
        return $playerInfoArray;
    }

    public function getVisibleTokenId()
    {
        $this->load();
        $ids = [];
        foreach ($this->board as $id => $terrain) {
            if ($terrain->spotStatus == SPOT_STATUS_TOKEN_VISIBLE) {
                $ids[] = $terrain->id();
            }
        }
        return $ids;
    }

    public function canTokenBeTaken($x, $y)
    {
        $this->load();
        $id = self::coordToId($x, $y);
        $terrain = $this->board[$id];
        if ($terrain === null) {
            return false;
        }
        return ($terrain->spotStatus == SPOT_STATUS_TOKEN_VISIBLE);
    }

    public function giveTokenToPlayer($x, $y, $playerId, $newSpotStatus)
    {
        $this->load();
        $id = self::coordToId($x, $y);
        $terrain = $this->board[$id];
        if ($terrain === null) {
            return null;
        }
        $terrain->spotStatus = $newSpotStatus;
        $terrain->playerId = $playerId;
        $this->save();
        return $terrain;
    }

    public function revealMiningTokens()
    {
        $revealedTerrain = [];
        $this->load();
        foreach ($this->board as $terrain) {
            if (
                $terrain->spotStatus == SPOT_STATUS_LOOT
                || $terrain->spotStatus == SPOT_STATUS_CAMP
                || $terrain->spotStatus == SPOT_STATUS_SETTLEMENT
            ) {
                for ($dir = 0; $dir < self::HEX_DIRECTION_COUNT; ++$dir) {
                    $neighbor = self::coordNeighbor($terrain->x, $terrain->y, $dir);
                    $id = self::coordToId($neighbor[0], $neighbor[1]);
                    if (array_key_exists($id, $this->board)) {
                        $neighborTerrain = $this->board[$id];
                        if (
                            $neighborTerrain->terrainType != TERRAIN_TYPE_WATER &&
                            $neighborTerrain->spotStatus == SPOT_STATUS_TOKEN_INVISIBLE
                        ) {
                            $neighborTerrain->spotStatus = SPOT_STATUS_TOKEN_VISIBLE;
                            $revealedTerrain[] = $neighborTerrain;
                        }
                    }
                }
            }
        }
        $this->save();
        return $revealedTerrain;
    }

    public function getInvisibleTokenIdNotViewedByPlayerId($playerId)
    {
        $idList = [];
        $this->load();
        foreach ($this->board as $id => $terrain) {
            if (
                $terrain->spotStatus == SPOT_STATUS_TOKEN_INVISIBLE
                && ($terrain->investmentViewPlayerId === null
                    || $terrain->investmentViewPlayerId != $playerId)
            ) {
                $idList[] = $id;
            }
        }
        return $idList;
    }

    public function canTokenBeViewed($x, $y)
    {
        $this->load();
        $id = self::coordToId($x, $y);
        $terrain = $this->board[$id];
        if ($terrain === null) {
            return false;
        }
        if (
            $terrain->spotStatus == SPOT_STATUS_TOKEN_INVISIBLE
            && $terrain->investmentViewPlayerId === null
        ) {
            return true;
        }
        return false;
    }

    public function setTokenViewedByPlayer($x, $y, $playerId)
    {
        $this->load();
        $id = self::coordToId($x, $y);
        $terrain = $this->board[$id];
        if ($terrain === null) {
            return null;
        }
        $terrain->investmentViewPlayerId = $playerId;
        $this->save();
        return $terrain;
    }

    public function getViewedMiningTokenClient($playerId)
    {
        $tokens = [];
        $this->load();
        foreach ($this->board as $id => $terrain) {
            if (
                $terrain->spotStatus == SPOT_STATUS_TOKEN_INVISIBLE
                && $terrain->investmentViewPlayerId !== null
                && $terrain->investmentViewPlayerId == $playerId
            ) {
                $tokens[] = [
                    'terrainType' => $terrain->terrainType,
                    'miningTokenId' => $terrain->miningTokenId,
                    'id' => $id,
                ];
            }
        }
        return $tokens;
    }

    public function getCampByPlayerId($playerId)
    {
        $ids = [];
        $this->load();
        foreach ($this->board as $id => $terrain) {
            if (
                $terrain->spotStatus == SPOT_STATUS_CAMP
                && $terrain->playerId !== null
                && $terrain->playerId == $playerId
            ) {
                $ids[] = $id;
            }
        }
        return $ids;
    }

    public function upgradeToSettlement($tokenId)
    {
        $this->load();
        $terrain = $this->board[$tokenId];
        if ($terrain === null) {
            return null;
        }
        // This should not append
        if ($terrain->spotStatus != SPOT_STATUS_CAMP) {
            return null;
        }
        $terrain->spotStatus = SPOT_STATUS_SETTLEMENT;
        $this->save();
        return $terrain;
    }

    public function getBuildingGroupPerPlayers()
    {
        $this->load();
        $groupPerPlayers = [];
        // Group building per players
        $idPerPlayers = [];
        foreach ($this->board as $id => $terrain) {
            if ($terrain->playerId === null) {
                continue;
            }
            if ($terrain->spotStatus != SPOT_STATUS_CAMP && $terrain->spotStatus != SPOT_STATUS_SETTLEMENT) {
                continue;
            }
            $idPerPlayers[$terrain->playerId][] = $id;
        }
        foreach ($idPerPlayers as $playerId => $ids) {
            $groups = $this->createBuildingGroup($playerId, $ids);
            foreach ($groups as $group) {
                $groupPerPlayers[$playerId][] = $group;
            }
            usort($groupPerPlayers[$playerId], function (&$a, &$b) {
                return (count($b) <=> count($a));
            });
        }
        return $groupPerPlayers;
    }

    public function getTerrainAdjacentWater($playerId)
    {
        $allTerrain = [];
        $this->load();
        foreach ($this->board as $terrain) {
            if ($terrain->playerId != $playerId) {
                continue;
            }
            for ($dir = 0; $dir < self::HEX_DIRECTION_COUNT; ++$dir) {
                $neighbor = self::coordNeighbor($terrain->x, $terrain->y, $dir);
                $id = self::coordToId($neighbor[0], $neighbor[1]);
                if (!array_key_exists($id, $this->board)) {
                    continue;
                }
                if ($this->board[$id]->terrainType == TERRAIN_TYPE_WATER) {
                    $allTerrain[] = $terrain;
                    break;
                }
            }
        }
        return $allTerrain;
    }

    public function getTerrainAdjacentBoardEdge($playerId)
    {
        $allTerrain = [];
        $this->load();
        foreach ($this->board as $terrain) {
            if ($terrain->playerId != $playerId) {
                continue;
            }
            for ($dir = 0; $dir < self::HEX_DIRECTION_COUNT; ++$dir) {
                $neighbor = self::coordNeighbor($terrain->x, $terrain->y, $dir);
                $id = self::coordToId($neighbor[0], $neighbor[1]);
                // If there are no hex, it's on the edge
                if (!array_key_exists($id, $this->board)) {
                    $allTerrain[] = $terrain;
                    break;
                }
                // If there is an excluded hex, except for water, it's on the edge.
                // This can happen in a two player game.
                if (
                    $this->board[$id]->spotStatus == SPOT_STATUS_TOKEN_EXCLUDED
                    && $this->board[$id]->terrainType != TERRAIN_TYPE_WATER
                ) {
                    $allTerrain[] = $terrain;
                    break;
                }
            }
        }
        return $allTerrain;
    }

    public function getTerrainAdjacentToOtherPlayers($playerId)
    {
        $allTerrain = [];
        $this->load();
        foreach ($this->board as $terrain) {
            if ($terrain->playerId != $playerId) {
                continue;
            }
            for ($dir = 0; $dir < self::HEX_DIRECTION_COUNT; ++$dir) {
                $neighbor = self::coordNeighbor($terrain->x, $terrain->y, $dir);
                $id = self::coordToId($neighbor[0], $neighbor[1]);
                if (!array_key_exists($id, $this->board)) {
                    continue;
                }
                if (
                    $this->board[$id]->playerId !== null
                    && $this->board[$id]->playerId != $playerId
                    && ($this->board[$id]->spotStatus == SPOT_STATUS_CAMP
                        || $this->board[$id]->spotStatus == SPOT_STATUS_SETTLEMENT)
                ) {
                    $allTerrain[] = $terrain;
                    break;
                }
            }
        }
        return $allTerrain;
    }

    public function getTerrainAdjacentToLooted($playerId)
    {
        $allTerrain = [];
        $this->load();
        foreach ($this->board as $terrain) {
            if ($terrain->playerId != $playerId) {
                continue;
            }
            for ($dir = 0; $dir < self::HEX_DIRECTION_COUNT; ++$dir) {
                $neighbor = self::coordNeighbor($terrain->x, $terrain->y, $dir);
                $id = self::coordToId($neighbor[0], $neighbor[1]);
                if (!array_key_exists($id, $this->board)) {
                    continue;
                }
                if ($this->board[$id]->spotStatus == SPOT_STATUS_LOOT) {
                    $allTerrain[] = $terrain;
                    break;
                }
            }
        }
        return $allTerrain;
    }

    public function getTerrainForPlayer($playerId)
    {
        $terrainList = [];
        $this->load();
        foreach ($this->board as $id => $terrain) {
            if (
                $terrain->playerId !== null
                && $terrain->playerId == $playerId
            ) {
                $terrainList[] = $terrain;
            }
        }
        return $terrainList;
    }

    public function getTerrainLongestLine($playerId)
    {
        $longestLine = [];
        $this->load();
        foreach ($this->board as $terrain) {
            if (
                $terrain->playerId == $playerId
                && ($terrain->spotStatus == SPOT_STATUS_CAMP
                    || $terrain->spotStatus == SPOT_STATUS_SETTLEMENT)
            ) {
                // Only check half of the directions since a line in the opposite
                // direction is the same line
                for ($dir = 0; $dir < intdiv(self::HEX_DIRECTION_COUNT, 2); ++$dir) {
                    $newLine = $this->getTerrainLineDirection($playerId, $terrain, $dir);
                    if (count($newLine) > count($longestLine)) {
                        $longestLine = $newLine;
                    }
                }
            }
        }
        return $longestLine;
    }

    private function getTerrainLineDirection($playerId, $terrain, $direction)
    {
        $line = [$terrain];
        // Follow the line in the provided direction and then in the opposite
        // direction to find all the terrain in the line
        $directionList = [
            $direction,
            ($direction + intdiv(self::HEX_DIRECTION_COUNT, 2)) % self::HEX_DIRECTION_COUNT
        ];
        foreach ($directionList as $dir) {
            $lastTerrain = $terrain;
            while (true) {
                $neighbor = self::coordNeighbor($lastTerrain->x, $lastTerrain->y, $dir);
                $id = self::coordToId($neighbor[0], $neighbor[1]);
                if (!array_key_exists($id, $this->board)) {
                    break;
                }
                $newTerrain = $this->board[$id];
                if (
                    $newTerrain->playerId == $playerId
                    && ($newTerrain->spotStatus == SPOT_STATUS_CAMP
                        || $newTerrain->spotStatus == SPOT_STATUS_SETTLEMENT)
                ) {
                    $line[] = $newTerrain;
                }
                $lastTerrain = $newTerrain;
            }
        }
        return $line;
    }

    public function debugFill($playerIdArray)
    {
        $this->load();
        $campCount = GWPlayerBoard::CAMP_COUNT_PER_PLAYER_COUNT[count($playerIdArray)];
        for ($i = 0; $i < $campCount; ++$i) {
            foreach ($playerIdArray as $playerId) {
                $id = array_rand($this->board);
                while (
                    $this->board[$id]->spotStatus != SPOT_STATUS_TOKEN_INVISIBLE
                    && $this->board[$id]->spotStatus != SPOT_STATUS_TOKEN_VISIBLE
                ) {
                    $id = array_rand($this->board);
                }
                $this->board[$id]->playerId = $playerId;
                $status = [SPOT_STATUS_CAMP, SPOT_STATUS_SETTLEMENT, SPOT_STATUS_LOOT];
                shuffle($status);
                $this->board[$id]->spotStatus = $status[0];
            }
        }
        $this->revealMiningTokens();
        $this->save();
    }

    private function createBuildingGroup($playerId, $ids)
    {
        $allGroups = [];
        while (!empty($ids)) {
            $id = array_shift($ids);
            $coord = self::idToCoord($id);
            $x = $coord[0];
            $y = $coord[1];
            $group = [$id];
            $this->createBuildingGroupNeighbor($playerId, $x, $y, $group, $ids);
            $allGroups[] = $group;
        }
        return $allGroups;
    }

    private function createBuildingGroupNeighbor($playerId, $x, $y, &$group, &$ids)
    {
        for ($dir = 0; $dir < self::HEX_DIRECTION_COUNT; ++$dir) {
            $neighbor = self::coordNeighbor($x, $y, $dir);
            $id = self::coordToId($neighbor[0], $neighbor[1]);
            if (!array_key_exists($id, $this->board)) {
                continue;
            }
            $neighborTerrain = $this->board[$id];
            if ($neighborTerrain->playerId != $playerId) {
                continue;
            }
            if ($neighborTerrain->spotStatus != SPOT_STATUS_CAMP  && $neighborTerrain->spotStatus != SPOT_STATUS_SETTLEMENT) {
                continue;
            }
            $idIndex = array_search($id, $ids);
            if ($idIndex === false) {
                continue;
            }
            array_splice($ids, $idIndex, 1);
            $group[] = $id;
            $this->createBuildingGroupNeighbor($playerId, $neighbor[0], $neighbor[1], $group, $ids);
        }
    }
}

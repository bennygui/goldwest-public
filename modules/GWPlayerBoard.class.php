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

class GWPlayerSupplyTrack
{
    public $playerId;
    public $section;
    public $resourceType;
    public $resourceCount;

    function __construct(int $playerId, int $section, string $resourceType, int $resourceCount)
    {
        $this->playerId = $playerId;
        $this->section = $section;
        $this->resourceType = $resourceType;
        $this->resourceCount = $resourceCount;
    }
}

class GWPlayerBoard extends APP_DbObject
{
    public const SUPPLY_TRACK_SECTIONS = [0, 1, 2, 3];
    private const PLAYER_STARTING_RESOURCE = [
        1 => [
            0 => [
                RESOURCE_TYPE_WOOD => 1,
            ],
            1 => [
                RESOURCE_TYPE_STONE => 1,
                RESOURCE_TYPE_COPPER => 1,
            ],
        ],
        2 => [
            0 => [
                RESOURCE_TYPE_WOOD => 1,
            ],
            1 => [
                RESOURCE_TYPE_STONE => 1,
                RESOURCE_TYPE_SILVER => 1,
            ],
        ],
        3 => [
            0 => [
                RESOURCE_TYPE_WOOD => 1,
            ],
            1 => [
                RESOURCE_TYPE_STONE => 1,
                RESOURCE_TYPE_GOLD => 1,
            ],
        ],
        4 => [
            0 => [
                RESOURCE_TYPE_WOOD => 1,
            ],
            1 => [
                RESOURCE_TYPE_STONE => 1,
                RESOURCE_TYPE_GOLD => 1,
            ],
            2 => [
                RESOURCE_TYPE_COPPER => 1,
            ],
        ],
    ];
    private const SETTLEMENT_COUNT = 12;
    public const CAMP_COUNT_PER_PLAYER_COUNT = [
        2 => 12,
        3 => 12,
        4 => 10,
    ];
    // This is the maximum on the player board but it can go over 7
    private const MAX_INFLUENCE_DISPLAY = 7;

    // Display
    private const RESOURCE_WIDTH = 40;
    private const RESOURCE_HEIGHT = 40;

    private $supplyTrack;

    function __construct()
    {
        $this->supplyTrack = null;
    }

    public function generate($playersBasicInfo)
    {
        // Generate player supply track based on player starting position
        $this->supplyTrack = [];
        foreach ($playersBasicInfo as $playerId => $playerInfo) {
            foreach (self::SUPPLY_TRACK_SECTIONS as $section) {
                foreach (RESOURCE_TYPES_ALL as $resourceType) {
                    $resourceCount = 0;
                    $startingNo = self::PLAYER_STARTING_RESOURCE[$playerInfo['player_no']];
                    if (
                        array_key_exists($section, $startingNo)
                        && array_key_exists($resourceType, $startingNo[$section])
                    ) {
                        $resourceCount = $startingNo[$section][$resourceType];
                    }
                    $this->supplyTrack[] = new GWPlayerSupplyTrack($playerId, $section, $resourceType, $resourceCount);
                }
            }
        }

        $this->save();
    }

    public function save()
    {
        if ($this->supplyTrack === null) {
            return;
        }
        self::DbQuery("DELETE FROM player_supply_track");
        $sql = "INSERT INTO player_supply_track (player_id, section, resource_type, resource_count) VALUES ";
        $sql_values = [];
        foreach ($this->supplyTrack as $track) {
            $sql_values[] = "({$track->playerId}, {$track->section}, '{$track->resourceType}', {$track->resourceCount})";
        }
        $sql .= implode(',', $sql_values);
        self::DbQuery($sql);
    }

    public function load()
    {
        if ($this->supplyTrack !== null) {
            return;
        }
        $this->supplyTrack = [];
        $valueArray = self::getObjectListFromDB("SELECT player_id, section, resource_type, resource_count FROM player_supply_track");
        foreach ($valueArray as $value) {
            $track = new GWPlayerSupplyTrack(
                $value['player_id'],
                $value['section'],
                $value['resource_type'],
                $value['resource_count']
            );
            $this->supplyTrack[] = $track;
        }
    }

    public function render($page, $currentPlayerId, $playersBasicInfo)
    {
        $this->load();

        $page->begin_block("goldwest_goldwest", "player-board");
        $playerIdByPosition = [];
        foreach ($playersBasicInfo as $playerId => $playerInfo) {
            if ($playerId == $currentPlayerId) {
                $this->renderPlayerBoard($page, $playerId, $playerInfo['player_name'], $playerInfo['player_color']);
            } else {
                $playerIdByPosition[$playerInfo['player_no']] = $playerId;
            }
        }
        foreach ($playerIdByPosition as $position => $playerId) {
            $playerInfo = $playersBasicInfo[$playerId];
            $this->renderPlayerBoard($page, $playerId, $playerInfo['player_name'], $playerInfo['player_color']);
        }
    }

    private function renderPlayerBoard($page, $playerId, $playerName, $playerColor)
    {
        $page->insert_block(
            "player-board",
            array(
                'PLAYER_COLOR' => $playerColor,
                'PLAYER_NAME' => $playerName,
                'PLAYER_ID' => $playerId,
            )
        );
    }

    public function getConstants($playerCount)
    {
        return [
            "SUPPLY_TRACK_SECTIONS" => self::SUPPLY_TRACK_SECTIONS,
            "MAX_INFLUENCE_DISPLAY" => self::MAX_INFLUENCE_DISPLAY,
            "RESOURCE_WIDTH" => self::RESOURCE_WIDTH,
            "RESOURCE_HEIGHT" => self::RESOURCE_HEIGHT,
            "SETTLEMENT_COUNT" => self::SETTLEMENT_COUNT,
            "CAMP_COUNT" => self::CAMP_COUNT_PER_PLAYER_COUNT[$playerCount],
        ];
    }

    public function getSupplyTrack()
    {
        $this->load();
        return $this->supplyTrack;
    }

    public function getPlayerSupplyTrack($playerId, $section)
    {
        $this->load();
        $supplyTrack = [];
        foreach ($this->supplyTrack as $track) {
            if (
                $track->playerId != $playerId
                || $track->section != $section
            ) {
                continue;
            }
            $supplyTrack[$track->resourceType] = $track;
        }
        return $supplyTrack;
    }

    public function getFilledSupplyTrack($playerId, $section)
    {
        $supplyTrack = [];
        foreach ($this->getPlayerSupplyTrack($playerId, $section) as $track) {
            if ($track->resourceCount <= 0) {
                continue;
            }
            $supplyTrack[$track->resourceType] = $track;
        }
        return $supplyTrack;
    }

    public function addResourcesToSupplyTrack($playerId, $section, $resources)
    {
        $this->load();
        foreach ($resources as $resourceType) {
            foreach ($this->supplyTrack as $track) {
                if (
                    $track->playerId == $playerId
                    && $track->section == $section
                    && $track->resourceType == $resourceType
                ) {
                    $track->resourceCount += 1;
                }
            }
        }
        $this->save();
    }
}

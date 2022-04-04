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

class GWBoardPlayerInfo
{
    private $playerId;
    private $terrainInfluence;
    private $terrainAdditionalInfluence;
    private $campCount;
    private $settlementCount;
    private $settlementPerTerrainCount;
    private $lootCount;

    function __construct(int $playerId)
    {
        $this->playerId = $playerId;
        $this->terrainInfluence = [];
        $this->terrainAdditionalInfluence = [];
        $this->settlementPerTerrainCount = [];
        foreach (TERRAIN_TYPES_BUILDABLE as $terrainType) {
            $this->terrainInfluence[$terrainType] = 0;
            $this->terrainAdditionalInfluence[$terrainType] = 0;
            $this->settlementPerTerrainCount[$terrainType] = 0;
        }
        $this->campCount = 0;
        $this->settlementCount = 0;
        $this->lootCount = 0;
    }

    function toClient()
    {
        return [
            "playerId" => $this->playerId,
            "influence" => $this->terrainInfluence,
            "additionalInfluence" => $this->terrainAdditionalInfluence,
            "usedCamp" => $this->usedCamp(),
            "usedSettlement" => $this->usedSettlement(),
            "usedLootCamp" => $this->usedLootCamp(),
        ];
    }

    public function addCamp(string $terrainType)
    {
        $this->terrainInfluence[$terrainType] += 1;
        $this->campCount += 1;
    }

    public function addSettlement(string $terrainType)
    {
        $this->addCamp($terrainType);
        $this->terrainAdditionalInfluence[$terrainType] += 1;
        $this->settlementCount += 1;
        $this->settlementPerTerrainCount[$terrainType] += 1;
    }

    public function addInfluence(string $terrainType)
    {
        $this->terrainAdditionalInfluence[$terrainType] += 1;
    }

    public function addLoot()
    {
        $this->lootCount += 1;
    }

    public function usedCamp()
    {
        return $this->campCount + $this->lootCount;
    }
    
    public function builtCamp()
    {
        return $this->campCount;
    }

    public function usedSettlement()
    {
        return $this->settlementCount;
    }

    public function usedSettlementTerrain(string $terrainType)
    {
        return $this->settlementPerTerrainCount[$terrainType];
    }

    public function usedLootCamp()
    {
        return $this->lootCount;
    }

    public function influence(string $terrainType)
    {
        return $this->terrainInfluence[$terrainType];
    }

    public function additionalInfluence(string $terrainType)
    {
        return $this->terrainAdditionalInfluence[$terrainType];
    }

    public function totalInfluence(string $terrainType)
    {
        return $this->influence($terrainType) + $this->additionalInfluence($terrainType);
    }

    public function endScoreInfluence(string $terrainType)
    {
        return [
            $this->totalInfluence($terrainType),
            $this->usedSettlementTerrain($terrainType),
        ];
    }
}

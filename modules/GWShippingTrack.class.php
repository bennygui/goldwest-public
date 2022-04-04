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

class GWShippingTrackBonusAndScore
{
    public $newDistance;
    public $score;
    public $bonus;

    public function __construct()
    {
        $this->newDistance = 0;
        $this->score = 0;
        $this->bonus = null;
    }
}

class GWShippingTrackBonus
{
    public const SCORE = [
        RESOURCE_TYPE_GOLD => [
            0 => 9,
            1 => 6
        ],
        RESOURCE_TYPE_SILVER => [
            0 => 8,
            1 => 5
        ],
        RESOURCE_TYPE_COPPER => [
            0 => 7,
            1 => 4
        ],
    ];
    public $resourceType;
    public $level;
    public $pos;
    public $playerId;

    public function __construct(string $resourceType, int $level, int $pos, ?int $playerId = null)
    {
        $this->resourceType = $resourceType;
        $this->level = $level;
        $this->pos = $pos;
        $this->playerId = $playerId;
    }

    public function getScore() {
        return self::SCORE[$this->resourceType][$this->level];
    }
}

class GWShippingTrackStageCoach
{
    public const RESOURCE_ORDER_ARRAY = [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_GOLD];

    public $playerId;
    public $resourceType;
    public $distance;

    public function __construct(int $playerId, string $resourceType, int $distance = 0)
    {
        $this->playerId = $playerId;
        $this->resourceType = $resourceType;
        $this->distance = $distance;
    }

    public static function cmp(GWShippingTrackStageCoach $a, GWShippingTrackStageCoach $b)
    {
        $ar = array_search($a->resourceType, self::RESOURCE_ORDER_ARRAY);
        $br = array_search($b->resourceType, self::RESOURCE_ORDER_ARRAY);
        $r = ($a <=> $b);
        if ($r !== 0) {
            return $r;
        }
        return ($a->playerId <=> $b->playerId);
    }
}

class GWShippingTrack extends APP_DbObject
{
    private const TRACK_LENGTH_WITH_BONUS = 11;
    private const TRACK_SPOT_BONUS_INDEXES = [4, 9];
    // When the new position is in this list, gain the score
    private const TRACK_SPOT_SCORE_INDEXES = [
        2 => 2,
        6 => 3,
    ];

    // Display sizes
    private const SPOT_WIDTH = 40;
    private const SPOT_HEIGHT = 40;
    private const SPOT_TOP_PADDING = 10;
    private const SPOT_LEFT_PADDING = 9;
    private const SPOT_GLOBAL_TOP_PADDING = 25;
    private const SPOT_GLOBAL_LEFT_PADDING = 5;
    private const STAGECOACH_WIDTH = 35;
    private const STAGECOACH_HEIGHT = 24;

    private $tracks = null;
    private $bonus = null;

    private function resort()
    {
        usort($this->tracks, ['GWShippingTrackStageCoach', 'cmp']);
    }

    public function generate($playerIdArray)
    {
        $this->tracks = [];
        foreach ($playerIdArray as $playerId) {
            foreach (GWShippingTrackStageCoach::RESOURCE_ORDER_ARRAY as $resourceType) {
                $this->tracks[] = new GWShippingTrackStageCoach($playerId, $resourceType);
            }
        }
        $this->resort();

        $this->bonus = [];
        foreach (RESOURCE_TYPES_METALS as $type) {
            foreach (self::TRACK_SPOT_BONUS_INDEXES as $pos) {
                for ($level = 0; $level <= 1; ++$level) {
                    $this->bonus[] = new GWShippingTrackBonus($type, $level, $pos);
                }
            }
        }

        $this->save();
    }

    public function load()
    {
        if ($this->tracks !== null) {
            return;
        }
        $this->tracks = [];
        $valueArray = self::getObjectListFromDB("SELECT player_id, resource_type, distance FROM shipping_track");
        foreach ($valueArray as $value) {
            $this->tracks[] = new GWShippingTrackStageCoach(
                $value['player_id'],
                $value['resource_type'],
                $value['distance']
            );
        }
        $this->resort();

        $this->bonus = [];
        $valueArray = self::getObjectListFromDB("SELECT resource_type, bonuslevel, pos, player_id FROM shipping_track_bonus");
        foreach ($valueArray as $value) {
            $this->bonus[] = new GWShippingTrackBonus(
                $value['resource_type'],
                $value['bonuslevel'],
                $value['pos'],
                $value['player_id']
            );
        }
    }

    public function save()
    {
        if ($this->tracks === null) {
            return;
        }
        self::DbQuery("DELETE FROM shipping_track");
        $sql = "INSERT INTO shipping_track (player_id, resource_type, distance) VALUES ";
        $sql_values = [];
        foreach ($this->tracks as $track) {
            $sql_values[] = "({$track->playerId}, '{$track->resourceType}', {$track->distance})";
        }
        $sql .= implode(',', $sql_values);
        self::DbQuery($sql);

        self::DbQuery("DELETE FROM shipping_track_bonus");
        $sql = "INSERT INTO shipping_track_bonus (resource_type, bonuslevel, pos, player_id) VALUES ";
        $sql_values = [];
        foreach ($this->bonus as $bonus) {
            $playerId = $bonus->playerId;
            if ($playerId === null) {
                $playerId = 'NULL';
            }
            $sql_values[] = "('{$bonus->resourceType}', {$bonus->level}, {$bonus->pos}, {$playerId})";
        }
        $sql .= implode(',', $sql_values);
        self::DbQuery($sql);
    }

    public function render($page)
    {
        $this->load();
        $page->begin_block("goldwest_goldwest", "shipping-track");
        foreach (GWShippingTrackStageCoach::RESOURCE_ORDER_ARRAY as $resourceIndex => $resourceType) {
            for ($i = 0; $i < self::TRACK_LENGTH_WITH_BONUS; ++$i) {
                $x = $i * (self::SPOT_WIDTH + self::SPOT_LEFT_PADDING) + self::SPOT_GLOBAL_LEFT_PADDING;
                $y = $resourceIndex * (self::SPOT_HEIGHT + self::SPOT_TOP_PADDING) + self::SPOT_GLOBAL_TOP_PADDING;
                $spot = 'spot';
                if ($i == 0) {
                    $spot = 'start';
                } elseif (array_search($i, self::TRACK_SPOT_BONUS_INDEXES) !== false) {
                    $spot = 'bonus';
                }
                $page->insert_block(
                    "shipping-track",
                    array(
                        'TOP' => $y,
                        'LEFT' => $x,
                        'SPOT_TYPE' => $spot,
                        'RESOURCE_TYPE' => $resourceType,
                        'DISTANCE' => $i,
                    )
                );
            }
        }
    }

    public function getConstants()
    {
        return [
            "TRACK_LENGTH_WITH_BONUS" => self::TRACK_LENGTH_WITH_BONUS,
            "STAGECOACH_WIDTH" => self::STAGECOACH_WIDTH,
            "STAGECOACH_HEIGHT" => self::STAGECOACH_HEIGHT,
            "TRACK_SPOT_BONUS_INDEXES" => self::TRACK_SPOT_BONUS_INDEXES,
            "BONUS_TILE_SCORE" => GWShippingTrackBonus::SCORE,
        ];
    }

    public function getPlayersDistance()
    {
        $this->load();
        $m = [];
        foreach ($this->tracks as $track) {
            $m[$track->playerId][$track->resourceType] = $track->distance;
        }
        return $m;
    }

    public function getBonus()
    {
        $this->load();
        return $this->bonus;
    }

    public function canShip($playerId, $resources)
    {
        $this->load();
        $buyTrack = [];
        foreach ($this->tracks as $track) {
            if ($track->playerId != $playerId) {
                continue;
            }
            // Cannot advance if already at the end
            if ($track->distance == self::TRACK_LENGTH_WITH_BONUS - 1) {
                continue;
            }
            if ($resources[$track->resourceType] > 0) {
                $nextDistance = $track->distance + 1;
                if (array_search($nextDistance, self::TRACK_SPOT_BONUS_INDEXES) !== false)
                    $nextDistance += 1;
                $buyTrack[$track->resourceType] = $nextDistance;
            }
        }
        return $buyTrack;
    }

    public function advanceTrackAndSave($playerId, $resourceType)
    {
        $bonusAndScore = new GWShippingTrackBonusAndScore();
        $this->load();
        foreach ($this->tracks as $track) {
            if ($track->playerId != $playerId || $track->resourceType != $resourceType) {
                continue;
            }
            // Cannot advance if already at the end (should not happen since canShip should be called first)
            if ($track->distance == self::TRACK_LENGTH_WITH_BONUS - 1) {
                return null;
            }
            $bonusAndScore->newDistance = $track->distance + 1;
            if (array_search($bonusAndScore->newDistance, self::TRACK_SPOT_BONUS_INDEXES) !== false) {
                foreach ($this->bonus as $bonus) {
                    if (
                        $bonus->resourceType != $resourceType
                        || $bonus->pos != $bonusAndScore->newDistance
                        || $bonus->playerId !== null
                    ) {
                        continue;
                    }
                    if ($bonusAndScore->bonus === null || $bonus->pos < $bonusAndScore->bonus->pos) {
                        $bonusAndScore->bonus = $bonus;
                    }
                }
                if ($bonusAndScore->bonus !== null) {
                    $bonusAndScore->bonus->playerId = $playerId;
                    $bonusAndScore->score += $bonusAndScore->bonus->getScore();
                }
                $bonusAndScore->newDistance += 1;
            }
            if (array_key_exists($bonusAndScore->newDistance, self::TRACK_SPOT_SCORE_INDEXES)) {
                $bonusAndScore->score += self::TRACK_SPOT_SCORE_INDEXES[$bonusAndScore->newDistance];
            }
            $track->distance = $bonusAndScore->newDistance;
        }
        $this->save();
        return $bonusAndScore;
    }

    public function debugFill($playerIdArray)
    {
        shuffle($playerIdArray);
        foreach ($playerIdArray as $playerId) {
            foreach (RESOURCE_TYPES_METALS as $resourceType) {
                $nbAdvance = rand(3, 8);
                for ($i = 0; $i < $nbAdvance; ++$i) {
                    $this->advanceTrackAndSave($playerId, $resourceType);
                }
            }
        }
    }
}

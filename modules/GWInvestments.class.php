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

class GWInvestments
{
    private const INVESTMENTS_COUNT = 20;
    private const INVESTMENTS_CHOOSE_COUNT = 8;
    private const INVESTMENTS_CARD_RESOURCE_PRICE = [
        0 => [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        1 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        2 => [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        3 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        4 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        5 => [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        6 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        7 => [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        8 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        9 => [RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        10 => [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        11 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER],
        12 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_COPPER],
        13 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER],
        14 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD],
        15 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        16 => [RESOURCE_TYPE_GOLD, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        17 => [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        18 => [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER, RESOURCE_TYPE_SILVER],
        19 => [RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_COPPER, RESOURCE_TYPE_SILVER],
    ];
    private const INVESTMENTS_CARD_SCORE = [
        0 => 8,
        1 => 7,
        2 => 5,
        3 => 9,
        4 => 6,
        5 => 13,
        6 => 10,
        7 => 10,
        8 => 15,
        9 => 9,
        10 => 11,
        11 => 11,
        12 => 8,
        13 => 6,
        14 => 12,
        15 => 11,
        16 => 9,
        17 => 7,
        18 => 7,
        19 => 7,
    ];
    private const INVESTMENTS_CARD_EFFECT = [
        0 => INVESTMENTS_CARD_EFFECT_BASIC,
        1 => INVESTMENTS_CARD_EFFECT_INFLUENCE_SI,
        2 => INVESTMENTS_CARD_EFFECT_FREE_OCCUPIED_BOOM_TOWN,
        3 => INVESTMENTS_CARD_EFFECT_MINING_TOKEN_LOOK_2,
        4 => INVESTMENTS_CARD_EFFECT_FREE_SHIPPING_TRACK_2,
        5 => INVESTMENTS_CARD_EFFECT_BASIC,
        6 => INVESTMENTS_CARD_EFFECT_BASIC,
        7 => INVESTMENTS_CARD_EFFECT_BASIC,
        8 => INVESTMENTS_CARD_EFFECT_BASIC,
        9 => INVESTMENTS_CARD_EFFECT_BASIC,
        10 => INVESTMENTS_CARD_EFFECT_BASIC,
        11 => INVESTMENTS_CARD_EFFECT_INFLUENCE_WO,
        12 => INVESTMENTS_CARD_EFFECT_INFLUENCE_CO,
        13 => INVESTMENTS_CARD_EFFECT_FREE_UNOCCUPIED_BOOM_TOWN,
        14 => INVESTMENTS_CARD_EFFECT_BASIC,
        15 => INVESTMENTS_CARD_EFFECT_BASIC,
        16 => INVESTMENTS_CARD_EFFECT_INFLUENCE_GO,
        17 => INVESTMENTS_CARD_EFFECT_UPGRADE_CAMP,
        18 => INVESTMENTS_CARD_EFFECT_TAKE_RESOURCE_2,
        19 => INVESTMENTS_CARD_EFFECT_FREE_SHIPPING_TRACK_1,
    ];

    private const BONUS_MAX = 5;
    private const BONUS_MIN = 1;
    private const BONUS_MAX_PER_PLAYER_COUNT = [
        2 => 3,
        3 => 4,
        4 => 5,
    ];

    private const PLACE_DECK = 'deck';
    private const PLACE_TABLE = 'table';
    private const PLACE_PLAYER = 'player';

    // Display info
    private const CARD_WIDTH = 100;
    private const CARD_HEIGHT = 150;
    private const BONUS_WIDTH = 60;
    private const BONUS_HEIGHT = 60;

    public $cards;
    public $bonus;

    function __construct($carddeck, $bonusdesk)
    {
        $this->cards = $carddeck;
        $this->cards->init("investment");
        $this->bonus = $bonusdesk;
        $this->bonus->init("investment_bonus");
    }

    public function generate($playerCount)
    {
        // Investments cards
        $creation = [];
        for ($id = 0; $id < self::INVESTMENTS_COUNT; ++$id) {
            $creation[] = ['type' => $id, 'type_arg' => 0, 'nbr' => 1];
        }
        $this->cards->createCards($creation, self::PLACE_DECK);
        $this->cards->shuffle(self::PLACE_DECK);
        for ($i = 0; $i < self::INVESTMENTS_CHOOSE_COUNT; ++$i) {
            $this->cards->pickCardForLocation(self::PLACE_DECK, self::PLACE_TABLE);
        }

        // Bonus
        $creation = [];
        for ($bonus = self::BONUS_MIN; $bonus <= self::BONUS_MAX_PER_PLAYER_COUNT[$playerCount]; ++$bonus) {
            $creation[] = ['type' => $bonus, 'type_arg' => 0, 'nbr' => 1];
        }
        $this->bonus->createCards($creation, self::PLACE_TABLE);
    }

    public function getCardsOnTable()
    {
        return $this->cards->getCardsInLocation(self::PLACE_TABLE);
    }

    public function getBonusOnTable()
    {
        return $this->bonus->getCardsInLocation(self::PLACE_TABLE);
    }

    public function getCardsByPlayers()
    {
        $byPlayer = [];
        foreach ($this->cards->getCardsInLocation(self::PLACE_PLAYER) as $card) {
            $byPlayer[$card['location_arg']][] = $card;
        }
        return $byPlayer;
    }

    public function getBonusByPlayers()
    {
        $byPlayer = [];
        foreach ($this->bonus->getCardsInLocation(self::PLACE_PLAYER) as $bonus) {
            $byPlayer[$bonus['location_arg']][] = $bonus;
        }
        return $byPlayer;
    }

    public function getConstants()
    {
        return [
            "CARD_WIDTH" => self::CARD_WIDTH,
            "CARD_HEIGHT" => self::CARD_HEIGHT,
            "INVESTMENTS_COUNT" => self::INVESTMENTS_COUNT,
            "BONUS_MIN" => self::BONUS_MIN,
            "BONUS_MAX" => self::BONUS_MAX,
            "BONUS_WIDTH" => self::BONUS_WIDTH,
            "BONUS_HEIGHT" => self::BONUS_HEIGHT,
        ];
    }

    public function canBuyCards($resources)
    {
        $buyCards = [];
        foreach ($this->getCardsOnTable() as $card) {
            $cardResources = self::INVESTMENTS_CARD_RESOURCE_PRICE[$card['type']];
            $avail = $resources;
            $canPay = true;
            foreach ($cardResources as $resouceType) {
                $avail[$resouceType] -= 1;
                if ($avail[$resouceType] < 0) {
                    $canPay = false;
                    break;
                }
            }
            if ($canPay) {
                $buyCards[] = $card;
            }
        }
        return $buyCards;
    }

    public function getPriceForCard($cardType)
    {
        return  self::INVESTMENTS_CARD_RESOURCE_PRICE[$cardType];
    }

    public function giveCardToPlayer($cardType, $playerId)
    {
        foreach ($this->getCardsOnTable() as $card) {
            if ($card['type'] == $cardType) {
                $this->cards->moveCard($card['id'], self::PLACE_PLAYER, $playerId);
                break;
            }
        }
        $largestBonus = null;
        foreach ($this->getBonusOnTable() as $bonus) {
            if ($largestBonus === null || $largestBonus['type'] < $bonus['type']) {
                $largestBonus = $bonus;
            }
        }
        if ($largestBonus !== null) {
            $this->bonus->moveCard($largestBonus['id'], self::PLACE_PLAYER, $playerId);
            return $largestBonus['type'];
        }
        return null;
    }

    public function getCardScore($cardType)
    {
        return self::INVESTMENTS_CARD_SCORE[$cardType];
    }

    public function getCardEffect($cardType)
    {
        return self::INVESTMENTS_CARD_EFFECT[$cardType];
    }

    public function getAddedInfuenceByPlayers($playerIdArray)
    {
        $playerInfluence = [];
        foreach ($playerIdArray as $playerId) {
            $playerInfluence[$playerId] = [];
            foreach (TERRAIN_TYPES_BUILDABLE as $terrainType) {
                $playerInfluence[$playerId][$terrainType] = 0;
            }
        }
        foreach ($this->getCardsByPlayers() as $playerId => $cardArray) {
            foreach ($cardArray as $card) {
                switch ($this->getCardEffect($card['type'])) {
                    case INVESTMENTS_CARD_EFFECT_INFLUENCE_SI:
                        $playerInfluence[$playerId][TERRAIN_TYPE_SILVER] += 1;
                        break;
                    case INVESTMENTS_CARD_EFFECT_INFLUENCE_WO:
                        $playerInfluence[$playerId][TERRAIN_TYPE_WOOD] += 1;
                        break;
                    case INVESTMENTS_CARD_EFFECT_INFLUENCE_CO:
                        $playerInfluence[$playerId][TERRAIN_TYPE_COPPER] += 1;
                        break;
                    case INVESTMENTS_CARD_EFFECT_INFLUENCE_GO:
                        $playerInfluence[$playerId][TERRAIN_TYPE_GOLD] += 1;
                        break;
                }
            }
        }
        return $playerInfluence;
    }

    public function debugFill($playerIdArray)
    {
        foreach ($playerIdArray as $playerId) {
            $cardCount = rand(1, 2);
            foreach ($this->getCardsOnTable() as $card) {
                if ($cardCount <= 0) {
                    break;
                }
                $cardCount -= 1;
                $this->giveCardToPlayer($card['type'], $playerId);
            }
        }
    }
}

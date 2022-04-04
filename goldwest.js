/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * goldwest implementation : © Guillaume Benny bennygui@gmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * goldwest.js
 *
 * goldwest user interface script
 *
 */
define([
        "dojo", "dojo/_base/declare",
        "ebg/core/gamegui",
        "ebg/counter",
        "ebg/stock",
        "ebg/zone",
        "ebg/counter",
    ],
    function(dojo, declare) {
        return declare("bgagame.goldwest", ebg.core.gamegui, {
            constructor: function() {
                this.BOARD_MAX_WIDTH = 1200;
                this.PLAYER_BOARD_MAX_WIDTH = 1200;
                this.GLOBAL_SUPPLY_TRACK_MAX_WIDTH = 1200;
                this.GLOBAL_SUPPLY_TRACK_WIDTH = 120;
                this.playerScoreCounter = {};
                this.playerColorName = {};
                this.playerColor = {};
                this.shippingTrackSpot = {};
                this.playerSupplyTrack = {};
                this.playerPanelSupplyTrack = {};
                this.globalSupplyTrack = [];
                this.playerBonusZone = {};
                this.PLAYER_BONUS_ZONE_WIDTH = 60;
                this.PLAYER_BONUS_ZONE_HEIGHT = 60;
                this.playerInvestmentsZone = {};
                this.playerInfluenceZone = {};
                this.SETTLEMENT_TOKEN_WIDTH = 60;
                this.SETTLEMENT_TOKEN_HEIGHT = 60;
                this.playerViewMiningTokenZone = null;
                this.MINING_TOKEN_WIDTH = 60;
                this.MINING_TOKEN_HEIGHT = 60;
                this.boomTownSpot = [];
                this.nextResourceId = 0;
                this.nextAnonymousSettlementId = 0;
                this.nextAnonymousTerrainId = 0;
                this.nextAnonymousMiningTokenId = 0;
                this.wantedZone = null;
                this.WANTED_ZONE_WIDTH = 40;
                this.WANTED_ZONE_HEIGHT = 40;
                this.BOOM_TOWN_SPOT_WIDTH = 60;
                this.BOOM_TOWN_SPOT_HEIGHT = 60;
                this.STATE_ALL = [];
                this.STG_ALL = [];
                this.NTF_ALL = [];
                this.players = {};
                this.constants = {
                    resources: {},
                };
                this.resourceName = {};
                this.boomtownScoringTimer = null;
                this.playerInfluenceCounter = {};
                this.playerResourceCounter = {};
                this.activatedResourceCounter = {};
                this.playerCampCounter = {};
                this.playerSettlementCounter = {};
                dojo.connect(window, "onresize", this, dojo.hitch(this, "resizeAll"));
            },

            /*
                setup:
                
                This method must set up the game user interface according to current game situation specified
                in parameters.
                
                The method is called each time the game interface is displayed to a player, ie:
                _ when the game starts
                _ when a player refreshes the game page (F5)
                
                "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
            */

            setup: function(gamedatas) {
                this.boomtownScoringTimer = null;
                this.resourceName = {
                    GO: _("Gold"),
                    SI: _("Silver"),
                    CO: _("Copper"),
                    WO: _("Wood"),
                    ST: _("Stone"),
                };
                this.terrainName = {
                    GO: _("Gold"),
                    SI: _("Silver"),
                    CO: _("Copper"),
                    WO: _("Wood"),
                };

                this.STATE_ALL = gamedatas.constants.STATE_ALL;
                this.NTF_ALL = gamedatas.constants.NTF_ALL;
                this.STG_ALL = gamedatas.constants.STG_ALL;
                this.TERRAIN_IDS_TO_TYPE = gamedatas.constants.TERRAIN_IDS_TO_TYPE;
                this.RESOURCE_IDS_TO_TYPE = gamedatas.constants.RESOURCE_IDS_TO_TYPE;
                this.constants.resources = gamedatas.constants.resources;
                this.constants.playerboard = gamedatas.constants.playerboard;
                this.constants.shipping = gamedatas.constants.shipping;
                this.players = gamedatas.players;

                this.addTooltip(
                    'gw-board-group-tooltip',
                    _("At end of game, players score 2 points per building in largest contiguous group of buildings (Camps or Settlements)."),
                    ''
                );

                this.addTooltip(
                    'gw-board-influence-tooltip',
                    _("At end of game, the terrain bonuses are awarded to the players with the most and 2nd most influence in each terrain. Ties in terrains are won by the player with the most Settlements in that terrain. Subsequent ties result in a split of the points for first and second, rounded down. In a two player game, neither player scores the 1st place bonus. Instead, the player with the most influence is awarded the 2nd place bonus. The other player receives no points."),
                    ''
                );

                this.addTooltip(
                    'gw-shipping-track-tooltip-2',
                    _("Score 2 points when your stagecoach passes over this line"),
                    ''
                );

                this.addTooltip(
                    'gw-shipping-track-tooltip-3',
                    _("Score 3 points when your stagecoach passes over this line"),
                    ''
                );

                this.addTooltip(
                    'gw-wanted',
                    _("Lose one point when looting (you do not have a Wood or a Stone to build a Camp or a Settlement). At end of game, the player with the most Camp here is the Most Wanted, and loses 1 point per Camp. The player with the next most is the Second Most Wanted and loses 1 point for each 2 Camp (rounded down). In the case of a tie for Most Wanted, all tied players receive the Most Wanted penalty and the Second Most Wanted penalty is ignored. In the case of a tie for Second Most Wanted, all tied players receive the Second Most Wanted penalty."),
                    ''
                );

                this.addTooltip('gw-boomtown-horizontal-tootip-1', _("Copper"), '');
                this.addTooltip('gw-boomtown-horizontal-tootip-2', _("Silver"), '');
                this.addTooltip('gw-boomtown-horizontal-tootip-3', _("Gold"), '');
                this.addTooltip('gw-boomtown-vertical-tootip-1', _("Gold"), '');
                this.addTooltip('gw-boomtown-vertical-tootip-2', _("Silver"), '');
                this.addTooltip('gw-boomtown-vertical-tootip-3', _("Copper"), '');

                for (let i in gamedatas.constants.playerboard.SUPPLY_TRACK_SECTIONS) {
                    let section = gamedatas.constants.playerboard.SUPPLY_TRACK_SECTIONS[i];
                    this.addTooltipToClass(
                        '.gw-player-board-score-tooltip-' + section,
                        dojo.string.substitute(
                            _("Score ${score} points when adding resources in this section of the supply track."), { score: section }
                        ),
                        ''
                    );
                }

                // Create invisible and visible mining tokens
                for (let i = 0; i < gamedatas.board.invisible.length; ++i) {
                    let info = gamedatas.board.invisible[i];
                    let newToken = this.createTerrain(info.terrainType, 'gw-board', info.terrainId);
                    let spot = $('gw-terrain-small-tile-' + info.terrainId);
                    newToken.object.style.top = spot.style.top;
                    newToken.object.style.left = spot.style.left;
                    this.addTooltipInvisibleMiningToken(info.terrainType, info.terrainId);
                }
                for (let i = 0; i < gamedatas.board.visible.length; ++i) {
                    let info = gamedatas.board.visible[i];
                    let newToken = this.createMiningToken(info.terrainType, info.miningTokenId, 'gw-board', info.terrainId);
                    let spot = $('gw-terrain-small-tile-' + info.terrainId);
                    newToken.object.style.top = spot.style.top;
                    newToken.object.style.left = spot.style.left;
                    this.addTooltipVisibleMiningToken(info.terrainType, info.miningTokenId, info.terrainId);
                }

                // Create Zone for shipping tracks
                for (let resIdx = 0; resIdx < gamedatas.constants.resources.metals.length; ++resIdx) {
                    let resType = gamedatas.constants.resources.metals[resIdx];
                    this.shippingTrackSpot[resType] = [];
                    for (let distance = 0; distance < gamedatas.constants.shipping.TRACK_LENGTH_WITH_BONUS; ++distance) {
                        this.shippingTrackSpot[resType][distance] = new ebg.zone();
                        this.shippingTrackSpot[resType][distance].create(
                            this,
                            'gw-track-spot-' + resType + '-' + distance,
                            gamedatas.constants.shipping.STAGECOACH_WIDTH,
                            gamedatas.constants.shipping.STAGECOACH_HEIGHT,
                        );
                        let pattern = 'verticalfit';
                        if (gamedatas.constants.shipping.TRACK_SPOT_BONUS_INDEXES.indexOf(distance) >= 0) {
                            pattern = 'diagonal';
                        }
                        this.shippingTrackSpot[resType][distance].setPattern(pattern);
                    }
                }
                // Create Zone for the Wanted area
                this.wantedZone = new ebg.zone();
                this.wantedZone.create(
                    this,
                    'gw-wanted-zone',
                    this.WANTED_ZONE_WIDTH,
                    this.WANTED_ZONE_HEIGHT,
                );

                // Create Zone for global supply track
                for (let i in gamedatas.constants.playerboard.SUPPLY_TRACK_SECTIONS) {
                    let section = gamedatas.constants.playerboard.SUPPLY_TRACK_SECTIONS[i];
                    let id = "gw-global-supply-track-" + section;
                    let zone = new ebg.zone();
                    zone.create(
                        this,
                        id,
                        gamedatas.constants.playerboard.RESOURCE_WIDTH,
                        gamedatas.constants.playerboard.RESOURCE_HEIGHT,
                    );
                    // Use custom pattern since the global supply track is hidden
                    let nbPerRow = Math.floor(this.GLOBAL_SUPPLY_TRACK_WIDTH / gamedatas.constants.playerboard.RESOURCE_WIDTH);
                    zone.instantaneous = true;
                    zone.setPattern('custom');
                    zone.itemIdToCoords = (i, control_width) => {
                        if (nbPerRow <= 0) {
                            nbPerRow = 1;
                        }
                        let y = Math.floor(i / nbPerRow);
                        let x = i % nbPerRow;
                        return {
                            x: x * gamedatas.constants.playerboard.RESOURCE_WIDTH,
                            y: y * gamedatas.constants.playerboard.RESOURCE_HEIGHT * 1.1,
                        };
                    };
                    this.globalSupplyTrack[section] = zone;
                }

                // Setup activated resource counter
                for (let resIdx = 0; resIdx < this.constants.resources.all.length; ++resIdx) {
                    let resType = this.constants.resources.all[resIdx];
                    this.activatedResourceCounter[resType] = new ebg.counter();
                    this.activatedResourceCounter[resType].create('gw-player-activated-resource-' + resType + '-count');
                    this.activatedResourceCounter[resType].setValue(0);
                }

                // Setting up player boards
                for (var player_id in gamedatas.players) {
                    var player = gamedatas.players[player_id];
                    this.playerColorName[player_id] = player['player_color_name'];
                    this.playerColor[player_id] = player['player_color'];

                    // Place Miner, order token, activated resources in player panel
                    dojo.place(
                        this.format_block('jstpl_player_panel', {
                            player_id: player_id,
                            player_color_name: player['player_color_name'],
                            player_order: player['player_no'],
                        }),
                        'player_board_' + player_id
                    );

                    // Tooltips for player panel
                    for (let terIdx = 0; terIdx < gamedatas.constants.terrains.buildable.length; ++terIdx) {
                        let terrainType = gamedatas.constants.terrains.buildable[terIdx];
                        this.addTooltipPlayerPanelInfluence(terrainType, player_id);
                    }
                    for (let resIdx = 0; resIdx < this.constants.resources.all.length; ++resIdx) {
                        let ressourceType = this.constants.resources.all[resIdx];
                        this.addTooltipPlayerPanelResources(ressourceType, player_id);
                    }
                    this.addTooltipPlayerOrder(player['player_no']);

                    this.addTooltip(
                        'gw-player-board-' + player_id + '-camp-group',
                        _("Number of camp built or looted."),
                        ''
                    );
                    this.addTooltip(
                        'gw-player-board-' + player_id + '-settlement-group',
                        _("Number of settlement built."),
                        ''
                    );

                    // Setup score counter
                    this.playerScoreCounter[player_id] = new ebg.counter();
                    this.playerScoreCounter[player_id].create('player_score_' + player_id);
                    this.playerScoreCounter[player_id].setValue(gamedatas.scores[player_id]);

                    // Setup resource counter
                    this.playerResourceCounter[player_id] = {};
                    for (let resIdx = 0; resIdx < this.constants.resources.all.length; ++resIdx) {
                        let resType = this.constants.resources.all[resIdx];
                        this.playerResourceCounter[player_id][resType] = new ebg.counter();
                        this.playerResourceCounter[player_id][resType].create('gw-player-resource-' + player_id + '-' + resType + '-count');
                        this.playerResourceCounter[player_id][resType].setValue(0);
                    }

                    // Place player stagecoach in the right spot
                    for (let resIdx = 0; resIdx < gamedatas.constants.resources.metals.length; ++resIdx) {
                        let resType = gamedatas.constants.resources.metals[resIdx];
                        dojo.place(
                            this.format_block('jstpl_stagecoach', {
                                player_color_name: player['player_color_name'],
                                resource_type: resType,
                                player_id: player_id,
                            }),
                            'gw-shipping-track'
                        );
                        let distance = gamedatas.shipping.distances[player_id][resType];
                        this.shippingTrackSpot[resType][distance].placeInZone(
                            'gw-stagecoach-' + resType + '-' + player_id,
                            player_id // weigth
                        );
                    }

                    // Place camps and settlements
                    let playerInfo = gamedatas.playerboard.playerinfo[player_id];
                    for (let campIdx = playerInfo.usedCamp; campIdx < gamedatas.constants.playerboard.CAMP_COUNT; ++campIdx) {
                        let campSpotId = "gw-player-board-camp-" + player_id + "-" + campIdx;
                        dojo.place(
                            this.format_block('jstpl_camp', {
                                player_color_name: player['player_color_name'],
                                player_id: player_id,
                                camp_index: campIdx,
                            }),
                            campSpotId
                        );
                    }
                    for (let settlementIdx = playerInfo.usedSettlement; settlementIdx < gamedatas.constants.playerboard.SETTLEMENT_COUNT; ++settlementIdx) {
                        let settlementSpotId = "gw-player-board-settlement-" + player_id + "-" + settlementIdx;
                        dojo.place(
                            this.format_block('jstpl_settlement', {
                                player_color_name: player['player_color_name'],
                                player_id: player_id,
                                settlement_index: settlementIdx,
                            }),
                            settlementSpotId,
                            'first'
                        );
                    }
                    // Camp and settlement counter
                    this.playerCampCounter[player_id] = new ebg.counter();
                    this.playerCampCounter[player_id].create('gw-player-board-' + player_id + '-camp-count');
                    this.playerSettlementCounter[player_id] = new ebg.counter();
                    this.playerSettlementCounter[player_id].create('gw-player-board-' + player_id + '-settlement-count');
                    this.updateCampAndSettlementCount(player_id, playerInfo.usedCamp, playerInfo.usedSettlement, true);

                    // Place camp in wanted zone
                    for (let i = 0; i < playerInfo.usedLootCamp; ++i) {
                        let newCamp = dojo.place(
                            this.format_block('jstpl_anonymous_camp', {
                                player_color_name: player['player_color_name'],
                            }),
                            'gw-wanted-zone'
                        );
                        newCamp.id = 'gw-inital-load-camp-' + player_id + '-' + i;
                        this.wantedZone.placeInZone(
                            newCamp.id,
                            player_id // weight
                        );
                    }

                    // Create influence zone on player board
                    this.playerInfluenceZone[player_id] = {};
                    for (let terIdx = 0; terIdx < gamedatas.constants.terrains.buildable.length; ++terIdx) {
                        let terrainType = gamedatas.constants.terrains.buildable[terIdx];
                        this.playerInfluenceZone[player_id][terrainType] = {};
                        for (let distance = 1; distance <= this.constants.playerboard.MAX_INFLUENCE_DISPLAY; ++distance) {
                            let influenceId = 'gw-influence-track-' + player_id + '-' + terrainType + '-' + (distance - 1);
                            let zone = new ebg.zone();
                            zone.create(
                                this,
                                influenceId,
                                this.SETTLEMENT_TOKEN_WIDTH,
                                this.SETTLEMENT_TOKEN_HEIGHT
                            );
                            zone.setPattern('custom');
                            zone.itemIdToCoords = function(i, control_width) {
                                return {
                                    x: 15 + i * 10,
                                    y: 15 + i * 10,
                                    w: 60,
                                    h: 60
                                };
                            };
                            this.playerInfluenceZone[player_id][terrainType][distance] = zone;
                        }
                        // All distance over 7 must go in the 7th zone on the player board.
                        // This is very rare.
                        for (
                            let distance = this.constants.playerboard.MAX_INFLUENCE_DISPLAY + 1; distance <= this.constants.playerboard.MAX_INFLUENCE_DISPLAY * 5;
                            ++distance) {
                            this.playerInfluenceZone[player_id][terrainType][distance] =
                                this.playerInfluenceZone[player_id][terrainType][this.constants.playerboard.MAX_INFLUENCE_DISPLAY];
                        }
                    }
                    // Place influence on player board and on player panel
                    this.playerInfluenceCounter[player_id] = {};
                    for (let terIdx = 0; terIdx < gamedatas.constants.terrains.buildable.length; ++terIdx) {
                        let terrainType = gamedatas.constants.terrains.buildable[terIdx];
                        let totalInfluence = 0;
                        for (let distance = 1; distance <= playerInfo.influence[terrainType]; ++distance) {
                            let influenceId = 'gw-influence-track-' + player_id + '-' + terrainType + '-0';
                            this.playerInfluenceZone[player_id][terrainType][distance].placeInZone(this.createTerrain(terrainType, influenceId).id);
                            ++totalInfluence;
                        }
                        for (let distance = 1; distance <= playerInfo.additionalInfluence[terrainType]; ++distance) {
                            let influenceId = 'gw-influence-track-' + player_id + '-' + terrainType + '-0';
                            this.playerInfluenceZone[player_id][terrainType][playerInfo.influence[terrainType] + distance].placeInZone(this.createAnonymousSettlement(this.playerColorName[player_id], influenceId).id);
                            ++totalInfluence;
                        }
                        this.playerInfluenceCounter[player_id][terrainType] = new ebg.counter();
                        this.playerInfluenceCounter[player_id][terrainType].create('gw-player-board-influence-' + player_id + '-' + terrainType + '-count');
                        this.playerInfluenceCounter[player_id][terrainType].setValue(totalInfluence);
                    }

                    // Create zones for player supply track
                    this.playerSupplyTrack[player_id] = {};
                    this.playerPanelSupplyTrack[player_id] = {};
                    for (let i in gamedatas.constants.playerboard.SUPPLY_TRACK_SECTIONS) {
                        let section = gamedatas.constants.playerboard.SUPPLY_TRACK_SECTIONS[i];
                        let id = "gw-supply-track-" + player_id + "-" + section;
                        let zone = new ebg.zone();
                        zone.create(
                            this,
                            id,
                            gamedatas.constants.playerboard.RESOURCE_WIDTH,
                            gamedatas.constants.playerboard.RESOURCE_HEIGHT,
                        );
                        this.playerSupplyTrack[player_id][id] = zone;

                        id = "gw-player-panel-supply-track-" + player_id + "-" + section;
                        zone = new ebg.zone();
                        zone.create(
                            this,
                            id,
                            gamedatas.constants.playerboard.RESOURCE_WIDTH / 1.2,
                            gamedatas.constants.playerboard.RESOURCE_HEIGHT,
                        );
                        this.playerPanelSupplyTrack[player_id][id] = zone;
                    }

                    // Place resources in player supply track
                    for (let i in gamedatas.playerboard.supplytrack) {
                        let track = gamedatas.playerboard.supplytrack[i];
                        if (track.playerId != player_id) {
                            continue;
                        }
                        for (let resCount = 0; resCount < track.resourceCount; ++resCount) {
                            let newResource = this.createResource(track.resourceType, 'gw-player-board-' + player_id);
                            this.playerSupplyTrack[player_id]["gw-supply-track-" + player_id + "-" + track.section].placeInZone(
                                newResource.id,
                                newResource.number // weigth
                            );
                        }
                    }
                    this.updatePlayerPanelSupplyTrack(player_id);

                    // Place shipping bonus in player zone
                    this.playerBonusZone[player_id] = new ebg.zone();
                    this.playerBonusZone[player_id].create(
                        this,
                        'gw-player-board-bonus-' + player_id,
                        this.PLAYER_BONUS_ZONE_WIDTH,
                        this.PLAYER_BONUS_ZONE_HEIGHT
                    );
                    for (let i in gamedatas.shipping.bonus) {
                        let bonus = gamedatas.shipping.bonus[i];
                        if (bonus.playerId != player_id) {
                            continue;
                        }
                        let newElement = dojo.place(
                            this.format_block('jstpl_shippingbonus', {
                                resource_type: bonus.resourceType,
                                level: bonus.level,
                                pos: bonus.pos,
                            }),
                            'gw-player-board-bonus-' + player_id
                        );
                        let id = 'gw-shipping-bonus-' + bonus.resourceType + '-' + bonus.level + '-' + bonus.pos;
                        this.playerBonusZone[player_id].placeInZone(id);
                        this.addTooltipShippingTrackBonus(newElement.id, bonus.resourceType, bonus.level);
                    }
                    // Place investment bonus in player zone
                    for (let i in gamedatas.investments.playerBonus[player_id]) {
                        let newElement = dojo.place(
                            this.format_block('jstpl_investmentbonus', {
                                bonus: gamedatas.investments.playerBonus[player_id][i].type,
                            }),
                            'gw-player-board-bonus-' + player_id
                        );
                        this.playerBonusZone[player_id].placeInZone(
                            newElement.id,
                            gamedatas.investments.playerBonus[player_id][i].type
                        );
                        this.addTooltipInvestmentBonus(newElement.id, gamedatas.investments.playerBonus[player_id][i].type);
                    }

                    // Create investments player zone
                    this.playerInvestmentsZone[player_id] = new ebg.stock();
                    this.playerInvestmentsZone[player_id].create(
                        this,
                        $('gw-player-board-investments-' + player_id),
                        gamedatas.constants.investments.CARD_WIDTH,
                        gamedatas.constants.investments.CARD_HEIGHT
                    );
                    this.playerInvestmentsZone[player_id].onItemCreate = (card_div, card_type_id, card_id) => {
                        this.addTooltipInvestmentCard(card_div.id, card_type_id);
                    };
                    this.playerInvestmentsZone[player_id].image_items_per_row = 8;
                    for (let id = 0; id < gamedatas.constants.investments.INVESTMENTS_COUNT; ++id) {
                        this.playerInvestmentsZone[player_id].addItemType(
                            id, // id
                            id, // weight
                            g_gamethemeurl + 'img/gw/investment-cards.jpg', // image
                            id // position in image
                        );
                    }
                    this.playerInvestmentsZone[player_id].setSelectionMode(0);
                    dojo.query('#gw-player-board-investments-' + player_id).addClass('gw-hidden');
                    for (let i in gamedatas.investments.playerCards[player_id]) {
                        this.playerInvestmentsZone[player_id].addToStockWithId(gamedatas.investments.playerCards[player_id][i].type, gamedatas.investments.playerCards[player_id][i].type);
                        dojo.query('#gw-player-board-investments-' + player_id).removeClass('gw-hidden');
                    }

                    // Zone to place viewed invisible mining tokens
                    if (player_id == this.player_id) {
                        this.playerViewMiningTokenZone = new ebg.zone();
                        this.playerViewMiningTokenZone.create(
                            this,
                            'gw-player-panel-' + player_id + '-view-mining-token',
                            this.MINING_TOKEN_WIDTH,
                            this.MINING_TOKEN_HEIGHT
                        );
                        for (let i in gamedatas.playerboard.viewedMiningToken) {
                            let token = gamedatas.playerboard.viewedMiningToken[i];
                            let newToken = this.createMiningToken(
                                token.terrainType,
                                token.miningTokenId,
                                'gw-player-panel-' + player_id + '-view-mining-token',
                                'PLAYER-VIEWED-' + token.id
                            );
                            this.playerViewMiningTokenZone.placeInZone(
                                newToken.id,
                                newToken.number // weigth
                            );
                            dojo.addClass(newToken.id, 'gw-permanent-clickable');
                            this.connect($(newToken.id), 'onclick', function(event) { this.onViewedMiningTokenClick(event, token.id); });
                        }
                    }
                } // for each player

                // Set activated resources for current player
                this.updateActivatedResources(gamedatas.STG);

                // Investments
                this.investmentTable = new ebg.stock();
                this.investmentTable.create(
                    this,
                    $('gw-investments'),
                    gamedatas.constants.investments.CARD_WIDTH,
                    gamedatas.constants.investments.CARD_HEIGHT
                );
                this.investmentTable.image_items_per_row = 8;
                this.investmentTable.onItemCreate = (card_div, card_type_id, card_id) => {
                    this.addTooltipInvestmentCard(card_div.id, card_type_id);
                };
                for (let id = 0; id < gamedatas.constants.investments.INVESTMENTS_COUNT; ++id) {
                    this.investmentTable.addItemType(
                        id, // id
                        id, // weight
                        g_gamethemeurl + 'img/gw/investment-cards.jpg', // image
                        id // position in image
                    );
                }
                for (let i in gamedatas.investments.cards) {
                    this.investmentTable.addToStockWithId(gamedatas.investments.cards[i].type, gamedatas.investments.cards[i].type);
                }
                this.investmentTable.setSelectionMode(0);

                // Investments bonus
                for (let i in gamedatas.investments.bonus) {
                    let newElement = dojo.place(
                        this.format_block('jstpl_investmentbonus', {
                            bonus: gamedatas.investments.bonus[i].type,
                        }),
                        'gw-investment-bonus-spot-' + gamedatas.investments.bonus[i].type
                    );
                    this.addTooltipInvestmentBonus(newElement.id, gamedatas.investments.bonus[i].type);
                }

                // Shipping track bonus
                for (let i in gamedatas.shipping.bonus) {
                    let bonus = gamedatas.shipping.bonus[i];
                    if (bonus.playerId !== null) {
                        continue;
                    }
                    let newElement = dojo.place(
                        this.format_block('jstpl_shippingbonus', {
                            resource_type: bonus.resourceType,
                            level: bonus.level,
                            pos: bonus.pos,
                        }),
                        'gw-shipping-track'
                    );
                    this.shippingTrackSpot[bonus.resourceType][bonus.pos].placeInZone(
                        'gw-shipping-bonus-' + bonus.resourceType + '-' + bonus.level + '-' + bonus.pos, bonus.level // weigth
                    );
                    this.addTooltipShippingTrackBonus(newElement.id, bonus.resourceType, bonus.level);
                }

                // Setup Boom Town
                this.boomTownSpot = [];
                for (let x = 0; x < gamedatas.constants.boomtown.TOWN_SIZE; ++x) {
                    this.boomTownSpot[x] = [];
                    for (let y = 0; y < gamedatas.constants.boomtown.TOWN_SIZE; ++y) {
                        let elementId = 'gw-boomtown-tile-' + x + '-' + y;
                        this.boomTownSpot[x][y] = new ebg.zone();
                        this.boomTownSpot[x][y].create(
                            this,
                            elementId,
                            this.BOOM_TOWN_SPOT_WIDTH,
                            this.BOOM_TOWN_SPOT_HEIGHT,
                        );
                        this.boomTownSpot[x][y].setPattern('diagonal');
                        let tile = gamedatas.boomtown.town[x][y];
                        this.addTooltipBoomTown(elementId, tile.officeId);
                        if (tile.playerId !== null) {
                            let newSettlement = this.createAnonymousSettlement(
                                this.playerColorName[tile.playerId],
                                elementId
                            );
                            this.boomTownSpot[x][y].placeInZone(
                                newSettlement.id,
                                tile.playerId // weigth
                            );
                        }
                        if (tile.investmentPlayerId !== null) {
                            let newSettlement = this.createAnonymousSettlement(
                                this.playerColorName[tile.investmentPlayerId],
                                elementId
                            );
                            this.boomTownSpot[x][y].placeInZone(
                                newSettlement.id,
                                tile.investmentPlayerId // weigth
                            );
                        }
                    }
                }

                // Create camps and settlements for the main board
                for (let id in gamedatas.board.camp) {
                    let playerId = gamedatas.board.camp[id];
                    let terrainId = 'gw-terrain-small-tile-' + id;
                    dojo.place(
                        this.format_block('jstpl_camp', {
                            player_color_name: this.playerColorName[playerId],
                            player_id: player_id,
                            camp_index: id,
                        }),
                        terrainId
                    );
                }
                for (let id in gamedatas.board.settlement) {
                    let playerId = gamedatas.board.settlement[id];
                    let terrainId = 'gw-terrain-small-tile-' + id;
                    dojo.place(
                        this.format_block('jstpl_settlement', {
                            player_color_name: this.playerColorName[playerId],
                            player_id: player_id,
                            settlement_index: id,
                        }),
                        terrainId,
                        'first'
                    );
                }

                // Global tooltips for player panel
                this.addTooltipToClass(
                    '.gw-miner',
                    _("The miner that represents the player color."),
                    ''
                );
                this.addTooltipToClass(
                    '.gw-player-panel-view-mining-token',
                    _("Mining tokens that you secretly looked at with an investment card. Click on them to view their positions on the board."),
                    ''
                );

                // Setup game notifications to handle (see "setupNotifications" method below)
                this.setupNotifications();
            },


            ///////////////////////////////////////////////////
            //// Game & client states

            // onEnteringState: this method is called each time we are entering into a new game state.
            //                  You can use this method to perform some user interface changes at this moment.
            //
            onEnteringState: function(stateName, args) {
                this.unselectEverything();
                this.updateActivatedResources(args.args);
                this.hideGlobalSupplyTrack();
                switch (stateName) {
                    case this.STATE_ALL.STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE:
                        if (this.isCurrentPlayerActive()) {
                            for (let section in this.playerSupplyTrack[this.player_id]) {
                                if (this.playerSupplyTrack[this.player_id][section].getItemNumber() > 0) {
                                    let elem = dojo.query('#' + section);
                                    elem.addClass('gw-clickable');
                                    let sectionNo = section.split('-').slice(-1)[0];
                                    this.connect(elem[0], 'onclick', function(event) { this.onSupplyTrackActivateClick(event, sectionNo); });
                                }
                            }
                            this.updateGlobalSupplyTrack(this.player_id);
                            this.showGlobalSupplyTrack();
                        }
                        break;
                    case this.STATE_ALL.STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE:
                        if (this.isCurrentPlayerActive()) {
                            for (let resIdx = 0; resIdx < this.constants.resources.all.length; ++resIdx) {
                                let resType = this.constants.resources.all[resIdx];
                                let stateValue = args.args["STG_NB_RESOURCES_" + resType];
                                if (stateValue <= 0) {
                                    continue;
                                }
                                // Resources in player panel
                                {
                                    let id = '#gw-player-board-resource-group-' + resType + '-' + this.player_id;
                                    let panel = dojo.query(id);
                                    panel.addClass('gw-clickable');
                                    this.connect(panel[0], 'onclick', function(event) { this.onChooseResourceToLeaveClick(event, resType); });
                                }
                                // Resources at the top of the screen
                                {
                                    let id = '#gw-player-activated-resources-group-' + resType;
                                    let panel = dojo.query(id);
                                    panel.addClass('gw-clickable');
                                    this.connect(panel[0], 'onclick', function(event) { this.onChooseResourceToLeaveClick(event, resType); });
                                }
                            }
                            for (let section in this.playerSupplyTrack[this.player_id]) {
                                let sectionNo = section.split('-').slice(-1)[0];
                                if (sectionNo == args.args[this.STG_ALL.STG_CHOSEN_SUPPLY_TRACK_SECTION]) {
                                    dojo.query('#' + section).addClass('gw-item-highlight');
                                    break;
                                }
                            }
                            this.updateGlobalSupplyTrack(this.player_id);
                            this.showGlobalSupplyTrack();
                        }
                        break;
                    case this.STATE_ALL.STATE_CHOOSE_METAL_USE:
                        if (this.isCurrentPlayerActive()) {
                            for (let i in args.args.boomtownPositions) {
                                let pos = args.args.boomtownPositions[i];
                                let id = '#gw-boomtown-tile-' + pos[0] + '-' + pos[1];
                                let tile = dojo.query(id);
                                tile.addClass('gw-clickable');
                                this.connect(tile[0], 'onclick', function(event) { this.onChooseBoomTownClick(event, pos[0], pos[1]); });
                            }
                            for (let i in args.args.investmentCards) {
                                let card = args.args.investmentCards[i];
                                let divId = '#' + this.investmentTable.getItemDivId(card.type);
                                let div = dojo.query(divId);
                                div.addClass('gw-clickable');
                                this.connect(div[0], 'onclick', function(event) { this.onChooseInvestmentCardClick(event, card.type); });
                            }
                            for (let resType in args.args.trackPositions) {
                                let distance = args.args.trackPositions[resType];
                                let id = '#gw-track-spot-' + resType + '-' + distance;
                                let tile = dojo.query(id);
                                tile.addClass('gw-clickable');
                                this.connect(tile[0], 'onclick', function(event) { this.onChooseShippingTrackClick(event, resType); });
                            }
                        }
                        break;
                    case this.STATE_ALL.STATE_CHOOSE_TOKEN_LOOT:
                    case this.STATE_ALL.STATE_CHOOSE_TOKEN_BUILD_CAMP:
                    case this.STATE_ALL.STATE_CHOOSE_TOKEN_BUILD_SETTLEMENT:
                        if (this.isCurrentPlayerActive()) {
                            for (let i in args.args.tokens) {
                                let id = '#gw-terrain-small-tile-' + args.args.tokens[i];
                                let tile = dojo.query(id);
                                tile.addClass('gw-clickable');
                                this.connect(tile[0], 'onclick', function(event) { this.onChooseMiningTokenClick(event, args.args.tokens[i]); });
                            }
                            this.updateGlobalSupplyTrack(this.player_id);
                            this.showGlobalSupplyTrack();
                        }
                        break;
                    case this.STATE_ALL.STATE_CHOOSE_SUPPLY_TRACK_TO_ADD:
                        if (this.isCurrentPlayerActive()) {
                            for (let section in this.playerSupplyTrack[this.player_id]) {
                                let elem = dojo.query('#' + section);
                                elem.addClass('gw-clickable');
                                let sectionNo = section.split('-').slice(-1)[0];
                                this.connect(elem[0], 'onclick', function(event) { this.onSupplyTrackAddClick(event, sectionNo); });
                            }
                            this.updateGlobalSupplyTrack(this.player_id);
                            this.showGlobalSupplyTrack();
                        }
                        break;
                    case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_SHIPPING_TRACK:
                        if (this.isCurrentPlayerActive()) {
                            for (let resType in args.args.trackPositions) {
                                let distance = args.args.trackPositions[resType];
                                let id = '#gw-track-spot-' + resType + '-' + distance;
                                let tile = dojo.query(id);
                                tile.addClass('gw-clickable');
                                this.connect(tile[0], 'onclick', function(event) { this.onChooseFreeShippingTrackClick(event, resType); });
                            }
                        }
                        break;
                    case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_UNOCCUPIED_BOOMTOWN:
                        if (this.isCurrentPlayerActive()) {
                            for (let i in args.args.boomtownPositions) {
                                let pos = args.args.boomtownPositions[i];
                                let id = '#gw-boomtown-tile-' + pos[0] + '-' + pos[1];
                                let tile = dojo.query(id);
                                tile.addClass('gw-clickable');
                                this.connect(tile[0], 'onclick', function(event) { this.onChooseFreeBoomTownClick(event, pos[0], pos[1]); });
                            }
                        }
                        break;
                    case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_OCCUPIED_BOOMTOWN:
                        if (this.isCurrentPlayerActive()) {
                            for (let i in args.args.boomtownPositions) {
                                let pos = args.args.boomtownPositions[i];
                                let id = '#gw-boomtown-tile-' + pos[0] + '-' + pos[1];
                                let tile = dojo.query(id);
                                tile.addClass('gw-clickable');
                                this.connect(tile[0], 'onclick', function(event) { this.onChooseFreeOccupiedBoomTownClick(event, pos[0], pos[1]); });
                            }
                        }
                        break;
                    case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW:
                        if (this.isCurrentPlayerActive()) {
                            for (let i in args.args.tokens) {
                                let id = '#gw-terrain-small-tile-' + args.args.tokens[i];
                                let tile = dojo.query(id);
                                tile.addClass('gw-clickable');
                                this.connect(tile[0], 'onclick', function(event) { this.onChooseMiningTokenToViewClick(event, args.args.tokens[i]); });
                            }
                        }
                        break;
                    case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_CAMP_TO_UPGRADE:
                        if (this.isCurrentPlayerActive()) {
                            for (let i in args.args.tokens) {
                                let id = '#gw-terrain-small-tile-' + args.args.tokens[i];
                                let tile = dojo.query(id);
                                tile.addClass('gw-clickable');
                                this.connect(tile[0], 'onclick', function(event) { this.onChooseCampToUpgradeClick(event, args.args.tokens[i]); });
                            }
                        }
                        break;
                    case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK:
                        if (this.isCurrentPlayerActive()) {
                            for (let section in this.playerSupplyTrack[this.player_id]) {
                                let elem = dojo.query('#' + section);
                                elem.addClass('gw-clickable');
                                let sectionNo = section.split('-').slice(-1)[0];
                                this.connect(elem[0], 'onclick', function(event) { this.onChooseFreeResourceTrackClick(event, sectionNo); });
                            }
                            this.updateGlobalSupplyTrack(this.player_id);
                            this.showGlobalSupplyTrack();
                        }
                        break;
                    case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_FREE_RESOURCE:
                        this.updateGlobalSupplyTrack(this.player_id);
                        this.showGlobalSupplyTrack();
                        break;
                    case this.STATE_ALL.STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK:
                        if (this.isCurrentPlayerActive()) {
                            for (let section in this.playerSupplyTrack[this.player_id]) {
                                let elem = dojo.query('#' + section);
                                elem.addClass('gw-clickable');
                                let sectionNo = section.split('-').slice(-1)[0];
                                this.connect(elem[0], 'onclick', function(event) { this.onTradingPostSupplyTrackAddClick(event, sectionNo); });
                            }
                            this.updateGlobalSupplyTrack(this.player_id);
                            this.showGlobalSupplyTrack();
                        }
                        break;

                }
            },

            // onLeavingState: this method is called each time we are leaving a game state.
            //                 You can use this method to perform some user interface changes at this moment.
            //
            onLeavingState: function(stateName) {
                dojo.addClass('gw-warn-no-undo', 'gw-hidden');
                switch (stateName) {}
            },

            // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
            //                        action status bar (ie: the HTML links in the status bar).
            //        
            onUpdateActionButtons: function(stateName, args) {
                if (this.isCurrentPlayerActive()) {
                    switch (stateName) {
                        case this.STATE_ALL.STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE:
                            // DEBUG! Comment for production
                            //this.addActionButton(
                            //    'gw-button-debug',
                            //    'DEBUG: Go To Last Turn',
                            //    function(event) {
                            //        dojo.stopEvent(event);
                            //        this.ajaxAction('chooseDebugGotoLastTurn', {});
                            //    }
                            //);
                            break;
                        case this.STATE_ALL.STATE_CHOOSE_SUPPLY_TRACK_RESOURCE_TO_LEAVE:
                            let sectionNo = args[this.STG_ALL.STG_CHOSEN_SUPPLY_TRACK_SECTION];
                            for (let resIdx = 0; resIdx < this.constants.resources.all.length; ++resIdx) {
                                let resType = this.constants.resources.all[resIdx];
                                let stateValue = args["STG_NB_RESOURCES_" + resType];
                                if (stateValue <= 0) {
                                    continue;
                                }
                                this.addActionButton(
                                    'gw-button-' + resType,
                                    dojo.string.substitute(
                                        _('Leave ${resource_image} in section ${section}'), {
                                            resource_image: '<span class="gw-inline-resource gw-' + resType + '"></span>',
                                            section: sectionNo,
                                        }
                                    ),
                                    function(event) {
                                        this.onChooseResourceToLeaveClick(event, resType);
                                    }
                                );
                            }
                            break;
                        case this.STATE_ALL.STATE_CHOOSE_SUPPLY_TRACK_TO_ADD:
                            this.addResourceListToTitle(args.resources);
                            break;
                        case this.STATE_ALL.STATE_CHOOSE_TO_CONFIRM_TURN:
                            this.addActionButton(
                                'gw-button-1',
                                _('Confirm your turn'),
                                function(event) {
                                    this.onConfirmTurnClick(event);
                                },
                                null, // unused
                                false, // blinking
                                'red'
                            );
                            break;
                        case this.STATE_ALL.STATE_INFORM_AFTER_CANCEL_VIEW:
                            this.addActionButton(
                                'gw-button-1',
                                _('Continue your turn'),
                                function(event) {
                                    this.onConfirmAfterCancelView(event);
                                },
                            );
                            break;
                        case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_MINING_TOKEN_TO_VIEW:
                            if (args !== undefined &&
                                this.STG_ALL.STG_CANCEL_ALLOWED in args &&
                                args[this.STG_ALL.STG_CANCEL_ALLOWED] != 0) {
                                dojo.removeClass('gw-warn-no-undo', 'gw-hidden');
                            }
                            break;
                        case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_FREE_RESOURCE:
                            for (let resIdx = 0; resIdx < this.constants.resources.all.length; ++resIdx) {
                                let resType = this.constants.resources.all[resIdx];
                                this.addActionButton(
                                    'gw-button-' + resType,
                                    dojo.string.substitute(
                                        '<span class="gw-inline-resource gw-${resource_type}""></span>', { resource_type: resType }
                                    ),
                                    function(event) {
                                        this.onChooseFreeResourceClick(event, resType);
                                    }
                                );
                            }
                            break;
                        case this.STATE_ALL.STATE_INVESTMENT_CHOOSE_FREE_RESOURCE_TRACK:
                            let resourceType = this.RESOURCE_IDS_TO_TYPE[args[this.STG_ALL.STG_INVESTMENT_FREE_RESOURCE_ID]];
                            this.addResourceListToTitle([resourceType]);
                            break;
                        case this.STATE_ALL.STATE_TRADING_POST_CHOOSE_WOOD_OR_STONE:
                            for (let resIdx = 0; resIdx < this.constants.resources.build.length; ++resIdx) {
                                let resType = this.constants.resources.build[resIdx];
                                this.addActionButton(
                                    'gw-button-' + resType,
                                    dojo.string.substitute(
                                        '<span class="gw-inline-resource gw-${resource_type}""></span>', { resource_type: resType }
                                    ),
                                    function(event) {
                                        this.onChooseTradingPostResourceClick(event, resType);
                                    }
                                );
                            }
                            break;
                        case this.STATE_ALL.STATE_TRADING_POST_KEEP_OR_SUPPLY_TRACK:
                            let resTypeId = args[this.STG_ALL.STG_TRADING_POST_RESOURCE_ID];
                            let resType = this.RESOURCE_IDS_TO_TYPE[resTypeId];
                            this.addActionButton(
                                'gw-button-' + resType,
                                dojo.string.substitute(
                                    _('Keep ${resource_html} for this turn'), {
                                        resource_html: dojo.string.substitute(
                                            '<span class="gw-inline-resource gw-${resource_type}""></span>', { resource_type: resType }
                                        )
                                    }
                                ),
                                function(event) {
                                    this.onTradingPostKeepResourceClick(event);
                                }
                            );
                    }
                    switch (stateName) {
                        case this.STATE_ALL.STATE_CHOOSE_SUPPLY_TRACK_TO_ACTIVATE:
                        case this.STATE_ALL.STATE_INFORM_AFTER_CANCEL_VIEW:
                            break;
                        default:
                            this.addCancelTurnButton(args);
                            break;
                    }
                }
            },

            ///////////////////////////////////////////////////
            //// Utility methods

            /*
    
                Here, you can defines some utility methods that you can use everywhere in your javascript
                script.
    
            */
            unselectEverything: function() {
                let elements = dojo.query('.gw-clickable');
                for (let i in elements) {
                    this.disconnect(elements[i], 'onclick');
                }
                elements.removeClass('gw-clickable');
                dojo.query('.gw-item-highlight').removeClass('gw-item-highlight');
            },

            updateLastTurnBanner: function(stateGlobals) {
                if (stateGlobals === null || !(this.STG_ALL.STG_LAST_TURN_COUNT in stateGlobals)) {
                    return;
                }
                if (stateGlobals[this.STG_ALL.STG_LAST_TURN_COUNT] > 0) {
                    dojo.removeClass('gw-warn-last-turn', 'gw-hidden');
                } else {
                    dojo.addClass('gw-warn-last-turn', 'gw-hidden');
                }
            },

            updateActivatedResources: function(stateGlobals) {
                if (stateGlobals === null || !(this.STG_ALL.STG_LAST_TURN_COUNT in stateGlobals) || this.getActivePlayerId() == null) {
                    return;
                }
                this.updateLastTurnBanner(stateGlobals);
                // Place activated resources: Top of screen
                let panel = dojo.query("#gw-player-activated-resources");
                panel.addClass('gw-hidden');
                for (let resIdx = 0; resIdx < this.constants.resources.all.length; ++resIdx) {
                    let resType = this.constants.resources.all[resIdx];
                    let stateValue = stateGlobals["STG_NB_RESOURCES_" + resType];
                    this.activatedResourceCounter[resType].toValue(stateValue);
                    if (this.player_id == this.getActivePlayerId() && stateValue > 0) {
                        panel.removeClass('gw-hidden');
                    }
                }
                for (let playerId in this.players) {
                    // Place activated resources: Player panel
                    let panel = dojo.query("#gw-player-panel-" + playerId + "-resources");
                    panel.addClass('gw-hidden');
                    for (let resIdx = 0; resIdx < this.constants.resources.all.length; ++resIdx) {
                        let resType = this.constants.resources.all[resIdx];
                        let stateValue = stateGlobals["STG_NB_RESOURCES_" + resType];
                        this.playerResourceCounter[playerId][resType].toValue(stateValue);
                        if (playerId == this.getActivePlayerId() && stateValue > 0) {
                            panel.removeClass('gw-hidden');
                        }
                    }
                    // Place choosen mining token
                    let token = dojo.query('#gw-player-mining-token-' + playerId);
                    token.addClass('gw-hidden');
                    token[0].innerHTML = '';
                    let miningTokenId = stateGlobals[this.STG_ALL.STG_CHOSEN_MINING_TOKEN_ID];
                    let miningTokenResourceId = stateGlobals[this.STG_ALL.STG_CHOSEN_MINING_TOKEN_TERRAIN_ID];
                    if (playerId == this.getActivePlayerId() && miningTokenId >= 0) {
                        token.removeClass('gw-hidden');
                        let terrainType = this.TERRAIN_IDS_TO_TYPE[miningTokenResourceId];
                        this.createMiningToken(terrainType, miningTokenId, 'gw-player-mining-token-' + playerId);
                    }
                    // Update camp and settlement count
                    this.updateCampAndSettlementCount(playerId, stateGlobals.playerinfo[playerId].usedCamp, stateGlobals.playerinfo[playerId].usedSettlement);
                }
            },

            addCancelTurnButton: function(args) {
                if (args !== undefined &&
                    args !== null &&
                    this.STG_ALL.STG_CANCEL_ALLOWED in args &&
                    args[this.STG_ALL.STG_CANCEL_ALLOWED] == 0) {
                    return;
                }
                this.addActionButton(
                    'gw-button-cancel',
                    _('Cancel and restart your turn'),
                    function(event) {
                        this.onCancelTurnClick(event);
                    },
                    null, // unused
                    false, // blinking
                    'gray'
                );
            },

            createResource: function(resourceType, parent) {
                let resourceId = this.nextResourceId++;
                dojo.place(
                    this.format_block('jstpl_resource', {
                        resource_type: resourceType,
                        resource_id: resourceId,
                    }),
                    parent
                );
                return {
                    id: 'gw-resource-' + resourceId,
                    number: resourceId,
                };
            },

            createAnonymousSettlement: function(playerColorName, parent) {
                let settlementId = this.nextAnonymousSettlementId++;
                let newObject = dojo.place(
                    this.format_block('jstpl_anonymous_settlement', {
                        player_color_name: playerColorName,
                        settlement_id: settlementId,
                    }),
                    parent,
                    'first'
                );
                return {
                    id: newObject.id,
                    number: settlementId,
                    object: newObject,
                };
            },

            createTerrain: function(terrainType, parent, terrainId = null) {
                let tokenId = terrainId === null ? this.nextAnonymousTerrainId++ : terrainId;
                let newObject = dojo.place(
                    this.format_block('jstpl_terrain_token', {
                        terrain_type: terrainType,
                        terrain_or_token_id: tokenId,
                    }),
                    parent
                );
                return {
                    id: newObject.id,
                    number: tokenId,
                    object: newObject,
                };
            },

            createMiningToken: function(terrainType, miningTokenId, parent, terrainId = null) {
                let tokenId = terrainId === null ? this.nextAnonymousMiningTokenId++ : terrainId;
                let newObject = dojo.place(
                    this.format_block('jstpl_mining_token', {
                        terrain_type: terrainType,
                        mining_token_id: miningTokenId,
                        terrain_or_token_id: tokenId,
                    }),
                    parent
                );
                return {
                    id: newObject.id,
                    number: tokenId,
                    object: newObject,
                };
            },

            displayBigScore: function(parent, playerId, score, x = null, y = null) {
                this.displayScoring(
                    parent,
                    this.playerColorName[playerId] == 'white' ? '000000' : this.playerColor[playerId],
                    score,
                    500,
                    x,
                    y
                );
            },

            displayFadeOutHighlight: function(parent) {
                let highlight = dojo.place('<div class="gw-terrain-highlight"></div>', parent);
                this.fadeOutAndDestroy(highlight, 3000);
            },

            showGlobalSupplyTrack: function() {
                this.unresizeAll();
                dojo.query('#gw-global-supply-wrap').removeClass('gw-hidden');
                this.resizeAll();
            },

            hideGlobalSupplyTrack: function() {
                dojo.query('#gw-global-supply-wrap').addClass('gw-hidden');
            },

            updateGlobalSupplyTrack: function(playerId) {
                let elements = dojo.query('#gw-global-supply-wrap .gw-clickable');
                for (let i in elements) {
                    this.disconnect(elements[i], 'onclick');
                }
                elements.removeClass('gw-clickable');
                dojo.query('#gw-global-supply-wrap .gw-item-highlight').removeClass('gw-item-highlight');
                for (let i in this.constants.playerboard.SUPPLY_TRACK_SECTIONS) {
                    let section = this.constants.playerboard.SUPPLY_TRACK_SECTIONS[i];
                    this.globalSupplyTrack[section].removeAll();
                    dojo.query('#gw-global-supply-button-' + section).addClass('gw-hidden');
                    this.disconnect($('gw-global-supply-button-' + section), 'onclick');
                }
                for (let section in this.playerSupplyTrack[playerId]) {
                    let sectionNo = section.split('-').slice(-1)[0];
                    let playerTrackElem = $(section);
                    if (playerTrackElem.classList.contains('gw-clickable')) {
                        let globalTrackElem = $(this.globalSupplyTrack[sectionNo].container_div);
                        globalTrackElem.classList.add('gw-clickable');
                        this.connect(globalTrackElem, 'onclick', function(event) { playerTrackElem.click() });
                        dojo.query('#gw-global-supply-button-' + sectionNo).removeClass('gw-hidden');
                        this.connect($('gw-global-supply-button-' + sectionNo), 'onclick', function(event) { playerTrackElem.click() });
                    }
                    if (playerTrackElem.classList.contains('gw-item-highlight')) {
                        let globalTrackElem = $(this.globalSupplyTrack[sectionNo].container_div);
                        globalTrackElem.classList.add('gw-item-highlight');
                    }
                    for (let id of this.playerSupplyTrack[playerId][section].getAllItems()) {
                        let playerResourceElem = $(id);
                        let resourceType = playerResourceElem.dataset.resource;
                        let newResource = this.createResource(resourceType, 'gw-global-supply');
                        this.globalSupplyTrack[sectionNo].placeInZone(newResource.id);
                    }
                }
            },

            updateCampAndSettlementCount: function(playerId, campCount, settlementCount, initialValue = false) {
                if (initialValue) {
                    this.playerCampCounter[playerId].setValue(campCount);
                    this.playerSettlementCounter[playerId].setValue(settlementCount);
                } else {
                    this.playerCampCounter[playerId].toValue(campCount);
                    this.playerSettlementCounter[playerId].toValue(settlementCount);
                }
                $('gw-player-board-' + playerId + '-camp-count-total').innerText = this.constants.playerboard.CAMP_COUNT;
            },

            addResourceListToTitle: function(resourceTypeList) {
                let resourcesHTML = '<span>';
                resourcesHTML += '<span>&nbsp;</span>';
                for (let i in resourceTypeList) {
                    let resType = resourceTypeList[i];
                    resourcesHTML += dojo.string.substitute(
                        '<span class="gw-inline-resource gw-${resource_type}""></span>', { resource_type: resType }
                    );
                }
                resourcesHTML += '<span>&nbsp;</span>';
                resourcesHTML += '</span>';
                dojo.place(
                    resourcesHTML,
                    'pagemaintitletext',
                    'append'
                );
            },

            updatePlayerPanelSupplyTrack: function(onlyPlayerId = null, onlySectionNo = null) {
                dojo.query('.gw-player-panel-supply').style('transform', 'none');
                for (let playerId in this.playerSupplyTrack) {
                    if (onlyPlayerId !== null && playerId != onlyPlayerId) {
                        continue;
                    }
                    for (let section in this.playerSupplyTrack[playerId]) {
                        let sectionNo = section.split('-').slice(-1)[0];
                        if (onlySectionNo !== null && sectionNo != onlySectionNo) {
                            continue;
                        }
                        let sectionPlayerPanel = "gw-player-panel-supply-track-" + playerId + "-" + sectionNo;
                        this.playerPanelSupplyTrack[playerId][sectionPlayerPanel].removeAll();
                        $(sectionPlayerPanel).innerHTML = '';

                        let zone = this.playerSupplyTrack[playerId][section];
                        let zoneItems = zone.getAllItems();
                        for (let i in zoneItems) {
                            let resourceType = $(zoneItems[i]).dataset.resource;
                            let newResource = this.createResource(resourceType, sectionPlayerPanel);
                            this.playerPanelSupplyTrack[playerId][sectionPlayerPanel].placeInZone(
                                newResource.id
                            );
                        }
                    }
                }
                dojo.query('.gw-player-panel-supply').style('transform', '');
            },

            updatePlayerScore: function(playerId, newScore) {
                this.playerScoreCounter[playerId].toValue(newScore);
            },

            resizeAll: function() {
                let pageId = 'page-content';

                let pageCoords = dojo.marginBox(pageId);
                let width = pageCoords.w;

                if (width >= this.BOARD_MAX_WIDTH || this.control3dmode3d) {
                    this.unresizeBoard();
                } else {
                    let scaling = width / this.BOARD_MAX_WIDTH;
                    dojo.style('gw-board', 'transform', 'scale(' + scaling + ')');
                    dojo.style('gw-board', 'transform-origin', '0 0');
                    dojo.style('gw-board-wrap', 'height', ($('gw-board').offsetHeight * scaling) + 'px');
                }

                if (width > this.PLAYER_BOARD_MAX_WIDTH || this.control3dmode3d) {
                    this.unresizePlayerBoard();
                } else {
                    let scaling = width / this.PLAYER_BOARD_MAX_WIDTH;
                    dojo.query('.gw-player-board-wrap').style('transform', 'scale(' + scaling + ')');
                    dojo.query('.gw-player-board-wrap').style('transform-origin', '0 0');
                    let wrap1 = dojo.query('.gw-player-board-wrap');
                    let wrap2 = dojo.query('.gw-player-board-wrap-wrap');
                    for (let i = 0; i < wrap1.length && i < wrap2.length; ++i) {
                        wrap2[i].style.height = (wrap1[i].offsetHeight * scaling) + 'px';
                    }
                }

                if (width > this.GLOBAL_SUPPLY_TRACK_MAX_WIDTH || this.control3dmode3d) {
                    this.unresizeGlobalSupplyTrack();
                } else {
                    let scaling = width / this.GLOBAL_SUPPLY_TRACK_MAX_WIDTH;
                    dojo.style('gw-global-supply', 'transform', 'scale(' + scaling + ') translate(-50%, 0)');
                    dojo.style('gw-global-supply', 'transform-origin', '0 0');
                    dojo.style('gw-global-supply-wrap', 'height', ($('gw-global-supply').offsetHeight * scaling) + 'px');
                }
            },

            unresizeAll: function() {
                this.unresizeBoard();
                this.unresizePlayerBoard();
                this.unresizeGlobalSupplyTrack();
            },

            unresizeBoard: function() {
                dojo.style('gw-board', 'transform', '');
                dojo.style('gw-board-wrap', 'height', '');
            },

            unresizePlayerBoard: function() {
                dojo.query('.gw-player-board-wrap').style('transform', '');
                dojo.query('.gw-player-board-wrap-wrap').style('height', '');
            },

            unresizeGlobalSupplyTrack: function() {
                dojo.style('gw-global-supply', 'transform', '');
                dojo.style('gw-global-supply-wrap', 'height', '');
            },

            sizer: function(functionName) {
                return function() {
                    this.unresizeAll();
                    try {
                        this[functionName](...arguments);
                    } finally {
                        this.resizeAll();
                    }
                }
            },

            addTooltipInvisibleMiningToken: function(terrainType, terrainId) {
                this.addTooltipHtml(
                    'gw-terrain-small-tile-' + terrainId,
                    this.format_block('jstpl_tooltip_invisible_mining_token', {
                        terrain_type_name: this.terrainName[terrainType],
                        description: _("Invisible mining token that will contain at least one resource of its type. It will be revealed at the end of the turn if one of its neighbor token is taken in the Build or Loot phase."),
                    })
                );
            },

            addTooltipVisibleMiningToken: function(terrainType, miningTokenId, terrainId) {
                this.addTooltipHtml(
                    'gw-terrain-small-tile-' + terrainId,
                    this.format_block('jstpl_tooltip_visible_mining_token', {
                        terrain_type: terrainType,
                        mining_token_id: miningTokenId,
                        terrain_type_name: this.terrainName[terrainType],
                        description: _('Visible mining token. You can obtain the resources from this token in the Build or Loot phase.'),
                    })
                );
            },

            addTooltipShippingTrackBonus: function(elementId, resourceType, bonusLevel) {
                let score = this.constants.shipping.BONUS_TILE_SCORE[resourceType][bonusLevel];
                this.addTooltipHtml(
                    elementId,
                    this.format_block('jstpl_tooltip_shipping_track_bonus', {
                        resource_type: resourceType,
                        level: bonusLevel,
                        resource_type_name: this.resourceName[resourceType],
                        score: score,
                        description: dojo.string.substitute(
                            _('Score ${score} points with this shipping track bonus.'), { score: score }
                        ),
                    })
                );
            },

            addTooltipInvestmentCard: function(elementId, cardId) {
                this.addTooltipHtml(
                    elementId,
                    this.format_block('jstpl_tooltip_investment_card', {
                        card_id: cardId,
                        title: _('Investment'),
                        description: [
                            _('This investment scores 8 points.'),
                            _('This investment scores 7 points and adds 1 additionnal influence in Silver terrain.'),
                            _('This investment scores 5 points and you can add 1 influence to a BoomTown space occupied by an other player.'),
                            _('This investment scores 9 points and you can look at 2 unrevealed mining tokens. If you choose this investment, you will not be able to cancel your turn.'),
                            _('This investment scores 6 points and you can advance two times on any shipping track.'),
                            _('This investment scores 13 points.'),
                            _('This investment scores 10 points.'),
                            _('This investment scores 10 points.'),
                            _('This investment scores 15 points.'),
                            _('This investment scores 9 points.'),
                            _('This investment scores 11 points.'),
                            _('This investment scores 11 poins and adds 1 additionnal influence in Wood terrain.'),
                            _('This investment scores 8 points and adds 1 additionnal influence in Copper terrain.'),
                            _('This investment scores 6 points and you can add 1 influence (to an unoccupied space) in BoomTown.'),
                            _('This investment scores 12 points.'),
                            _('This investment scores 11 points.'),
                            _('This investment scores 9 points and adds 1 additionnal influence in Gold terrain.'),
                            _('This investment scores 7 points and you can upgrade a camp to a settlement and gain the influence in the terrain.'),
                            _('This investment scores 7 points and you can add 2 resources to your supply track, without scoring suply track points.'),
                            _('This investment scores 7 points and you can advance one time on any shipping track.'),
                        ][cardId],
                    })
                );
            },

            addTooltipInvestmentBonus: function(elementId, bonus) {
                this.addTooltipHtml(
                    elementId,
                    this.format_block('jstpl_tooltip_investment_bonus', {
                        bonus: bonus,
                        title: _('Investment bonus'),
                        description: dojo.string.substitute(
                            _('Score ${score} points with this investment bonus. You get investment bonuses by buying investment cards first.'), { score: bonus }
                        ),
                    })
                );
            },

            addTooltipBoomTown: function(elementId, officeId) {
                let strId = officeId < 0 ? 'm' + Math.abs(officeId) : 'p' + officeId;
                this.addTooltipHtml(
                    elementId,
                    this.format_block('jstpl_tooltip_boomtown_office', {
                        office_id: strId,
                        title: {
                            m1: _('4 Points'),
                            m2: _('Copper'),
                            m3: _('Silver'),
                            m4: _('Gold'),
                            m5: _('Wood or Stone'),
                            p0: _('Docks'),
                            p1: _('Frontier Office'),
                            p2: _('Homestead Office'),
                            p3: _('Courthouse'),
                            p4: _('Deeds Office'),
                            p5: _('Depot'),
                            p6: _("Mayor's Office"),
                            p7: _('Saloon'),
                            p8: _("Sheriff's Office"),
                            p9: _('Shipping Office'),
                            p10: _("Surveyor's Office"),
                            p11: _('Town Hall'),
                            p12: _('Insurance Office'),
                            p13: _('Hoosegow'),
                        }[strId],
                        description: {
                            m1: _("Score 4 points."),
                            m2: _("Gain a resource: Copper. You can use either use this resource on this turn or add it to your supply track and score points."),
                            m3: _("Gain a resource: Silver. You can use either use this resource on this turn or add it to your supply track and score points."),
                            m4: _("Gain a resource: Gold. You can use either use this resource on this turn or add it to your supply track and score points."),
                            m5: _("Gain a resource: Either Wood or Stone. You can use either use this resource on this turn or add it to your supply track and score points."),
                            p0: _("At end game, score 1 point for each Camp and 2 points for each Settlement touching the water tile."),
                            p1: _("At end game, score 1 point per building (Camp or Settlement) touching the edge of the board."),
                            p2: _("At end game, score 2 points per influence in the terrain you have the least influence in."),
                            p3: _("At end game, score 1 point per Mining Token in the terrain you have the most influence in. Note: Score is per Mining Token, not per influence."),
                            p4: _("At end game, score 2 additional points for each 1st or 2nd place Terrain majority that you win. In a two player game, neither player scores the 1st place Terrain majority, only the 2nd place is awarded."),
                            p5: _("At end game, score 2 points for each building in your 2nd largest contiguous group. If you only have 1 contiguous group, then you score no points from the Depot."),
                            p6: _("At end game, score 3 points for each 2 influence tokens you have in Boomtown (rounded down)."),
                            p7: _("At end game, score 2 points for each Investment card in front of you."),
                            p8: _("At end game, score 1 point per building (Camp or Settlement) touching at least 1 opponents' building."),
                            p9: _("At end game, score 2 points for each Shipping Bonus Token you have collected."),
                            p10: _("At end game, score 1 point per building (Camp or Settlement) in longest straight line. The buildings do not have to be contiguous."),
                            p11: _("At end game, score 1 point for each Settlement you have on the board."),
                            p12: _("At end game, score 1 point per building (Camp or Settlement) touching at least 1 looted (empty) terrain."),
                            p13: _("At end game, score 1 point for each camp of any color in the Wanted Area."),
                        }[strId],
                    })
                );
            },

            addTooltipPlayerPanelInfluence: function(terrainType, playerId) {
                let id = 'gw-player-board-influence-group-' + terrainType + '-' + playerId;
                this.addTooltipHtml(
                    id,
                    this.format_block('jstpl_tooltip_planel_influence', {
                        terrain_type_name: this.terrainName[terrainType],
                        description: _("Influence level for this terrain type."),
                    })
                );
            },

            addTooltipPlayerPanelResources: function(ressourceType, playerId) {
                let id = 'gw-player-board-resource-group-' + ressourceType + '-' + playerId;
                this.addTooltipHtml(
                    id,
                    this.format_block('jstpl_tooltip_planel_resource', {
                        resource_type_name: this.resourceName[ressourceType],
                        description: _("Resources of this type that where activated from the supply track."),
                    })
                );
                id = 'gw-player-activated-resources-group-' + ressourceType;
                this.addTooltipHtml(
                    id,
                    this.format_block('jstpl_tooltip_planel_resource', {
                        resource_type_name: this.resourceName[ressourceType],
                        description: _("Resources of this type that where activated from the supply track."),
                    })
                );
            },

            addTooltipPlayerOrder: function(playerOrder) {
                let id = 'gw-order-token-p' + playerOrder;
                let title = [
                    _('1st player'),
                    _('2nd player'),
                    _('3rd player'),
                    _('4th player'),
                ][playerOrder - 1];
                this.addTooltipHtml(
                    id,
                    this.format_block('jstpl_tooltip_player_order', {
                        player_order: playerOrder,
                        title: title,
                        description: _("Represents the turn order and the initial resources."),
                    })
                );
            },

            /** More convenient version of ajaxcall, do not to specify game name, and any of the handlers */
            ajaxAction: function(action, args, func, err, lock) {
                if (!args) {
                    args = [];
                }
                delete args.action;
                if (!args.hasOwnProperty('lock') || args.lock) {
                    args.lock = true;
                } else {
                    delete args.lock;
                }
                if (typeof func == "undefined" || func == null) {
                    var self = this;
                    func = function(result) {};
                }

                // restore server server if error happened
                if (typeof err == "undefined") {
                    var self = this;
                    err = function(iserr, message) {
                        if (iserr) {
                            self.cancelLocalStateEffects();
                        }
                    };
                }
                var name = this.game_name;
                this.ajaxcall("/" + name + "/" + name + "/" + action + ".html", args, this, func, err);
            },

            cancelLocalStateEffects: function() {
                //workaround for problem restoreServerGameState and error calculating reflexion times...
                try {
                    if (this.last_server_state && this.last_server_state && this.last_server_state.reflexion && !this.last_server_state.reflexion.initial_ts) {
                        this.last_server_state.reflexion.initial_ts = dojo.clone(this.gamedatas.gamestate.reflexion.initial_ts);
                    }
                    if (this.last_server_state && this.last_server_state && this.last_server_state.reflexion && !this.last_server_state.reflexion.initial) {
                        this.last_server_state.reflexion.initial = dojo.clone(this.gamedatas.gamestate.reflexion.initial);
                    }
                } catch (err) {
                    //nothing
                }
                this.restoreServerGameState();
            },

            ///////////////////////////////////////////////////
            //// Player's action

            onSupplyTrackActivateClick: function(event, section) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseSupplyTrackToActivate')) {
                    return;
                }
                this.ajaxAction('chooseSupplyTrackToActivate', {
                    section: section,
                });
            },

            onChooseResourceToLeaveClick: function(event, resType) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseSupplyTrackResourceToLeave')) {
                    return;
                }
                this.ajaxAction('chooseSupplyTrackResourceToLeave', {
                    resourceType: resType,
                });
            },

            onChooseBoomTownClick: function(event, x, y) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseBoomTown')) {
                    return;
                }
                this.ajaxAction('chooseBoomTown', {
                    x: x,
                    y: y,
                });
            },

            onChooseFreeBoomTownClick: function(event, x, y) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseFreeBoomTown')) {
                    return;
                }
                this.ajaxAction('chooseFreeBoomTown', {
                    x: x,
                    y: y,
                });
            },

            onChooseFreeOccupiedBoomTownClick: function(event, x, y) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseFreeOccupiedBoomTown')) {
                    return;
                }
                this.ajaxAction('chooseFreeOccupiedBoomTown', {
                    x: x,
                    y: y,
                });
            },

            onChooseInvestmentCardClick: function(event, cardType) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseInvestment')) {
                    return;
                }
                this.ajaxAction('chooseInvestment', {
                    cardType: cardType,
                });
            },

            onChooseShippingTrackClick: function(event, resourceType) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseShippingTrack')) {
                    return;
                }
                this.ajaxAction('chooseShippingTrack', {
                    resourceType: resourceType,
                });
            },

            onChooseFreeShippingTrackClick: function(event, resourceType) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseFreeShippingTrack')) {
                    return;
                }
                this.ajaxAction('chooseFreeShippingTrack', {
                    resourceType: resourceType,
                });
            },

            onChooseMiningTokenClick: function(event, tokenId) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseMiningToken')) {
                    return;
                }
                this.ajaxAction('chooseMiningToken', {
                    tokenId: tokenId,
                });
            },

            onChooseMiningTokenToViewClick: function(event, tokenId) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseMiningTokenToView')) {
                    return;
                }
                this.ajaxAction('chooseMiningTokenToView', {
                    tokenId: tokenId,
                });
            },

            onChooseCampToUpgradeClick: function(event, tokenId) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseCampToUpgrade')) {
                    return;
                }
                this.ajaxAction('chooseCampToUpgrade', {
                    tokenId: tokenId,
                });
            },

            onSupplyTrackAddClick: function(event, section) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseSupplyTrackToAdd')) {
                    return;
                }
                this.ajaxAction('chooseSupplyTrackToAdd', {
                    section: section,
                });
            },
            onConfirmTurnClick: function(event) {
                dojo.stopEvent(event);
                if (!this.checkAction('confirmTurn')) {
                    return;
                }
                this.ajaxAction('confirmTurn', {});
            },
            onConfirmAfterCancelView: function(event) {
                dojo.stopEvent(event);
                if (!this.checkAction('confirmAfterCancelView')) {
                    return;
                }
                this.ajaxAction('confirmAfterCancelView', {});
            },
            onCancelTurnClick: function(event) {
                dojo.stopEvent(event);
                if (!this.checkAction('cancelTurn')) {
                    return;
                }
                this.ajaxAction('cancelTurn', {});
            },
            onViewedMiningTokenClick: function(event, tokenPositionId) {
                dojo.stopEvent(event);
                dojo.addClass('gw-terrain-token-' + tokenPositionId, 'gw-zoom-animation');
                setTimeout(function() {
                    dojo.removeClass('gw-terrain-token-' + tokenPositionId, 'gw-zoom-animation');
                }, 1000);
            },
            onChooseFreeResourceClick: function(event, resType) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseFreeResource')) {
                    return;
                }
                this.ajaxAction('chooseFreeResource', {
                    resourceType: resType,
                });
            },
            onChooseTradingPostResourceClick: function(event, resType) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseTradingPostResource')) {
                    return;
                }
                this.ajaxAction('chooseTradingPostResource', {
                    resourceType: resType,
                });
            },
            onChooseFreeResourceTrackClick: function(event, section) {
                dojo.stopEvent(event);
                if (!this.checkAction('chooseFreeResourceTrack')) {
                    return;
                }
                this.ajaxAction('chooseFreeResourceTrack', {
                    section: section,
                });
            },
            onTradingPostKeepResourceClick: function(event) {
                dojo.stopEvent(event);
                if (!this.checkAction('tradingPostKeepResource')) {
                    return;
                }
                this.ajaxAction('tradingPostKeepResource', {});
            },
            onTradingPostSupplyTrackAddClick: function(event, section) {
                dojo.stopEvent(event);
                if (!this.checkAction('tradingPostSupplyTrackAdd')) {
                    return;
                }
                this.ajaxAction('tradingPostSupplyTrackAdd', {
                    section: section,
                });
            },

            ///////////////////////////////////////////////////
            //// Reaction to cometD notifications

            /*
                setupNotifications:
                
                In this method, you associate each of your game notifications with your local method to handle it.
                
                Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                      your goldwest.game.php file.
    
            */
            setupNotifications: function() {
                dojo.subscribe(this.NTF_ALL.NTF_ACTIVATE_SUPPLY_TRACK, this, this.sizer('notif_ActivateSupplyTrack'));
                dojo.subscribe(this.NTF_ALL.NTF_LEAVE_IN_SUPPLY_TRACK, this, this.sizer('notif_LeaveInSupplyTrack'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_BOOMTOWN, this, this.sizer('notif_UpdateBoomTown'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_SHIPPING_TRACK, this, this.sizer('notif_UpdateShippingTrack'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_SHIPPING_BONUS, this, this.sizer('notif_UpdateShippingBonus'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_SCORE, this, this.sizer('notif_UpdateScore'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_INVESTMENT_CARD, this, this.sizer('notif_UpdateInvestmentCard'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_INVESTMENT_CARD_BONUS, this, this.sizer('notif_UpdateInvestmentCardBonus'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_GET_MINING_TOKEN, this, this.sizer('notif_UpdateGetMiningToken'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_ADD_RESOURCES_TO_TRACK, this, this.sizer('notif_UpdateAddResourcesToTrack'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_REVEAL_TOKEN, this, this.sizer('notif_UpdateRevealToken'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_INVESTMENT_INFLUENCE, this, this.sizer('notif_UpdateInvestmentInfluence'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_VIEW_MINING_TOKEN, this, this.sizer('notif_UpdateViewMiningToken'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_UPGRADE_TO_SETTLEMENT, this, this.sizer('notif_UpdateUpgradeToSettlement'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_ADD_RESOURCE_TO_HAND, this, this.sizer('notif_UpdateAddResourceToHand'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_TERRAIN_HIGHLIGHT, this, this.sizer('notif_UpdateTerrainHighlight'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_SCORE_WANTED, this, this.sizer('notif_UpdateScoreWanted'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_SCORE_INFLUENCE, this, this.sizer('notif_UpdateScoreInfluence'));
                dojo.subscribe(this.NTF_ALL.NTF_UPDATE_SCORE_BOOMTOWN, this, this.sizer('notif_UpdateScoreBoomTown'));

                this.notifqueue.setSynchronous(this.NTF_ALL.NTF_UPDATE_SHIPPING_TRACK, 500);
                this.notifqueue.setSynchronous(this.NTF_ALL.NTF_UPDATE_SHIPPING_BONUS, 500);
                this.notifqueue.setSynchronous(this.NTF_ALL.NTF_UPDATE_TERRAIN_HIGHLIGHT, 2000);
                this.notifqueue.setSynchronous(this.NTF_ALL.NTF_UPDATE_SCORE_WANTED, 2000);
                this.notifqueue.setSynchronous(this.NTF_ALL.NTF_UPDATE_SCORE_INFLUENCE, 2000);
                this.notifqueue.setSynchronous(this.NTF_ALL.NTF_UPDATE_SCORE_BOOMTOWN, 2000);
            },

            // From this point and below, you can write your game notifications handling methods

            notif_ActivateSupplyTrack: function(notif) {
                this.unselectEverything();
                let sectionId = 'gw-supply-track-' + notif.args.player_id + '-' + notif.args.section;
                let playerBoardId = 'player_board_' + notif.args.player_id;
                let zone = this.playerSupplyTrack[notif.args.player_id][sectionId];
                let zoneItems = zone.getAllItems();
                for (let i in zoneItems) {
                    zone.removeFromZone(zoneItems[i], true /*destroy*/ , playerBoardId);
                }
                this.updatePlayerPanelSupplyTrack(notif.args.player_id, notif.args.section);
                this.updateActivatedResources(notif.args);
            },
            notif_LeaveInSupplyTrack: function(notif) {
                this.unselectEverything();
                let activatedResourceId = 'gw-player-board-resource-group-' + notif.args.resourceType + '-' + notif.args.player_id;
                if (this.player_id == notif.args.player_id) {
                    activatedResourceId = 'gw-player-activated-resources-group-' + notif.args.resourceType;
                }
                let newResource = this.createResource(
                    notif.args.resourceType,
                    activatedResourceId
                );
                let sectionId = 'gw-supply-track-' + notif.args.player_id + '-' + notif.args.section;
                this.attachToNewParent(newResource.id, sectionId);
                this.playerSupplyTrack[notif.args.player_id]["gw-supply-track-" + notif.args.player_id + "-" + notif.args.section].placeInZone(
                    newResource.id,
                    newResource.number // weigth
                );
                this.updatePlayerPanelSupplyTrack(notif.args.player_id, notif.args.section);
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateBoomTown: function(notif) {
                this.unselectEverything();
                let newSettlement = this.createAnonymousSettlement(
                    this.playerColorName[notif.args.player_id],
                    'player_board_' + notif.args.player_id
                );
                let destinationId = 'gw-boomtown-tile-' + notif.args.x + '-' + notif.args.y;
                this.attachToNewParent(newSettlement.id, destinationId);
                let anim = this.slideToObject(newSettlement.id, destinationId);
                dojo.connect(anim, 'onEnd', this, function(node) {
                    this.boomTownSpot[notif.args.x][notif.args.y].placeInZone(
                        newSettlement.id,
                        notif.args.player_id // weigth
                    );
                });
                anim.play();
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateShippingTrack: function(notif) {
                this.unselectEverything();
                let stagecoachId = 'gw-stagecoach-' + notif.args.resourceType + '-' + notif.args.player_id;
                this.shippingTrackSpot[notif.args.resourceType][notif.args.newDistance].placeInZone(
                    stagecoachId,
                    notif.args.player_id // weigth
                );
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateShippingBonus: function(notif) {
                this.unselectEverything();
                let id = 'gw-shipping-bonus-' + notif.args.resourceType + '-' + notif.args.bonusLevel + '-' + notif.args.bonusPos;
                this.playerBonusZone[notif.args.player_id].placeInZone(id);
                this.addTooltipShippingTrackBonus(id, notif.args.resourceType, notif.args.bonusLevel);
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateScore: function(notif) {
                this.unselectEverything();
                if ('totalScore' in notif.args && notif.args.totalScore !== null) {
                    this.updatePlayerScore(notif.args.player_id, notif.args.totalScore);
                }
                this.updateLastTurnBanner(notif.args);
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateInvestmentCard: function(notif) {
                this.unselectEverything();
                let playerInvestmentsId = 'gw-player-board-investments-' + notif.args.player_id;
                dojo.query('#' + playerInvestmentsId).removeClass('gw-hidden');
                this.investmentTable.removeFromStockById(notif.args.card, playerInvestmentsId);
                this.playerInvestmentsZone[notif.args.player_id].addToStockWithId(notif.args.card, notif.args.card);
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateInvestmentCardBonus: function(notif) {
                this.unselectEverything();
                let id = 'gw-investment-bonus-' + notif.args.bonus;
                this.playerBonusZone[notif.args.player_id].placeInZone(id);
                this.addTooltipInvestmentBonus(id, notif.args.bonus);
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateGetMiningToken: function(notif) {
                this.unselectEverything();
                // Move selected token to player information
                let miningTokenOriginId = 'gw-mining-token-' + notif.args.tokenPositionId;
                let destinationId = 'player_board_' + notif.args.player_id;
                let animMiningToken = this.slideToObject(miningTokenOriginId, destinationId);
                dojo.connect(animMiningToken, 'onEnd', this, function(node) {
                    dojo.destroy(miningTokenOriginId);
                });
                animMiningToken.play();
                // Create tokens for influence track
                let tokenPositionId = 'gw-terrain-small-tile-' + notif.args.tokenPositionId;
                this.removeTooltip(tokenPositionId);
                let terrain = [];
                if (!notif.args.isLoot) {
                    terrain[0] = this.createTerrain(notif.args.terrainType, tokenPositionId);
                    if (notif.args.isSettlement) {
                        terrain[1] = this.createAnonymousSettlement(this.playerColorName[notif.args.player_id], tokenPositionId);
                    }
                }
                for (let i in terrain) {
                    let distance = parseInt(notif.args.influenceBeforeDistance) + parseInt(i) + 1;
                    this.playerInfluenceZone[notif.args.player_id][notif.args.terrainType][distance].placeInZone(terrain[i].id);
                    this.playerInfluenceCounter[notif.args.player_id][notif.args.terrainType].toValue(distance);
                }
                // Move camp to loot or to to the terrain, possibly with a settlement
                if (notif.args.isSettlement) {
                    let settlementId = 'gw-settlement-' + notif.args.player_id + '-' + notif.args.settlementBefore;
                    this.attachToNewParent(settlementId, tokenPositionId);
                    let anim = this.slideToObject(settlementId, tokenPositionId);
                    dojo.connect(anim, 'onEnd', function() {
                        $(settlementId).style.top = '';
                        $(settlementId).style.left = '';
                    });
                    anim.play();
                }
                let campId = 'gw-camp-' + notif.args.player_id + '-' + notif.args.campBefore;
                if (!notif.args.isLoot) {
                    this.attachToNewParent(campId, tokenPositionId);
                    let anim = this.slideToObject(campId, tokenPositionId);
                    anim.play();
                } else {
                    this.wantedZone.placeInZone(
                        campId,
                        notif.args.player_id // weight
                    );
                }
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateAddResourcesToTrack: function(notif) {
                this.unselectEverything();
                let originId = 'player_board_' + notif.args.player_id;
                let sectionId = 'gw-supply-track-' + notif.args.player_id + '-' + notif.args.section;
                for (let i in notif.args.resources) {
                    let resourceType = notif.args.resources[i];
                    let newResource = this.createResource(resourceType, originId);
                    this.attachToNewParent(newResource.id, sectionId);
                    this.playerSupplyTrack[notif.args.player_id][sectionId].placeInZone(
                        newResource.id,
                        newResource.number // weigth
                    );
                }
                this.updatePlayerPanelSupplyTrack(notif.args.player_id, notif.args.section);
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateRevealToken: function(notif) {
                this.unselectEverything();
                for (let id in notif.args.revealedInfo) {
                    let newToken = this.createMiningToken(
                        notif.args.revealedInfo[id].terrainType,
                        notif.args.revealedInfo[id].miningTokenId,
                        'gw-board',
                        id).object;
                    let oldToken = $('gw-terrain-token-' + id);
                    newToken.style.left = oldToken.style.left;
                    newToken.style.top = oldToken.style.top;
                    this.fadeOutAndDestroy(oldToken);
                    // Remove from viewed if it is there
                    if (this.playerViewMiningTokenZone !== null) {
                        this.playerViewMiningTokenZone.removeFromZone(
                            'gw-mining-token-PLAYER-VIEWED-' + id,
                            true // destroy
                        )
                    }
                    this.addTooltipVisibleMiningToken(
                        notif.args.revealedInfo[id].terrainType,
                        notif.args.revealedInfo[id].miningTokenId,
                        id
                    );
                }
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateInvestmentInfluence: function(notif) {
                this.unselectEverything();
                // Create token for influence track
                let tokenPositionId = 'gw-investments';
                let terrain = this.createAnonymousSettlement(this.playerColorName[notif.args.player_id], tokenPositionId);
                this.playerInfluenceZone[notif.args.player_id][notif.args.terrainType][notif.args.influenceBeforeDistance + 1].placeInZone(terrain.id);
                this.playerInfluenceCounter[notif.args.player_id][notif.args.terrainType].toValue(notif.args.influenceBeforeDistance + 1);
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateViewMiningToken: function(notif) {
                this.unselectEverything();
                let newToken = this.createMiningToken(
                    notif.args.terrainType,
                    notif.args.miningTokenId,
                    'gw-terrain-small-tile-' + notif.args.tokenPositionId,
                    'PLAYER-VIEWED-' + notif.args.tokenPositionId
                );
                this.playerViewMiningTokenZone.placeInZone(
                    newToken.id,
                    newToken.number // weigth
                );
                dojo.addClass(newToken.id, 'gw-permanent-clickable');
                this.connect($(newToken.id), 'onclick', function(event) { this.onViewedMiningTokenClick(event, notif.args.tokenPositionId); });
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateUpgradeToSettlement: function(notif) {
                this.unselectEverything();
                let tokenPositionId = 'gw-terrain-small-tile-' + notif.args.tokenPositionId;
                let terrain = this.createAnonymousSettlement(this.playerColorName[notif.args.player_id], tokenPositionId);
                this.playerInfluenceZone[notif.args.player_id][notif.args.terrainType][notif.args.influenceBeforeDistance + 1].placeInZone(terrain.id);
                this.playerInfluenceCounter[notif.args.player_id][notif.args.terrainType].toValue(notif.args.influenceBeforeDistance + 1);
                // Move settlement to terrain
                let settlementId = 'gw-settlement-' + notif.args.player_id + '-' + notif.args.settlementBefore;
                this.attachToNewParent(settlementId, tokenPositionId);
                let animSettlement = this.slideToObject(settlementId, tokenPositionId);
                dojo.connect(animSettlement, 'onEnd', function() {
                    $(settlementId).style.top = '';
                    $(settlementId).style.left = '';
                });
                animSettlement.play();
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateAddResourceToHand: function(notif) {
                this.unselectEverything();
                let originId = 'page-title';
                let destinationId = 'player_board_' + notif.args.player_id;
                let newResource = this.createResource(notif.args.resourceType, originId);
                this.attachToNewParent(newResource.id, destinationId);
                this.slideToObjectAndDestroy(newResource.id, destinationId);
                this.updateActivatedResources(notif.args);
            },
            notif_UpdateTerrainHighlight: function(notif) {
                this.updatePlayerScore(notif.args.player_id, notif.args.totalScore);
                let x = 0;
                let y = 0;
                for (let i in notif.args.ids) {
                    let terrainId = 'gw-terrain-small-tile-' + notif.args.ids[i];
                    this.displayFadeOutHighlight(terrainId);
                    x += $(terrainId).offsetLeft;
                    y += $(terrainId).offsetTop;
                }
                // Need to remove half width andd height since displayScoring centers the score
                x = (x / notif.args.ids.length) - $('gw-board').offsetWidth / 2;
                y = (y / notif.args.ids.length) - $('gw-board').offsetHeight / 2;
                this.displayBigScore('gw-board', notif.args.player_id, notif.args.score, x, y);
            },
            notif_UpdateScoreWanted: function(notif) {
                this.updatePlayerScore(notif.args.player_id, notif.args.totalScore);
                this.displayBigScore('gw-wanted', notif.args.player_id, notif.args.score * -1);
            },
            notif_UpdateScoreInfluence: function(notif) {
                this.updatePlayerScore(notif.args.player_id, notif.args.totalScore);
                for (let i = 0; i < Math.min(notif.args.influence, this.constants.playerboard.MAX_INFLUENCE_DISPLAY); ++i) {
                    let influenceId = 'gw-influence-track-' + notif.args.player_id + '-' + notif.args.terrainType + '-' + i;
                    this.displayFadeOutHighlight(influenceId);
                    if (i + 1 == notif.args.influence) {
                        this.displayBigScore(influenceId, notif.args.player_id, notif.args.score);
                    }
                }
                this.displayBigScore('gw-board-influence-score-' + notif.args.terrainType, notif.args.player_id, notif.args.score);
            },
            notif_UpdateScoreBoomTown: function(notif) {
                this.updatePlayerScore(notif.args.player_id, notif.args.totalScore);
                let tileId = 'gw-boomtown-tile-' + notif.args.x + '-' + notif.args.y;
                this.displayBigScore(tileId, notif.args.player_id, notif.args.score);
                for (let i in notif.args.ids) {
                    let terrainId = 'gw-terrain-small-tile-' + notif.args.ids[i];
                    this.displayFadeOutHighlight(terrainId);
                }
                dojo.query('#gw-boomtown').addClass('gw-scoring');
                clearTimeout(this.boomtownScoringTimer);
                this.boomtownScoringTimer = setTimeout(function() {
                    dojo.query('#gw-boomtown').removeClass('gw-scoring');
                }, 3000);
            },
        });
    });
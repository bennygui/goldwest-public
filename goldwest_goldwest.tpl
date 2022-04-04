{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- goldwest implementation : © Guillaume Benny bennygui@gmail.com
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------
-->

<div id="gw-warn-no-undo" class="gw-top-warning gw-hidden">
    <div>{WARN_NO_UNDO_TEXT}</div>
</div>
<div id="gw-warn-last-turn" class="gw-top-warning gw-hidden">
    <div>{WARN_LAST_TURN_TEXT}</div>
</div>

<div id="gw-player-activated-resources" class="gw-player-panel gw-hidden">
    <div id="gw-player-activated-resources-group-GO" class="gw-player-board-resource-group">
        <div class="gw-player-board-resource gw-GO"></div>
        <div class="gw-player-board-resource-count" id="gw-player-activated-resource-GO-count">0</div>
    </div>
    <div id="gw-player-activated-resources-group-SI" class="gw-player-board-resource-group">
        <div class="gw-player-board-resource gw-SI"></div>
        <div class="gw-player-board-resource-count" id="gw-player-activated-resource-SI-count">0</div>
    </div>
    <div id="gw-player-activated-resources-group-CO" class="gw-player-board-resource-group">
        <div class="gw-player-board-resource gw-CO"></div>
        <div class="gw-player-board-resource-count" id="gw-player-activated-resource-CO-count">0</div>
    </div>
    <div id="gw-player-activated-resources-group-WO" class="gw-player-board-resource-group">
        <div class="gw-player-board-resource gw-WO"></div>
        <div class="gw-player-board-resource-count" id="gw-player-activated-resource-WO-count">0</div>
    </div>
    <div id="gw-player-activated-resources-group-ST" class="gw-player-board-resource-group">
        <div class="gw-player-board-resource gw-ST"></div>
        <div class="gw-player-board-resource-count" id="gw-player-activated-resource-ST-count">0</div>
    </div>
</div>

<div id="gw-global-supply-wrap" class="gw-hidden">
    <div id="gw-global-supply">
        <a href="#" class="action-button bgabutton bgabutton_blue" onclick="return false;" id="gw-global-supply-button-3">{SECTION_3}</a>
        <a href="#" class="action-button bgabutton bgabutton_blue" onclick="return false;" id="gw-global-supply-button-2">{SECTION_2}</a>
        <a href="#" class="action-button bgabutton bgabutton_blue" onclick="return false;" id="gw-global-supply-button-1">{SECTION_1}</a>
        <a href="#" class="action-button bgabutton bgabutton_blue" onclick="return false;" id="gw-global-supply-button-0">{SECTION_0}</a>
        <div id="gw-global-supply-track-0" class="gw-global-supply-track gw-p0"></div>
        <div id="gw-global-supply-track-1" class="gw-global-supply-track gw-p1"></div>
        <div id="gw-global-supply-track-2" class="gw-global-supply-track gw-p2"></div>
        <div id="gw-global-supply-track-3" class="gw-global-supply-track gw-p3"></div>
    </div>
</div>

<div id="gw-global-board" class="whiteblock">
    <div id="gw-board-wrap">
        <div id="gw-board">
            <!-- BEGIN terrain-large-tile -->
            <div class="gw-terrain-large-tile gw-pos-{POS} gw-tile-{TILE} gw-rotation-{ROTATION}"></div>
            <!-- END terrain-large-tile -->
            <!-- BEGIN terrain-small-tile -->
            <div id="gw-terrain-small-tile-{TERRAIN_ID}" class="gw-terrain-small-tile gw-{TERRAIN_TYPE}" style="left: {LEFT}px; top:{TOP}px;"></div>
            <!-- END terrain-small-tile -->
            <div>
                <div id="gw-investment-bonus-spot-1" class="gw-investment-bonus-spot gw-p1"></div>
                <div id="gw-investment-bonus-spot-2" class="gw-investment-bonus-spot gw-p2"></div>
                <div id="gw-investment-bonus-spot-3" class="gw-investment-bonus-spot gw-p3"></div>
                <div id="gw-investment-bonus-spot-4" class="gw-investment-bonus-spot gw-p4"></div>
                <div id="gw-investment-bonus-spot-5" class="gw-investment-bonus-spot gw-p5"></div>
            </div>
            <div id='gw-boomtown'>
                <!-- BEGIN boomtown-tile -->
                <div id="gw-boomtown-tile-{POS_X}-{POS_Y}" class="gw-boomtown-tile gw-{OFFICE_ID}" style="left: {LEFT}px; top:{TOP}px;"></div>
                <!-- END boomtown-tile -->
                <!-- BEGIN boomtown-office-double -->
                <div class="gw-boomtown-office-double gw-{OFFICE_ID} gw-{VERTICAL_CLASS}" style="left: {LEFT}px; top:{TOP}px;"></div>
                <!-- END boomtown-office-double -->
                <!-- BEGIN boomtown-office-single -->
                <div class="gw-boomtown-office-single gw-{OFFICE_ID}" style="left: {LEFT}px; top:{TOP}px;"></div>
                <!-- END boomtown-office-single -->
            </div>
            <div id="gw-shipping-track">
                <!-- BEGIN shipping-track -->
                <div id="gw-track-spot-{RESOURCE_TYPE}-{DISTANCE}" class="gw-track-spot gw-{SPOT_TYPE}" style="left: {LEFT}px; top:{TOP}px;"></div>
                <!-- END shipping-track -->
            </div>
            <div id="gw-wanted">
                <div id="gw-wanted-zone">
                </div>
            </div>
            <div id="gw-board-group-tooltip"></div>
            <div id="gw-board-influence-tooltip"></div>
            <div id="gw-board-influence-score-WO"></div>
            <div id="gw-board-influence-score-GO"></div>
            <div id="gw-board-influence-score-SI"></div>
            <div id="gw-board-influence-score-CO"></div>
            <div id="gw-shipping-track-tooltip-2"></div>
            <div id="gw-shipping-track-tooltip-3"></div>
            <div id="gw-boomtown-horizontal-tootip-1"></div>
            <div id="gw-boomtown-horizontal-tootip-2"></div>
            <div id="gw-boomtown-horizontal-tootip-3"></div>
            <div id="gw-boomtown-vertical-tootip-1"></div>
            <div id="gw-boomtown-vertical-tootip-2"></div>
            <div id="gw-boomtown-vertical-tootip-3"></div>
        </div>
    </div>
    <div id="gw-investments"></div>
</div>

<!-- BEGIN player-board -->
<div class="whiteblock">
    <h3 style='color: #{PLAYER_COLOR};'>{PLAYER_NAME}</h3>
    <div class="gw-player-board-wrap-wrap">
        <div class="gw-player-board-wrap">
            <div id='gw-player-board-{PLAYER_ID}' class='gw-player-board'>
                <div id="gw-supply-track-{PLAYER_ID}-0" class="gw-supply-track gw-p0"></div>
                <div id="gw-supply-track-{PLAYER_ID}-1" class="gw-supply-track gw-p1"></div>
                <div id="gw-supply-track-{PLAYER_ID}-2" class="gw-supply-track gw-p2"></div>
                <div id="gw-supply-track-{PLAYER_ID}-3" class="gw-supply-track gw-p3"></div>
                <div id="gw-influence-track-{PLAYER_ID}-WO-0" class="gw-influence-track gw-p0 gw-WO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-WO-1" class="gw-influence-track gw-p1 gw-WO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-WO-2" class="gw-influence-track gw-p2 gw-WO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-WO-3" class="gw-influence-track gw-p3 gw-WO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-WO-4" class="gw-influence-track gw-p4 gw-WO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-WO-5" class="gw-influence-track gw-p5 gw-WO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-WO-6" class="gw-influence-track gw-p6 gw-WO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-GO-0" class="gw-influence-track gw-p0 gw-GO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-GO-1" class="gw-influence-track gw-p1 gw-GO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-GO-2" class="gw-influence-track gw-p2 gw-GO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-GO-3" class="gw-influence-track gw-p3 gw-GO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-GO-4" class="gw-influence-track gw-p4 gw-GO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-GO-5" class="gw-influence-track gw-p5 gw-GO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-GO-6" class="gw-influence-track gw-p6 gw-GO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-SI-0" class="gw-influence-track gw-p0 gw-SI"></div>
                <div id="gw-influence-track-{PLAYER_ID}-SI-1" class="gw-influence-track gw-p1 gw-SI"></div>
                <div id="gw-influence-track-{PLAYER_ID}-SI-2" class="gw-influence-track gw-p2 gw-SI"></div>
                <div id="gw-influence-track-{PLAYER_ID}-SI-3" class="gw-influence-track gw-p3 gw-SI"></div>
                <div id="gw-influence-track-{PLAYER_ID}-SI-4" class="gw-influence-track gw-p4 gw-SI"></div>
                <div id="gw-influence-track-{PLAYER_ID}-SI-5" class="gw-influence-track gw-p5 gw-SI"></div>
                <div id="gw-influence-track-{PLAYER_ID}-SI-6" class="gw-influence-track gw-p6 gw-SI"></div>
                <div id="gw-influence-track-{PLAYER_ID}-CO-0" class="gw-influence-track gw-p0 gw-CO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-CO-1" class="gw-influence-track gw-p1 gw-CO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-CO-2" class="gw-influence-track gw-p2 gw-CO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-CO-3" class="gw-influence-track gw-p3 gw-CO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-CO-4" class="gw-influence-track gw-p4 gw-CO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-CO-5" class="gw-influence-track gw-p5 gw-CO"></div>
                <div id="gw-influence-track-{PLAYER_ID}-CO-6" class="gw-influence-track gw-p6 gw-CO"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-0" class="gw-player-board-camp gw-r0 gw-c0"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-2" class="gw-player-board-camp gw-r0 gw-c1"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-4" class="gw-player-board-camp gw-r0 gw-c2"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-6" class="gw-player-board-camp gw-r0 gw-c3"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-8" class="gw-player-board-camp gw-r0 gw-c4"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-10" class="gw-player-board-camp gw-r0 gw-c5"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-1" class="gw-player-board-camp gw-r1 gw-c0"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-3" class="gw-player-board-camp gw-r1 gw-c1"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-5" class="gw-player-board-camp gw-r1 gw-c2"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-7" class="gw-player-board-camp gw-r1 gw-c3"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-9" class="gw-player-board-camp gw-r1 gw-c4"></div>
                <div id="gw-player-board-camp-{PLAYER_ID}-11" class="gw-player-board-camp gw-r1 gw-c5"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-0" class="gw-player-board-settlement gw-r0 gw-c0"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-2" class="gw-player-board-settlement gw-r0 gw-c1"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-4" class="gw-player-board-settlement gw-r0 gw-c2"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-6" class="gw-player-board-settlement gw-r0 gw-c3"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-8" class="gw-player-board-settlement gw-r0 gw-c4"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-10" class="gw-player-board-settlement gw-r0 gw-c5"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-1" class="gw-player-board-settlement gw-r1 gw-c0"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-3" class="gw-player-board-settlement gw-r1 gw-c1"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-5" class="gw-player-board-settlement gw-r1 gw-c2"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-7" class="gw-player-board-settlement gw-r1 gw-c3"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-9" class="gw-player-board-settlement gw-r1 gw-c4"></div>
                <div id="gw-player-board-settlement-{PLAYER_ID}-11" class="gw-player-board-settlement gw-r1 gw-c5"></div>
                <div class="gw-player-board-score-tooltip-0"></div>
                <div class="gw-player-board-score-tooltip-1"></div>
                <div class="gw-player-board-score-tooltip-2"></div>
                <div class="gw-player-board-score-tooltip-3"></div>
            </div>
        </div>
    </div>
    <div id="gw-player-board-bonus-{PLAYER_ID}" class="gw-player-board-bonus"></div>
    <div id="gw-player-board-investments-{PLAYER_ID}" class="gw-player-board-investments"></div>
</div>
<!-- END player-board -->

<script type="text/javascript">
    // Javascript HTML templates
    var jstpl_stagecoach = '<div class="gw-stagecoach gw-${player_color_name}" id="gw-stagecoach-${resource_type}-${player_id}"></div>';
    var jstpl_shippingbonus = '<div class="gw-shipping-bonus gw-${resource_type}-${level}" id="gw-shipping-bonus-${resource_type}-${level}-${pos}"></div>';
    var jstpl_investmentbonus = '<div class="gw-investment-bonus gw-p${bonus}" id="gw-investment-bonus-${bonus}"></div>';
    var jstpl_resource = '<div class="gw-resource gw-${resource_type}" data-resource="${resource_type}" id="gw-resource-${resource_id}"></div>';
    var jstpl_camp = '<div class="gw-camp gw-${player_color_name}" id="gw-camp-${player_id}-${camp_index}"></div>';
    var jstpl_anonymous_camp = '<div class="gw-camp gw-${player_color_name}"></div>';
    var jstpl_settlement = '<div class="gw-settlement gw-${player_color_name}" id="gw-settlement-${player_id}-${settlement_index}"></div>';
    var jstpl_anonymous_settlement = '<div class="gw-settlement gw-${player_color_name}" id="gw-anonymous-settlement-${settlement_id}"></div>';
    var jstpl_mining_token = '<div id="gw-mining-token-${terrain_or_token_id}" class="gw-mining-token gw-${terrain_type}-${mining_token_id}"></div>';
    var jstpl_terrain_token = '<div id="gw-terrain-token-${terrain_or_token_id}" class="gw-mining-token gw-${terrain_type}"></div>';
    var jstpl_player_panel =
        '<div class="gw-player-panel">' +
        '   <div class="gw-player-mining-token" id="gw-player-mining-token-${player_id}"></div>' +
        '</div>' +
        '<div class="gw-player-panel">' +
        '   <div class="gw-miner gw-${player_color_name}"></div>' +
        '   <div class="gw-order-token gw-p${player_order}" id="gw-order-token-p${player_order}"></div>' +
        '   <div class="gw-player-board-camp-group" id="gw-player-board-${player_id}-camp-group">' +
        '      <div class="gw-player-panel-camp gw-${player_color_name}"></div>' +
        '      <div class="gw-player-board-camp-count"><span id="gw-player-board-${player_id}-camp-count">12</span>/<span id="gw-player-board-${player_id}-camp-count-total">12</span></div>' +
        '   </div>' +
        '   <div class="gw-player-board-settlement-group" id="gw-player-board-${player_id}-settlement-group">' +
        '      <div class="gw-player-panel-camp gw-${player_color_name}"></div>' +
        '      <div class="gw-player-panel-settlement gw-${player_color_name}"></div>' +
        '      <div class="gw-player-board-settlement-count" id="gw-player-board-${player_id}-settlement-count">0</div>' +
        '   </div>' +
        '</div>' +
        '<div class="gw-player-panel-supply-wrap">' +
        '   <div id="gw-player-panel-supply-${player_id}" class="gw-player-panel-supply">' +
        '       <div id="gw-player-panel-supply-track-${player_id}-0" class="gw-player-panel-supply-track gw-p0"></div>' +
        '       <div id="gw-player-panel-supply-track-${player_id}-1" class="gw-player-panel-supply-track gw-p1"></div>' +
        '       <div id="gw-player-panel-supply-track-${player_id}-2" class="gw-player-panel-supply-track gw-p2"></div>' +
        '       <div id="gw-player-panel-supply-track-${player_id}-3" class="gw-player-panel-supply-track gw-p3"></div>' +
        '   </div>' +
        '</div>' +
        '<div class="gw-player-panel">' +
        '   <div id="gw-player-board-influence-group-WO-${player_id}" class="gw-player-board-influence-group">' +
        '      <div class="gw-player-board-influence gw-WO"></div>' +
        '      <div class="gw-player-board-influence-count" id="gw-player-board-influence-${player_id}-WO-count">0</div>' +
        '   </div>' +
        '   <div id="gw-player-board-influence-group-GO-${player_id}" class="gw-player-board-influence-group">' +
        '      <div class="gw-player-board-influence gw-GO"></div>' +
        '      <div class="gw-player-board-influence-count" id="gw-player-board-influence-${player_id}-GO-count">0</div>' +
        '   </div>' +
        '   <div id="gw-player-board-influence-group-SI-${player_id}" class="gw-player-board-influence-group">' +
        '      <div class="gw-player-board-influence gw-SI"></div>' +
        '      <div class="gw-player-board-influence-count" id="gw-player-board-influence-${player_id}-SI-count">0</div>' +
        '   </div>' +
        '   <div id="gw-player-board-influence-group-CO-${player_id}" class="gw-player-board-influence-group">' +
        '      <div class="gw-player-board-influence gw-CO"></div>' +
        '      <div class="gw-player-board-influence-count" id="gw-player-board-influence-${player_id}-CO-count">0</div>' +
        '   </div>' +
        '</div>' +
        '<div id="gw-player-panel-${player_id}-resources" class="gw-player-panel">' +
        '   <div id="gw-player-board-resource-group-GO-${player_id}" class="gw-player-board-resource-group">' +
        '      <div class="gw-player-board-resource gw-GO"></div>' +
        '      <div class="gw-player-board-resource-count" id="gw-player-resource-${player_id}-GO-count">0</div>' +
        '   </div>' +
        '   <div id="gw-player-board-resource-group-SI-${player_id}" class="gw-player-board-resource-group">' +
        '      <div class="gw-player-board-resource gw-SI"></div>' +
        '      <div class="gw-player-board-resource-count" id="gw-player-resource-${player_id}-SI-count">0</div>' +
        '   </div>' +
        '   <div id="gw-player-board-resource-group-CO-${player_id}" class="gw-player-board-resource-group">' +
        '      <div class="gw-player-board-resource gw-CO"></div>' +
        '      <div class="gw-player-board-resource-count" id="gw-player-resource-${player_id}-CO-count">0</div>' +
        '   </div>' +
        '   <div id="gw-player-board-resource-group-WO-${player_id}" class="gw-player-board-resource-group">' +
        '      <div class="gw-player-board-resource gw-WO"></div>' +
        '      <div class="gw-player-board-resource-count" id="gw-player-resource-${player_id}-WO-count">0</div>' +
        '   </div>' +
        '   <div id="gw-player-board-resource-group-ST-${player_id}" class="gw-player-board-resource-group">' +
        '      <div class="gw-player-board-resource gw-ST"></div>' +
        '      <div class="gw-player-board-resource-count" id="gw-player-resource-${player_id}-ST-count">0</div>' +
        '   </div>' +
        '</div>' +
        '<div id="gw-player-panel-${player_id}-view-mining-token" class="gw-player-panel-view-mining-token">' +
        '</div>' +
        '';
    var jstpl_tooltip_invisible_mining_token = '<h3>${terrain_type_name}</h3><p>${description}</p>';
    var jstpl_tooltip_visible_mining_token = '<div class="gw-tooltip-wiggle gw-tooltip-mining-token gw-${terrain_type}-${mining_token_id}"></div><h3>${terrain_type_name}</h3><p>${description}</p>';
    var jstpl_tooltip_shipping_track_bonus = '<div class="gw-tooltip-wiggle gw-tooltip-shipping-bonus gw-${resource_type}-${level}"></div><h3>${resource_type_name}: ${score}</h3><p>${description}</p>';
    var jstpl_tooltip_investment_card = '<div class="gw-tooltip-wiggle gw-tooltip-investment-card gw-p${card_id}"></div><h3>${title}</h3><p>${description}</p>';
    var jstpl_tooltip_investment_bonus = '<div class="gw-tooltip-wiggle gw-tooltip-investment-bonus gw-p${bonus}"></div><h3>${title}</h3><p>${description}</p>';
    var jstpl_tooltip_boomtown_office = '<div class="gw-tooltip-wiggle gw-tooltip-office gw-${office_id}"></div><h3>${title}</h3><p>${description}</p>';
    var jstpl_tooltip_planel_influence = '<h3>${terrain_type_name}</h3><p>${description}</p>';
    var jstpl_tooltip_planel_resource = '<h3>${resource_type_name}</h3><p>${description}</p>';
    var jstpl_tooltip_player_order = '<div class="gw-tooltip-wiggle gw-order-token-tooltip gw-p${player_order}"></div><h3>${title}</h3><p>${description}</p>';
</script>

{OVERALL_GAME_FOOTER}
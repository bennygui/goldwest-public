-- ------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- goldwest implementation : © Guillaume Benny bennygui@gmail.com
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-- -----

-- dbmodel.sql

ALTER TABLE `player` ADD `player_color_name` varchar(10) NOT NULL DEFAULT '';
ALTER TABLE `player` ADD `zombie_turn_count` smallint(5) NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS `board` (
  `x` smallint(5) NOT NULL,
  `y` smallint(5) NOT NULL,
  `terrain_type` char(2) NOT NULL,
  `spot_status` char(2) NULL,
  `mining_token_id` smallint(5) NULL,
  `player_id` int(10) unsigned NULL,
  `investment_view_player_id` int(10) unsigned NULL,
  PRIMARY KEY (`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `board_large_tile` (
  `pos` smallint(5) NOT NULL,
  `tile` smallint(5) NOT NULL,
  `rotation` smallint(5) NOT NULL,
  PRIMARY KEY (`pos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `boomtown` (
  `x` smallint(5) NOT NULL,
  `y` smallint(5) NOT NULL,
  `office_id` smallint(5) NOT NULL,
  `player_id` int(10) unsigned NULL,
  `investment_player_id` int(10) unsigned NULL,
  PRIMARY KEY (`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `investment` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `investment_bonus` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `card_type` varchar(16) NOT NULL,
  `card_type_arg` int(11) NOT NULL,
  `card_location` varchar(16) NOT NULL,
  `card_location_arg` int(11) NOT NULL,
  PRIMARY KEY (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `shipping_track` (
  `player_id` int(10) unsigned NOT NULL,
  `resource_type` char(2) NOT NULL,
  `distance` smallint(5) NOT NULL,
  PRIMARY KEY (`player_id`,`resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- bonuslevel: 0 for the 1st bonus, 1 for the second bonus.
-- pos: position on the track: 4 or 9.
-- player: player that has the bonus.
CREATE TABLE IF NOT EXISTS `shipping_track_bonus` (
  `resource_type` char(2) NOT NULL,
  `bonuslevel` smallint(5) NOT NULL,
  `pos` smallint(5) NOT NULL,
  `player_id` int(10) unsigned NULL,
  PRIMARY KEY (`resource_type`, `bonuslevel`, `pos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `player_supply_track` (
  `player_id` int(10) unsigned NOT NULL,
  `section` smallint(5) NOT NULL,
  `resource_type` char(2) NOT NULL,
  `resource_count` smallint(5) NOT NULL,
  PRIMARY KEY (`player_id`, `section`, `resource_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

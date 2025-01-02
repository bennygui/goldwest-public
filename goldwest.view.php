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
 * goldwest.view.php
 *
 */

require_once(APP_BASE_PATH . "view/common/game.view.php");

class view_goldwest_goldwest extends game_view
{
  function getGameName()
  {
    return "goldwest";
  }
  function build_page($viewArgs)
  {
    // Get players & players number
    $players = $this->game->loadPlayersBasicInfos();
    $players_nbr = count($players);
    $currentPlayerId = $this->game->currentPlayerId();

    /*********** Place your code below:  ************/

    $this->tpl['WARN_NO_UNDO_TEXT'] = self::_('Warning: If you do this action, you will not be able to cancel your turn before this point');
    $this->tpl['WARN_LAST_TURN_TEXT'] = self::_('This is the last turn, there will be no "Build or Loot" phase');
    $this->tpl['SECTION_3'] = self::_('Section 3');
    $this->tpl['SECTION_2'] = self::_('Section 2');
    $this->tpl['SECTION_1'] = self::_('Section 1');
    $this->tpl['SECTION_0'] = self::_('Section 0');
    $this->game->board->render($this->page);
    $this->game->boomtown->render($this->page);
    $this->game->shipping->render($this->page);
    $this->game->playerBoard->render($this->page, $currentPlayerId, $players);

    /*********** Do not change anything below this line  ************/
  }
}

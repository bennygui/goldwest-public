<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * goldwest implementation : © Guillaume Benny bennygui@gmail.com
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * goldwest.action.php
 *
 * goldwest main action entry point
 *
 */


class action_goldwest extends APP_GameAction
{
  // Constructor: please do not modify
  public function __default()
  {
    if (self::isArg('notifwindow')) {
      $this->view = "common_notifwindow";
      $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
    } else {
      $this->view = "goldwest_goldwest";
      self::trace("Complete reinitialization of board game");
    }
  }

  public function chooseSupplyTrackToActivate()
  {
    self::setAjaxMode();
    $section = self::getArg("section", AT_posint, true);
    $this->game->chooseSupplyTrackToActivate($section);
    self::ajaxResponse();
  }

  public function chooseSupplyTrackResourceToLeave()
  {
    self::setAjaxMode();
    $resourceType = self::getArg("resourceType", AT_enum, true, null, RESOURCE_TYPES_ALL);
    $this->game->chooseSupplyTrackResourceToLeave($resourceType);
    self::ajaxResponse();
  }

  public function chooseBoomTown()
  {
    self::setAjaxMode();
    $x = self::getArg("x", AT_posint, true);
    $y = self::getArg("y", AT_posint, true);
    $this->game->chooseBoomTown($x, $y, false);
    self::ajaxResponse();
  }

  public function chooseFreeBoomTown()
  {
    self::setAjaxMode();
    $x = self::getArg("x", AT_posint, true);
    $y = self::getArg("y", AT_posint, true);
    $this->game->chooseBoomTown($x, $y, true);
    self::ajaxResponse();
  }

  public function chooseFreeOccupiedBoomTown()
  {
    self::setAjaxMode();
    $x = self::getArg("x", AT_posint, true);
    $y = self::getArg("y", AT_posint, true);
    $this->game->chooseFreeOccupiedBoomTown($x, $y);
    self::ajaxResponse();
  }

  public function chooseInvestment()
  {
    self::setAjaxMode();
    $cardType = self::getArg("cardType", AT_posint, true);
    $this->game->chooseInvestment($cardType);
    self::ajaxResponse();
  }

  public function chooseShippingTrack()
  {
    self::setAjaxMode();
    $resourceType = self::getArg("resourceType", AT_enum, true, null, RESOURCE_TYPES_ALL);
    $this->game->chooseShippingTrack($resourceType, false);
    self::ajaxResponse();
  }

  public function chooseFreeShippingTrack()
  {
    self::setAjaxMode();
    $resourceType = self::getArg("resourceType", AT_enum, true, null, RESOURCE_TYPES_ALL);
    $this->game->chooseShippingTrack($resourceType, true);
    self::ajaxResponse();
  }

  public function chooseMiningToken()
  {
    self::setAjaxMode();
    $tokenId = self::getArg("tokenId", AT_alphanum, true);
    $this->game->chooseMiningToken($tokenId);
    self::ajaxResponse();
  }

  public function chooseMiningTokenToView()
  {
    self::setAjaxMode();
    $tokenId = self::getArg("tokenId", AT_alphanum, true);
    $this->game->chooseMiningTokenToView($tokenId);
    self::ajaxResponse();
  }

  public function chooseCampToUpgrade()
  {
    self::setAjaxMode();
    $tokenId = self::getArg("tokenId", AT_alphanum, true);
    $this->game->chooseCampToUpgrade($tokenId);
    self::ajaxResponse();
  }

  public function chooseSupplyTrackToAdd()
  {
    self::setAjaxMode();
    $section = self::getArg("section", AT_posint, true);
    $this->game->chooseSupplyTrackToAdd($section);
    self::ajaxResponse();
  }

  public function chooseFreeResource()
  {
    self::setAjaxMode();
    $resourceType = self::getArg("resourceType", AT_enum, true, null, RESOURCE_TYPES_ALL);
    $this->game->chooseFreeResource($resourceType);
    self::ajaxResponse();
  }

  public function chooseTradingPostResource()
  {
    self::setAjaxMode();
    $resourceType = self::getArg("resourceType", AT_enum, true, null, RESOURCE_TYPES_BUILD);
    $this->game->chooseTradingPostResource($resourceType);
    self::ajaxResponse();
  }

  public function tradingPostKeepResource()
  {
    self::setAjaxMode();
    $this->game->tradingPostKeepResource();
    self::ajaxResponse();
  }

  public function tradingPostSupplyTrackAdd()
  {
    self::setAjaxMode();
    $section = self::getArg("section", AT_posint, true);
    $this->game->tradingPostSupplyTrackAdd($section);
    self::ajaxResponse();
  }

  public function chooseFreeResourceTrack()
  {
    self::setAjaxMode();
    $section = self::getArg("section", AT_posint, true);
    $this->game->chooseFreeResourceTrack($section);
    self::ajaxResponse();
  }

  public function confirmTurn()
  {
    self::setAjaxMode();
    $this->game->confirmTurn();
    self::ajaxResponse();
  }

  public function confirmAfterCancelView()
  {
    self::setAjaxMode();
    $this->game->confirmAfterCancelView();
    self::ajaxResponse();
  }

  public function cancelTurn()
  {
    self::setAjaxMode();
    $this->game->cancelTurn();
    self::ajaxResponse();
  }
  
  // DEBUG! Comment for production
  //public function chooseDebugGotoLastTurn()
  //{
  //  self::setAjaxMode();
  //  $this->game->chooseDebugGotoLastTurn();
  //  self::ajaxResponse();
  //}
}

<?php
/**
* Twitter Bot
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 13:03:10 [Mar 13, 2016])
*/
//
//
class twitterbot extends module {
/**
* twitterbot
*
* Module class constructor
*
* @access private
*/
function twitterbot() {
  $this->name="twitterbot";
  $this->title="TwitterBot";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 $out['CKEY']=$this->config['CKEY'];
 $out['CSECRET']=$this->config['CSECRET'];
 $out['ATOKEN']=$this->config['ATOKEN'];
 $out['ASECRET']=$this->config['ASECRET'];
 $out['DISABLED']=$this->config['DISABLED'];
 if ($this->view_mode=='update_settings') {
   global $ckey;
   $this->config['CKEY']=$ckey;
   global $csecret;
   $this->config['CSECRET']=$csecret;
   global $atoken;
   $this->config['ATOKEN']=$atoken;
   global $asecret;
   $this->config['ASECRET']=$asecret;
   global $disabled;
   $this->config['DISABLED']=$disabled;
   $this->saveConfig();
   $this->redirect("?ok=1");
 }

 if ($_GET['ok']) {
  $out['OK']=1;
 }
 
}

/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
 function processSubscription($event, $details='') {
  $this->getConfig();
  if ($event=='SAY') {
    $level=$details['level'];
    $message=$details['message'];
    

   $consumerKey = $this->config['CKEY'];
   if (!$consumerKey && defined('SETTINGS_TWITTER_CKEY')) {
    $consumerKey    = SETTINGS_TWITTER_CKEY;
   }
   $consumerSecret = $this->config['CSECRET'];
   if (!$consumerSecret && defined('SETTINGS_TWITTER_CSECRET')) {
    $consumerSecret    = SETTINGS_TWITTER_CSECRET;
   }
   $oAuthToken = $this->config['ATOKEN'];
   if (!$oAuthToken && defined('SETTINGS_TWITTER_ATOKEN')) {
    $oAuthToken    = SETTINGS_TWITTER_ATOKEN;
   }

   $oAuthSecret = $this->config['ASECRET'];
   if (!$oAuthSecret && defined('SETTINGS_TWITTER_ASECRET')) {
    $oAuthSecret    = SETTINGS_TWITTER_ASECRET;
   }

   if ($consumerKey == '' || $consumerSecret == '' || $oAuthSecret == '' || $oAuthToken == '')
   return 0;

    
    if (!$this->config['DISABLED'])
    {
           require_once(DIR_MODULES . 'twitterbot/twitteroauth.php');

           // create a new instance
           $tweet = new TwitterOAuth($consumerKey, $consumerSecret, $oAuthToken, $oAuthSecret);

           // add time to message
           $twitMessage = date('H:i:s') . ' ' . $message;
           //send a tweet
           $tweet->post('statuses/update', array('status' => $twitMessage));
    }
  }
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  subscribeToEvent($this->name, 'SAY');
  parent::install();
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgTWFyIDEzLCAyMDE2IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/

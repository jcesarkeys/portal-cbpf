<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\UserLoggedin;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class UserLoggedin extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'User is Logged in';
	//static $setup = array('simple' => array('title' => 'Permissions'));
	static $group = array('security' => 'Security');
	//static $platforms = array('joomla');
	var $events = array('true' => 0, 'false' => 0);
	var $events_status = array('true' => 'success', 'false' => 'fail');

	var $defaults = array(
		'username' => '',
		'password' => '',
	);

	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		$user = \GCore\Libs\Base::getUser();
		
		if(!empty($user['id'])){
			$this->events['true'] = 1;
			return true;
		}else{
			$this->events['false'] = 1;
			return false;
		}
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config user_loggedin_action_config', 'user_loggedin_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][dummy]', array('type' => 'hidden', /*'label' => l_('CF_EVENTLOOP_EVENT'), 'class' => 'M', 'sublabel' => l_('CF_EVENTLOOP_EVENT_DESC')*/));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}
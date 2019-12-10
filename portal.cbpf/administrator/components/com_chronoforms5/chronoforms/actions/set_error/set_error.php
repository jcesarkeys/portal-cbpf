<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\SetError;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class SetError extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Set Error';
	static $group = array('validation' => 'Validation');
	var $defaults = array(
		'error' => 'An error has occurred.',
	);

	function execute(&$form, $action_id){
		$config = !empty($form->actions_config[$action_id]) ? $form->actions_config[$action_id] : array();
		$config = new \GCore\Libs\Parameter($config);
		
		$form->errors[] = $config->get('error', 'An error has occurred.');
	}

	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config set_error_action_config', 'set_error_action_config__XNX_');
		echo \GCore\Helpers\Html::formSecStart();
		echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][error]', array('type' => 'text', 'label' => l_('CF_SETERROR_ERROR'), 'value' => '', 'class' => 'L', 'sublabel' => l_('CF_SETERROR_ERROR_DESC')));
		echo \GCore\Helpers\Html::formSecEnd();
		echo \GCore\Helpers\Html::formEnd();
	}
}
<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');
jimport('joomla.version');

/*
 * Checks if the SYW library is installed and has the version needed for the extension to run properly
 */
class JFormFieldSYWlibtest extends JFormField {
	
	public $type = 'SYWlibtest';
	
	protected $minversion;
	protected $downloadlink;
	
	protected function getLabel()
	{
		return '';
	}
	
	protected function getInput()
	{
		$html = '';
		
		if (!JFolder::exists(JPATH_ROOT.'/libraries/syw')) {
			$html .= '<div class="alert alert-warning">';
			$html .= '<span>'.JText::_('SYW_MISSING_SYWLIBRARY').'</span><br />';
			$html .= '<a href="'.$this->downloadlink.'" target="_blank">'.JText::_('SYW_DOWNLOAD_SYWLIBRARY').'</a>';
			$html .= '</div>';
		} else {
			jimport('syw.version');
			if (!SYWVersion::isCompatible($this->minversion)) {
				$html .= '<div class="alert alert-warning">';
				$html .= '<span>'.JText::_('SYW_NONCOMPATIBLE_SYWLIBRARY').'</span><br />';
				$html .= '<span>'.JText::_('SYW_UPDATE_SYWLIBRARY').' '.JText::_('SYW_OR').' </span>';
				$html .= '<a href="'.$this->downloadlink.'" target="_blank">'.strtolower(JText::_('SYW_DOWNLOAD_SYWLIBRARY')).'</a>';
				$html .= '</div>';
			}
		}
		
		return $html;
	}
	
	/**
	 * @since      3.2
	 * @deprecated 3.2.3 Use renderField() instead
	 */
	public function getControlGroup()
	{
		if (!JFolder::exists(JPATH_ROOT.'/libraries/syw')) {
			return parent::getControlGroup();
		} else {
			jimport('syw.version');
			if (!SYWVersion::isCompatible($this->minversion)) {
				return parent::getControlGroup();
			}
		}
		
		return '';
	}
	
	public function renderField($options = array())
	{
		if (!JFolder::exists(JPATH_ROOT.'/libraries/syw')) {
			return parent::renderField();
		} else {
			jimport('syw.version');
			if (!SYWVersion::isCompatible($this->minversion)) {
				return parent::renderField();
			}
		}
		
		return '';
	}
	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);
		
		if ($return) {
			$this->minversion = $this->element['minversion'];
			$this->downloadlink = $this->element['downloadlink'];
		}
		
		return $return;
	}
	
}
?>
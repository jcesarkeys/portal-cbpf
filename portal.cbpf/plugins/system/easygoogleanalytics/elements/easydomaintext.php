<?php
/**
* @version		3.2
* @author		Michael A. Gilkes (michael@valorapps.com)
* @copyright	Michael Albert Gilkes
* @license		GNU/GPLv2
*/

/*

Easy Google Analytics Plugin for Joomla!
Copyright (C) 2011-2015  Michael Albert Gilkes

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

// no direct access
defined('_JEXEC') or die('Restricted access');

//get joomla form related functions
jimport('joomla.form.formfield');

class JFormFieldEasyDomainText extends JFormField
{
	//The form field type
	protected $type = 'easydomaintext';
	
	//setup the custom field's details
	protected function getInput()
	{
		//get the domain name (sld.tld)
		$hostname = $this->getSLD_dot_TLD();
		
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		
		// Initialize JavaScript field attributes.
		$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		$value = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
		$value = (strlen($value) > 0) ? $value : $hostname;
		
		$html = '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.$value.'"' .
				$class.$size.$disabled.$readonly.$onchange.$maxLength.'/>';
		
		return $html;
	}
	
	protected function getSLD_dot_TLD()
	{
		$host = $_SERVER['HTTP_HOST'];
		$domain = isset($host) ? $host : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs))
		{
			$domain = $regs['domain'];
		}
		
		return $domain;
	}
}

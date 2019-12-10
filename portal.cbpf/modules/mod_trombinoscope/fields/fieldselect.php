<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined( '_JEXEC' ) or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JFormHelper::loadFieldClass('list');

class JFormFieldFieldSelect extends JFormFieldList
{
	public $type = 'FieldSelect';
	
	static $core_fields = null;
	static $config_params = null;
	static $plugin_params = null;
	static $is_standalone = null;
	
	static function getCoreFields()
	{
		if (!isset(self::$core_fields)) {
			JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
			self::$core_fields = FieldsHelper::getFields('com_contact.contact');
		}
	
		return self::$core_fields;
	}
	
	static function getConfigParams()
	{
		if (!isset(self::$config_params)) {
			if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_trombinoscopeextended/config.xml')) {
				self::$config_params = JComponentHelper::getParams('com_trombinoscopeextended');
			} else {
				self::$config_params = array();
			}
		}
	
		return self::$config_params;
	}
	
	static function getPluginParams()
	{
		if (!isset(self::$plugin_params)) {
			if (JPluginHelper::isEnabled('content', 'additionalcontactfields')) {
				$plugin = JPluginHelper::getPlugin('content', 'additionalcontactfields');
				self::$plugin_params = json_decode($plugin->params);
			} else {
				self::$plugin_params = new stdClass();
			}
		}
	
		return self::$plugin_params;
	}

	static function isStandalone() 
	{
		if (!isset(self::$is_standalone)) {
			$folder = JPATH_ROOT.'/components/com_trombinoscopeextended/views/trombinoscope';
			if (!JFolder::exists($folder)) {
				self::$is_standalone = true;
			} else {
				self::$is_standalone = false;
			}
		}
		
		return self::$is_standalone;
	}
	
	protected function isInConfig($field_id) 
	{
		$config_params = self::getConfigParams();
		
		if (empty($config_params)) {
			return false;
		}
		
		if ($config_params->get('mapping_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('birthdate_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('facebook_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('twitter_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('linkedin_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('googleplus_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('skype_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('youtube_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('instagram_feature', '') == $field_id) {
			return true;
		}
		
		if ($config_params->get('pinterest_feature', '') == $field_id) {
			return true;
		}
		
		return false;
	}

	protected function getOptions() 
	{
		$options = array();
		
		$options[] = JHTML::_('select.option', 'empty', JText::_('MOD_TROMBINOSCOPE_VALUE_EMPTY'), 'value', 'text', $disable=false);
				
		$options[] = JHTML::_('select.option', 'c_p', JText::_('MOD_TROMBINOSCOPE_VALUE_POSITION'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'tel', JText::_('MOD_TROMBINOSCOPE_VALUE_TELEPHONE'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'mob', JText::_('MOD_TROMBINOSCOPE_VALUE_MOBILE'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'fax', JText::_('MOD_TROMBINOSCOPE_VALUE_FAX'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'mail', JText::_('MOD_TROMBINOSCOPE_VALUE_EMAIL'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'web', JText::_('MOD_TROMBINOSCOPE_VALUE_WEBPAGE'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'add', JText::_('MOD_TROMBINOSCOPE_VALUE_ADDRESS'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'sub', JText::_('MOD_TROMBINOSCOPE_VALUE_SUBURB'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'st', JText::_('MOD_TROMBINOSCOPE_VALUE_STATE'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'p_c', JText::_('MOD_TROMBINOSCOPE_VALUE_POSTCODE'), 'value', 'text', $disable=false);
		//$options[] = JHTML::_('select.option', 'f_a', JText::_('MOD_TROMBINOSCOPE_VALUE_FORMATTEDADDRESS'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'f_f_a', JText::_('MOD_TROMBINOSCOPE_VALUE_FULLYFORMATTEDADDRESS'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'cou', JText::_('MOD_TROMBINOSCOPE_VALUE_COUNTRY'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'misc', JText::_('MOD_TROMBINOSCOPE_VALUE_MISC'), 'value', 'text', $disable=false);

		// if content plugin 'additional contact fields' is enabled, add the fields here
		
		// NEW: if the plugin is disabled and/or the field is not selected, show the option if a custom field will cover the feature

		$config_params = self::getConfigParams();
		$plugin_params = self::getPluginParams(); // params from additional fields plugin, if exists and enabled
		
		// gender
		
		if (isset($plugin_params->gender) && $plugin_params->gender) {
			$options[] = JHTML::_('select.option', 'gen', JText::_('MOD_TROMBINOSCOPE_VALUE_GENDER'), 'value', 'text', $disable=false);
		}
		
		// birthdate
			
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('birthdate_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->birthdate) && $plugin_params->birthdate) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'dob', JText::_('MOD_TROMBINOSCOPE_VALUE_BIRTHDATE'), 'value', 'text', $disable=false);
			$options[] = JHTML::_('select.option', 'age', JText::_('MOD_TROMBINOSCOPE_VALUE_AGE'), 'value', 'text', $disable=false);
		}
		
		// company
		
		if (isset($plugin_params->company) && $plugin_params->company) {
			$options[] = JHTML::_('select.option', 'com', JText::_('MOD_TROMBINOSCOPE_VALUE_COMPANY'), 'value', 'text', $disable=false);
		}
		
		// department
		
		if (isset($plugin_params->department) && $plugin_params->department) {
			$options[] = JHTML::_('select.option', 'dep', JText::_('MOD_TROMBINOSCOPE_VALUE_DEPARTMENT'), 'value', 'text', $disable=false);
		}
		
		// map
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('mapping_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->map) && $plugin_params->map) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'map', JText::_('MOD_TROMBINOSCOPE_VALUE_MAP'), 'value', 'text', $disable=false);
		}
		
		// summary
		
		if (isset($plugin_params->summary) && $plugin_params->summary) {
			$options[] = JHTML::_('select.option', 'sum', JText::_('MOD_TROMBINOSCOPE_VALUE_SUMMARY'), 'value', 'text', $disable=false);
		}
		
		// Facebook
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('facebook_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->facebook) && $plugin_params->facebook) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'facebook', 'Facebook', 'value', 'text', $disable=false);
		}
		
		// Twitter
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('twitter_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->twitter) && $plugin_params->twitter) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'twitter', 'Twitter', 'value', 'text', $disable=false);
		}
		
		// LinkedIn
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('linkedin_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->linkedin) && $plugin_params->linkedin) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'linkedin', 'LinkedIn', 'value', 'text', $disable=false);
		}
		
		// Google+
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('googleplus_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->googleplus) && $plugin_params->googleplus) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'googleplus', 'Google+', 'value', 'text', $disable=false);
		}
		
		// YouTube
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('youtube_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->youtube) && $plugin_params->youtube) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'youtube', 'YouTube', 'value', 'text', $disable=false);
		}
		
		// Instagram
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('instagram_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->instagram) && $plugin_params->instagram) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'instagram', 'Instagram', 'value', 'text', $disable=false);
		}
		
		// Pinterest
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('pinterest_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->pinterest) && $plugin_params->pinterest) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'pinterest', 'Pinterest', 'value', 'text', $disable=false);
		}
		
		// Skype
		
		$use_feature = false;
		if (!empty($config_params) && $config_params->get('skype_feature', '') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			// the feature is handled by the custom field
			$use_feature = true;
		} else {
			if (isset($plugin_params->skype) && $plugin_params->skype) {
				$use_feature = true;
			}
		}
		
		if ($use_feature) {
			$options[] = JHTML::_('select.option', 'skype', 'Skype', 'value', 'text', $disable=false);
		}	
		
// 		if (JPluginHelper::isEnabled('content', 'additionalcontactfields')) {
		
// 			$plugin = JPluginHelper::getPlugin('content', 'additionalcontactfields');
// 			$params = json_decode($plugin->params);
		
// 			if (isset($params->gender) && $params->gender) {
// 				$options[] = JHTML::_('select.option', 'gen', JText::_('MOD_TROMBINOSCOPE_VALUE_GENDER'), 'value', 'text', $disable=false);
// 			}
		
// 			if (isset($params->birthdate) && $params->birthdate) {
// 				$options[] = JHTML::_('select.option', 'dob', JText::_('MOD_TROMBINOSCOPE_VALUE_BIRTHDATE'), 'value', 'text', $disable=false);
// 				$options[] = JHTML::_('select.option', 'age', JText::_('MOD_TROMBINOSCOPE_VALUE_AGE'), 'value', 'text', $disable=false);
// 			}
		
// 			if (isset($params->company) && $params->company) {
// 				$options[] = JHTML::_('select.option', 'com', JText::_('MOD_TROMBINOSCOPE_VALUE_COMPANY'), 'value', 'text', $disable=false);
// 			}
		
// 			if (isset($params->department) && $params->department) {
// 				$options[] = JHTML::_('select.option', 'dep', JText::_('MOD_TROMBINOSCOPE_VALUE_DEPARTMENT'), 'value', 'text', $disable=false);
// 			}
		
// 			if (isset($params->map) && $params->map) {
// 				$options[] = JHTML::_('select.option', 'map', JText::_('MOD_TROMBINOSCOPE_VALUE_MAP'), 'value', 'text', $disable=false);
// 			}
			
// 			if (isset($params->summary) && $params->summary) {
// 				$options[] = JHTML::_('select.option', 'sum', JText::_('MOD_TROMBINOSCOPE_VALUE_SUMMARY'), 'value', 'text', $disable=false);
// 			}
		
// 			if (isset($params->skype) && $params->skype) {
// 				$options[] = JHTML::_('select.option', 'skype', 'Skype', 'value', 'text', $disable=false);
// 			}
// 		}
		
		$options[] = JHTML::_('select.option', 'a', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKA'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'a_sw', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKA_SAMEWINDOW'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'b', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKB'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'b_sw', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKB_SAMEWINDOW'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'c', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKC'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'c_sw', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKC_SAMEWINDOW'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'd', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKD'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'd_sw', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKD_SAMEWINDOW'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'e', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKE'), 'value', 'text', $disable=false);
		$options[] = JHTML::_('select.option', 'e_sw', JText::_('MOD_TROMBINOSCOPE_VALUE_LINKE_SAMEWINDOW'), 'value', 'text', $disable=false);
		
		// get Joomla! fields
		// test the fields folder first to avoid message warning that the component is missing
		if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
		
			$fields = self::getCoreFields();
				
			// supported field types
			$allowed_types = array('calendar', 'checkboxes', 'email', 'integer', 'list', 'radio', 'tel', 'text', 'textarea', 'url');
			
			// organize the fields according to their group
				
			$fieldsPerGroup = array(
				0 => array()
			);
				
			$groupTitles = array(
				0 => JText::_('MOD_TROMBINOSCOPE_VALUE_NOGROUPFIELD')
			);
				
			foreach ($fields as $field) {
					
				if (!in_array($field->type, $allowed_types) || self::isInConfig($field->id)) {
					continue;
				}
					
				if (!array_key_exists($field->group_id, $fieldsPerGroup)) {
					$fieldsPerGroup[$field->group_id] = array();
					$groupTitles[$field->group_id] = $field->group_title;
				}
					
				$fieldsPerGroup[$field->group_id][] = $field;
			}
				
			// loop trough the groups
				
			foreach ($fieldsPerGroup as $group_id => $groupFields) {
			
				if (!$groupFields) {
					continue;
				}
			
				$options[] = JHtml::_('select.optgroup', $groupTitles[$group_id]);
			
				foreach ($groupFields as $field) {
					$options[] = JHTML::_('select.option', 'jfield:'.$field->type.':'.$field->id, $field->title, 'value', 'text', $disable = (self::isStandalone()) ? true : false);
				}
			
				$options[] = JHtml::_('select.optgroup', $groupTitles[$group_id]);
			}
		}		
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
	
}
?>

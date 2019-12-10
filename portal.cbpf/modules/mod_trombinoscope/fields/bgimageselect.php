<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined( '_JEXEC' ) or die;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

// if (version_compare(JVERSION, '3.7', 'lt')) {
	
// 	class JFormFieldBGImageSelect extends JFormField
// 	{
// 		public $type = 'BGImageSelect';
		
// 		protected function getInput()
// 		{
// 			$html = '';			
					
// 			$html .= '<div style="margin-bottom:0" class="alert alert-info">';								
// 			$html .= '<span>';
// 			if (!self::isStandalone()) {
// 				$html .= JText::sprintf('MOD_TROMBINOSCOPE_MESSAGE_NEEDJOOMLAFORCUSTOMFIELDS');
// 			} else {
// 				$html .= JText::sprintf('MOD_TROMBINOSCOPE_MESSAGE_NEEDJOOMLAFORCUSTOMFIELDSANDPAIDVERSION');
// 			}
// 			$html .= '</span>';
// 			$html .= '</div>';
		
// 			return $html;
// 		}
		
// 		protected function isStandalone()
// 		{
// 			$folder = JPATH_ROOT.'/components/com_trombinoscopeextended/views/trombinoscope';
// 			if (!JFolder::exists($folder)) {
// 				return true;
// 			}
		
// 			return false;
// 		}
// 	}
// } else {

	JFormHelper::loadFieldClass('list');
	
	class JFormFieldBGImageSelect extends JFormFieldList
	{
		public $type = 'BGImageSelect';
	
		static $core_fields = null;
		static $is_Standalone;
		
		static function isStandalone() {
			
			if (!isset(self::$is_Standalone)) {
				
				self::$is_Standalone = false;
				
				$folder = JPATH_ROOT.'/components/com_trombinoscopeextended/views/trombinoscope'; // when adding themes, even if the component is not installed, it adds the folder
				if (!JFolder::exists($folder)) {
					self::$is_Standalone = true;
				}
			}
			
			return self::$is_Standalone;
		}
	
		static function getCoreFields()
		{
			if (!isset(self::$core_fields)) {
				JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
				self::$core_fields = FieldsHelper::getFields('com_contact.contact');
			}
	
			return self::$core_fields;
		}
	
		protected function getOptions() 
		{
			$options = array();
			
			// get Joomla! fields
			// test the fields folder first to avoid message warning that the component is missing
			if (JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
	
				$fields = self::getCoreFields();
	
				// supported field types
				$allowed_types = array('media');
	
				foreach ($fields as $field) {
					if (in_array($field->type, $allowed_types)) {
	
						$field_category_title = $field->group_title; // group may be missing
						if (empty($field_category_title)) {
							$field_category_title = JText::_('MOD_TROMBINOSCOPE_VALUE_NOGROUPFIELD');
						}
	
						$options[] = JHTML::_('select.option', $field->id, $field_category_title.': '.$field->title, 'value', 'text', $disable = (self::isStandalone()) ? true : false);
					}
				}
			}
	
			// Merge any additional options in the XML definition.
			$options = array_merge(parent::getOptions(), $options);
	
			return $options;
		}
	}
//}
?>
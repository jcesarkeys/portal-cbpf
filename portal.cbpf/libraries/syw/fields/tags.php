<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');

JFormHelper::loadFieldClass('list');

class JFormFieldTags extends JFormFieldList
{
	public $type = 'Tags';
	
	/* hide tags selection if no tags */
	protected function getLabel() 
	{
		$folder = JPATH_ROOT.'/components/com_tags';
		if (JFolder::exists($folder)) {
			return parent::getLabel();
		} 
		
		return '';
	}
	
	protected function getInput() 
	{
		$html = '';
		
		$folder = JPATH_ROOT.'/components/com_tags';
		if (JFolder::exists($folder)) {
			return parent::getInput();
		} else {
			$lang = JFactory::getLanguage();
			$lang->load('lib_syw.sys', JPATH_SITE);
						
			$html .= '<div style="margin-bottom:0" class="alert alert-error">';			
				$html .= '<span>'.JText::_('LIB_SYW_TAGS_NOTSUPPORTED').'</span>';
			$html .= '</div>';
		}
		
		return $html;
	}
	
	protected function getOptions()
	{
		$options = array();
		
		if (isset($this->element['show_root']))	{
			array_unshift($options, JHtml::_('select.option', '0', JText::_('JGLOBAL_ROOT')));
		}
		
		$folder = JPATH_ROOT.'/components/com_tags';
		if (JFolder::exists($folder)) {
			
			$content_type = $this->element['contenttype'];
		
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			
			$query->select('DISTINCT a.id AS value, a.path, a.title AS text, a.level');
			$query->from('#__tags AS a');
			$query->join('LEFT', $db->quoteName('#__tags').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');	

			if (!empty($content_type)) { // get only tags associated with the content type
				$query->join('INNER', $db->quoteName('#__contentitem_tag_map').' AS m ON m.tag_id = a.id AND m.type_alias ='.$db->quote($content_type));
			}			
			
			$query->where('a.published = 1');
			$query->where($db->quoteName('a.alias').' <> '.$db->quote('root'));
			$query->group('a.id, a.title, a.level, a.lft, a.rgt, a.parent_id, a.path');
			$query->order('a.lft ASC');
			
			$db->setQuery($query);
			
			try {
				$options = $db->loadObjectList();
			} catch (RuntimeException $e) {
				return false;
			}				
			
			$this->prepareOptionsNested($options);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
	
	/**
	 * Add "-" before nested tags, depending on level
	 *
	 * @param   array  &$options  Array of tags
	 * @return  array  The field option objects
	 * @since   3.1
	 */
	protected function prepareOptionsNested(&$options)
	{
		if ($options) {
			foreach ($options as &$option) {
				$repeat = (isset($option->level) && $option->level - 1 >= 0) ? $option->level - 1 : 0;
				$option->text = str_repeat('- ', $repeat) . $option->text;
			}
		}
	
		return $options;
	}
	
}

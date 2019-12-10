<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SYWK2 
{
	static $k2_exists = NULL;
	
	static function exists()
	{	
		if (isset(self::$k2_exists)) {
			return self::$k2_exists;
		}
		
		//self::$k2_exists = JComponentHelper::isEnabled('com_k2', true); // this generates a warning when K2 is missing
		
		/*if (!self::$k2_exists) {
			$lang = JFactory::getLanguage();
			$lang->load('lib_syw', JPATH_ADMINISTRATOR);
			JFactory::getApplication()->enqueueMessage(JText::_('LIB_SYW_DISCARD_MESSAGE'), 'warning');
		}*/
		
		self::$k2_exists = true;
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id AS id, element AS "option", params, enabled');
		$query->from('#__extensions');
		$query->where($query->qn('type') . ' = ' . $db->quote('component'));
		$query->where($query->qn('element') . ' = ' . $db->quote('com_k2'));
		$db->setQuery($query);
		
		$cache = JFactory::getCache('_system', 'callback');
		
		$k2_component = $cache->get(array($db, 'loadObject'), null, 'com_k2', false);
		
		if ($error = $db->getErrorMsg() || empty($k2_component)) {
			self::$k2_exists = false;
		}
		
		return self::$k2_exists;
	}	
	
	/*
	 * Get all tag objects for k2
	 *
	 * @return array of tag objects (false if error)
	 */
	static function getTags($whole = false, $tag_ids = array(), $include = true)
	{
		$tags = array();
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		if ($whole) { // get the whole object
			$query->select('tag.id, tag.name AS title, tag.published');
		} else {
			$query->select('tag.id, tag.name AS title');
		}
		$query->from('#__k2_tags AS tag');
		
		$query->join('LEFT', $db->quoteName('#__k2_tags_xref').' AS xref ON tag.id = xref.tagID');
		$query->join('LEFT', $db->quoteName('#__k2_items').' AS items ON xref.itemID= items.id');
				
		// access groups
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$query->where('items.access IN (' . $groups . ')');
		
		// language
		if (JLanguageMultilang::isEnabled()) {
			$language = JHelperContent::getCurrentLanguage();
			$query->where($db->quoteName('items.language').' IN ('.$db->quote($language).', '.$db->quote('*').')');
		}		
		
		$query->where('tag.published = 1');
				
		// get tags with specific ids
		if (is_array($tag_ids) && count($tag_ids) > 0) {
			JArrayHelper::toInteger($tag_ids);
			$tag_ids = implode(',', $tag_ids);
			
			$test_type = $include ? 'IN' : 'NOT IN';
			$query->where($db->quoteName('tag.id').' '.$test_type.' ('.$tag_ids.')');
		}
		
		//$query->order('xref.id ASC');
		$query->order('tag.name ASC');
		
		$db->setQuery($query);
		
		try {
			$tags = $db->loadObjectList();
		} catch (RuntimeException $e) {
			return false;
		}
		
		return $tags;
	}
	
}

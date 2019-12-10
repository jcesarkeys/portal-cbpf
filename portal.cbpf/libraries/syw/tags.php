<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class SYWTags {
	
	/*
	* Get all tag objects for a specific content type (optional)
	*
	* @return array of tag objects (false if error)
	*/
	static function getTags($content_type = '', $whole = false, $tag_ids = array(), $include = true)
	{
		$tags = array();
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
			
		if ($whole) { // get the whole object
			$query->select('a.*');
		} else {
			$query->select('a.id, a.path, a.title, a.level');
		}
		$query->from('#__tags AS a');
		$query->join('LEFT', $db->quoteName('#__tags').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
	
		// get tags for a specific content type
		if (!empty($content_type)) { // get only tags associated with the content type
			$query->join('INNER', $db->quoteName('#__contentitem_tag_map').' AS m ON m.tag_id = a.id AND m.type_alias ='.$db->quote($content_type));
		}
			
		$query->where('a.published = 1');
		$query->where($db->quoteName('a.alias').' <> '.$db->quote('root'));
	
		// get tags with specific ids
		if (is_array($tag_ids) && count($tag_ids) > 0) {
			JArrayHelper::toInteger($tag_ids);
			$tag_ids = implode(',', $tag_ids);			
			
			$test_type = $include ? 'IN' : 'NOT IN';
			$query->where($db->quoteName('a.id').' '.$test_type.' ('.$tag_ids.')');
		}
	
		// access groups
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$query->where('a.access IN (' . $groups . ')');
	
		// language
		if (JLanguageMultilang::isEnabled()) {
			$language = JHelperContent::getCurrentLanguage();
			$query->where($db->quoteName('a.language').' IN ('.$db->quote($language).', '.$db->quote('*').')');
		}
	
		$query->group('a.id, a.title, a.level, a.lft, a.rgt, a.parent_id, a.path');
		$query->order('a.lft ASC');
			
		$db->setQuery($query);
			
		try {
			$tags = $db->loadObjectList();
		} catch (RuntimeException $e) {
			return false;
		}
	
		return $tags;
	}
	
}
<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.categories');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

jimport('syw.image');
jimport('syw.tags');
jimport('syw.text');
jimport('syw.utilities');

abstract class modTrombinoscopeHelper
{
	protected static $lookup;
		
	static $address_format;
	static $text_type;
	static $sort_locale;
	
	static $commonStylesLoaded = false;	
	static $userStylesLoaded = false;
	
	static function getContacts($params, $module)
	{		
		$app = JFactory::getApplication();
		$option = $app->input->get('option', '');
		$view = $app->input->get('view', '');
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$related_id = ''; // to avoid the contact to be visible in the list of related contacts

		self::$address_format = $params->get('a_fmt', 'zss');
		self::$text_type = $params->get('t', 'info');
		self::$sort_locale = $params->get('sort_locale', 'en_US');
		
		// contact selection
		$selection = $params->get('selection', 'categories');
				
		// metakeys filtering
		
		$metakeys = array();
		$item_on_page_keys = array();
		
		if ($selection == 'related') {
			
			$item_on_page_id = '';
			if (($option == 'com_contact' || $option == 'com_trombinoscopeextended') && $view == 'contact') {
				$temp = $app->input->getString('id');
				$temp = explode(':', $temp);
				$item_on_page_id = $temp[0];
			}
			
			if ($item_on_page_id) { // the content is a standard contact or a TCP contact page
				
				$query->select($db->quoteName('metakey'));
				$query->from($db->quoteName('#__contact_details'));
				$query->where($db->quoteName('id').' = '.$item_on_page_id);
				
				$db->setQuery($query);
				
				try {
					$result = $db->loadResult();
				} catch (RuntimeException $e) {
					$app->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
					return null;
				}
				
				$result = trim($result);
				if (empty($result)) {
					return array(); // won't find a related contact if no key is present
				}	
				
				$keys = explode(',', $result);	
				
				// assemble any non-blank word(s)
				foreach ($keys as $key) {
					$key = trim($key);
					if ($key) {
						$item_on_page_keys[] = $key;
					}
				}
					
				if (empty($item_on_page_keys)) {
					return array();
				}
				
				$query->clear();	
				
				$related_id = $item_on_page_id;
			} else {
				return null; // no result (was not on contact page)
			}
		}
			
		// explode the meta keys on a comma
		$keys = explode(',', $params->get('keys', ''));
				
		// assemble any non-blank word(s)
		foreach ($keys as $key) {
			$key = trim($key);
			if ($key) {
				$metakeys[] = $key;
			}
		}
		
		if (!empty($item_on_page_keys)) {
			if (!empty($metakeys)) { // if none of the tags we filter are in the content item on the page, return nothing
		
				$keys_in_common = array_intersect($item_on_page_keys, $metakeys);
				if (empty($keys_in_common)) {
					return array();
				}
		
				$metakeys = $keys_in_common;
		
			} else {
				$metakeys = $item_on_page_keys;
			}
		}
		
		// tags filtering
		
		$tags = $params->get('tags', array());
		$item_on_page_tagids = array();
		
		if ($selection == 'relatedbytags' || $selection == 'relatedcontactbytags') {
		
			if ($option == 'com_trombinoscopeextended' && $view == 'contact') { // because tags are recorded with com_contact
				$option = 'com_contact';
			}
				
			$get_the_tags = false;
			if ($selection == 'relatedcontactbytags' && $option == 'com_contact' && $view == 'contact') {
				$get_the_tags = true;
			} else if ($selection == 'relatedbytags') {
				$get_the_tags = true;
			}
			
			if ($get_the_tags) {
				$temp = $app->input->getString('id');
				$temp = explode(':', $temp);
				$item_on_page_id = $temp[0];
			
				if ($item_on_page_id) {
					$helper_tags = new JHelperTags;
					$tag_objects = $helper_tags->getItemTags($option.'.'.$view, $item_on_page_id);
					foreach ($tag_objects as $tag_object) {
						$item_on_page_tagids[] = $tag_object->tag_id;
					}
				}				
			
				if (empty($item_on_page_tagids)) {
					return array(); // no result because no tag found for the object on the page
				}
				
				if ($option == 'com_contact' && $view == 'contact') { // we do get rid of the contact only if the related element is a contact
					$related_id = $item_on_page_id;
				}
			} else {
				return null; // no result (was not on contact page)
			}
		}
		
		if (!empty($tags)) {
			
			// if all selected, get all available tags
			$array_of_tag_values = array_count_values($tags);
			if (isset($array_of_tag_values['all']) && $array_of_tag_values['all'] > 0) { // 'all' was selected
				$tags = array();
				$tag_objects = SYWTags::getTags('com_contact.contact');
				if ($tag_objects !== false) {
					foreach ($tag_objects as $tag_object) {
						$tags[] = $tag_object->id;
					}
				}
				
				if (empty($tags) /*&& $params->get('tags_inex', 1)*/) { // won't return any contact if no contact has been associated to any tag (TODO when include tags only)
					return array();
				}
			}
		}
		
		if (!empty($item_on_page_tagids)) {
			if (!empty($tags)) { // if none of the tags we filter are in the content item on the page, return nothing
		
				// take the tags common to the item on the page and the module selected tags
				$tags_in_common = array_intersect($item_on_page_tagids, $tags);
				if (empty($tags_in_common)) {
					return array();
				}
					
				if ($params->get('tags_match', 'any') == 'all') {
					if (count($tags_in_common) != count($tags)) {
						return array();
					}
				}
					
				$tags = $tags_in_common;
		
			} else {
				$tags = $item_on_page_tagids;
			}
		}
		
		$user = JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		
		// START OF DATABASE QUERY
				
		$subquery1 = ' CASE WHEN ';
		$subquery1 .= $query->charLength('cd.alias');
		$subquery1 .= ' THEN ';
		$cd_id = $query->castAsChar('cd.id');
		$subquery1 .= $query->concatenate(array($cd_id, 'cd.alias'), ':');
		$subquery1 .= ' ELSE ';
		$subquery1 .= $cd_id.' END AS slug';
		
		$subquery2 = ' CASE WHEN ';
		$subquery2 .= $query->charLength('cc.alias');
		$subquery2 .= ' THEN ';
		$cc_id = $query->castAsChar('cc.id');
		$subquery2 .= $query->concatenate(array($cc_id, 'cc.alias'), ':');
		$subquery2 .= ' ELSE ';
		$subquery2 .= $cc_id.' END AS catslug';
		
		$subquery = $subquery1.','.$subquery2;
		
		$subqueryfield1 = self::_getSubQuery($params->get('f1', 'none'), '1');
		$subqueryfield2 = self::_getSubQuery($params->get('f2', 'none'), '2');
		$subqueryfield3 = self::_getSubQuery($params->get('f3', 'none'), '3');
		$subqueryfield4 = self::_getSubQuery($params->get('f4', 'none'), '4');
		$subqueryfield5 = self::_getSubQuery($params->get('f5', 'none'), '5');
		$subqueryfield6 = self::_getSubQuery($params->get('f6', 'none'), '6');
		$subqueryfield7 = self::_getSubQuery($params->get('f7', 'none'), '7');		
		
		$subqueryfields = $subqueryfield1.$subqueryfield2.$subqueryfield3.$subqueryfield4.$subqueryfield5.$subqueryfield6.$subqueryfield7;
		
		$subquerylinkfield1 = self::_getSubQuery($params->get('lf1', 'none'), 'l1');
		$subquerylinkfield2 = self::_getSubQuery($params->get('lf2', 'none'), 'l2');
		$subquerylinkfield3 = self::_getSubQuery($params->get('lf3', 'none'), 'l3');
		$subquerylinkfield4 = self::_getSubQuery($params->get('lf4', 'none'), 'l4');
		$subquerylinkfield5 = self::_getSubQuery($params->get('lf5', 'none'), 'l5');
		
		$subquerylinkfields = $subquerylinkfield1.$subquerylinkfield2.$subquerylinkfield3.$subquerylinkfield4.$subquerylinkfield5;
		
		$query->select('cd.id, cd.catid, trim(cd.name) AS name, cc.lft as c_order, cd.user_id, cd.featured, cc.title AS category, cd.params, cd.image,'.$subquery.$subqueryfields.$subquerylinkfields);
		
		$query->from('#__contact_details AS cd');
		$query->join('INNER', '#__categories AS cc ON cd.catid = cc.id');
				
		$count = '';
		
		if ($selection == 'contact') {
			$contact_id = $params->get('contact_id', '');
			if (!empty($contact_id)) {
				$query->where('cd.id='.$contact_id);
			} else {
				return null;
			}
		} else if ($selection == 'user') {
			if ($user->id > 0) {
				$query->where('cd.user_id='.$user->id);
			} else {
				return null;
			}
		} else {
			
			$count = trim($params->get('count', ''));
			
			// filter by category
			
			$categories = self::getCategories($params->get('cat', array()), $params->get('includesubcat', 'no'));
			if ($categories != '') {
				$query->where('cd.catid IN ('.$categories.')');
			}	
			
			// include
			
			$contact_ids_include = trim($params->get('in', ''));
			if (!empty($contact_ids_include)) {
				$query->where('cd.id IN ('.$contact_ids_include.')');
			}
			
			// exclude
			
			$contact_ids_exclude = array_filter(explode(",", trim($params->get('ex', ''))));
			
			if (!empty($related_id)) {
				$contact_ids_exclude[] = $related_id;
			}
			
			if (!empty($contact_ids_exclude)) {
				$query->where('cd.id NOT IN ('.implode(",", $contact_ids_exclude).')');
			}
			
			// filter by metakeys
			
			if (!empty($metakeys)) {
				$concat_string = $query->concatenate(array('","', ' REPLACE(cd.metakey, ", ", ",")', ' ","')); // remove single space after commas in keywords
				$query->where('('.$concat_string.' LIKE "%'.implode('%" OR '.$concat_string.' LIKE "%', $metakeys).'%")');
			}
			
			// filter by tags
			
			if (!empty($tags)) {
					
				$tags_to_match = implode(',', $tags);
			
				$query->select('COUNT(t.id) AS tags_count');
				$query->join('INNER', $db->quoteName('#__contentitem_tag_map', 'm').' ON '.$db->quoteName('m.content_item_id').' = '.$db->quoteName('cd.id').' AND '.$db->quoteName('m.type_alias').' = '.$db->quote('com_contact.contact'));
				$query->join('INNER', $db->quoteName('#__tags', 't') . ' ON '.$db->quoteName('m.tag_id').' = '.$db->quoteName('t.id'));
				$query->where($db->quoteName('t.id').' IN ('.$tags_to_match.')');
				$query->where($db->quoteName('t.access').' IN ('.$groups.')');
				$query->where($db->quoteName('t.published').' = 1');
				
				if ($params->get('tags_match', 'any') == 'all') {
					$query->having('COUNT('.$db->quoteName('t.id').') = '.count($tags));
				}
				
				$query->group($db->quoteName('cd.id'));
			}
			
			// featured switch
			
			$featured = $params->get('f', 's');
			if ($featured == 'o') {
				$query->where('cd.featured = 1');
			} else if ($featured == 'h') {
				$query->where('cd.featured = 0');
			} else if ($featured == 'sf') {
				$query->order("cd.featured DESC");
			}
			
			// category order
		
			$catorder = $params->get('c_order', '');
			switch ($catorder) {
				case 'oa' : $query->order('cc.lft ASC'); break;
				case 'od' : $query->order('cc.lft DESC'); break;
				default : break;
			}
			
			// general ordering
	
			$order = $params->get('order', 'oa');
			switch ($order) {
				case 'oa' : $query->order('cd.ordering ASC'); break;
				case 'od' : $query->order('cd.ordering DESC'); break;
				case 'na' : $query->order('cd.name ASC'); break;
				case 'nd' : $query->order('cd.name DESC'); break;
				case 'fnf_fa' : $query->order('cd.ordering ASC'); break;
				case 'fnf_fd' : $query->order('cd.ordering DESC'); break;
				case 'fnf_la' : $query->order('cd.ordering ASC'); break;
				case 'fnf_ld' : $query->order('cd.ordering DESC'); break;
				case 'random' : $query->order('rand()'); break;
				case 'manual' :
					$manual_order_ids = trim($params->get('manual_ids', '')); // TODO trim the commas as well? actually, do this in all fields with lists of elements with commas
					if (!empty($manual_order_ids)) {
						//$query->order('FIELD(cd.id, '.self::$manual_order_ids.')'); // MySQL specific
							
						$array_ids = explode(',', $manual_order_ids);
						$order = 'CASE cd.id';
						$i = 0;
						foreach ($array_ids as $id) {
							$order .= ' WHEN '.$id.' THEN '.$i++;
						}
						$order .= ' ELSE 999 END, cd.id';
						$query->order($order);
					} else {
						$query->order('cd.id ASC');
					}
					break;
				case 'sna' : $query->order($db->escape('cd.sortname1').' ASC');
					$query->order($db->escape('cd.sortname2').' ASC');
					$query->order($db->escape('cd.sortname3').' ASC');
					break;
				case 'snd' : $query->order($db->escape('cd.sortname1').' DESC');
					$query->order($db->escape('cd.sortname2').' DESC');
					$query->order($db->escape('cd.sortname3').' DESC');
					break;
				default : $query->order('cd.ordering ASC'); break;
			}
		}
		
		// access filter
		
		$query->where('cd.access IN ('.$groups.')');
		$query->where('cc.access IN ('.$groups.')');
		
		$query->where($db->quoteName('cc.published').' = 1');
		
		// date filter
		
		$nullDate = $db->Quote($db->getNullDate());
		$nowDate = $db->Quote(JFactory::getDate()->toSql());
		$query->where('cd.published = 1');
		$query->where('(cd.publish_up = '.$nullDate.' OR cd.publish_up <= '.$nowDate.')');
		$query->where('(cd.publish_down = '.$nullDate.' OR cd.publish_down >= '.$nowDate.')');
		
		// launch query		
		
		if (!empty($count)) {
			$db->setQuery($query, 0, intval($count));
		} else {		
			$db->setQuery($query);
		}		
		
		try {
			$items = $db->loadObjectList();
		} catch (RuntimeException $e) {
			$app->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return null;
		}	
		
		// END OF DATABASE QUERY
		
		if (empty($items)) {
			return array();
		}
		
		// ITEM DATA ADDITIONS

		$helper_tags = new JHelperTags;
		
		foreach ($items as $item) {
			$item->firstpart = self::_substring_index(trim($item->name), ' ', 1);
			$item->secondpart = self::_substring_index(self::_substring_index(trim($item->name), ' ', 2), ' ', -1);
			$item->lastpart = self::_substring_index(trim($item->name), ' ', -1);
			
			// keep original image (needed if showing picture in popup)
			
			$item->original_image = $item->image;
			
			// get tags
			
			$show_tags = $params->get('s_tag', 'h') != 'h' ? true : false;			
			if ($show_tags) { // get the tags if needed for show
				$item->tags = $helper_tags->getItemTags('com_contact.contact', $item->id);
			}
			
			// get the individual background 
			
			$individual_bg_option = $params->get('individual_bg_pic', '');			
			if ($individual_bg_option) {	
				if ($individual_bg_option == 'def_bg') { // default bg picture selected
					if ($params->get('d_bg_pic', '')) {
						$item->individual_bg = $params->get('d_bg_pic');
					}
				} else if ($individual_bg_option == 'pic') { // contact picture selected
					if (!empty($item->image)) {
						$item->individual_bg = $item->image;
					} else if ($params->get('d_bg_pic', '')) {
						$item->individual_bg = $params->get('d_bg_pic');
					}
				} else {
					$query->clear();
					
					$query->select($db->quoteName('value'));
					$query->from($db->quoteName('#__fields_values'));
					$query->where($db->quoteName('field_id').' = '.$individual_bg_option);
					$query->where($db->quoteName('item_id').' = '.$item->id);
					
					$db->setQuery($query);
					
					$item->individual_bg = '';
					try {
						$item->individual_bg = $db->loadResult();
					} catch (RuntimeException $e) {
						JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
					}
					
					if (empty($item->individual_bg) && $params->get('d_bg_pic', '')) {
						$item->individual_bg = $params->get('d_bg_pic');
					}
				}
			} 
		}
		
		// SPECIFIC ORDERING

		if ($selection != 'contact' && $selection != 'user') {
			
			$format_style = $params->get('name_fmt', 'none');
			if ($format_style != '') {
				if ($catorder == "oa") {
					switch ($order) {
						case 'fnf_fa' : // follow the name format - order on 1st part (asc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortCascSascFasc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortCascFascSasc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortCascLascFasc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortCascFascLasc"); break;
								default : break;
							}
							break;
						case 'fnf_fd' : // follow the name format - order on 1st part (desc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortCascSdescFdesc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortCascFdescSdesc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortCascLdescFdesc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortCascFdescLdesc"); break;
								default : break;
							}
							break;
						case 'fnf_la' : // follow the name format - order on 2nd part (asc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortCascFascSasc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortCascSascFasc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortCascFascLasc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortCascLascFasc"); break;
								default : break;
							}
							break;
						case 'fnf_ld' : // follow the name format - order on 2nd part (desc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortCascFdescSdesc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortCascSdescFdesc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortCascFdescLdesc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortCascLdescFdesc"); break;
								default : break;
							}
							break;
						default : break;
					}
				} else if ($catorder == "od") {
					switch ($order) {
						case 'fnf_fa' : // follow the name format - order on 1st part (asc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortCdescSascFasc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortCdescFascSasc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortCdescLascFasc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortCdescFascLasc"); break;
								default : break;
							}
							break;
						case 'fnf_fd' : // follow the name format - order on 1st part (desc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortCdescSdescFdesc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortCdescFdescSdesc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortCdescLdescFdesc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortCdescFdescLdesc"); break;
								default : break;
							}
							break;
						case 'fnf_la' : // follow the name format - order on 2nd part (asc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortCdescFascSasc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortCdescSascFasc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortCdescFascLasc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortCdescLascFasc"); break;
								default : break;
							}
							break;
						case 'fnf_ld' : // follow the name format - order on 2nd part (desc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortCdescFdescSdesc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortCdescSdescFdesc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortCdescFdescLdesc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortCdescLdescFdesc"); break;
								default : break;
							}
							break;
						default : break;
					}
				} else {
					switch ($order) {
						case 'fnf_fa' : // follow the name format - order on 1st part (asc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortSascFasc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortFascSasc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortLascFasc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortFascLasc"); break;
								default : break;
							}
							break;
						case 'fnf_fd' : // follow the name format - order on 1st part (desc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortSdescFdesc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortFdescSdesc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortLdescFdesc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortFdescLdesc"); break;
								default : break;
							}
							break;
						case 'fnf_la' : // follow the name format - order on 2nd part (asc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortFascSasc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortSascFasc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortFascLasc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortLascFasc"); break;
								default : break;
							}
							break;
						case 'fnf_ld' : // follow the name format - order on 2nd part (desc)
							switch ($format_style) {
								case 'rsf' : case 'rcf' : case 'rsfd' : case 'rdsf' : usort($items, "modTrombinoscopeHelper::sortFdescSdesc"); break;
								case 'fsr' : case 'fcr' : case 'fdsr' : case 'fsrd' : usort($items, "modTrombinoscopeHelper::sortSdescFdesc"); break;
								case 'lsp' : case 'lcp' : case 'ldsp' : case 'lspd' : usort($items, "modTrombinoscopeHelper::sortFdescLdesc"); break;
								case 'psl' : case 'pcl' : case 'psld' : case 'pdsl' : usort($items, "modTrombinoscopeHelper::sortLdescFdesc"); break;
								default : break;
							}
							break;
						default : break;
					}
				}
			}
		}
		
		return $items;
	}	
	
	protected static function _getSubQuery($field, $index)
	{
		$subquery = '';
		
		$db = JFactory::getDbo();
		
		if ($field != "none") {
			$subquery .= ', ';
				
			switch ($field) {
				case 'empty' :
					$subquery .= "'empty' AS field".$index.", 'empty' AS fieldname".$index;
					break;
				case 'gen' : // gender
					$subquery .= "'gender' AS field".$index.", 'gender' AS fieldname".$index;
					break;
				case 'dob' : // birthdate
					$subquery .= "'birthdate' AS field".$index.", 'birthdate' AS fieldname".$index;
					break;
				case 'age' : // age
					$subquery .= "'age' AS field".$index.", 'age' AS fieldname".$index;
					break;
				case 'com' : // company
					$subquery .= "'company' AS field".$index.", 'company' AS fieldname".$index;
					break;
				case 'dep' : // department
					$subquery .= "'department' AS field".$index.", 'department' AS fieldname".$index;
					break;
				case 'map' : // map
					$subquery .= "'map' AS field".$index.", 'map' AS fieldname".$index;
					break;					
				case 'sum' : // summary
					$subquery .= "'summary' AS field".$index.", 'summary' AS fieldname".$index;
					break;					
				case 'skype' : // skype
					$subquery .= "'skype' AS field".$index.", 'skype' AS fieldname".$index;
					break;
				case 'facebook' : // facebook
					$subquery .= "'facebook' AS field".$index.", 'facebook' AS fieldname".$index;
					break;
				case 'twitter' : // twitter
					$subquery .= "'twitter' AS field".$index.", 'twitter' AS fieldname".$index;
					break;
				case 'linkedin' : // linkedin
					$subquery .= "'linkedin' AS field".$index.", 'linkedin' AS fieldname".$index;
					break;
				case 'googleplus' : // googleplus
					$subquery .= "'googleplus' AS field".$index.", 'googleplus' AS fieldname".$index;
					break;
				case 'youtube' : // youtube
					$subquery .= "'youtube' AS field".$index.", 'youtube' AS fieldname".$index;
					break;
				case 'instagram' : // instagram
					$subquery .= "'instagram' AS field".$index.", 'instagram' AS fieldname".$index;
					break;
				case 'pinterest' : // pinterest
					$subquery .= "'pinterest' AS field".$index.", 'pinterest' AS fieldname".$index;
					break;					
				case 'c_p' : // con_position
					$subquery .= "trim(cd.con_position) AS field".$index.", 'con_position' AS fieldname".$index;
					break;
				case 'tel' : // telephone
					$subquery .= "trim(cd.telephone) AS field".$index.", 'telephone' AS fieldname".$index;
					break;
				case 'mob' : // mobile
					$subquery .= "trim(cd.mobile) AS field".$index.", 'mobile' AS fieldname".$index;
					break;
				case 'mail' : // email_to
					$subquery .= "trim(cd.email_to) AS field".$index.", 'email_to' AS fieldname".$index;
					break;
				case 'web' : // webpage
					$subquery .= "trim(cd.webpage) AS field".$index.", 'webpage' AS fieldname".$index;
					break;
				case 'add' : // address
					$subquery .= "trim(cd.address) AS field".$index.", 'address' AS fieldname".$index;
					break;
				case 'sub' : // suburb
					$subquery .= "trim(cd.suburb) AS field".$index.", 'suburb' AS fieldname".$index;
					break;
				case 'st' : // state
					$subquery .= "trim(cd.state) AS field".$index.", 'state' AS fieldname".$index;
					break;
				case 'p_c' : // postcode
					$subquery .= "trim(cd.postcode) AS field".$index.", 'postcode' AS fieldname".$index;
					break;
				case 'cou' : // country
					$subquery .= "trim(cd.country) AS field".$index.", 'country' AS fieldname".$index;
					break;					
				case 'a' : // links a..e
					$subquery .= "'linka' AS field".$index.", 'linka' AS fieldname".$index;
					break;
				case 'b' :
					$subquery .= "'linkb' AS field".$index.", 'linkb' AS fieldname".$index;
					break;
				case 'c' :
					$subquery .= "'linkc' AS field".$index.", 'linkc' AS fieldname".$index;
					break;
				case 'd' :
					$subquery .= "'linkd' AS field".$index.", 'linkd' AS fieldname".$index;
					break;
				case 'e' :
					$subquery .= "'linke' AS field".$index.", 'linke' AS fieldname".$index;
					break;					
				case 'a_sw' : // links a..e same window
					$subquery .= "'linka' AS field".$index.", 'linka_sw' AS fieldname".$index;
					break;
				case 'b_sw' :
					$subquery .= "'linkb' AS field".$index.", 'linkb_sw' AS fieldname".$index;
					break;
				case 'c_sw' :
					$subquery .= "'linkc' AS field".$index.", 'linkc_sw' AS fieldname".$index;
					break;
				case 'd_sw' :
					$subquery .= "'linkd' AS field".$index.", 'linkd_sw' AS fieldname".$index;
					break;
				case 'e_sw' :
					$subquery .= "'linke' AS field".$index.", 'linke_sw' AS fieldname".$index;
					break;					
				case 'f_a' : // formatted address
					$subqueryformattedaddress = '';
					switch (self::$address_format) {
						case 'ssz' :
							$subqueryformattedaddress = "CONCAT(trim(cd.suburb), ', ', trim(cd.state), ' ', trim(cd.postcode))";
							break;
						case 'zss' :
							$subqueryformattedaddress = "CONCAT(trim(cd.postcode), ' ', trim(cd.suburb), ', ', trim(cd.state))";
							break;
						case 'zs' :
							$subqueryformattedaddress = "CONCAT(trim(cd.postcode), ' ', trim(cd.suburb))";
							break;
						case 'sz' :
							$subqueryformattedaddress = "CONCAT(trim(cd.suburb), ' ', trim(cd.postcode))";
							break;
						case 'ss' :
							$subqueryformattedaddress = "CONCAT(trim(cd.suburb), ', ', trim(cd.state))";
							break;
						default :
							$subqueryformattedaddress = '';
						break;
					}
					$subquery .= $subqueryformattedaddress." AS field".$index.", 'formattedaddress' AS fieldname".$index;
					break;				
				case 'f_f_a' : // fully formatted address
					$subqueryformattedaddress = '';
					switch (self::$address_format) {
						case 'ssz' :
							$subqueryformattedaddress = "CONCAT(trim(cd.address), '$', trim(cd.suburb), ', ', trim(cd.state), ' ', trim(cd.postcode))";
							break;
						case 'zss' :
							$subqueryformattedaddress = "CONCAT(trim(cd.address), '$', trim(cd.postcode), ' ', trim(cd.suburb), ', ', trim(cd.state))";
							break;
						case 'zs' :
							$subqueryformattedaddress = "CONCAT(trim(cd.address), '$', trim(cd.postcode), ' ', trim(cd.suburb))";
							break;
						case 'sz' :
							$subqueryformattedaddress = "CONCAT(trim(cd.address), '$', trim(cd.suburb), ' ', trim(cd.postcode))";
							break;
						case 'ss' :
							$subqueryformattedaddress = "CONCAT(trim(cd.address), '$', trim(cd.suburb), ', ', trim(cd.state))";
							break;
						default :
							$subqueryformattedaddress = '';
						break;
					}
					$subquery .= $subqueryformattedaddress." AS field".$index.", 'fullyformattedaddress' AS fieldname".$index;
					break;
				case 'misc' :					
					if (self::$text_type == 'info') { // take misc
						$subquery .= "trim(cd.".$field.") AS field".$index.", '".$field."' AS fieldname".$index;
					} else { // take metadescription
						$subquery .= "trim(cd.metadesc) AS field".$index.", '".$field."' AS fieldname".$index;
					}
					break;
				default :
					$field_temp = explode(':', $field); // $field is jfield:type:field in case of a custom field
						
					if (count($field_temp) < 2) {
						$subquery .= "trim(cd.".$field.") AS field".$index.", '".$field."' AS fieldname".$index;
					} else { // custom fields
						$subquery .= $db->quote($field)." AS field".$index.", ".$db->quote($field)." AS fieldname".$index;
					}
			}
		}
	
		return $subquery;
	}
	
	/**
	 * Get the categories as a string for the sql query
	 */
	protected static function getCategories($categories_array, $get_sub_categories)
	{
		$categories = '';
		
		if (count($categories_array) > 0) {
			
			$array_of_category_values = array_count_values($categories_array);
			if (isset($array_of_category_values['all']) && $array_of_category_values['all'] > 0) { // 'all' was selected
				return $categories;
			}
			
			if ($get_sub_categories != 'no') {
				$categories_object = JCategories::getInstance('Contact'); // new JCategories('com_contact');
				foreach ($categories_array as $category) {
					$category_object = $categories_object->get($category); // if category unpublished, unset
					if (isset($category_object) && $category_object->hasChildren()) {
						if ($get_sub_categories == 'all') {
							$sub_categories_array = $category_object->getChildren(true); // true is for recursive
						} else {
							$sub_categories_array = $category_object->getChildren();
						}
						foreach ($sub_categories_array as $subcategory_object) {
							$categories_array[] = $subcategory_object->id;
						}
					}
			
				}
			
				$categories_array = array_unique($categories_array);
			}
			
			if (!empty($categories_array)) {
				$categories = implode(',', $categories_array);
			}
		} 
	
		return $categories;
	}
	
	public static function getFormattedName($name, $style, $uppercase)
	{		
		$formatted_name = $name;
				
		// case Olivier-Daniel Buisard Something Jr
		// firstpart -> Olivier-Daniel
		// remainingpart -> Buisard Something Jr
		$name_parts = explode(' ', $name);
		
		$firstpart = $name_parts[0];		
		if ($uppercase) {
			$firstpart = ucfirst($firstpart);
		}		
		
		unset($name_parts[0]);
		$remainingpart = '';
		if (count($name_parts) > 0) {
			if ($uppercase) {
				$remainingpart = ucwords(implode(' ', $name_parts)); // make sure there is no extra space when testing
			} else {
				$remainingpart = implode(' ', $name_parts);
			}
		}		
		
		// case Buisard Something Jr Olivier-Daniel
		// lastpart -> Olivier-Daniel
		// previouspart -> Buisard Something Jr
		$name_parts = explode(' ', $name);
		$name_parts_length = count($name_parts);
		
		$lastpart = $name_parts[$name_parts_length - 1];
		if ($uppercase) {
			$lastpart = ucfirst($lastpart);
		}
		
		unset($name_parts[$name_parts_length - 1]);
		$previouspart = '';
		if (count($name_parts) > 0) {
			if ($uppercase) {
				$previouspart = ucwords(implode(' ', $name_parts)); // make sure there is no extra space when testing
			} else {
				$previouspart = implode(' ', $name_parts);
			}
		}
		
		switch ($style) {
			case 'rsf' :
				$formatted_name = $firstpart;
				if (!empty($remainingpart)) {
					$formatted_name = $remainingpart." ".$formatted_name;
				}
				break;
			case 'fsr' :
				$formatted_name = $firstpart;
				if (!empty($remainingpart)) {
					$formatted_name = $formatted_name." ".$remainingpart;
				}
				break;
			case 'rcf' :
				$formatted_name = $firstpart;
				if (!empty($remainingpart)) {
					$formatted_name = $remainingpart.", ".$formatted_name;
				}
				break;
			case 'fcr' :
				$formatted_name = $firstpart;
				if (!empty($remainingpart)) {
					$formatted_name = $formatted_name.", ".$remainingpart;
				}
				break;				
			case 'psl' :
				$formatted_name = $lastpart;
				if (!empty($previouspart)) {
					$formatted_name = $previouspart." ".$formatted_name;
				}
				break;
			case 'lsp' :
				$formatted_name = $lastpart;
				if (!empty($previouspart)) {
					$formatted_name = $formatted_name." ".$previouspart;
				}
				break;
			case 'pcl' :
				$formatted_name = $lastpart;
				if (!empty($previouspart)) {
					$formatted_name = $previouspart.", ".$formatted_name;
				}
				break;
			case 'lcp' :
				$formatted_name = $lastpart;
				if (!empty($previouspart)) {
					$formatted_name = $formatted_name.", ".$previouspart;
				}
				break;				
			case 'rsfd' :
				$formatted_name = substr($firstpart, 0, 1).'.';
				if (!empty($remainingpart)) {
					$formatted_name = $remainingpart." ".$formatted_name;
				}
				break;
			case 'fdsr' :
				$formatted_name = substr($firstpart, 0, 1).'.';
				if (!empty($remainingpart)) {
					$formatted_name = $formatted_name." ".$remainingpart;
				}
				break;
			case 'psld' :
				$formatted_name = substr($lastpart, 0, 1).'.';
				if (!empty($previouspart)) {
					$formatted_name = $previouspart." ".$formatted_name;
				}
				break;
			case 'ldsp' :
				$formatted_name = substr($lastpart, 0, 1).'.';
				if (!empty($previouspart)) {
					$formatted_name = $formatted_name." ".$previouspart;
				}
				break;
			case 'rdsf' :
				$formatted_name = $firstpart;
				if (!empty($remainingpart)) {
					$formatted_name = substr($remainingpart, 0, 1).". ".$formatted_name;
				}
				break;
			case 'fsrd' :
				$formatted_name = $firstpart;
				if (!empty($remainingpart)) {
					$formatted_name = $formatted_name." ".substr($remainingpart, 0, 1).'.';
				}
				break;
			case 'pdsl' :
				$formatted_name = $lastpart;
				if (!empty($previouspart)) {
					$formatted_name = substr($previouspart, 0, 1).". ".$formatted_name;
				}
				break;
			case 'lspd' :
				$formatted_name = $lastpart;
				if (!empty($previouspart)) {
					$formatted_name = $formatted_name." ".substr($previouspart, 0, 1).'.';
				}
				break;				
			default : 
				if ($uppercase) {
					$formatted_name = ucwords($formatted_name);
				}
				break;
		}
		
		return $formatted_name;
	}
	
	public static function getTooltipClass($add = false)
	{
		$class = '';
		if ($add) {
			$class = ' hasTooltip';
		}
	
		return $class;
	}
	
	public static function getTitleAttribute($title, $add = false) 
	{
		$title_attribute = '';
		if ($add) {
			$title_attribute = ' title="'.$title.'"';
		}
		
		return $title_attribute;
	}
	
	/**
	 * 
	 * @param unknown $contact_params
	 * @param unknown $config_field_name
	 * @param unknown $plugin_field_name
	 * @return unknown|string we only deal with simple values
	 */
	protected static function getSpecialFeatureValue($contact_params, $config_field_name, $plugin_field_name) 
	{
		if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_trombinoscopeextended/config.xml')) {
			$config_params = JComponentHelper::getParams('com_trombinoscopeextended'); // from config.xml
		}
			
		if (isset($config_params) && $config_params->get($config_field_name, '') && JFolder::exists(JPATH_ADMINISTRATOR . '/components/com_fields') && JComponentHelper::isEnabled('com_fields') && JComponentHelper::getParams('com_contact')->get('custom_fields_enable', '1')) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');
			$field_model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));
			$contact_id = explode(':', $contact_params->get('id'));
			$contact_id = $contact_id[0];
			return $field_model->getFieldValue($config_params->get($config_field_name), $contact_id);
		} else {
			if (JPluginHelper::isEnabled('content', 'additionalcontactfields')) {
				return $contact_params->get($plugin_field_name, '');
			}
		}
		
		return '';
	}
	
	static $isStandalone = null;
	
	public static function isStandalone()
	{
		if (!isset(self::$isStandalone)) {
			
			self::$isStandalone = false;
			
			$folder = JPATH_ROOT.'/components/com_trombinoscopeextended/views/trombinoscope';
			if (!JFolder::exists($folder)) {
				self::$isStandalone = true;
			}
		}
		
		return self::$isStandalone;
	}	
	
	static $contactGlobals = null;
	
	static function getContactGlobalParams()
	{
		if (!isset(self::$contactGlobals)) {
					
			self::$contactGlobals = new JRegistry();
			
			$global_contact_params = JComponentHelper::getParams('com_contact');
			
			self::$contactGlobals->set("linka_name", $global_contact_params->get('linka_name'));
			self::$contactGlobals->set("linkb_name", $global_contact_params->get('linkb_name'));
			self::$contactGlobals->set("linkc_name", $global_contact_params->get('linkc_name'));
			self::$contactGlobals->set("linkd_name", $global_contact_params->get('linkd_name'));
			self::$contactGlobals->set("linke_name", $global_contact_params->get('linke_name'));
			
			self::$contactGlobals->set("default_image", $global_contact_params->get('image'));		
		}
		
		return self::$contactGlobals;
	}
	
	static function showLinks($params, $prefix = '')
	{	
		$j = 1;
		while ($params->get($prefix.'lf'.$j) != null) {
			if ($params->get($prefix.'lf'.$j, 'none') != 'none') {
				return true;
			}
			$j++;
		}
		
		return false;		
	}	
	
	static function getRequestedName($params, $item)
	{
		$infos = array();
		
		$info_details = array();
		
		$info_details['name'] = 'name';
		$info_details['value'] = $item->name;
		$info_details['show_what'] = $params->get('s_name_lbl', 0);
		$info_details['label'] = $params->get('name_lbl', '');
		$info_details['icon'] = $params->get('name_icon', '');
		$info_details['show_tooltip'] = $params->get('name_tooltip', 1);
		
		$infos[0] = $info_details;
		
		return $infos;
	}
	
	static function getRequestedNameOutput($infos, $params, $item, $extraclass = '') {
		
		if (empty($infos)) {
			return '';
		}
		
		return self::getNewFieldOutput(0, $infos[0], $params, $item, $extraclass);
	}
	
	static function setRequestedNameElement(&$infos, $key, $value)
	{
		$infos[0][$key] = $value;
	}
	
	static function getRequestedLinks($params, $item, $prefix = '')
	{
		$links = array();
		
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		
		$j = 1;
		while ($params->get($prefix.'lf'.$j) != null) {
			
			$fieldaccess = $params->get($prefix.'lf'.$j.'_access', 1);
			if ($params->get($prefix.'lf'.$j, 'none') != 'none' && in_array($fieldaccess, $groups)) {
				
				$info_details = array();
				
				$fieldname = 'fieldnamel'.$j;
				$fieldvalue = 'fieldl'.$j;
				
				$info_details['name'] = $item->$fieldname; // name
				$info_details['value'] = $item->$fieldvalue; // value				
				$info_details['show_what'] = 2;
				$info_details['label'] = '';
				$info_details['icon'] = $params->get($prefix.'lf'.$j.'_icon', '');
				$info_details['show_tooltip'] = true;
				
				$links[$j] = $info_details;
			}
			$j++;
		}
		
		return $links;
	}
	
	static function getRequestedLinksOutput($infos, $params, $item, $extraclass = '') {
		
		$info_block = '';
		
		if (empty($infos)) {
			return $info_block;
		}
		
		foreach ($infos as $key => $value) {
			$info_block .= self::getNewFieldOutput($key, $value, $params, $item, $extraclass, true);
		}
		
		return $info_block;
	}
	
	static function getRequestedInfos($params, $item, $prefix = '')
	{
		$infos = array();
		
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		
		$j = 1;
		while ($params->get($prefix.'f'.$j) != null) {
			
			$fieldaccess = $params->get($prefix.'f'.$j.'_access', 1);
			if ($params->get($prefix.'f'.$j, 'none') != 'none' && in_array($fieldaccess, $groups)) {
				
				$info_details = array();
					
				$fieldname = 'fieldname'.$j;
				$fieldvalue = 'field'.$j;
				
				$info_details['name'] = $item->$fieldname; // name
				$info_details['value'] = $item->$fieldvalue; // value
				$info_details['show_what'] = $params->get($prefix.'s_f'.$j.'_lbl', 0);
				$info_details['label'] = $params->get($prefix.'f'.$j.'_lbl');
				$info_details['icon'] = $params->get($prefix.'f'.$j.'_icon', '');
				$info_details['show_tooltip'] = $params->get($prefix.'f'.$j.'_tooltip', 1) == 1 ? true : false;
				
				$infos[$j] = $info_details;
			}
			$j++;
		}
		
		return $infos;
	}
	
	static function setRequestedSingleInfoElement($index, &$infos, $key, $value)
	{
		$infos[$index][$key] = $value;
	}
	
	static function getRequestedSingleInfoOutput($index, $infos, $params, $item, $extraclass = '') {
		
		if (!isset($infos[$index])) {
			return '';
		}
		
		return self::getNewFieldOutput($index, $infos[$index], $params, $item, $extraclass);
	}
	
	static function getRequestedInfosOutput($infos, $params, $item, $extraclass = '') {
		
		$info_block = '';
		
		if (empty($infos)) {
			return $info_block;
		}
		
		ksort($infos);
		
		foreach ($infos as $key => $value) {
			$info_block .= self::getNewFieldOutput($key, $value, $params, $item, $extraclass);
		}
		
		return $info_block;
	}
	
	// deprecated - kept for backward compatibility with template overrides - remove in 3.0
	public static function getFieldOutput($index, $field, $fieldname, $fieldaccess, $prefield, $fieldlabel, $fieldicon, $fieldtooltip, $params, $globalparams, $trombparams, $iconlinkonly = false) 
	{
		// restricted access
		
		$user = JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		
		if (!in_array($fieldaccess, $groups)) {
			return '';
		}
		
		// $params is the contact params + $item->slug (id) and $item->catid (catid)
		$item = new StdClass;
		$item->slug = $params->get('id');
		$item->catid = $params->get('catid');
		$item->params = $params->toString();
		
		$trombparams->set('k_s', $trombparams->get('keep_space'));
		$trombparams->set('link_e', $trombparams->get('link_email'));
		$trombparams->set('cloak_e', $trombparams->get('cloak_email'));
		$trombparams->set('e_substitut', $trombparams->get('email_substitut'));
		$trombparams->set('w_substitut', $trombparams->get('webpage_substitut'));
		$trombparams->set('l_c', $trombparams->get('letter_count'));
		$trombparams->set('s_t', $trombparams->get('strip_tags'));
		$trombparams->set('s_f_lbl', $trombparams->get('all_pre'));
		$trombparams->set('lbl_separator', $trombparams->get('label_separator'));
		$trombparams->set('d_format', $trombparams->get('date_format'));
		$trombparams->set('dob_format', $trombparams->get('birthdate_format'));
		$trombparams->set('a_link_map', $trombparams->get('link_address_with_map'));
		
		$info_details= array();
		
		$info_details['name'] = $fieldname; // name
		$info_details['value'] = $field; // value
		$info_details['show_what'] = $prefield;
		$info_details['label'] = $fieldlabel;
		$info_details['icon'] = $fieldicon;
		$info_details['show_tooltip'] = $fieldtooltip;
		
		return self::getNewFieldOutput($index, $info_details, $trombparams, $item, '', $iconlinkonly);
	}
	
	static function getNewFieldOutput($index, $info_details, $trombparams, $item, $extraclass = '', $iconlinkonly = false) 
	{		
		$html = '';
		
		$field = $info_details['value'];
		$fieldname = $info_details['name'];
		$prefield = $info_details['show_what'];
		$fieldlabel = $info_details['label'];
		$fieldicon = $info_details['icon'];
		$fieldtooltip = $info_details['show_tooltip'];
		
		$params = new JRegistry();
		$params->loadString($item->params);
		
		$globalparams = self::getContactGlobalParams();
		
		// restricted access
		
// 		$user = JFactory::getUser();
// 		$groups	= $user->getAuthorisedViewLevels();
// 		if (!in_array($fieldaccess, $groups)) {
// 			return $html;
// 		}
		
		$value = '';
		$value_substitute = '';
		$value_is_link = false;
		$show_link = false;
		$target = '_blank';
		$label = '';
		$title = '';
		$class = '';
		$icon_class = '';
		$generated_link_tag = '';
		
		switch ($fieldname) {
			case 'empty' :	
				$class = 'empty';
				break;
				
			case 'name' :
				$value = $field; 
				$class = 'fieldname'; $icon_class = !empty($fieldicon) ? $fieldicon : 'user';
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_NAME') : $fieldlabel;
				break;
				
			case 'name_link' :
				$value = $field;
				$value_is_link = true;
				$generated_link_tag = $field;
				$class = 'fieldname'; $icon_class = !empty($fieldicon) ? $fieldicon : 'user';
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_NAME') : $fieldlabel;
				break;
				
			case 'con_position' :
				$value = $field;
				if (strpos($field, 'POSITION_') !== false) {
					$field_array = explode(',', $field);
					$field_array_fixed = array();
					$last_field = '';
					foreach ($field_array as $field_element) {
						$field_array_fixed[] = JText::_(trim($field_element));
					}
					$count = count($field_array);
					if ($count > 1) {
						$last_field = $field_array_fixed[$count - 1];
						unset($field_array_fixed[$count - 1]);
						$value = implode(', ', $field_array_fixed);
						$value .= JText::_('TROMBINOSCOPEEXTENDED_AND').' '.$last_field;
					} else {
						$value = JText::_(trim($field));
					}
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_POSITION') : $fieldlabel;
				$class = 'fieldposition'; $icon_class = !empty($fieldicon) ? $fieldicon : 'briefcase';
				break;
				
			case 'telephone' : 
				$value = $field;
				if (!empty($value) && SYWUtilities::isMobile()) {
					$value = 'tel:'.$field;
					$value_is_link = true;
					$show_link = true;
					$value_substitute = $field;
					$title = $field;
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_TELEPHONE') : $fieldlabel;
				$class = 'fieldtel'; $icon_class = !empty($fieldicon) ? $fieldicon : 'phone';
				break;
				
			case 'mobile' :	 
				$value = $field;
				if (!empty($value) && SYWUtilities::isMobile()) {
					$value = 'tel:'.$field;
					$value_is_link = true;
					$show_link = true;
					$value_substitute = $field;
					$title = $field;
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_MOBILE') : $fieldlabel;
				$class = 'fieldmobile'; $icon_class = !empty($fieldicon) ? $fieldicon : 'mobile';
				break;
				
			case 'fax' :	
				$value = $field;
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_FAX') : $fieldlabel;
				$class = 'fieldfax'; $icon_class = !empty($fieldicon) ? $fieldicon : 'fax';
				break;
				
			case 'email_to' :				
				if (!empty($field)) {
					$value_substitute = ($trombparams->get('e_substitut') == '') ? $field : $trombparams->get('e_substitut');					
				}
				switch ($trombparams->get('link_e')) {
					case 1: // mailto
						if (!empty($field)) {
							$value = 'mailto:'.$field;
							$title = $field;
							$value_is_link = true;
							$show_link = true;
							if ($trombparams->get('cloak_e')) {	
								if ($trombparams->get('e_substitut') != '') {
									$generated_link_tag = JHtml::_('email.cloak', $field, true, $trombparams->get('e_substitut'), false);
								} else {
									$generated_link_tag = JHtml::_('email.cloak', $field);
								}
							}
						}
						break;
					case 2: // contact
						if (!empty($field)) {
							$value_is_link = true;
							$target = '_self';
							$show_link = true;
							$title = JText::_('MOD_TROMBINOSCOPE_LABEL_EMAIL');
							$value = JRoute::_(self::getContactRoute('contact', $item->slug, $item->catid));
						}
						break;						
					case 3: case 4: // te contact with or without form
						if (!empty($field)) {
							$value_is_link = true;
							$target = '_self';
							$show_link = true;
							$title = JText::_('MOD_TROMBINOSCOPE_LABEL_EMAIL');

							if (!self::isStandalone()) {
								$form_anchor = '';
								if ($trombparams->get('link_e') == 3) {
									$form_anchor = '&anchor=form#teform';
								}
								$value = JRoute::_(self::getContactRoute('trombinoscopeextended', $item->slug, $item->catid).$form_anchor);
							} else { // defaults to standard contact page
								$value = JRoute::_(self::getContactRoute('contact', $item->slug, $item->catid));
							}
						}				
						break;				
					default: // no link
						//if ($trombparams->get("cloak_email"]) { // if cloak and no link, show the email address no matter what
							//$value = JHtml::_('email.cloak', $field);                                                                           // creates a link?
						//} else {
							if (!$iconlinkonly) {
								$value = $field;
							}
						//}
						break;
				} 
				
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_EMAIL') : $fieldlabel;
				$class = 'fieldemail'; $icon_class = !empty($fieldicon) ? $fieldicon : 'mail';
				break;
				
			case 'webpage' : 
				$value_is_link = true;
				$show_link = true;
				if (!empty($field)) {
					//$value = (0 === strpos($field, 'http')) ? $field : 'http://'.$field; // unnecessary because saved with it
					$value = $field;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
						$value_substitute = ($trombparams->get('w_substitut') == '') ? $title : $trombparams->get('w_substitut');
					} else {
						$title = $value;
						$value_substitute = ($trombparams->get('w_substitut') == '') ? $value : $trombparams->get('w_substitut');
					}
				}				
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_WEBPAGE') : $fieldlabel;
				$class = 'fieldwebpage'; $icon_class = !empty($fieldicon) ? $fieldicon : 'earth';
				break;
				
			case 'address' : 
				$value = trim($field, "$, \t\n\r\0\x0B"); // single quotes won't work
				$value = str_replace('$', "\n", $value);
				$title = $value;
				if (!empty($value)) {
					if ($trombparams->get('a_link_map')) {
						$mapvalue = self::getSpecialFeatureValue($params, 'mapping_feature', 'te_map');
						if ($mapvalue) {
							if (substr($mapvalue, 0, 4) != "http") {
								$mapvalue = 'https://'.$mapvalue;
							}
							$value_is_link = true;							
							$generated_link_tag = '<address><a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$mapvalue.'" target="_blank"'.self::getTitleAttribute($title, $fieldtooltip).'>'.nl2br($value).'</a></address>';
						} else {
							$value = '<address>'.nl2br($value).'</address>';
						}
					} else {
						$value = '<address>'.nl2br($value).'</address>';
					}
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_ADDRESS') : $fieldlabel;
				$class = 'fieldaddress'; $icon_class = !empty($fieldicon) ? $fieldicon : 'home';
				break;
				
			case 'fullyformattedaddress' : // address + zipcode... formatted
				$value = trim($field, "$, \t\n\r\0\x0B"); // single quotes won't work
				$value = str_replace('$', "\n", $value);
				$title = $value;
				if (!empty($value)) {
					if ($trombparams->get('a_link_map') == 1) { // auto
						$mapvalue = self::getAutoMapLink($value, $trombparams->get('auto_map_params'));
						$value_is_link = true;
						$generated_link_tag = '<address><a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$mapvalue.'" target="_blank"'.self::getTitleAttribute($title, $fieldtooltip).'>'.nl2br($value).'</a></address>';
					} else if ($trombparams->get('a_link_map') == 2) { // field
						$mapvalue = self::getSpecialFeatureValue($params, 'mapping_feature', 'te_map');
						if ($mapvalue) {
							if (substr($mapvalue, 0, 4) != "http") {
								$mapvalue = 'https://'.$mapvalue;
							}
							$value_is_link = true;
							$generated_link_tag = '<address><a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$mapvalue.'" target="_blank"'.self::getTitleAttribute($title, $fieldtooltip).'>'.nl2br($value).'</a></address>';
						} else {
							$value = '<address>'.nl2br($value).'</address>';
						}
					} else if ($trombparams->get('a_link_map') == 3) { // field first						
						$mapvalue = self::getSpecialFeatureValue($params, 'mapping_feature', 'te_map');
						if ($mapvalue) {
							if (substr($mapvalue, 0, 4) != "http") {
								$mapvalue = 'https://'.$mapvalue;
							}
						} else {
							$mapvalue = self::getAutoMapLink($value, $trombparams->get('auto_map_params'));
						}
						$value_is_link = true;
						$generated_link_tag = '<address><a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$mapvalue.'" target="_blank"'.self::getTitleAttribute($title, $fieldtooltip).'>'.nl2br($value).'</a></address>';
					} else {
						$value = '<address>'.nl2br($value).'</address>';
					}
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_FORMATTEDADDRESS') : $fieldlabel;
				$class = 'fieldformattedaddress'; $icon_class = !empty($fieldicon) ? $fieldicon : 'home';
				break;
				
			case 'suburb' : 
				$value = $field;
				$class = 'fieldsuburb';
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_SUBURB') : $fieldlabel;
				break;

			case 'state' : 
				$value = $field;
				$class = 'fieldstate';
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_STATE') : $fieldlabel;
				break;
				
			case 'postcode' : 
				$value = $field; 
				$class = 'fieldpostcode';
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_POSTCODE') : $fieldlabel;
				break;
				
			case 'country' : 
				$value = $field; 				
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_COUNTRY') : $fieldlabel;
				$class = 'fieldcountry'; $icon_class = !empty($fieldicon) ? $fieldicon : 'flag2';
				break;
				
			case 'misc' : 
				
				$letter_count = trim($trombparams->get('l_c'));
				$number_of_letters = -1;
				if ($letter_count != '') {
					$number_of_letters = (int)($letter_count);
				}

				$value = SYWText::getText($field, 'html', $number_of_letters, $trombparams->get('s_t'), trim($trombparams->get('keep_tags')));	
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_MISC') : $fieldlabel;
				$title = $label;
				$class = 'fieldmisc'; $icon_class = !empty($fieldicon) ? $fieldicon : 'info';
				break;
				
			case 'linka' :
				$value = $params->get('linka', '');		 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$label = empty($fieldlabel) ? self::getLabelForLink('linka', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinka'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linkb' :
				$value = $params->get('linkb', '');		 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$label = empty($fieldlabel) ? self::getLabelForLink('linkb', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinkb'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linkc' :
				$value = $params->get('linkc', '');		 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$label = empty($fieldlabel) ? self::getLabelForLink('linkc', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinkc'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linkd' :
				$value = $params->get('linkd', '');		 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$label = empty($fieldlabel) ? self::getLabelForLink('linkd', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinkd'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linke' :
				$value = $params->get('linke', '');		 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$label = empty($fieldlabel) ? self::getLabelForLink('linke', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinke'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linka_sw' : 
				$value = $params->get('linka', ''); 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$target = '_self';
				$label = empty($fieldlabel) ? self::getLabelForLink('linka', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinka'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linkb_sw' : 
				$value = $params->get('linkb', ''); 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$target = '_self';
				$label = empty($fieldlabel) ? self::getLabelForLink('linkb', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinkb'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linkc_sw' : 
				$value = $params->get('linkc', ''); 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$target = '_self';
				$label = empty($fieldlabel) ? self::getLabelForLink('linkc', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinkc'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linkd_sw' : 
				$value = $params->get('linkd', ''); 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$target = '_self';
				$label = empty($fieldlabel) ? self::getLabelForLink('linkd', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinkd'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'linke_sw' : 
				$value = $params->get('linke', ''); 
				if (!empty($value)) {
					//$value = (0 === strpos($value, 'http')) ? $value : 'http://'.$value;
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$target = '_self';
				$label = empty($fieldlabel) ? self::getLabelForLink('linke', $value, $params, $globalparams) : $fieldlabel;
				$class = 'fieldlinke'; $icon_class = !empty($fieldicon) ? $fieldicon : self::getIconForLink($value);
				break;
				
			case 'gender' : // gender
				$additional_field = $params->get('te_gender', '');
				if ($additional_field) {
					if ($additional_field == 'm') {
						$value = JText::_('MOD_TROMBINOSCOPE_VALUE_MALE');
					} else if ($additional_field == 'f') {
						$value = JText::_('MOD_TROMBINOSCOPE_VALUE_FEMALE');
					} else if ($value == 'c') {
						$value = JText::_('MOD_TROMBINOSCOPE_VALUE_THIRDGENDER');
					}
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_GENDER') : $fieldlabel;
				$class = 'fieldgender'; $icon_class = !empty($fieldicon) ? $fieldicon : 'users';
				break;
				
			case 'birthdate':
				$value = self::getSpecialFeatureValue($params, 'birthdate_feature', 'te_birthdate');
				if ($value) {
					$value = JHtml::_('date', $value, $trombparams->get('dob_format'));
				}				
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_BIRTHDATE') : $fieldlabel;
				$class = 'fieldbirthdate'; $icon_class = !empty($fieldicon) ? $fieldicon : 'gift';
				break;
				
			case 'age':
				$value = self::getSpecialFeatureValue($params, 'birthdate_feature', 'te_birthdate');					
				if ($value) {
					$value = JText::sprintf('MOD_TROMBINOSCOPE_YEARSOLD', date_create($value)->diff(date_create('today'))->y);
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_AGE') : $fieldlabel;
				$class = 'fieldage'; $icon_class = !empty($fieldicon) ? $fieldicon : 'gift';
				break;
				
			case 'company':
				$additional_field = trim($params->get('te_company', ''));
				if ($additional_field) {
					$value = $additional_field;
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_COMPANY') : $fieldlabel;
				$class = 'fieldcompany'; $icon_class = !empty($fieldicon) ? $fieldicon : 'office';
				break;
				
			case 'department':
				$additional_field = trim($params->get('te_department', ''));
				if ($additional_field) {
					$value = $additional_field;
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_DEPARTMENT') : $fieldlabel;
				$class = 'fielddepartment'; $icon_class = !empty($fieldicon) ? $fieldicon : 'tree';
				break;
				
			case 'map':
				$value = self::getSpecialFeatureValue($params, 'mapping_feature', 'te_map');				
				if ($value) {
					if (substr($value, 0, 4) != "http") {
						$value = 'https://'.$value;
					}
					if (!$trombparams->get('protocol')) {
						$title = self::remove_protocol($value);
					}
				}
				$value_is_link = true;
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_MAP') : $fieldlabel;
				$class = 'fieldmap'; $icon_class = !empty($fieldicon) ? $fieldicon : 'location';
				break;
				
			case 'summary':
				$additional_field = trim($params->get('te_summary', ''));
				if ($additional_field) {
					// allow most phrasing elements (allowed in span - no p or div or object...)
					$value = strip_tags($additional_field, '<a><em><strong><small><mark><abbr><dfn><i><b><s><u><code><var><samp><kbd><sup><sub><q><cite><span><bdo><bdi><br><br/><wbr><ins><del><img><map><area><button><label>');
				}
				$label = empty($fieldlabel) ? JText::_('MOD_TROMBINOSCOPE_LABEL_SUMMARY') : $fieldlabel;
				$title = $label;
				$class = 'fieldsummary'; $icon_class = !empty($fieldicon) ? $fieldicon : 'info';
				break;
				
			case 'skype':
				$value = self::getSpecialFeatureValue($params, 'skype_feature', 'te_skype');
				
				if ($iconlinkonly) {
					if ($value) {
						if (substr($value, 0, 5) != "skype") {
							$value = 'skype:'.$value.'?chat';
						}
						$value_is_link = true;
					}
				} else {
					$title = "Skype";
				}
				
				$label = empty($fieldlabel) ? 'Skype' : $fieldlabel;
				$class = 'fieldskype'; $icon_class = !empty($fieldicon) ? $fieldicon : 'skype';
				break;
				
			case 'facebook':
				$value = self::getSpecialFeatureValue($params, 'facebook_feature', 'te_facebook');
				
				if ($iconlinkonly) {
					if ($value) {
						if (substr($value, 0, 4) != "http") {
							$value = 'https://www.facebook.com/'.$value;
						}
					}
					$value_is_link = true;
				} else {
					$title = "Facebook";
					if (substr($value, 0, 4) == "http") {
						$value_is_link = true;
						$show_link = true;
						if (!$trombparams->get('protocol')) {
							$value_substitute = self::remove_protocol($value);
						}
					}
				}
				
				$label = empty($fieldlabel) ? 'Facebook' : $fieldlabel;
				$class = 'fieldfacebook'; $icon_class = !empty($fieldicon) ? $fieldicon : 'facebook';
				break;
				
			case 'twitter':
				$value = self::getSpecialFeatureValue($params, 'twitter_feature', 'te_twitter');
				
				if ($iconlinkonly) {
					if ($value) {
						if (substr($value, 0, 4) != "http") {
							$value = 'https://twitter.com/'.$value;
						}
					}
					$value_is_link = true;
				} else {
					$title = "Twitter";
					if (substr($value, 0, 4) == "http") {
						$value_is_link = true;
						$show_link = true;
						if (!$trombparams->get('protocol')) {
							$value_substitute = self::remove_protocol($value);
						}
					}
				}
				
				$label = empty($fieldlabel) ? 'Twitter' : $fieldlabel;
				$class = 'fieldtwitter'; $icon_class = !empty($fieldicon) ? $fieldicon : 'twitter';
				break;
				
			case 'linkedin':
				$value = self::getSpecialFeatureValue($params, 'linkedin_feature', 'te_linkedin');
				
				if ($iconlinkonly) {
					if ($value) {
						if (substr($value, 0, 4) != "http") {
							$value = 'https://www.linkedin.com/in/'.$value;
						}
					}
					$value_is_link = true;
				} else {
					$title = "LinkedIn";
					if (substr($value, 0, 4) == "http") {
						$value_is_link = true;
						$show_link = true;
						if (!$trombparams->get('protocol')) {
							$value_substitute = self::remove_protocol($value);
						}
					}
				}
				
				$label = empty($fieldlabel) ? 'LinkedIn' : $fieldlabel;
				$class = 'fieldlinkedin'; $icon_class = !empty($fieldicon) ? $fieldicon : 'linkedin';
				break;
				
			case 'googleplus':
				$value = self::getSpecialFeatureValue($params, 'googleplus_feature', 'te_googleplus');
				
				if ($iconlinkonly) {
					if ($value) {
						if (substr($value, 0, 4) != "http") {
							$value = 'https://plus.google.com/+'.$value.'/posts';
						}
					}
					$value_is_link = true;
				} else {
					$title = "Google+";
					if (substr($value, 0, 4) == "http") {
						$value_is_link = true;
						$show_link = true;
						if (!$trombparams->get('protocol')) {
							$value_substitute = self::remove_protocol($value);
						}
					}
				}
				
				$label = empty($fieldlabel) ? 'Google+' : $fieldlabel;
				$class = 'fieldgoogleplus'; $icon_class = !empty($fieldicon) ? $fieldicon : 'googleplus';
				break;
				
			case 'youtube':
				$value = self::getSpecialFeatureValue($params, 'youtube_feature', 'te_youtube');
				
				if ($iconlinkonly) {
					if ($value) {
						if (substr($value, 0, 4) != "http") {
							$value = 'https://www.youtube.com/user/'.$value;
						}
					}
					$value_is_link = true;
				} else {
					$title = "YouTube";
					if (substr($value, 0, 4) == "http") {
						$value_is_link = true;
						$show_link = true;
						if (!$trombparams->get('protocol')) {
							$value_substitute = self::remove_protocol($value);
						}
					}
				}
				
				$label = empty($fieldlabel) ? 'YouTube' : $fieldlabel;
				$class = 'fieldyoutube'; $icon_class = !empty($fieldicon) ? $fieldicon : 'youtube';
				break;
				
			case 'instagram':
				$value = self::getSpecialFeatureValue($params, 'instagram_feature', 'te_instagram');
				
				if ($iconlinkonly) {
					if ($value) {
						if (substr($value, 0, 4) != "http") {
							$value = 'http://instagram.com/'.$value;
						}
					}
					$value_is_link = true;
				} else {
					$title = "Instagram";
					if (substr($value, 0, 4) == "http") {
						$value_is_link = true;
						$show_link = true;
						if (!$trombparams->get('protocol')) {
							$value_substitute = self::remove_protocol($value);
						}
					}
				}
				
				$label = empty($fieldlabel) ? 'Instagram' : $fieldlabel;
				$class = 'fieldinstagram'; $icon_class = !empty($fieldicon) ? $fieldicon : 'instagram';
				break;
				
			case 'pinterest':
				$value = self::getSpecialFeatureValue($params, 'pinterest_feature', 'te_pinterest');
				
				if ($iconlinkonly) {
					if ($value) {
						if (substr($value, 0, 4) != "http") {
							$value = 'http://www.pinterest.com/'.$value;
						}
					}
					$value_is_link = true;
				} else {
					$title = "Pinterest";
					if (substr($value, 0, 4) == "http") {
						$value_is_link = true;
						$show_link = true;
						if (!$trombparams->get('protocol')) {
							$value_substitute = self::remove_protocol($value);
						}
					}
				}
				
				$label = empty($fieldlabel) ? 'Pinterest' : $fieldlabel;
				$class = 'fieldpinterest'; $icon_class = !empty($fieldicon) ? $fieldicon : 'pinterest';
				break;
				
			default:
				$type_temp = explode(':', $fieldname); // $fieldname is jfield:type:field
				
				if (count($type_temp) < 2) {
					break;
				}
				
				if ($type_temp[0] == 'jfield') { // Joomla fields
				
					$field_id = $type_temp[2];
					$field_type = $type_temp[1];
						
					$contact_id = explode(':', $item->slug);
					$contact_id = $contact_id[0];
						
					$db = JFactory::getDBO();
						
					$query = $db->getQuery(true);
						
					// TODO check if more efficient to call all fields once statically (see contact view) through the com_fields helper (depends on how many fields are used overall)
					
					// not using GROUP_CONCAT to make sure compatible with all databases
					$query->select($db->quoteName(array('fv.value', 'f.label', 'f.name', 'f.fieldparams'), array('value', 'title', 'alias', 'params')));
					$query->from($db->quoteName('#__fields_values', 'fv'));
					$query->where($db->quoteName('fv.field_id').' = '.$field_id);
					$query->where($db->quoteName('fv.item_id').' = '.$contact_id);				
					$query->join('LEFT', $db->quoteName('#__fields', 'f').' ON '.$db->quoteName('f.id').' = '.$db->quoteName('fv.field_id'));
						
					$db->setQuery($query);
						
					$results = array();
					try {
						$results = $db->loadAssocList();
					} catch (RuntimeException $e) {						
						JFactory::getApplication()->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
					}
				
					if (count($results) > 0) {
						
						$options = array();
						
						$field_params = json_decode($results[0]['params']);
						if (isset($field_params->options) && is_object($field_params->options)) {
							foreach ($field_params->options as $key => $value) {
								$options[$value->value] = $value->name;
							}
						}
						
						$field_values = array();
						foreach ($results as $result) {
							if (!empty($options)) {
								if (isset($options[$result['value']])) {
									if (JFactory::getLanguage()->hasKey($options[$result['value']])) {
										$field_values[] = JText::_($options[$result['value']]);
									} else {
										$field_values[] = $options[$result['value']];
									}
								} else {
									//$field_values[] = ''; // could happen, for instance 3 values then get down to 2 
								}
							} else {
								$field_values[] = $result['value'];
							}
						}
						
						if (JFactory::getLanguage()->hasKey($results[0]['title'])) {
							$field_title = JText::_($results[0]['title']);
						} else {
							$field_title = $results[0]['title'];
						}
						$field_alias = $results[0]['alias'];
				
						$value = '';
						if (!empty($field_values)) {
							$value = implode(', ', $field_values);
						}
						
						if ($field_type == 'url') {
							
							$value_is_link = true;
							
							if (substr($value, 0, 6) == "mailto") {
								$title = str_replace('mailto:', '', $value);
 								if ($trombparams->get('cloak_e')) {
	 								$generated_link_tag = JHtml::_('email.cloak', $title, true, $field_title, false);
 								}
							} else if (!$trombparams->get('protocol')) {
								$title = self::remove_protocol($value);
							} else {
								$title = $value;
							}
							
						} else if ($field_type == 'calendar') {
							$value = JHTML::_('date', $value, $trombparams->get('d_format'));
						} else {
							$value = htmlentities($value);
							$title = $field_title;
						}
						
						$label = empty($fieldlabel) ? $field_title : $fieldlabel;
						$class = 'customfield-'.$field_alias; 
						$icon_class = !empty($fieldicon) ? $fieldicon : 'info';
					}
				}
		}
		
		// html code building
		
		if ($iconlinkonly) {
			if (!empty($value)) {
				$html .= '<li class="iconlink index'.$index;
				if (!empty($class)) {
					$html .= ' '.$class;
				}
				if (!empty($extraclass)) {
					$html .= ' '.$extraclass;
				}
				$html .= '">';
				if (!empty($value_substitute)) {
					$html .= '<a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$value.'" target="'.$target.'"'.self::getTitleAttribute($value_substitute, $fieldtooltip).'><i class="icon SYWicon-'.$icon_class.'"></i><span>'.$value_substitute.'</span></a>';
				} else {
					$html .= '<a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$value.'" target="'.$target.'"'.self::getTitleAttribute($label, $fieldtooltip).'><i class="icon SYWicon-'.$icon_class.'"></i><span>'.$value.'</span></a>';
				}
				$html .= '</li>';
			}
		} else {
			if (!$trombparams->get('k_s') && empty($value) && $class != 'empty') {
				return $html;
			} else {				
				$html .= '<div class="personfield index'.$index;
				if (!empty($class)) {
					$html .= ' '.$class;
				}
				if (!empty($extraclass)) {
					$html .= ' '.$extraclass;
				}
				$html .= '">';
				if (!empty($value)) {
					
					if ($prefield == 1) { // labels	
						$html .= '<span class="fieldlabel">'.$label.$trombparams->get('lbl_separator').'</span>';
					} else if ($prefield == 2) { // icons	
						if (!empty($icon_class)) {
							$html .= '<i class="icon SYWicon-'.$icon_class.'"></i>';
						} else {
							$html .= '<i class="noicon"></i>';
						}
					} else { // no icon or no label for the field
						if ($trombparams->get('s_f_lbl') == 1 && $class != 'fieldname') { // force 'no label' even if there is one
							$html .= '<span class="fieldlabel"></span>';
						} else if ($trombparams->get('s_f_lbl') == 2 && $class != 'fieldname') { // force 'no icon' even if one exists for the field
							$html .= '<i class="noicon"></i>';
						}
					}					
								
					if ($value_is_link) {
						if (!empty($generated_link_tag)) {
							$html .= $generated_link_tag;
						} else {
							if ($show_link) {
								$label_as_title = empty($title) ? $label : $title;
								if (!empty($value_substitute)) {
									$html .= '<a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$value.'" target="'.$target.'"'.self::getTitleAttribute($label_as_title, $fieldtooltip).'><span>'.$value_substitute.'</span></a>';
								} else {
									$html .= '<a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$value.'" target="'.$target.'"'.self::getTitleAttribute($label_as_title, $fieldtooltip).'><span>'.$value.'</span></a>';
								}
							} else {
								$value_as_title = empty($title) ? $value : $title;
								$html .= '<a class="fieldvalue'.self::getTooltipClass($fieldtooltip).'" href="'.$value.'" target="'.$target.'"'.self::getTitleAttribute($value_as_title, $fieldtooltip).'><span>'.$label.'</span></a>';
							}
						}
					} else {
						$value_as_title = empty($title) ? $value : $title;
						if (!empty($value_substitute)) {
							$html .= '<span class="fieldvalue'.self::getTooltipClass($fieldtooltip).'"'.self::getTitleAttribute($value_as_title, $fieldtooltip).'>'.$value_substitute.'</span>';
						} else {
							$html .= '<span class="fieldvalue'.self::getTooltipClass($fieldtooltip).'"'.self::getTitleAttribute($value_as_title, $fieldtooltip).'>'.$value.'</span>';
						}
					}
				} else {
					$html .= '<span>&nbsp;</span>';
				}
					
				$html .= '</div>';
			}
		}
		
		return $html;
	}
	
	public static function getLabelForLink($field, $link, $params, $globalparams) {
		
		$label = $params->get($field.'_name');
		
		$label = ($label) ? $label : $globalparams->get($field.'_name');
		
		if (!empty($label)) {
			return $label;
		}		
			
		if (strpos($link, 'facebook') > 0) {
			return 'Facebook';
		}
		
		if (strpos($link, 'linkedin') > 0) {
			return 'LinkedIn';
		}
		
		if (strpos($link, 'twitter') > 0) {
			return 'Twitter';
		}
		
		if (strpos($link, 'plus.google') > 0) {
			return 'Google+';
		}
		
		if (strpos($link, 'instagram') > 0) {
			return 'Instagram';
		}
		
		if (strpos($link, 'tumblr') > 0) {
			return 'Tumblr';
		}
		
		if (strpos($link, 'pinterest') > 0) {
			return 'Pinterest';
		}
		
		if (strpos($link, 'youtube') > 0) {
			return 'YouTube';
		}
		
		if (strpos($link, 'vimeo') > 0) {
			return 'Vimeo';
		}
		
		if (strpos($link, 'wordpress') > 0) {
			return 'Wordpress';
		}
		
		if (strpos($link, 'skype') > 0) {
			return 'Skype';
		}
		
		if (strpos($link, 'blogspot') > 0) {
			return 'Blogger';
		}
		
		return JText::_('MOD_TROMBINOSCOPE_LABEL_LINK');
	}
	
	public static function getIconForLink($link)
	{
		if (strpos($link, 'facebook') > 0) {
			return 'facebook';
		}
	
		if (strpos($link, 'linkedin') > 0) {
			return 'linkedin';
		}
	
		if (strpos($link, 'twitter') > 0) {
			return 'twitter';
		}
	
		if (strpos($link, 'plus.google') > 0) {
			return 'googleplus';
		}
	
		if (strpos($link, 'instagram') > 0) {
			return 'instagram';
		}
	
		if (strpos($link, 'tumblr') > 0) {
			return 'tumblr';
		}
	
		if (strpos($link, 'pinterest') > 0) {
			return 'pinterest';
		}
	
		if (strpos($link, 'youtube') > 0) {
			return 'youtube';
		}
	
		if (strpos($link, 'vimeo') > 0) {
			return 'vimeo';
		}
	
		if (strpos($link, 'wordpress') > 0) {
			return 'wordpress';
		}
	
		if (strpos($link, 'skype') > 0) {
			return 'skype';
		}
	
		if (strpos($link, 'blogspot') > 0) {
			return 'blogger';
		}
	
		return 'earth';
	}
	
	/**
	* Create the contact link
	*/
	public static function getContactRoute($component, $id, $catid, $language = 0)
	{
		$needles = array(
			'contact'  => array((int) $id)
		);

		$link = 'index.php?option=com_'.$component.'&view=contact&referer=module&id='. $id;
				
		if ($catid > 1) {
			$categories = JCategories::getInstance('Contact');
			$category = $categories->get($catid);
			if ($category) {
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}
		
		if ($language && $language != "*" && JLanguageMultilang::isEnabled()) {
			$link .= '&lang=' . $language;
			$needles['language'] = $language;
		}
	
		if ($item = self::_findItem($component, $needles)) {
			$link .= '&Itemid='.$item;
		//} elseif ($item = self::_findItem($component)) {
			//$link .= '&Itemid='.$item;
		//} else {
			//$link .= '&Itemid=0';
		}
	
		return $link;
	}
	
	/**
	* Create the category link
	*/
	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof JCategoryNode) {
			$id = $catid->id;
			$category = $catid;
		} else {
			$id = (int) $catid;
			$category = JCategories::getInstance('Contact')->get($id);
		}
	
		if ($id < 1 || !($category instanceof JCategoryNode)) {
			$link = '';
		} else {
			$needles = array();

			//if ($item = self::_findItem('contact', $needles)) {
				//$link = 'index.php?Itemid='.$item;
			//} else {
			$link = 'index.php?option=com_contact&view=category&id='.$id;
			
				//if($category) {
			$catids = array_reverse($category->getPath());
			$needles['category'] = $catids;
			$needles['categories'] = $catids;
					
			if ($language && $language != "*" && JLanguageMultilang::isEnabled()) {
				$link .= '&lang=' . $language;
				$needles['language'] = $language;
			}
					
			if ($item = self::_findItem('contact', $needles)) {
				$link .= '&Itemid='.$item;
					//} elseif ($item = self::_findItem('contact')) {
						//$link .= '&Itemid='.$item;
					//} else {
						//$link .= '&Itemid=0';
			}
				//}
			//}
		}
	
		return $link;
	}
	
	protected static function _findItem($component, $needles = null)
	{
		$app = JFactory::getApplication();
		$menus = $app->getMenu('site');
		$language = isset($needles['language']) ? $needles['language'] : '*';
	
		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language])) {
			self::$lookup[$language] = array();
	
			$thecomponent = JComponentHelper::getComponent('com_'.$component);
			$attributes = array('component_id');
			$values = array($thecomponent->id);
			
			if ($language != '*') {
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}
			
			$items = $menus->getItems($attributes, $values);
			
			if ($items != null) {			
				foreach ($items as $item) {
					if (isset($item->query) && isset($item->query['view'])) {
						$view = $item->query['view'];
						if (!isset(self::$lookup[$language][$view])) {
							self::$lookup[$language][$view] = array();
						}
						if (isset($item->query['id'])) {
							/**
							 * Here it will become a bit tricky
							 * language != * can override existing entries
							 * language == * cannot override existing entries
							 */
							if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*') {
								self::$lookup[$language][$view][$item->query['id']] = $item->id;
							}
						}
					}
				}
			}
		}
	
		if ($needles) {
			foreach ($needles as $view => $ids) {
				if (isset(self::$lookup[$language][$view])) {
					foreach($ids as $id) {
						if (isset(self::$lookup[$language][$view][(int)$id])) {
							return self::$lookup[$language][$view][(int)$id];
						}
					}
				}
			}
		}

		// Check if the active menuitem matches the requested language
		$active = $menus->getActive();
		if ($active && ($language == '*' || in_array($active->language, array('*', $language)) || !JLanguageMultilang::isEnabled())) {
			return $active->id;
		}

		// If not found, return language specific home link
		$default = $menus->getDefault($language);

		return !empty($default->id) ? $default->id : null;
	}
	
	/**
	* Create the cropped image
	* 
	* @param string $module_id
	* @param string $item_id
	* @param string $imagesrc
	* @param string $tmp_path
	* @param boolean $clear_cache
	* @param integer $head_width
	* @param integer $head_height
	* @param boolean $crop_picture
	* @param array $image_quality_array
	* @param string $filter
	* @param boolean $create_high_resolution
	* 
	* @return the thumbnail path if no error, 'error' if error, the original path otherwise if conditions are not met to create the thumbnail
	*/
	public static function getCroppedImage($module_id, $item_id, $imagesrc, $tmp_path, $clear_cache, $head_width, $head_height, $crop_picture, $quality, $filter, $create_highres_images = false)
	{	
		$extensions = get_loaded_extensions();
		if (!in_array('gd', $extensions)) {
			return $imagesrc;
		}
		
		$imageext = explode('.', $imagesrc);
		$imageext = $imageext[count($imageext) - 1];
		$imageext = strtolower($imageext);
			
		$filename = $tmp_path.'/thumb_'.$module_id.'_'.$item_id.'.'.$imageext;
		$filename_highres = $tmp_path.'/thumb_'.$module_id.'_'.$item_id.'@2x.'.$imageext;
		if ((is_file(JPATH_ROOT.'/'.$filename) && !$clear_cache && !$create_highres_images)
			|| (is_file(JPATH_ROOT.'/'.$filename) && !$clear_cache && $create_highres_images && is_file(JPATH_ROOT.'/'.$filename_highres))) {
				
			// thumbnail already exists
			
		} else { // create the thumbnail
			
			$image = new SYWImage($imagesrc);
			
			if (is_null($image->getImagePath())) {
				return 'error';
			} else if (is_null($image->getImageMimeType())) {
				return 'error';
			} else if (is_null($image->getImage()) || $image->getImageWidth() == 0) {
				return 'error';
			} else {
				
				// START find image compression plugin
				
				$compression_plugin = null;
				$compression_plugins_exist = JPluginHelper::importPlugin('imagecompression');
				
				if ($compression_plugins_exist) {
					
					$dispatcher = JEventDispatcher::getInstance();						
					
					$plugins_available_for_compression = $dispatcher->trigger('onImageCompressionCheckAvailability', array($imageext, true));
						
					foreach ($plugins_available_for_compression as $plugin_available_for_compression) {
						
						$plugin_name = array_keys($plugin_available_for_compression)[0];
						$available = $plugin_available_for_compression[$plugin_name];
						
						if ($available) {
							$filename_temp = $tmp_path.'/thumb_temp_'.$module_id.'_'.$item_id.'.'.$imageext;
							$filename_highres_temp = $tmp_path.'/thumb_temp_'.$module_id.'_'.$item_id.'@2x.'.$imageext;
							
							$plugin = JPluginHelper::getPlugin('imagecompression', $plugin_name);
							$classname = 'plgImageCompression'.$plugin_name;
							$compression_plugin = new $classname($dispatcher, (array) $plugin);
							
							break;
							// uses the first plugin that returns true - useful if a plugin is limited (reached a limit of use)
							// therefore the order of the plugins is important
						}
					}
				}
				
				// END find image compression plugin
								
				switch ($imageext){
					case 'jpg': case 'jpeg': break; // compression: 0 to 100
					case 'png': // compression: 0 to 9
						$pngQuality = ($quality - 100) / 11.111111;
						$quality = round(abs($pngQuality));
						break; 
					default : $quality = -1; break;
				}
				
				switch ($filter) {
					case 'sepia': $filter = array(IMG_FILTER_GRAYSCALE, array('type' => IMG_FILTER_COLORIZE, 'arg1' => 90, 'arg2' => 60, 'arg3' => 30)); break;
					case 'grayscale': $filter = IMG_FILTER_GRAYSCALE; break;
					case 'sketch': $filter = IMG_FILTER_MEAN_REMOVAL; break;
					case 'negate': $filter = IMG_FILTER_NEGATE; break;
					case 'emboss': $filter = IMG_FILTER_EMBOSS; break;
					case 'edgedetect': $filter = IMG_FILTER_EDGEDETECT; break;
					default: $filter = null; break;
				}
				
				if (!is_null($compression_plugin)) {
					$creation_success = $image->createThumbnail($head_width, $head_height, $crop_picture, $quality, $filter, $filename_temp, $create_highres_images);
				} else {
					$creation_success = $image->createThumbnail($head_width, $head_height, $crop_picture, $quality, $filter, $filename, $create_highres_images);
				}
				
				if (!$creation_success) {
					return 'error';
				}
								
				// START image compression
				
				if (!is_null($compression_plugin)) {
										
					//$optimization_success = $dispatcher->trigger('onImageCompressionCompress', array($filename_temp, $filename));
					$optimization_success = $compression_plugin->onImageCompressionCompress($filename_temp, $filename);
					if ($optimization_success) {
						//$dispatcher->trigger('onImageCompressionSuccess', array(false));
						$compression_plugin->onImageCompressionSuccess(false);
					} else {
						//$dispatcher->trigger('onImageCompressionFailure');
						$compression_plugin->onImageCompressionFailure();
					}
				
					if ($create_highres_images) {
						//$optimization_highres_success = $dispatcher->trigger('onImageCompressionCompress', array($filename_highres_temp, $filename_highres));
						$optimization_highres_success = $compression_plugin->onImageCompressionCompress($filename_highres_temp, $filename_highres);
						if ($optimization_highres_success) {
							//$dispatcher->trigger('onImageCompressionSuccess', array(false));
							$compression_plugin->onImageCompressionSuccess(false);
						} else {
							//$dispatcher->trigger('onImageCompressionFailure');
							$compression_plugin->onImageCompressionFailure();
						}
					}
				}
				
				// END image compression
			} 
		
			$image->destroy();
		}
		
		return $filename;
	}
	
	protected static function remove_protocol($url) 
	{
		$disallowed = array('http://', 'https://');
		foreach($disallowed as $d) {
			if(strpos($url, $d) === 0) {
				return str_replace($d, '', $url);
			}
		}
		return $url;
	}
	
	static function getAutoMapLink($address, $params = '', $embed = false) {
	
		$address_array = explode("\n", $address);
		$address = '';
		foreach ($address_array as $address_line) {
			$address_line = str_replace(',', ' ', $address_line);
			$address_line = str_replace('#', ' ', $address_line);
			$address .= trim($address_line, ", \t\n\r\0\x0B").' ';
		}
	
		$address = trim($address, ", \t\n\r\0\x0B");
		
		$address = preg_replace('/\s+/', ' ', $address); // to replace multiple occurences of white space into one
	
		$url = 'https://maps.google.com/maps?q='.urlencode($address);
		
		if (!empty($params)) {
			$url .= '&'.$params;
		}
		
		if ($embed) {
			$url .= '&output=embed';
		}
	
		return $url;	
	}
	
	protected static function compare($a, $b) {
		if (class_exists('Collator') && self::$sort_locale !== 'en_US' && self::$sort_locale !== 'en_GB') { // needs php_intl
			$collator = new Collator(self::$sort_locale);
			return $collator->compare($a, $b);
		} else {
			return ($a < $b) ? -1 : 1;
		}
	}
	
	/* sort by category ASC lastpart ASC firstpart ASC */
	protected static function sortCascLascFasc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->lastpart == $b->lastpart) {
				if ($a->firstpart == $b->firstpart) {
					return 0;
				}
				//return ($a->firstpart < $b->firstpart) ? -1 : 1;
				return self::compare($a->firstpart, $b->firstpart);
			}
			//return ($a->lastpart < $b->lastpart) ? -1 : 1;
			return self::compare($a->lastpart, $b->lastpart);
		}
		return ($a->c_order < $b->c_order) ? -1 : 1;
	}
	
	/* sort by category ASC by lastpart DESC firstpart DESC */
	protected static function sortCascLdescFdesc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->lastpart == $b->lastpart) {
				if ($a->firstpart == $b->firstpart) {
					return 0;
				}
				//return ($a->firstpart > $b->firstpart) ? -1 : 1;
				return self::compare($b->firstpart, $a->firstpart);
			}
			//return ($a->lastpart > $b->lastpart) ? -1 : 1;
			return self::compare($b->lastpart, $a->lastpart);
		}
		return ($a->c_order < $b->c_order) ? -1 : 1;
	}
	
	/* sort by category ASC by firstpart ASC lastpart ASC */
	protected static function sortCascFascLasc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->firstpart == $b->firstpart) {
				if ($a->lastpart == $b->lastpart) {
					return 0;
				}
				//return ($a->lastpart < $b->lastpart) ? -1 : 1;
				return self::compare($a->lastpart, $b->lastpart);
			}
			//return ($a->firstpart < $b->firstpart) ? -1 : 1;
			return self::compare($a->firstpart, $b->firstpart);
		}
		return ($a->c_order < $b->c_order) ? -1 : 1;
	}
	
	/* sort by category ASC by firstpart DESC lastpart DESC */
	protected static function sortCascFdescLdesc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->firstpart == $b->firstpart) {
				if ($a->lastpart == $b->lastpart) {
					return 0;
				}
				//return ($a->lastpart > $b->lastpart) ? -1 : 1;
				return self::compare($b->lastpart, $a->lastpart);
			}
			//return ($a->firstpart > $b->firstpart) ? -1 : 1;
			return self::compare($b->firstpart, $a->firstpart);
		}
		return ($a->c_order < $b->c_order) ? -1 : 1;
	}
	
	/* sort by category ASC by secondpart ASC firstpart ASC */
	protected static function sortCascSascFasc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->secondpart == $b->secondpart) {
				if ($a->firstpart == $b->firstpart) {
					return 0;
				}
				//return ($a->firstpart < $b->firstpart) ? -1 : 1;
				return self::compare($a->firstpart, $b->firstpart);
			}
			//return ($a->secondpart < $b->secondpart) ? -1 : 1;
			return self::compare($a->secondpart, $b->secondpart);
		}
		return ($a->c_order < $b->c_order) ? -1 : 1;
	}
	
	/* sort by category ASC by secondpart DESC firstpart DESC */
	protected static function sortCascSdescFdesc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->secondpart == $b->secondpart) {
				if ($a->firstpart == $b->firstpart) {
					return 0;
				}
				//return ($a->firstpart > $b->firstpart) ? -1 : 1;
				return self::compare($b->firstpart, $a->firstpart);
			}
			//return ($a->secondpart > $b->secondpart) ? -1 : 1;
			return self::compare($b->secondpart, $a->secondpart);
		}
		return ($a->c_order < $b->c_order) ? -1 : 1;
	}
	
	/* sort by category ASC by firstpart ASC secondpart ASC */
	protected static function sortCascFascSasc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->firstpart == $b->firstpart) {
				if ($a->secondpart == $b->secondpart) {
					return 0;
				}
				//return ($a->secondpart < $b->secondpart) ? -1 : 1;
				return self::compare($a->secondpart, $b->secondpart);
			}
			//return ($a->firstpart < $b->firstpart) ? -1 : 1;
			return self::compare($a->firstpart, $b->firstpart);
		}
		return ($a->c_order < $b->c_order) ? -1 : 1;
	}
	
	/* sort by category ASC by firstpart DESC secondpart DESC */
	protected static function sortCascFdescSdesc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->firstpart == $b->firstpart) {
				if ($a->secondpart == $b->secondpart) {
					return 0;
				}
				//return ($a->secondpart > $b->secondpart) ? -1 : 1;
				return self::compare($b->secondpart, $a->secondpart);
			}
			//return ($a->firstpart > $b->firstpart) ? -1 : 1;
			return self::compare($b->firstpart, $a->firstpart);
		}
		return ($a->c_order < $b->c_order) ? -1 : 1;
	}
	
	/* sort by category DESC lastpart ASC firstpart ASC */
	protected static function sortCdescLascFasc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->lastpart == $b->lastpart) {
				if ($a->firstpart == $b->firstpart) {
					return 0;
				}
				//return ($a->firstpart < $b->firstpart) ? -1 : 1;
				return self::compare($a->firstpart, $b->firstpart);
			}
			//return ($a->lastpart < $b->lastpart) ? -1 : 1;
			return self::compare($a->lastpart, $b->lastpart);
		}
		return ($a->c_order > $b->c_order) ? -1 : 1;
	}
	
	/* sort by category DESC by lastpart DESC firstpart DESC */
	protected static function sortCdescLdescFdesc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->lastpart == $b->lastpart) {
				if ($a->firstpart == $b->firstpart) {
					return 0;
				}
				//return ($a->firstpart > $b->firstpart) ? -1 : 1;
				return self::compare($b->firstpart, $a->firstpart);
			}
			//return ($a->lastpart > $b->lastpart) ? -1 : 1;
			return self::compare($b->lastpart, $a->lastpart);
		}
		return ($a->c_order > $b->c_order) ? -1 : 1;
	}
	
	/* sort by category DESC by firstpart ASC lastpart ASC */
	protected static function sortCdescFascLasc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->firstpart == $b->firstpart) {
				if ($a->lastpart == $b->lastpart) {
					return 0;
				}
				//return ($a->lastpart < $b->lastpart) ? -1 : 1;
				return self::compare($a->lastpart, $b->lastpart);
			}
			//return ($a->firstpart < $b->firstpart) ? -1 : 1;
			return self::compare($a->firstpart, $b->firstpart);
		}
		return ($a->c_order > $b->c_order) ? -1 : 1;
	}
	
	/* sort by category DESC by firstpart DESC lastpart DESC */
	protected static function sortCdescFdescLdesc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->firstpart == $b->firstpart) {
				if ($a->lastpart == $b->lastpart) {
					return 0;
				}
				//return ($a->lastpart > $b->lastpart) ? -1 : 1;
				return self::compare($b->lastpart, $a->lastpart);
			}
			//return ($a->firstpart > $b->firstpart) ? -1 : 1;
			return self::compare($b->firstpart, $a->firstpart);
		}
		return ($a->c_order > $b->c_order) ? -1 : 1;
	}
	
	/* sort by category DESC by secondpart ASC firstpart ASC */
	protected static function sortCdescSascFasc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->secondpart == $b->secondpart) {
				if ($a->firstpart == $b->firstpart) {
					return 0;
				}
				//return ($a->firstpart < $b->firstpart) ? -1 : 1;
				return self::compare($a->firstpart, $b->firstpart);
			}
			//return ($a->secondpart < $b->secondpart) ? -1 : 1;
			return self::compare($a->secondpart, $b->secondpart);
		}
		return ($a->c_order > $b->c_order) ? -1 : 1;
	}
	
	/* sort by category DESC by secondpart DESC firstpart DESC */
	protected static function sortCdescSdescFdesc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->secondpart == $b->secondpart) {
				if ($a->firstpart == $b->firstpart) {
					return 0;
				}
				//return ($a->firstpart > $b->firstpart) ? -1 : 1;
				return self::compare($b->firstpart, $a->firstpart);
			}
			//return ($a->secondpart > $b->secondpart) ? -1 : 1;
			return self::compare($b->secondpart, $a->secondpart);
		}
		return ($a->c_order > $b->c_order) ? -1 : 1;
	}
	
	/* sort by category DESC by firstpart ASC secondpart ASC */
	protected static function sortCdescFascSasc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->firstpart == $b->firstpart) {
				if ($a->secondpart == $b->secondpart) {
					return 0;
				}
				//return ($a->secondpart < $b->secondpart) ? -1 : 1;
				return self::compare($a->secondpart, $b->secondpart);
			}
			//return ($a->firstpart < $b->firstpart) ? -1 : 1;
			return self::compare($a->firstpart, $b->firstpart);
		}
		return ($a->c_order > $b->c_order) ? -1 : 1;
	}
	
	/* sort by category DESC by firstpart DESC secondpart DESC */
	protected static function sortCdescFdescSdesc($a, $b) {
	
		if ($a->c_order == $b->c_order) {
			if ($a->firstpart == $b->firstpart) {
				if ($a->secondpart == $b->secondpart) {
					return 0;
				}
				//return ($a->secondpart > $b->secondpart) ? -1 : 1;
				return self::compare($b->secondpart, $a->secondpart);
			}
			//return ($a->firstpart > $b->firstpart) ? -1 : 1;
			return self::compare($b->firstpart, $a->firstpart);
		}
		return ($a->c_order > $b->c_order) ? -1 : 1;
	}
	
	/* sort by lastpart ASC firstpart ASC */
	protected static function sortLascFasc($a, $b) {
	
		if ($a->lastpart == $b->lastpart) {
			if ($a->firstpart == $b->firstpart) {
				return 0;
			}
			//return ($a->firstpart < $b->firstpart) ? -1 : 1;
			return self::compare($a->firstpart, $b->firstpart);
		}
		//return ($a->lastpart < $b->lastpart) ? -1 : 1;
		return self::compare($a->lastpart, $b->lastpart);
	}
	
	/* sort by lastpart DESC firstpart DESC */
	protected static function sortLdescFdesc($a, $b) {
	
		if ($a->lastpart == $b->lastpart) {
			if ($a->firstpart == $b->firstpart) {
				return 0;
			}
			//return ($a->firstpart > $b->firstpart) ? -1 : 1;
			return self::compare($b->firstpart, $a->firstpart);
		}
		//return ($a->lastpart > $b->lastpart) ? -1 : 1;
		return self::compare($b->lastpart, $a->lastpart);
	}
	
	/* sort by firstpart ASC lastpart ASC */
	protected static function sortFascLasc($a, $b) {
	
		if ($a->firstpart == $b->firstpart) {
			if ($a->lastpart == $b->lastpart) {
				return 0;
			}
			//return ($a->lastpart < $b->lastpart) ? -1 : 1;
			return self::compare($a->lastpart, $b->lastpart);
		}
		//return ($a->firstpart < $b->firstpart) ? -1 : 1;
		return self::compare($a->firstpart, $b->firstpart);
	}
	
	/* sort by firstpart DESC lastpart DESC */
	protected static function sortFdescLdesc($a, $b) {
	
		if ($a->firstpart == $b->firstpart) {
			if ($a->lastpart == $b->lastpart) {
				return 0;
			}
			//return ($a->lastpart > $b->lastpart) ? -1 : 1;
			return self::compare($b->lastpart, $a->lastpart);
		}
		//return ($a->firstpart > $b->firstpart) ? -1 : 1;
		return self::compare($b->firstpart, $a->firstpart);
	}
	
	/* sort by secondpart ASC firstpart ASC */
	protected static function sortSascFasc($a, $b) {
	
		if ($a->secondpart == $b->secondpart) {
			if ($a->firstpart == $b->firstpart) {
				return 0;
			}
			//return ($a->firstpart < $b->firstpart) ? -1 : 1;
			return self::compare($a->firstpart, $b->firstpart);
		}
		//return ($a->secondpart < $b->secondpart) ? -1 : 1;
		return self::compare($a->secondpart, $b->secondpart);
	}
	
	/* sort by secondpart DESC firstpart DESC */
	protected static function sortSdescFdesc($a, $b) {
	
		if ($a->secondpart == $b->secondpart) {
			if ($a->firstpart == $b->firstpart) {
				return 0;
			}
			//return ($a->firstpart > $b->firstpart) ? -1 : 1;
			return self::compare($b->firstpart, $a->firstpart);
		}
		//return ($a->secondpart > $b->secondpart) ? -1 : 1;
		return self::compare($b->secondpart, $a->secondpart);
	}
	
	/* sort by firstpart ASC secondpart ASC */
	protected static function sortFascSasc($a, $b) {
	
		if ($a->firstpart == $b->firstpart) {
			if ($a->secondpart == $b->secondpart) {
				return 0;
			}
			//return ($a->secondpart < $b->secondpart) ? -1 : 1;
			return self::compare($a->secondpart, $b->secondpart);
		}
		//return ($a->firstpart < $b->firstpart) ? -1 : 1;
		return self::compare($a->firstpart, $b->firstpart);
	}
	
	/* sort by firstpart DESC secondpart DESC */
	protected static function sortFdescSdesc($a, $b) {
	
		if ($a->firstpart == $b->firstpart) {
			if ($a->secondpart == $b->secondpart) {
				return 0;
			}
			//return ($a->secondpart > $b->secondpart) ? -1 : 1;
			return self::compare($b->secondpart, $a->secondpart);
		}
		//return ($a->firstpart > $b->firstpart) ? -1 : 1;
		return self::compare($b->firstpart, $a->firstpart);
	}
	
	protected static function _substring_index($subject, $delim, $count)
	{
	    if ($count < 0) {
	        return implode($delim, array_slice(explode($delim, $subject), $count));
	    } else {
	        return implode($delim, array_slice(explode($delim, $subject), 0, $count));
	    }
	}
	
	/**
	 * Load common stylesheet to all module instances
	 */
	static function loadCommonStylesheet($debug = false) {
		
		if (self::$commonStylesLoaded) {
			return;
		}
		
		$doc = JFactory::getDocument();
		if ($debug) {
			$doc->addStyleSheet(JURI::base(true).'/modules/mod_trombinoscope/themes/common_styles.css');
		} else {
			$doc->addStyleSheet(JURI::base(true).'/modules/mod_trombinoscope/themes/common_styles-min.css');
		}
		
		self::$commonStylesLoaded = true;
	}
	
	/**
	 * Load user stylesheet to all module instances
	 * if the file has 'substitute' in the name, it will replace all module styles
	 */
	static function loadUserStylesheet($styles_substitute = false) {
	
		if (self::$userStylesLoaded) {
			return;
		}
	
		jimport('joomla.filesystem.file');
		$doc = JFactory::getDocument();
	
		$prefix = 'common_user';
		if ($styles_substitute) {
			$prefix = 'substitute';
		}
	
		if (!JFile::exists(JPATH_ROOT.'/modules/mod_trombinoscope/themes/'.$prefix.'_styles-min.css')) {
			$doc->addStyleSheet(JURI::base(true).'/modules/mod_trombinoscope/themes/'.$prefix.'_styles.css');
		} else {
			$doc->addStyleSheet(JURI::base(true).'/modules/mod_trombinoscope/themes/'.$prefix.'_styles-min.css');
		}
	
		self::$userStylesLoaded = true;
	}

}

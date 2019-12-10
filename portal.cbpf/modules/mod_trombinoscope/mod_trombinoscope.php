<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');
require_once (dirname(__FILE__).'/headerfilesmaster.php');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

$list = modTrombinoscopeHelper::getContacts($params, $module);

if (empty($list)) {
	return;
}

jimport('syw.fonts');
jimport('syw.cache');
jimport('syw.stylesheets');
jimport('syw.libraries');
jimport('syw.utilities');

$standalone = modTrombinoscopeHelper::isStandalone();

$isMobile = SYWUtilities::isMobile();

$class_suffix = $module->id;
$params->set('suffix', $class_suffix);

$urlPath = JURI::base().'modules/mod_trombinoscope/';
$doc = JFactory::getDocument();
$app = JFactory::getApplication();

$user = JFactory::getUser();
$groups	= $user->getAuthorisedViewLevels();

$globalparams = modTrombinoscopeHelper::getContactGlobalParams();

$config_params = new JRegistry();
if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_trombinoscopeextended/config.xml')) {
	$config_params = JComponentHelper::getParams('com_trombinoscopeextended');
}

$show_errors = ($params->get('show_errors', '') == '') ? $config_params->get('show_errors', 0) : $params->get('show_errors', '');
$remove_whitespaces = ($params->get('remove_whitespaces', '') == '') ? $config_params->get('remove_whitespaces', 0) : $params->get('remove_whitespaces', '');

$module_link = $params->get('modulel', '');
$module_link_label = '';
$module_link_isExternal = false;
$module_link_class = trim($params->get('modulel_class', ''));

if ($module_link) {	
	if ($module_link == 'extern') {
		$external_url = trim($params->get('modulel_external_url', ''));
		if ($external_url) {
			$module_link = $external_url;
			$module_link_isExternal = true;
			$module_link_label = trim($params->get('modulel_lbl', '')) == '' ? $external_url : trim($params->get('modulel_lbl'));
		} else {
			$module_link = '';
		}
	} else if (is_numeric($module_link)) {
		$menu = $app->getMenu();
		$menuitem = $menu->getItem($module_link);
			
		switch ($menuitem->type)
		{
			case 'separator':
			case 'heading':
				$module_link = '';
				break;
			case 'url':
				if ((strpos($menuitem->link, 'index.php?') === 0) && (strpos($menuitem->link, 'Itemid=') === false)) {
					// if this is an internal Joomla link, ensure the Itemid is set
					$module_link = $menuitem->link.'&Itemid='.$menuitem->id;
				} else {
					$module_link = $menuitem->link;
				}
				break;
			case 'alias': // if this is an alias use the item id stored in the parameters to make the link
				$module_link = 'index.php?Itemid='.$menuitem->params->get('aliasoptions');
				break;
			default:
				$module_link = $menuitem->link.'&Itemid='.$menuitem->id;
				break;
		}
		
		$module_link_label = trim($params->get('modulel_lbl', '')) == '' ? $menuitem->title : trim($params->get('modulel_lbl'));
	} else { // backward compatibility, we get old value until the instance is saved again
		$module_link_label = trim($params->get('modulel_lbl', ''));
	}
}

$photo_align = $params->get('pic_a');

$default_picture = $params->get('d_pic', '');
$keep_space = $params->get('k_s', true);
$contact_link = $params->get('l_to_c', 'none');
$link_access = $params->get('link_access', '1');
$generic_link = $params->get('generic_l', '');

$popup_x = $params->get('popup_x', '600');
$popup_y = $params->get('popup_y', '500');

$link_label = $params->get('l_lbl', '');
$link_to_edit = $params->get('l_to_edit', false);
$edit_link_label = $params->get('editl_lbl', '');

$format_style = $params->get('name_fmt', 'none');
$uppercase = $params->get('name_upper', 0);
$pre_name = $params->get('s_name_lbl', 0);
$name_label = $params->get('name_lbl', '');
$name_icon = $params->get('name_icon', '');
$name_tooltip = $params->get('name_tooltip', true);

$show_allfields_label = $params->get('s_f_lbl', 0);

$label_separator = $params->get('lbl_separator', '');

$style_social_icons = $params->get('social_styling', false);

$card_width = $params->get('card_w', 100);
$min_card_width = trim($params->get('min_card_w', ''));
$card_width_unit = $params->get('card_w_u', '%');
$card_height = $params->get('card_h', '');

$border_width = $params->get('border_w', 0);
$border_radius = $params->get('border_r', 0);

$picture_width = $params->get('pic_w', 100);
$picture_height = $params->get('pic_h', 120);
$picture_width = $picture_width - $border_width * 2;
$picture_height = $picture_height - $border_width * 2;

$picture_hover_type = $params->get('pic_hover_type', 'none');
if ($picture_hover_type != 'none') {
	$picture_hover_type = 'hvr-'.$picture_hover_type;
}

$show_picture = $params->get('s_pic', true);
$keep_picture_space = $params->get('k_pic_s', true);
$overflow = $params->get('overflow', false);

$show_vcard = $params->get('s_v', false);
$vcard_type = $params->get('vcard_type', 'p');

$show_featured = $params->get('s_f', false);
$featured_icon = $params->get('f_icon', 'star');

$crop_picture = $params->get('crop_pic', 0);

$quality = $params->get('quality', 100);
if ($quality > 100) {
	$quality = 100;
}
if ($quality < 0) {
	$quality = 0;
}

$clear_cache = ($params->get('clear_cache', '') == '') ? $config_params->get('clear_cache', 1) : $params->get('clear_cache', '');

$subdirectory = 'thumbnails/tc';

$thumb_path = ($params->get('thumb_path', '') == '') ? $config_params->get('thumb_path_mod', 'images') : $params->get('thumb_path', '');

if ($thumb_path == 'cache') {
	$subdirectory = 'mod_trombinoscopecontacts';
}
$tmp_path = SYWCache::getTmpPath($thumb_path, $subdirectory);

$filter = $params->get('filter', 'none');

$category_showing = $params->get('s_cat', 'sl');
$cat_view_id = $params->get('cat_views', 'auto');
$show_category = false;
$link_to_category = false;
$link_to_view_category = false;
switch ($category_showing) {
	case 's' :
		$show_category = true;
		break;
	case 'sl' :
		$show_category = true;
		$link_to_category = true;
		break;
	case 'sv' :
		$show_category = true;
		$link_to_view_category = true;
		break;
	default :
		break;
}

$tags_showing = $params->get('s_tag', 'h');
$tags_view_id = $params->get('tag_views', '');
$show_tags = false;
$link_to_tags = false;
$link_to_view_tags = false;
switch ($tags_showing) {
	case 's' :
		$show_tags = true;
		break;
	case 'sl' :
		$show_tags = true;
		$link_to_tags = true;
		break;
	case 'sv' :
		$show_tags = true;
		$link_to_view_tags = true;
		break;
	default :
		break;
}

$header_showing = $params->get('s_h', 'h');
$header_html_tag = $params->get('h_tag', '4');
$header_view_id = $params->get('header_views', 'auto');
$show_category_header = false;
$link_to_category_header = false;
$link_to_view_category_header = false;
switch ($header_showing) {
	case 'sc' :
		$show_category_header = true;
		break;
	case 'slc' :
		$show_category_header = true;
		$link_to_category_header = true;
		break;
	case 'svc' :
		$show_category_header = true;
		$link_to_view_category_header = true;
		break;
	default :
		break;
}

$cat_order = $params->get('c_order', '');

// deprecated - REMOVE in v3.0 - the removal will break overrides
$field1 = $params->get('f1', 'none');
$field2 = $params->get('f2', 'none');
$field3 = $params->get('f3', 'none');
$field4 = $params->get('f4', 'none');
$field5 = $params->get('f5', 'none');
$field6 = $params->get('f6', 'none');
$field7 = $params->get('f7', 'none');

// deprecated - REMOVE in v3.0 - the removal will break overrides
$show_field1_label = $params->get('s_f1_lbl', 0);
$show_field2_label = $params->get('s_f2_lbl', 0);
$show_field3_label = $params->get('s_f3_lbl', 0);
$show_field4_label = $params->get('s_f4_lbl', 0);
$show_field5_label = $params->get('s_f5_lbl', 0);
$show_field6_label = $params->get('s_f6_lbl', 0);
$show_field7_label = $params->get('s_f7_lbl', 0);

// deprecated - REMOVE in v3.0 - the removal will break overrides
$field1_label = $params->get('f1_lbl', '');
$field2_label = $params->get('f2_lbl', '');
$field3_label = $params->get('f3_lbl', '');
$field4_label = $params->get('f4_lbl', '');
$field5_label = $params->get('f5_lbl', '');
$field6_label = $params->get('f6_lbl', '');
$field7_label = $params->get('f7_lbl', '');

// deprecated - REMOVE in v3.0 - the removal will break overrides
$field1_icon = $params->get('f1_icon', '');
$field2_icon = $params->get('f2_icon', '');
$field3_icon = $params->get('f3_icon', '');
$field4_icon = $params->get('f4_icon', '');
$field5_icon = $params->get('f5_icon', '');
$field6_icon = $params->get('f6_icon', '');
$field7_icon = $params->get('f7_icon', '');

// deprecated - REMOVE in v3.0 - the removal will break overrides
$field1_tooltip = $params->get('f1_tooltip', true);
$field2_tooltip = $params->get('f2_tooltip', true);
$field3_tooltip = $params->get('f3_tooltip', true);
$field4_tooltip = $params->get('f4_tooltip', true);
$field5_tooltip = $params->get('f5_tooltip', true);
$field6_tooltip = $params->get('f6_tooltip', true);
$field7_tooltip = $params->get('f7_tooltip', true);

// deprecated - REMOVE in v3.0 - the removal will break overrides
$field1_access = $params->get('f1_access', '1');
$field2_access = $params->get('f2_access', '1');
$field3_access = $params->get('f3_access', '1');
$field4_access = $params->get('f4_access', '1');
$field5_access = $params->get('f5_access', '1');
$field6_access = $params->get('f6_access', '1');
$field7_access = $params->get('f7_access', '1');

// deprecated - REMOVE in v3.0 - the removal will break overrides
$linkfield1 = $params->get('lf1', 'none');
$linkfield2 = $params->get('lf2', 'none');
$linkfield3 = $params->get('lf3', 'none');
$linkfield4 = $params->get('lf4', 'none');
$linkfield5 = $params->get('lf5', 'none');

// deprecated - REMOVE in v3.0 - the removal will break overrides
$linkfield1_icon = $params->get('lf1_icon', '');
$linkfield2_icon = $params->get('lf2_icon', '');
$linkfield3_icon = $params->get('lf3_icon', '');
$linkfield4_icon = $params->get('lf4_icon', '');
$linkfield5_icon = $params->get('lf5_icon', '');

// deprecated - REMOVE in v3.0 - the removal will break overrides
$linkfield1_access = $params->get('lf1_access', '1');
$linkfield2_access = $params->get('lf2_access', '1');
$linkfield3_access = $params->get('lf3_access', '1');
$linkfield4_access = $params->get('lf4_access', '1');
$linkfield5_access = $params->get('lf5_access', '1');

$show_links = modTrombinoscopeHelper::showLinks($params);

$extraclasseslinkfields = '';
$extraclassesfields = '';

$letter_count = $params->get('l_c', '');
$strip_tags = $params->get('s_t', 1);
$keep_tags = $params->get('keep_tags');

$link_email = $params->get('link_e', 1);
$cloak_email = $params->get('cloak_e', 1);
$email_substitut = $params->get('e_substitut', '');
$webpage_substitut = $params->get('w_substitut', '');
$protocol = $params->get('protocol', true);

$address_format = $params->get('a_fmt', 'ssz');
$address_link_with_map = $params->get('a_link_map', false);
$auto_map_params = trim($params->get('auto_map_params', ''));
$date_format = $params->get('d_format', 'd F Y');
$birthdate_format = $params->get('dob_format', 'F d');

// deprecated - REMOVE in v3.0 - the removal will break overrides
$trombparams = new JRegistry();
$trombparams->set('protocol', $protocol);
$trombparams->set('keep_space', $keep_space);
$trombparams->set('link_email', $link_email);
$trombparams->set('cloak_email', $cloak_email);
$trombparams->set('email_substitut', $email_substitut);
$trombparams->set('webpage_substitut', $webpage_substitut);
$trombparams->set('letter_count', $letter_count);
$trombparams->set('strip_tags', $strip_tags);
$trombparams->set('keep_tags', $keep_tags);
$trombparams->set('all_pre', $show_allfields_label);
$trombparams->set('label_separator', $label_separator);
$trombparams->set('date_format', $date_format);
$trombparams->set('birthdate_format', $birthdate_format);
$trombparams->set('link_address_with_map', $address_link_with_map);
$trombparams->set('auto_map_params', $auto_map_params);

$clear_header_files_cache = ($params->get('clear_css_cache', '') == '') ? $config_params->get('clear_css_cache', 1) : $params->get('clear_css_cache', '');
$generate_inline_scripts = ($params->get('inline_scripts', '') == '') ? $config_params->get('inline_scripts', 0) : $params->get('inline_scripts', '');

// carousel

$arrow_class = '';
$show_arrows = false;

$arrow_prev_left = false;
$arrow_next_right = false;
$arrow_prev_top = false;
$arrow_next_bottom = false;
$arrow_prevnext_bottom = false;

$carousel_configuration = $params->get('carousel_config', 'none');
if ($card_width_unit == 'px' && $carousel_configuration != 'none') {

	jimport('syw.libraries');

	JHtml::_('jquery.framework');

	SYWLibraries::loadCarousel();

	switch ($params->get('arrows', 'none')) {
		case 'around':
			$show_arrows = true;
			if ($carousel_configuration == 'h') {
				$arrow_class = ' side_navigation';
				$arrow_prev_left = true;
				$arrow_next_right = true;
			} else {
				$arrow_class = ' above_navigation';
				$arrow_prev_top = true;
				$arrow_next_bottom = true;
			}
			break;
		case 'under':
			$arrow_class = ' under_navigation';
			$show_arrows = true;
			$arrow_prevnext_bottom = true;
			break;
		case 'title':
			$show_arrows = true;
			break;
	}
	
	$extra_pagination_classes = $params->get('arrowstyle', '');
	$bootstrap_size = $params->get('arrowsize_bootstrap', '');
	if (!empty($extra_pagination_classes)) {
		if ($bootstrap_size == 'small') {
			$extra_pagination_classes .= ' pagination-small pagination-sm'; // for Bootstrap 2.3 and 3.3
		} else if ($bootstrap_size == 'mini') {
			$extra_pagination_classes .= ' pagination-mini pagination-sm'; // for Bootstrap 2.3 and 3.3 (no mini)
		}
	}

	$cache_anim_js = new TC_JSAnimationFileCache('mod_trombinoscopecontacts', $params);
		
	if ($generate_inline_scripts) {
		
		$doc->addScriptDeclaration($cache_anim_js->getBuffer());
		
	} else {
		
		$result = $cache_anim_js->cache('animation_'.$module->id.'.js', $clear_header_files_cache);
		
		if ($result) {
			$doc->addScript(JURI::base(true).'/cache/mod_trombinoscopecontacts/animation_'.$module->id.'.js');
		}
	}
	
} else {
	// remove animation.js if it exists
	if (JFile::exists(JPATH_CACHE.'/mod_trombinoscopecontacts/animation_'.$module->id.'.js')) {
		JFile::delete(JPATH_CACHE.'/mod_trombinoscopecontacts/animation_'.$module->id.'.js');
	}
}

// adding responsiveness...

if ($card_width_unit == '%' && !empty($min_card_width)) { // and there is a min/max width
	
	JHtml::_('jquery.framework');
	
	$cache_js = new TC_JSFileCache('mod_trombinoscopecontacts', $params);
	
	if ($generate_inline_scripts) {
		
		$doc->addScriptDeclaration($cache_js->getBuffer());
		
	} else {
		
		$result = $cache_js->cache('style_'.$module->id.'.js', $clear_header_files_cache);
		
		if ($result) {
			$doc->addScript(JURI::base(true).'/cache/mod_trombinoscopecontacts/style_'.$module->id.'.js');
		}
	}
	
} else {
	// remove style.js if it exists
	if (JFile::exists(JPATH_CACHE.'/mod_trombinoscopecontacts/style_'.$module->id.'.js')) {
		JFile::delete(JPATH_CACHE.'/mod_trombinoscopecontacts/style_'.$module->id.'.js');
	}
}

// styles

if (JFile::exists(JPATH_ROOT.'/modules/mod_trombinoscope/themes/substitute_styles.css') || JFile::exists(JPATH_ROOT.'/modules/mod_trombinoscope/themes/substitute_styles-min.css')) {
	modTrombinoscopeHelper::loadUserStylesheet(true);

	// remove style.css if it exists
	if (JFile::exists(JPATH_CACHE.'/mod_trombinoscopecontacts/style_'.$module->id.'.css')) {
		JFile::delete(JPATH_CACHE.'/mod_trombinoscopecontacts/style_'.$module->id.'.css');
	}
} else {

	$user_styles = trim($params->get('style_overrides', ''));
	if (!empty($user_styles)) {
		$user_styles = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $user_styles); // minify the CSS code
	}
	
	$cache_css = new TC_CSSFileCache('mod_trombinoscopecontacts', $params);
	$cache_css->addDeclaration($user_styles);
		
	$result = $cache_css->cache('style_'.$module->id.'.css', $clear_header_files_cache);
	
	if ($result) {
		$doc->addStyleSheet(JURI::base(true).'/cache/mod_trombinoscopecontacts/style_'.$module->id.'.css');
	}

	modTrombinoscopeHelper::loadCommonStylesheet();
	
	if (JFile::exists(JPATH_ROOT.'/modules/mod_trombinoscope/themes/common_user_styles.css') || JFile::exists(JPATH_ROOT.'/modules/mod_trombinoscope/themes/common_user_styles-min.css')) {
		modTrombinoscopeHelper::loadUserStylesheet();
	}
}

if ($picture_hover_type != 'none') {
	SYWStylesheets::load2DTransitions();
}

// handle high resolution images
$create_highres_images = $params->get('create_highres', false);	
if ($show_picture && $create_highres_images) {
	JHtml::_('jquery.framework');
	SYWLibraries::loadLazysizes();	
	SYWLibraries::triggerLazysizes('.te .picture img');
}

// load icon font
$load_icon_font = ($params->get('load_icon_font', '') == '') ? $config_params->get('load_icon_font', 1) : $params->get('load_icon_font', '');
if ($load_icon_font) {
	SYWFonts::loadIconFont();
}

require(JModuleHelper::getLayoutPath('mod_trombinoscope', $params->get('layout', 'default')));
?>

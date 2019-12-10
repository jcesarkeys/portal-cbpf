<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die ;

require_once (JPATH_ADMINISTRATOR.'/components/com_k2/elements/base.php');

/**
 * Supports a modal K2 item picker (overcomes the K2 core item custom field shortcomings)
 */
class K2ElementItem extends K2Element
{
    function fetchElement($name, $value, &$node, $control_name)
    {
        $db = JFactory::getDBO();
        $doc = JFactory::getDocument();
        
        JHtml::_('bootstrap.tooltip');
        
        JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/tables');
        $item = JTable::getInstance('K2Item', 'Table');
        
        if ($value) {
        	$item->load($value);
        } else {
        	$item->title = JText::_('LIB_SYW_K2ITEM_SELECT_AN_ITEM');
        }
        
        // Build the script        

        static $scriptSelect;
        if (!$scriptSelect) {
        	$scriptSelect = true;
        	
        	$script = array();
	       	$script[] = '	function jSelectItem(id, title, object) {'; // needs to have specific name, otherwise won't work in multiple fields
			$script[] = '		jQuery("#" + object + "_id").val(id);';
			$script[] = '		jQuery("#" + object + "_name").val(title);';
			$script[] = '		jQuery("#" + object + "_clear").removeClass("hidden");';
			$script[] = '		jQuery("#modalK2Item" + object).modal("hide");';
			$script[] = '	}';
			
			$doc->addScriptDeclaration(implode("\n", $script));
        } 
        
        static $scriptClear;
        if (!$scriptClear) {
        	$scriptClear = true;
        	
        	$script = array();        		
        	$script[] = '	function jClearArticle(id) {';
        	$script[] = '		jQuery("#" + id + "_id").val("");';
        	$script[] = '		jQuery("#" + id + "_name").val("'.htmlspecialchars(JText::_('LIB_SYW_K2ITEM_SELECT_AN_ITEM', true), ENT_COMPAT, 'UTF-8').'");';
        	$script[] = '		jQuery("#" + id + "_clear").addClass("hidden");';
        	$script[] = '		return false;';
        	$script[] = '	}';
        	
			$doc->addScriptDeclaration(implode("\n", $script));
        }
        
        $link = 'index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object='.$name;
        JHTML::_('behavior.modal', 'a.modal');
            
        $value = (int) $value;
        if(!$value) {
        	$value = '';
		}
		
		$html = '<span class="input-append">';
		$html .= '    <input type="text" class="input-large" id="'.$name.'_name" value="'.htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" />';
		$html .= '    <a href="#modalK2Item'.$name.'" class="btn hasTooltip" role="button" data-toggle="modal" title="'.JText::_('LIB_SYW_K2ITEM_SELECT_AN_ITEM').'"><i class="icon-file"></i> '.JText::_('JSELECT').'</a>';
		$html .= '    <a id="'.$name.'_clear" href="#" class="btn hasTooltip'.($value ? '' : ' hidden').'" title="'.JText::_('JCLEAR').'" onclick="return jClearArticle(\''.$this->id.'\')"><i class="icon-remove"></i></a>';
		$html .= '</span>';
		
		$class = '';
		if($node->attributes()->required) {
			$class = 'required ';
		}
		
		$html .= '<input type="hidden" id="'.$name.'_id" name="'.$name.'" class="'.$class.'modal-value" value="'.$value.'" />';
		
		$modal_params = array();
		$modal_params['url'] = $link;
		$modal_params['title'] = JText::_('LIB_SYW_K2ITEM_SELECT_AN_ITEM');
		$modal_params['width'] = '800px';
		$modal_params['height'] = '300px';
		$modal_params['footer'] = '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">'.JText::_("JLIB_HTML_BEHAVIOR_CLOSE").'</button>';
		
		$html .= JHTML::_('bootstrap.renderModal', 'modalK2Item'.$name, $modal_params);

        return $html;
    }

}

class JFormFieldK2Item extends K2ElementItem
{
    var $type = 'k2item';
}

class JElementK2Item extends K2ElementItem
{
    var $_name = 'k2item';
}

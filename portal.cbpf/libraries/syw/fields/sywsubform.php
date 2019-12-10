<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
*/

// no direct access
defined('_JEXEC') or die ;

if (version_compare(JVERSION, '3.8.2', 'lt')) {

	jimport('joomla.form.formfield');
	class JFormFieldSYWSubform extends JFormField
	{
		public $type = 'SYWSubform';

		protected function getLabel()
		{
			return '';
		}

		protected function getInput()
		{
			$html = '';
				
			$lang = JFactory::getLanguage();
			$lang->load('lib_syw.sys', JPATH_SITE);

			if ($this->message) {
				$html .= '<div style="margin-bottom:0" class="alert alert-warning">';
					$html .= '<span>'.JText::_($this->message).'</span>';
				$html .= '</div>';
			}
				
			return $html;
		}

		public function setup(SimpleXMLElement $element, $value, $group = null)
		{
			$return = parent::setup($element, $value, $group);

			if ($return) {
				$this->message = isset($this->element['text']) ? trim($this->element['text']) : '';
			}

			return $return;
		}
	}

} else {

	JFormHelper::loadFieldClass('subform');
	class JFormFieldSYWSubform extends JFormFieldSubform
	{
		public $type = 'SYWSubform';
		
		static $added_script = false;
		
		static function addScripts() 
		{
			if (!self::$added_script) {
				self::$added_script = true;
				
				// fixes to aknowledge Bootstrap
				
				// DOES NOT WORK BECAUSE THERE IS A BUG IN THE SUBFORM JAVASCRIPT
				// Field subform (multiple) produces wrong id #16480 #15187
				// fixed in Joomla 3.8.2				
				
				$script = 'jQuery(document).ready(function () { ';
					$script .= 'jQuery(document).on("subform-row-add", function(event, row) { ';
						
						$script .= 'jQuery(row).find("select").chosen(); '; // fix select (class="advancedSelect" does not always work)
						
						$script .= 'jQuery(row).find(".hasTooltip").tooltip();';
						$script .= 'jQuery(row).find(".hasPopover").popover({ container: "body", trigger: "hover focus" });';
						
						$script .= 'jQuery(row).find(".radio.btn-group label").addClass("btn");'; // turn radios into btn-group
						
						// Prevent clicks on disabled fields
						$script .= 'jQuery(row).find("fieldset.btn-group").each(function() {';
							$script .= 'if (jQuery(this).prop("disabled")) {';
							$script .= 'jQuery(this).css("pointer-events", "none").off("click");';
								$script .= 'jQuery(this).find(".btn").addClass("disabled");';
							$script .= '}';
						$script .= '});';
						
						// Add btn-* styling to checked fields according to their values
						$script .= 'jQuery(row).find(".btn-group label:not(.active)").click(function() { ';
							$script .= 'var label = jQuery(this); ';
							$script .= 'var input = jQuery("#" + label.attr("for")); ';
								
							$script .= 'if (!input.prop("checked")) { ';
								$script .= 'label.closest(".btn-group").find("label").removeClass("active btn-success btn-danger btn-primary"); ';
								$script .= 'if (input.val() == "") { ';
									$script .= 'label.addClass("active btn-primary"); ';
								$script .= '} else if (input.val() == 0) { ';
									$script .= 'label.addClass("active btn-danger"); ';
								$script .= '} else { ';
									$script .= 'label.addClass("active btn-success"); ';
								$script .= '} ';
								$script .= 'input.prop("checked", true); ';
								$script .= 'input.trigger("change"); ';
							$script .= '} ';
						$script .= '}); ';
						
						$script .= 'jQuery(row).find(\'.btn-group input[checked="checked"]\').each(function() { ';
							$script .= 'var input = jQuery(this); ';
							$script .= 'if (input.val() == "") { ';
								$script .= 'input.parent().find(\'label[for="\' + input.attr(\'id\') + \'"]\').addClass("active btn-primary"); ';
							$script .= '} else if (input.val() == 0) { ';
								$script .= 'input.parent().find(\'label[for="\' + input.attr(\'id\') + \'"]\').addClass("active btn-danger"); ';
							$script .= '} else { ';
								$script .= 'input.parent().find(\'label[for="\' + input.attr(\'id\') + \'"]\').addClass("active btn-success"); ';
							$script .= '} ';
						$script .= '}); ';
						
					$script .= '}) ';
				$script .= '});';
				
				JFactory::getDocument()->addScriptDeclaration($script);
			}
		}

		protected function getInput()
		{
			self::addScripts();			

			return parent::getInput();
		}
	}

}
?>
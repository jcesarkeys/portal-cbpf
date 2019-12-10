<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldImageRadio extends JFormField
{
	public $type = 'ImageRadio';	
	
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
			
		// Initialize some field attributes.
		$class     = !empty($this->class) ? ' class="radio ' . $this->class . '"' : ' class="radio"';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$readonly  = $this->readonly;

		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . ' >';

		// Get the field options.
		$options = $this->getOptions();
		
		JHtml::_('stylesheet', 'syw/fonts-min.css', false, true);
		JHtml::_('bootstrap.tooltip');

		// Build the radio field output.
		foreach ($options as $i => $option) {
			// Initialize some option attributes.
			$checked = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class_attribute = !empty($option->class) ? ' class="' . $option->class . '"' : '';
			$class = !empty($option->class) ? ' ' . $option->class : '';

			$disabled = !empty($option->disable) || ($readonly && !$checked);

			$disabled = $disabled ? ' disabled' : '';

			// Initialize some JavaScript option attributes.
			$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';

			$html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
					. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class_attribute . $required . $onclick
				. $onchange . $disabled . ' />';
	
			$title =  JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname));
			$html[] = '<label title="'. $title .'" for="' . $this->id . $i . '" class="hasTooltip'.$class.'">';
			if (!empty($option->image)) {
				$html[] = '<img style="margin-top: 0" src="'.JURI::root().$option->image.'" alt="'. $title .'" />';
			} else if (!empty($option->icon)) {
				$html[] = '<i class="'.$option->icon.'"></i>&nbsp;'.$title;
			} else {
				$html[] = $title;
			}
			$html[] = '</label>';
		
			$required = '';
		}
	
		// End the radio field output.
		$html[] = '</fieldset>';
	
		return implode($html);
	}

	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option) {

			// Only add <option /> elements.
			if ($option->getName() != 'option') {
				continue;
			}
			
			$disabled = (string) $option['disabled'];
			$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
				$disabled
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];
			$tmp->onchange = (string) $option['onchange'];
			
			$tmp->image = '';
			if (isset($option['imagesrc'])) {
				$tmp->image = (string) $option['imagesrc'];
			}
			
			$tmp->icon = '';
			if (isset($option['icon'])) {
				$tmp->icon = (string) $option['icon'];
			}

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		reset($options);

		return $options;
	}
}

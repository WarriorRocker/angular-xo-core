<?php

/**
 * An abstract class extending tab used to construct an options page in a custom way.
 *
 * @since 1.0.0
 */
class XoOptionsAbstractFieldsTab extends XoOptionsAbstractTab
{
	public function GenerateSection($heading, $description = '') {
		echo '<h2>' . $heading . '</h2>';

		if ($description)
			echo '<p>' . $description . '</p>';
	}

	public function GenerateForm($method = 'POST', $action = '', $hiddenParameters = array(), callable $callback = NULL) {
		echo '<form method="' . $method . '" action="' . $action . '">';

		foreach ($hiddenParameters as $name => $value)
			echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />';

		if (is_callable($callback))
			$callback();

		echo '</form>';
	}

	public function GenerateTable(callable $callback = NULL) {
		echo '<table class="form-table"><tbody>';

		if (is_callable($callback))
			$callback();

		echo '</tbody></table>';
	}

	public function GenerateFieldRow($name, $title, Callable $callback = NULL, $description = '') {
		echo '<tr>'
		. '<th scope="row"><label for="' . $name . '">' . $title . '</label></th>'
		. '<td>';

		if (is_callable($callback))
			call_user_func($callback, $name);

		if ($description)
			echo '<p class="description">' . $description . '</p>';

		echo '</td></tr>';
	}

	public function GenerateInputTextField($name, $states = array(), $value = NULL, $description = NULL) {
		$output = '<' . implode(' ', array(
			'input',
			'type="text"',
			'name="' . $name . '"',
			'id="' . $name . '"',
			'value="' . $value . '"',
			disabled(true, in_array('override', $states), false)
		)) . '>';

		if ($description)
			$output .= '<p class="description">' . $description . '</p>';

		echo $output;
	}

	public function GenerateInputCheckboxField($name, $states = array(), $value = NULL, $description = NULL) {
		$output = '<' . implode(' ', array(
			'input',
			'type="checkbox"',
			'name="' . $name . '"',
			'id="' . $name . '"',
			'value="1"',
			checked(1, $value, false),
			disabled(true, in_array('override', $states), false)
		)) . '>';

		if ($description)
			$output .= '<p class="description">' . $description . '</p>';

		echo $output;
	}

	public function GenerateSelectField($name, $states, $choices, $default, $value, $description = NULL) {
		$output = '<' . implode(' ', array(
			'select',
			'name="' . $name . '"',
			'id="' . $name . '"',
			disabled(true, in_array('override', $states), false)
		)) . '>';

		if ($default)
			$output .= '<option value="" '
			. selected($default['value'], $value, false)
			. '>' . $default['name'] . '</option>';

		foreach ($choices as $choiceValue => $choiceName)
			$output .= '<option value="' . $choiceValue . '" '
				. selected($choiceValue, $value, false)
				. '>' . $choiceName . '</option>';

		$output .= '</select>';

		$output .= $this->GenerateFieldDescription($description);

		echo $output;
	}

	public function GenerateTextareaField($name, $states = array(), $value = NULL, $description = NULL, $rows = 10, $cols = 50) {
		$output = '';

		if ($description)
			$output .= '<p><label for="' . $name . '">'
				. $description . '</label></p>';

		$output .= '<' . implode(' ', array(
			'textarea',
			'name="' . $name . '"',
			'id="' . $name . '"',
			'rows="' . $rows . '"',
			'cols="' . $cols . '"',
			'class="large-text code"',
			disabled(true, in_array('override', $states), false)
		)) . '>' . $value . '</textarea>';

		echo $output;
	}

	function GenerateFieldDescription($descriptions) {
		$output = '';

		if (is_array($descriptions))
			foreach ($descriptions as $description)
				$output .= $this->GenerateFieldDescription($description);

		else if ($descriptions)
			$output = '<p class="description">' . $descriptions . '</p>';

		return $output;
	}
}
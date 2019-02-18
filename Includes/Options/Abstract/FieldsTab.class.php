<?php

/**
 * An abstract class extending tab used to construct an options page in a custom way.
 *
 * @since 1.0.0
 */
class XoOptionsAbstractFieldsTab extends XoOptionsAbstractTab
{
	/**
	 * Generate a section heading and description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $heading The heading to display wrapped in <h2>.
	 * @param mixed $description An optional description to display wrapped in <p>.
	 * @return void
	 */
	public function GenerateSection($heading, $description = '') {
		echo '<h2>' . $heading . '</h2>';

		// Display the description only if provided
		if ($description)
			echo '<p>' . $description . '</p>';
	}

	/**
	 * Generate a form which has a configurable method and action, optional hidden parameters, and additional interior content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method The method used with the form tag, default is POST.
	 * @param string $action The action used with the form tag, default is blank (the current URL).
	 * @param array $hiddenParameters Optional hidden parameters set by a name => value relationship.
	 * @param callable $callback Optional callback for adding additional content (fields) within the form.
	 * @return void
	 */
	public function GenerateForm($method = 'POST', $action = '', $hiddenParameters = array(), callable $callback = NULL) {
		// Open the form tag
		echo '<form method="' . $method . '" action="' . $action . '">';

		// Iterate through the optional hidden parameters and add them to the form
		foreach ($hiddenParameters as $name => $value)
			echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />';

		// Call the callback if provided to add additional content within the form
		if (is_callable($callback))
			$callback();

		// Close the form tag
		echo '</form>';
	}

	/**
	 * Generate a table with optional interior content.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $callback Optional callback for adding additional content (rows) within the table.
	 * @return void
	 */
	public function GenerateTable(callable $callback = NULL) {
		// Open the table tag and tbody
		echo '<table class="form-table"><tbody>';

		// Call the callback if provided to add additional content within the table
		if (is_callable($callback))
			$callback();

		// Close the table tag and tbody
		echo '</tbody></table>';
	}

	/**
	 * Generate a field row with name and title, optional td content, and an optional description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name of the field used for the label for property.
	 * @param string $title The title text of the label.
	 * @param callable $callback Optional callback for adding additional content (field) within the td.
	 * @return void
	 */
	public function GenerateFieldRow($name, $title, Callable $callback = NULL) {
		// Open the tr, add the label, and open the td
		echo '<tr>'
		. '<th scope="row"><label for="' . $name . '">' . $title . '</label></th>'
		. '<td>';

		// Call the callback if provided to add additional content within the td
		if (is_callable($callback))
			call_user_func($callback, $name);

		// Close the td and tr
		echo '</td></tr>';
	}

	/**
	 * Generate an input text field with name, field states, current value, and optional description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name of the input field.
	 * @param array $states States used to set field properties such as disabled.
	 * @param string $value The value of the input field.
	 * @param array|string $description Optional description(s) shown below the input field.
	 * @return void
	 */
	public function GenerateInputTextField($name, $states = array(), $value = NULL, $description = NULL) {
		// Use implode to combine properties for input field
		$output = '<' . implode(' ', array(
			// Set basic field properties
			'input',
			'type="text"',
			'name="' . $name . '"',
			'id="' . $name . '"',
			'value="' . $value . '"',

			// Set the field as disabled if set in states array
			disabled(true, in_array('override', $states), false)
		)) . '>';

		// Optionally add the description
		$output .= $this->GenerateFieldDescription($description);

		// Return the full field output
		echo $output;
	}

	/**
	 * Generate an input checkbox field with name, field states, current value, and optional description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name of the input field.
	 * @param array $states States used to set field properties such as disabled.
	 * @param mixed $value A value which if truthy sets the checked property of the field.
	 * @param array|string $description Optional description(s) shown below the input field.
	 * @return void
	 */
	public function GenerateInputCheckboxField($name, $states = array(), $value = NULL, $description = NULL) {
		// Use implode to combine properties for input field
		$output = '<' . implode(' ', array(
			// Set basic field properties
			'input',
			'type="checkbox"',
			'name="' . $name . '"',
			'id="' . $name . '"',
			'value="1"',

			// Check the value and if true set the field as checked
			checked(1, $value, false),

			// Set the field as disabled if set in states array
			disabled(true, in_array('override', $states), false)
		)) . '>';

		// Optionally add the description
		$output .= $this->GenerateFieldDescription($description);

		// Return the full field output
		echo $output;
	}

	/**
	 * Generate a select field with name, field states, choices, current value, and optional description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name of the select field.
	 * @param array $states States used to set field properties such as disabled.
	 * @param mixed $choices Choices for the select set using a value => name relationship..
	 * @param array|string $default Default option for the select set using a value => name relationship or single string with no value.
	 * @param string $value The current value which will be selected.
	 * @param array|string $description Optional description(s) shown below the select field.
	 * @return void
	 */
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

	/**
	 * Generate an input text field with name, field states, current value, and optional description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name of the textarea field.
	 * @param array $states States used to set field properties such as disabled.
	 * @param string $value The content of the textarea field.
	 * @param array|string $description Optional description(s) shown below the input field.
	 * @param int $rows Rows set for the textarea field, default is 10.
	 * @param mixed $cols Columns set for the textarea field, default is 50.
	 * @return void
	 */
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

	/**
	 * Helper function to display the description text below an input field.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $descriptions A string or array of (array of...) strings to wrap in the description paragraph(s).
	 * @return string Output HTML from all descriptions.
	 */
	function GenerateFieldDescription($descriptions) {
		$output = '';

		// Check if the input is an array
		if (is_array($descriptions))
			// Iterate through all items
			foreach ($descriptions as $description)
				// Add the output of multiple descriptions
				$output .= $this->GenerateFieldDescription($description);

		// Check if the description is empty
		else if ($descriptions)
			// Add the output of the single description
			$output = '<p class="description">' . $descriptions . '</p>';

		return $output;
	}
}
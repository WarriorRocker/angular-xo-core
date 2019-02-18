<?php

/**
 * Filter class providing various overrides and additions for the WordPress editor.
 * 
 * @since 1.0.0
 */
class XoFilterEditorOptions
{
	/**
	 * @var Xo
	 */
	var $Xo;

	function __construct(Xo $Xo) {
		$this->Xo = $Xo;

		add_action('admin_init', array($this, 'AdminInit'), 10, 0);
	}

	function AdminInit() {
		// If we are on the edit screen
		if ((!empty($_REQUEST['post'])) &&
			($post = get_post($_REQUEST['post']))) {
			if ($attrs = $this->Xo->Services->TemplateReader->GetTemplateForPost($post))
				$this->SetEditorOptions($post, $attrs);

		// When ACF gets the field groups via ajax
		} else if ((!empty($_REQUEST['post_id'])) &&
			($post = get_post($_REQUEST['post_id']))) {

			// If the template was also supplied
			if ($template = $_REQUEST['page_template']) {

				// If the template was found by the template reader
				if ($attrs = $this->Xo->Services->TemplateReader->GetAnnotatedTemplate($template))
					$this->SetEditorOptions($post, $attrs);

			// Get the template assigned to the post
			} else {
				if ($attrs = $this->Xo->Services->TemplateReader->GetTemplateForPost($post))
					$this->SetEditorOptions($post, $attrs);
			}
		}
	}

	function SetEditorOptions($post, $attrs) {
		$this->DisableContentEditor($post, $attrs);

		if (class_exists('ACF'))
			$this->SetEditorAcfGroups($post, $attrs);
	}

	private function DisableContentEditor($post, $attrs) {
		if ((isset($attrs['disableEditor'])) && ($attrs['disableEditor'] == 1))
	        remove_post_type_support($post->post_type, 'editor');
	}

	private function SetEditorAcfGroups(WP_Post $post, $attrs) {
		add_filter('acf/get_field_groups', function($groups) use ($post, $attrs) {
		    foreach ($groups as &$group) {
				if ((!empty($attrs['acfGroups'])) && (in_array($group['key'], $attrs['acfGroups']))) {
					if ($post->post_type == 'page') {
						$group['location'][] = array(
							array(
								'param' => 'page_template',
								'operator' => '==',
								'value' => $attrs['template']
							)
						);
					} else {
						$group['location'][] = array(
							array(
								'param' => 'post_type',
								'operator' => '==',
								'value' => $post->post_type
							)
						);
					}
				}
		    }

		    return $groups;
		}, 30, 1);
	}
}
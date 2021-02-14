<?php

/**
 * Service class used to parse and manage WP Rewrites.
 *
 * @since 2.0.0
 */
class XoServiceRewrites
{
	/**
	 * Obtain the WP_Query for a given url.
	 * 
	 * Obtained logic from WP class parse_request method.
	 *
	 * @since 2.0.0
	 *
	 * @return string[] WP_Query array of query variables and their respective values.
	 */
	public function GetWpQueryForURL($url) {
		$match = $this->MatchWpRewrite($url);

		if (empty($match)) {
			return false;
		}

		$vars = $this->MatchToQueryVars($match);

		$query_vars = $this->FormatQueryVars($vars);
		$query_vars = $this->FilterNonPublicQueryVars($query_vars);

		// Resolve conflicts between posts with numeric slugs and date archive queries.
		$query_vars = wp_resolve_numeric_slug_conflicts($query_vars);

		// Filters the array of parsed query variables
		$query_vars = apply_filters('request', $query_vars);

		print_r($query_vars);

	}

	protected function MatchWpRewrite($url) {
		global $wp_rewrite;

		$url = trim($url, '/');

		// Fetch the rewrite rules.
		$rewrite = $wp_rewrite->wp_rewrite_rules();

		// Look for matches.
		foreach ( (array) $rewrite as $match => $query ) {
			$matches = [];

			if ( preg_match( "#^$match#", $url, $matches ) ) {

				if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
					// This is a verbose page match, let's check to be sure about it.
					$page = get_page_by_path( $matches[ $varmatch[1] ] );
					if ( ! $page ) {
						continue;
					}

					// Check post status
					$post_status_obj = get_post_status_object( $page->post_status );
					if ( ! $post_status_obj->public && ! $post_status_obj->protected
						&& ! $post_status_obj->private && $post_status_obj->exclude_from_search ) {
						continue;
					}
				}

				// Got a match.
				return [$match, $query, $matches];
			}
		}

		return false;
	}

	protected function MatchToQueryVars($match) {
		$vars = [];

		// Trim the query of everything up to the '?'.
		$query = preg_replace('!^.+\?!', '', $match[1]);

		// Substitute the substring matches into the query.
		$query = addslashes(WP_MatchesMapRegex::apply($query, $match[2]));

		// Parse the query.
		parse_str($query, $vars);
		
		return $vars;
	}

	protected function FormatQueryVars($vars) {
		global $wp;

		$public_query_vars = apply_filters( 'query_vars', $wp->public_query_vars );

		foreach ( get_post_types( array(), 'objects' ) as $post_type => $t ) {
			if ( is_post_type_viewable( $t ) && $t->query_var ) {
				$post_type_query_vars[ $t->query_var ] = $post_type;
			}
		}

		$query_vars = [];

		foreach ( $public_query_vars as $wpvar ) {
			if ( isset( $vars[ $wpvar ] ) ) {
				$query_vars[ $wpvar ] = $vars[ $wpvar ];
			}

			if ( ! empty( $query_vars[ $wpvar ] ) ) {
				if ( ! is_array( $query_vars[ $wpvar ] ) ) {
					$query_vars[ $wpvar ] = (string) $query_vars[ $wpvar ];
				} else {
					foreach ( $query_vars[ $wpvar ] as $vkey => $v ) {
						if ( is_scalar( $v ) ) {
							$query_vars[ $wpvar ][ $vkey ] = (string) $v;
						}
					}
				}

				if ( isset( $post_type_query_vars[ $wpvar ] ) ) {
					$query_vars['post_type'] = $post_type_query_vars[ $wpvar ];
					$query_vars['name']      = $query_vars[ $wpvar ];
				}
			}
		}

		return $query_vars;
	}

	protected function FilterNonPublicQueryVars($query_vars) {
		// Don't allow non-publicly queryable taxonomies to be queried from the front end.
		if ( ! is_admin() ) {
			foreach ( get_taxonomies( array( 'publicly_queryable' => false ), 'objects' ) as $taxonomy => $t ) {
				/*
					* Disallow when set to the 'taxonomy' query var.
					* Non-publicly queryable taxonomies cannot register custom query vars. See register_taxonomy().
					*/
				if ( isset( $query_vars['taxonomy'] ) && $taxonomy === $query_vars['taxonomy'] ) {
					unset( $query_vars['taxonomy'], $query_vars['term'] );
				}
			}
		}

		// Limit publicly queried post_types to those that are 'publicly_queryable'.
		if ( isset( $query_vars['post_type'] ) ) {
			$queryable_post_types = get_post_types( array( 'publicly_queryable' => true ) );
			if ( ! is_array( $query_vars['post_type'] ) ) {
				if ( ! in_array( $query_vars['post_type'], $queryable_post_types, true ) ) {
					unset( $query_vars['post_type'] );
				}
			} else {
				$query_vars['post_type'] = array_intersect( $query_vars['post_type'], $queryable_post_types );
			}
		}
	
		return $query_vars;
	}
}
<?php

/**
 * Service class used to generate permalinks primarily used for drafts and previews.
 *
 * @since 2.0.0
 */
class XoServicePermalinks
{
	protected $draftPrefix = 'xo-draft-';

	/**
	 * Obtain the permalink for a given post.
	 * 
	 * Obtained logic from WP class get_permalink method.
	 *
	 * @since 2.0.0
	 */
	public function GetPermalink($post = 0, $leavename = false ) {
		$rewritecode = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			$leavename ? '' : '%postname%',
			'%post_id%',
			'%category%',
			'%author%',
			$leavename ? '' : '%pagename%',
		);
	 
		if ( is_object( $post ) && isset( $post->filter ) && 'sample' === $post->filter ) {
			$sample = true;
		} else {
			$post   = get_post( $post );
			$sample = false;
		}
	 
		if ( empty( $post->ID ) ) {
			return false;
		}

		// ...

		if ( 'page' === $post->post_type ) {
			return $this->GetPageLink( $post, $leavename, $sample );
		} elseif ( 'attachment' === $post->post_type ) {
			return get_attachment_link( $post, $leavename );
		} elseif ( in_array( $post->post_type, get_post_types( array( '_builtin' => false ) ), true ) ) {
			return $this->GetPostPermalink( $post, $leavename, $sample );
		}

		$permalink = get_option( 'permalink_structure' );

		$permalink = apply_filters( 'pre_post_link', $permalink, $post, $leavename );

		$category = '';
        if ( strpos( $permalink, '%category%' ) !== false ) {
            $cats = get_the_category( $post->ID );
            if ( $cats ) {
                $cats = wp_list_sort(
                    $cats,
                    array(
                        'term_id' => 'ASC',
                    )
                );
 
                // Filters the category that gets used in the %category% permalink token.
                $category_object = apply_filters( 'post_link_category', $cats[0], $cats, $post );
 
                $category_object = get_term( $category_object, 'category' );
                $category        = $category_object->slug;
                if ( $category_object->parent ) {
                    $category = get_category_parents( $category_object->parent, false, '/', true ) . $category;
                }
            }
 			// Show default category in permalinks,
            // without having to assign it explicitly.
            if ( empty( $category ) ) {
                $default_category = get_term( get_option( 'default_category' ), 'category' );
                if ( $default_category && ! is_wp_error( $default_category ) ) {
                    $category = $default_category->slug;
                }
            }
        }

		$author = '';
        if ( strpos( $permalink, '%author%' ) !== false ) {
            $authordata = get_userdata( $post->post_author );
            $author     = $authordata->user_nicename;
        }
 
        // This is not an API call because the permalink is based on the stored post_date value,
        // which should be parsed as local time regardless of the default PHP timezone.
        $date = explode( ' ', str_replace( array( '-', ':' ), ' ', $post->post_date ) );

		// Drafts
		$draft_or_pending = get_post_status( $post ) && in_array( get_post_status( $post ), array( 'draft', 'pending', 'auto-draft', 'future' ), true );
 
		$post_name = $draft_or_pending ? $this->draftPrefix . $post->ID : $post->post_name;
 
        $rewritereplace = array(
            $date[0],
            $date[1],
            $date[2],
            $date[3],
            $date[4],
            $date[5],
            $post_name,
            $post->ID,
            $category,
            $author,
            $post_name,
        );
 
        $permalink = home_url( str_replace( $rewritecode, $rewritereplace, $permalink ) );
        $permalink = user_trailingslashit( $permalink, 'single' );

		return apply_filters( 'post_link', $permalink, $post, $leavename );
	}

	/**
	 * Obtain the permalink for a given custom post.
	 * 
	 * Obtained logic from WP class get_post_permalink method.
	 *
	 * @since 2.0.0
	 */
	public function GetPostPermalink( $id = 0, $leavename = false, $sample = false ) {
		global $wp_rewrite;

		$post = get_post( $id );

		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$post_link = $wp_rewrite->get_extra_permastruct( $post->post_type );
	 
		$draft_or_pending = get_post_status( $post ) && in_array( get_post_status( $post ), array( 'draft', 'pending', 'auto-draft', 'future' ), true );

		$slug = $draft_or_pending ? $this->draftPrefix . $post->ID : $post->post_name;

		$post_type = get_post_type_object( $post->post_type );

		if ( $post_type->hierarchical ) {
			$slug = get_page_uri( $post );
		}

		if ( ! empty( $post_link ) && ( ! $sample ) ) {
			if ( ! $leavename ) {
				$post_link = str_replace( "%$post->post_type%", $slug, $post_link );
			}
			$post_link = home_url( user_trailingslashit( $post_link ) );
		} else {
			if ( $post_type->query_var && ( isset( $post->post_status ) && ! $draft_or_pending ) ) {
				$post_link = add_query_arg( $post_type->query_var, $slug, '' );
			} else {
				$post_link = add_query_arg(
					array(
						'post_type' => $post->post_type,
						'p'         => $post->ID,
					),
					''
				);
			}
			$post_link = home_url( $post_link );
		}
	 
		// Filters the permalink for a post of a custom post type.
		return apply_filters( 'post_type_link', $post_link, $post, $leavename, $sample );
	}

	/**
	 * Obtain the permalink for a given page.
	 * 
	 * Obtained logic from WP class get_page_link method.
	 *
	 * @since 2.0.0
	 */
	public function GetPageLink( $post = false, $leavename = false, $sample = false ) {
		global $wp_rewrite;

		$post = get_post( $post );
	 
		if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID ) {
			$link = home_url( '/' );
		} else {
			$draft_or_pending = in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ), true );
 
			$link = $wp_rewrite->get_page_permastruct();
		
			if ( ! empty( $link ) && ( ( isset( $post->post_status ) && ! $draft_or_pending ) || $sample ) ) {
				if ( ! $leavename ) {
					$link = str_replace( '%pagename%', get_page_uri( $post ), $link );
				}
		
				$link = home_url( $link );
				$link = user_trailingslashit( $link, 'page' );
			}
			
			// Drafts
			else {
				$link = home_url( $this->draftPrefix . $post->ID );
			}
		
			// Filters the permalink for a non-page_on_front page.
			$link = apply_filters( '_get_page_link', $link, $post->ID );
		}
	 
		// Filters the permalink for a page.
		return apply_filters( 'page_link', $link, $post->ID, $sample );
	}
}

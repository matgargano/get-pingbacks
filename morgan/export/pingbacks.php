<?php

namespace Morgan\Export;

class Pingbacks {

	public function init() {

		$this->attach_hooks();
	}

	public function attach_hooks() {
		add_action( 'init', array( $this, 'add_rewrite_rule' ) );
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
	}

	public function query_vars( $vars ) {
		$vars[] = 'pingbacks';
		$vars[] = 'pb_post_id';

		return $vars;
	}

	public function add_rewrite_rule() {

		add_rewrite_rule( '^get-pingbacks/?$', 'index.php?pingbacks=true', 'top' );
		add_rewrite_rule( '^get-pingbacks\/([0-9]+)\/?$', 'index.php?pingbacks=true&pb_post_id=$matches[1]', 'top' );

	}


	public function template_include( $template ) {

		if ( get_query_var( 'pingbacks' ) == 'true' ) {
			if ( current_user_can( 'manage_options' ) ) {
				$this->build_pingbacks( get_query_var( 'pb_post_id' ) );
				die;

			}

			if ( ! $template = get_404_template() ) {

				$template = get_index_template();
			}


		}


		return $template;
	}


	public function build_pingbacks( $post_id = null ) {
		$file_addition = '';
		if ( $post_id ) {
			$file_addition = '-post' . (int) $post_id;
		}
		header( "Content-type: text/csv" );
		header( "Content-Disposition: attachment; filename=" . date( 'YmdHis' ) . $file_addition . "-pingbacks.csv" );
		header( "Pragma: no-cache" );
		header( "Expires: 0" );
		$template = '"%s","%s","%s", "%s", "%s"' . "\n";
		$args     = array( 'type' => 'pingback' );

		if ( (int) $post_id ) {
			$args = array_merge( $args, array( 'post_id' => $post_id ) );
		}
		$comments = get_comments( $args );
		foreach ( $comments as $comment ) :
			echo sprintf( $template, esc_attr( $comment->comment_author ), esc_attr( $comment->comment_author_url ), esc_attr( $comment->comment_content ), esc_attr( $comment->comment_date ), get_permalink( $comment->comment_post_ID ) );

		endforeach;


		die;
	}


}
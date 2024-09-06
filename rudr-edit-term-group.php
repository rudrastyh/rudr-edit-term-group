<?php
/*
 Plugin Name: Edit Term Group
 Description: Allows to easily edit term group for categories, tags and custom taxonomy terms
 Author: Misha Rudrastyh
 Author URI: https://rudrastyh.com
 Version: 1.0
 License: GPL v2 or later
 License URI: http://www.gnu.org/licenses/gpl-2.0.html
 Text Domain: rudr-edit-term-group
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'Rudr_Edit_Term_Group' ) ) {

	class Rudr_Edit_Term_Group {

		public function __construct() {

			add_action( 'init', array( $this, 'init' ), 9999 );

		}

		public function init() {
			$taxonomies = get_taxonomies( array( 'public' => true ) );
			if( $taxonomies ) {
				foreach( $taxonomies as $taxonomy ) {
					// add taxonomy term page
					add_action( $taxonomy . '_add_form_fields', array( $this, 'add_term' ) );
					add_action( 'created_' . $taxonomy, array( $this, 'add_term_save' ) );
					// edit taxonomy term page
					add_action( $taxonomy . '_edit_form_fields', array( $this, 'edit_term' ), 25, 2 );
					// columns
					add_filter( 'manage_edit-' . $taxonomy . '_columns', array( $this, 'add_column' ) );
					add_filter( 'manage_' . $taxonomy . '_custom_column', array( $this, 'populate_column' ), 25, 3 );
					add_filter( 'manage_edit-' . $taxonomy . '_sortable_columns', array( $this, 'sortable_column' ) );
					add_action( 'admin_head', array( $this, 'column_css' ) );
					// quick edit
					add_action( 'quick_edit_custom_box', array( $this, 'quick_edit' ), 25, 2 );
					add_action( 'admin_footer', array( $this, 'populate_quick_edit' ) );

				}
			}
		}

		public function add_term() {
			?>
				<div class="form-field term-group-wrap">
					<label for="term_group"><?php esc_html_e( 'Term group', 'rudr-edit-term-group' ); ?></label>
					<input type="number" class="small-text" id="term_group" name="term_group" />
				</div>
			<?php
		}

		public function add_term_save( $term_id ) {

			check_admin_referer( 'add-tag', '_wpnonce_add-tag' );

			$term_group = intval( wp_unslash( ( isset( $_POST[ 'term_group' ] ) ? $_POST[ 'term_group' ] : 0 ) ) );
			$taxonomy = str_replace( 'created_', '', current_filter() );

			wp_update_term( $term_id, $taxonomy, array( 'term_group' => $term_group ) );

		}

		public function edit_term( $term ) {
    	?>
		    <tr class="form-field term-group-wrap">
	        <th scope="row">
						<label for="term_group"><?php esc_html_e( 'Term group', 'rudr-edit-term-group' ); ?></label>
					</th>
	        <td>
            <input type="number" class="small-text" id="term_group" name="term_group" value="<?php echo intval( $term->term_group ) ?>" />
	        </td>
		    </tr>
    	<?php
		}

		public function add_column( $columns ) {
			$columns[ 'term_group' ] = __( 'Term group', 'rudr-edit-term-group' );
	    return $columns;
		}

		public function populate_column( $out, $column_name, $term_id ) {

			if( 'term_group' === $column_name ) {
				$term = get_term( $term_id );
				$out = intval( $term->term_group );
	    }

	    return $out;
		}

		public function sortable_column( $sortable_columns ) {
			$sortable_columns[ 'term_group' ] = 'term_group';
			return $sortable_columns;
		}

		public function column_css() {
			?>
				<style>
				.column-term_group{
					text-align: center;
				}
				.fixed .column-term_group {
			    width: 15%;
				}
				</style>
			<?php
		}

		public function quick_edit( $column_name, $screen ) {

			if ( 'term_group' !== $column_name || 'edit-tags' !== $screen ) {
				return false;
			}


      ?>
	      <fieldset class="inline-edit-col-right">
          <div class="inline-edit-col">
            <label>
							<span class="title"><?php esc_html_e( 'Term group', 'rudr-edit-term-group' ); ?></span>
							<span class="input-text-wrap">
								<input type="number" class="small-text inline-edit-menu-order-input" name="term_group" value="" />
							</span>
            </label>
          </div>
	      </fieldset>
      <?php

		}

		public function populate_quick_edit() {

			?>
				<script>
					jQuery( function( $ ) {
						$( '.editinline' ).on( 'click', function() {
							const termId = $( this ).parents( 'tr' ).attr( 'id' );
							const termGroup  = parseInt( $( 'td.term_group', '#' + termId ).text() );
							$( ':input[name="term_group"]', '.inline-edit-row' ).val( termGroup );
						} );
					} );
				</script>
			<?php

		}

	}

	new Rudr_Edit_Term_Group;
}

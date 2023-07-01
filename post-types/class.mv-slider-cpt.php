<?php

if ( ! class_exists('MV_Slider_Post_Type') ) {
    class MV_Slider_Post_Type {
        function __construct() {
            add_action( 'init', array( $this, 'create_post_type' ) );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            // for saving the post
            add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

            // This filter will add columns in view of custom post type
            // The hook name = manage_yourCustomPostType_posts_columns
            add_filter( 'manage_mv-slider_posts_columns', array( $this, 'mv_slider_cpt_columns' ) );

            // Now to add the values to the newly created columns
            add_action( 'manage_mv-slider_posts_custom_column', array( $this, 'mv_slider_custom_columns' ), 10, 2 );

            // for making the newly created columns sortable
            add_filter( 'manage_edit-mv-slider_sortable_columns', array( $this, 'mv_slider_sortable_columns' ) );
        }

        public function create_post_type() {
            // show_in_menu = false korar karone custom_post_type jeta create hoise oita ar admin er sidebar e show korbe na. amra already ekta admin_menu_page create korsi ar ei custom_post_type er slug ta submenu te diye disi. tai show_in_menu = false kore disi.
            register_post_type(
                'mv-slider',
                array(
                    'label' => 'Slider',
                    'desciption' => 'Sliders',
                    'labels' => array(
                        'name' => 'Sliders',
                        'singular_name' => 'Slider'
                    ),
                    'public' => true,
                    'supports' => array( 'title', 'editor', 'thumbnail' ),
                    'hierarchical' => false,
                    'show_ui' => true,
                    'show_in_menu' => false,
                    'menu_position' => 5,
                    'show_in_admin_bar' => true,
                    'show_in_nav_menu' => true,
                    'can_export' => true,
                    'has_archive' => false,
                    'exclude_from_search' => false,
                    'publicly_queryable' => true,
                    'show_in_rest' => true,
                    'menu-icon' => 'dashicons-image-alt2'
                )
            );
        }

        // this hooks recieve one paramter.
        // Columns er jaygay onno name dewa jabe
        // This parameter here is an array that contains all the columns of my post type table.
        public function mv_slider_cpt_columns($columns) {
            $columns['mv_slider_link_text'] = esc_html__('Link Text', 'mv-slider');
            $columns['mv_slider_link_url'] = esc_html__('Link URL', 'mv-slider');
            return $columns;
        }

        // To show the values of extra columns
        // $post_id te post_meta table e je post_id ache oita astese.
        // $column er vitore meta_key column er value astese.
        public function mv_slider_custom_columns( $column, $post_id ) {

            switch( $column ) {
                case 'mv_slider_link_text':
                    echo esc_html( get_post_meta( $post_id, 'mv_slider_link_text', true ) ) ;
                    break;
                case 'mv_slider_link_url':
                    echo esc_url( get_post_meta($post_id, 'mv_slider_link_url', true) );
                    break;
            }
        }
        // this function will accept one parameter
        // je column ta sortable korte chacchi tar index create kore oi column er key $column['index'] e assign kore dile hobe.
        public function mv_slider_sortable_columns( $columns ) {
            $columns['mv_slider_link_text'] = 'mv_slider_link_text';
            $columns['mv_slider_link_url'] = 'mv_slider_link_url';
            return $columns;
        }

        public function add_meta_boxes() {
            add_meta_box(
                'mv_slider_meta_box',
                'Link Options',
                array($this, 'add_inner_meta_boxes'),
                'mv-slider',
                'normal',
                'high'
            );
        }

        public function add_inner_meta_boxes( $post ) {
            require_once(MV_SLIDER_PATH . 'views/mv-slider_metabox.php');
        }

        public function save_post( $post_id ) {
            // start: validation
            if ( isset( $_POST['mv_slider_nonce'] ) ) {
                if ( ! wp_verify_nonce( $_POST['mv_slider_nonce'], 'mv_slider_nonce' ) ) {
                    return ;
                }
            }

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return ;
            }

            if ( isset($_POST['post_type']) && $_POST['post_type'] ==='mv-slider' ) {
                if ( !current_user_can( 'edit_page', $post_id ) ) {
                    return ;
                } else if ( !current_user_can('edit_post', $post_id) ) {
                    return ;
                }
            }
            // End Validation

            if ( isset( $_POST['action']) && $_POST['action'] == 'editpost' ) {
                $old_link_text = get_post_meta( $post_id, 'mv_slider_link_text', true );
                $new_link_text = sanitize_text_field($_POST['mv_slider_link_text']);
                $old_link_url = get_post_meta($post_id, 'mv_slider_link_url', true);
                $new_link_url = sanitize_text_field($_POST['mv_slider_link_url']);

                // difference between add_post_meta and update_post_meta is add_post_meta only adds new value to database where update_post_meta adds new values to database if it doesn't exist otherwise update the values
                if ( empty ( $new_link_text ) ) {
                    update_post_meta( $post_id, 'mv_slider_link_text', 'Add Some Text' );
                } else {
                    update_post_meta( $post_id, 'mv_slider_link_text', $new_link_text,  $old_link_text );
                }

                if ( empty ($new_link_url) ) {
                    update_post_meta( $post_id, 'mv_slider_link_url', '#' );
                } else {
                    update_post_meta( $post_id, 'mv_slider_link_url', $new_link_url, $old_link_url );
                }

            }
        }
    }
}

<?php

/**
 * Plugin Name: AdminPass: Password Bypass & Display
 * Plugin URI: https://wordpress.org/plugins/adminpass-password-bypass-display
 * Description: AdminPass allows administrators to bypass password protection and admins/editors to view passwords for protected pages, posts, and custom post types.
 * Version: 1.3
 * Requires at least: 5.7
 * Requires PHP: 7.2
 * Author: Belov Digital Agency
 * Author URI: https://belovdigital.agency
 * License: GPL-2.0-or-later
 * Text Domain: adminpass
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function bda_adminpass_enqueue_adminpass_script( $hook_suffix ) {
    if ( !is_admin() ) return;

    $current_screen = get_current_screen();
    if ( $current_screen && $current_screen->is_block_editor && ( current_user_can( 'manage_options' ) || current_user_can( 'edit_others_posts' ) ) ) {
        wp_enqueue_script(
            'adminpass-script',
            plugin_dir_url( __FILE__ ) . 'adminpass.js',
            array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-data', 'wp-i18n' ),
            filemtime( plugin_dir_path( __FILE__ ) . 'adminpass.js' ),
            true
        );
    }
}
add_action( 'admin_enqueue_scripts', 'bda_adminpass_enqueue_adminpass_script' );

function bda_adminpass_bypass_password_protection_for_admin( $posts ) {
    if ( empty( $posts ) || !is_user_logged_in() || !current_user_can( 'manage_options' ) ) {
        return $posts;
    }

    foreach ( $posts as $post ) {
        if ( post_password_required( $post ) ) {
            $post->post_password = ''; // Remove password requirement
        }
    }

    return $posts;
}
add_filter( 'the_posts', 'bda_adminpass_bypass_password_protection_for_admin' );

function bda_adminpass_bypass_single_post_password( $content ) {
    if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
        global $post;
        if ( post_password_required( $post ) ) {
            $post->post_password = '';
            return $post->post_content;
        }
    }
    return $content;
}
add_filter( 'the_content', 'bda_adminpass_bypass_single_post_password', 1 );

// Add this function to handle password form short-circuiting
function bda_adminpass_bypass_password_form( $output, $post = 0 ) {
    if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
        return ''; // Return empty string to bypass password form
    }
    return $output;
}
add_filter( 'the_password_form', 'bda_adminpass_bypass_password_form', 10, 2 );
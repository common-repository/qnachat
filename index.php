<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Name: QnAChat
 * Plugin URI:  https://plugins.aleswebs.com
 * Description: Simplify communication on your WordPress website with a sleek chat form for users to contact you. Quick links to FAQs provide instant answers, making customer support a breeze.
 * Version:     1.0.2
 * Author:      Ales
 * Author URI:  https://aleswebs.com
 * Text Domain: qnachat
 * License:     GPLv3 or later
 * Requires at least: 5.9
 * Requires PHP: 7.2
 * Tested up to: 6.4
 *
 * QnAChat is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * QnAChat is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

 define('QNAC_FILE', __FILE__);
 define('QNAC_NAME', 'default');
 define('QNAC_URL', plugin_dir_url(__FILE__));
 define('QNAC_PATH', plugin_dir_path(__FILE__));
 define('QNAC_TEMPLATE_DIR', QNAC_PATH . '/templates/');

 require_once (QNAC_PATH.'init.php');
 require_once (QNAC_PATH.'includes/functions.php');
 require_once (QNAC_PATH.'includes/admin/admin.php');
 require_once (QNAC_PATH.'includes/on_plugin_activate.php'); 
 
 
add_action('admin_menu', 'register_qnac');
function register_qnac(){
    $qnac_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPHN2ZwoJdmVyc2lvbj0iMS4xIgoJeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIgoJeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiCgl4PSIwJSIgeT0iMCUiCgl3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIgoJdmlld0JveD0iMCAwIDIwLjAgMjAuMCIKCWVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDIwLjAgMjAuMCIKCXhtbDpzcGFjZT0icHJlc2VydmUiPgoJPHBhdGgKCQlmaWxsPSIjRkZGRkZGIgoJCXN0cm9rZT0iIzAwMDAwMCIKCQlmaWxsLW9wYWNpdHk9IjEuMDAwIgoJCXN0cm9rZS1vcGFjaXR5PSIxLjAwMCIKCQlmaWxsLXJ1bGU9Im5vbnplcm8iCgkJc3Ryb2tlLXdpZHRoPSIwLjAiCgkJc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIKCQlzdHJva2UtbGluZWNhcD0ic3F1YXJlIgoJCWQ9Ik05LjAxLDkuNjlDOS4xNyw5LjY5LDkuMzEsOS44Miw5LjMxLDkuOTlDOS4zMSwxMC4xNSw5LjE3LDEwLjI5LDkuMDEsMTAuMjlDOC44NCwxMC4yOSw4LjcxLDEwLjE1LDguNzEsOS45OUM4LjcxLDkuODIsOC44NCw5LjY5LDkuMDEsOS42OXoiLz4KCTxwYXRoCgkJZmlsbD0iI0ZGRkZGRiIKCQlzdHJva2U9IiMwMDAwMDAiCgkJZmlsbC1vcGFjaXR5PSIxLjAwMCIKCQlzdHJva2Utb3BhY2l0eT0iMS4wMDAiCgkJZmlsbC1ydWxlPSJub256ZXJvIgoJCXN0cm9rZS13aWR0aD0iMC4wIgoJCXN0cm9rZS1saW5lam9pbj0icm91bmQiCgkJc3Ryb2tlLWxpbmVjYXA9InNxdWFyZSIKCQlkPSJNOS44Nyw5LjY5QzEwLjA0LDkuNjksMTAuMTcsOS44MiwxMC4xNyw5Ljk5QzEwLjE3LDEwLjE1LDEwLjA0LDEwLjI5LDkuODcsMTAuMjlDOS43MSwxMC4yOSw5LjU3LDEwLjE1LDkuNTcsOS45OUM5LjU3LDkuODIsOS43MSw5LjY5LDkuODcsOS42OXoiLz4KCTxwYXRoCgkJZmlsbD0iI0ZGRkZGRiIKCQlzdHJva2U9IiMwMDAwMDAiCgkJZmlsbC1vcGFjaXR5PSIxLjAwMCIKCQlzdHJva2Utb3BhY2l0eT0iMS4wMDAiCgkJZmlsbC1ydWxlPSJub256ZXJvIgoJCXN0cm9rZS13aWR0aD0iMC4wIgoJCXN0cm9rZS1saW5lam9pbj0icm91bmQiCgkJc3Ryb2tlLWxpbmVjYXA9InNxdWFyZSIKCQlkPSJNMTAuNzUsOS42OUMxMC45Miw5LjY5LDExLjA1LDkuODIsMTEuMDUsOS45OUMxMS4wNSwxMC4xNSwxMC45MiwxMC4yOSwxMC43NSwxMC4yOUMxMC41OSwxMC4yOSwxMC40NiwxMC4xNSwxMC40Niw5Ljk5QzEwLjQ2LDkuODIsMTAuNTksOS42OSwxMC43NSw5LjY5eiIvPgoJPHBhdGgKCQlmaWxsPSIjRkZGRkZGIgoJCXN0cm9rZT0iIzAwMDAwMCIKCQlmaWxsLW9wYWNpdHk9IjEuMDAwIgoJCXN0cm9rZS1vcGFjaXR5PSIxLjAwMCIKCQlmaWxsLXJ1bGU9Im5vbnplcm8iCgkJc3Ryb2tlLXdpZHRoPSIwLjAiCgkJc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIKCQlzdHJva2UtbGluZWNhcD0ic3F1YXJlIgoJCWQ9Ik01LjU1LDEyLjI1TDEwLjIxLDEyLjAyIi8+Cgk8cGF0aAoJCWZpbGw9IiNGRkZGRkYiCgkJc3Ryb2tlPSIjMDAwMDAwIgoJCWZpbGwtb3BhY2l0eT0iMS4wMDAiCgkJc3Ryb2tlLW9wYWNpdHk9IjEuMDAwIgoJCWZpbGwtcnVsZT0ibm9uemVybyIKCQlzdHJva2Utd2lkdGg9IjAuMCIKCQlzdHJva2UtbGluZWpvaW49InJvdW5kIgoJCXN0cm9rZS1saW5lY2FwPSJzcXVhcmUiCgkJZD0iTTYuNzcsMTAuMTlRNi43NywxNC40NiwxLjUwLDEzLjE4QTguOTIgOS4wMSAzNi4zMSAxIDEgMS41MiwxMy4yM0wxMC4zNSwxMy4wOEEzLjE1IDMuMTIgNTcuMzYgMSAwIDYuNzcsMTAuMTl6Ii8+Cjwvc3ZnPg==';
    add_menu_page( 'QnAChat', 'QnAChat', 'manage_options', 'qnac', 'qnac_callback',  $qnac_icon, 55.1);
    add_submenu_page('', 'Advanced Settings', 'Advanced Settings', 'manage_options', 'qnac_advanced_settings', 'qnac_advanced_settings_callback' );
    add_submenu_page('qnac', 'FAQs Settings', 'FAQs Settings', 'manage_options', 'qnac_faqs', 'qnac_faqs_callback' );
}

function qnac_front_end_enqueue_scripts(){
    wp_enqueue_style( 'qnac_widget', QNAC_URL. 'assets/css/chat_widget.css' , false, date("h:i:s"), 'all' );
    wp_enqueue_script( 'qnac_front_scripts', QNAC_URL. 'assets/js/front_end.js' , array('jquery'), date("h:i:s") );
}
add_action( 'wp_enqueue_scripts', 'qnac_front_end_enqueue_scripts' );


add_action( 'admin_enqueue_scripts', 'qnac_enqueue_by_screen' );
function qnac_enqueue_by_screen( $hook_suffix ) {
    if ( $hook_suffix === 'toplevel_page_qnac' ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'qnac_admin_scripts', QNAC_URL. 'assets/js/back_end.js' , array('jquery'), date("h:i:s") );
        wp_enqueue_script( 'qnac_settings_scripts', QNAC_URL. 'assets/js/settings.js' , array('jquery'), date("h:i:s") );
        wp_enqueue_script('jquery');
        wp_enqueue_style( 'qnac_conversations_styles', QNAC_URL. 'assets/css/conversations.css' , false, date("h:i:s"), 'all' );
        wp_enqueue_style( 'qnac_settings_styles', QNAC_URL. 'assets/css/settings.css' , false, date("h:i:s"), 'all' );
    }
    if ( $hook_suffix === 'admin_page_qnac_advanced_settings' ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'qnac_advanced_settings_scripts', QNAC_URL. 'assets/js/advanced_settings.js' , array('jquery'), date("h:i:s") );
        wp_enqueue_script('jquery');
        wp_enqueue_style( 'qnac_settings_styles', QNAC_URL. 'assets/css/settings.css' , false, date("h:i:s"), 'all' );
        wp_enqueue_media();
    } 
    if ( $hook_suffix === 'qnachat_page_qnac_faqs' ) {
        wp_enqueue_script( 'qnac_faq_set_scripts', QNAC_URL. 'assets/js/faq_set.js' , array('jquery'), date("h:i:s") );
        wp_enqueue_style( 'qnac_faqs_styles', QNAC_URL. 'assets/css/faqs.css' , false, date("h:i:s"), 'all' );
    }     
}

function qnac_advanced_settings_callback(){
    include_once(QNAC_PATH.'includes/advanced_settings.php');
}
function qnac_callback(){
    include_once(QNAC_PATH.'includes/chats_manager.php');
}

function qnac_faqs_callback(){
    include_once(QNAC_PATH.'includes/faqs_settings.php');
}
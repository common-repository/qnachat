<?php

if (!defined('ABSPATH')) {
    exit;
}

function qnac_create_chats_table() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'qnac_chats';

  if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    return;
  }

  $charset_collate = $wpdb->get_charset_collate();
  $sql = "CREATE TABLE $table_name (
    conversation_id VARCHAR(50) NOT NULL,
    logged_in BOOLEAN,
    status VARCHAR(100) NOT NULL,
    data varchar(500) DEFAULT NULL,
    conversation LONGTEXT NOT NULL,
    PRIMARY KEY (conversation_id)
  ) $charset_collate;";
  
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);
}
qnac_create_chats_table();

function qnac_prepare_settings() {
    $default_settings = array(
        'offline' => false,
        'disable' => false,
        'auto-insert' => true,
        'guests' => true,
        'availability' => array(
            'main' => true,
            'time' => '08:00-18:00',
            'days' => 'mon,tue,wed,thu,fri',
            'offline-label' => 'Mon-Sat 8AM-6PM',
        ),
        'theme-color' => '#0094ea',
    );

    $default_adv_settings = array(
        'chat-icon' => QNAC_URL . 'assets/images/qnachat.png',
        'icon-size' => '45',
        'position' => 'r,b',
        'fine-tune-position' => array(
            'x' => '30',
            'y' => '30'
        ),
        'request-info' => array(
            'main' => false,
            'exclude-registered' => true,
            'r-message' => 'Please provide us with your info for further assistance.',
            'r-notice' => 'Kindly provide your information in the form above.',
            'r-info' => 'name,email,phone'
        ),
        'email-notifications' => array(
            'main' => false,
            'email' => get_option('admin_email'),
            'email-noti-guests' => true
        ),
        'enable-faqs' => array(
            'main' => true,
        )        
    );

    $saved_settings = get_option('qnac_settings');
    $saved_adv_settings = get_option('qnac_advanced_settings');

    if (is_array($saved_settings)) {
        $merged_settings = array_merge($default_settings , $saved_settings);
        update_option('qnac_settings', $merged_settings);
    } else {
        update_option('qnac_settings', $default_settings);
    }

    if (is_array($saved_adv_settings)) {
        $merged_adv_settings = array_merge($default_adv_settings, $saved_adv_settings);
        update_option('qnac_advanced_settings', $merged_adv_settings);
    } else {
        update_option('qnac_advanced_settings', $default_adv_settings);
    }
}

qnac_prepare_settings();
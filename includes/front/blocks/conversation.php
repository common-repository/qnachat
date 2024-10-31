<?php
/**
 * Front-end chat widget conversation.
 */

if (!defined('ABSPATH')) {
    exit;
}
$adv_settings = get_option('qnac_advanced_settings');
$require_info = $adv_settings['request-info']['main'];
$exclude_registered = $adv_settings['request-info']['exclude-registered'];

$conversation = unserialize(qnac_get_value_by_conversation_id('conversation', $conversation_id));
if (!empty($conversation) && is_array($conversation)) {
    foreach ($conversation as $key => $message) {
        include(QNAC_PATH . 'includes/front/blocks/single_message.php');
    }
    echo '<div class="reply typing hidden"><span><span></span></span></div>';
} else {
    echo '<div status="freeze" class="reply typing hidden"><span><span></span></span></div>';
}

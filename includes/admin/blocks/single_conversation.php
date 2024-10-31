<?php
/**
 * Admin - Single conversation.
 */

if (!defined('ABSPATH')) {
    exit;
}

$conversation = unserialize(qnac_get_value_by_conversation_id('conversation', $conversation_id));

if (!empty($conversation) && is_array($conversation)) {
    foreach ($conversation as $message) {
        qnac_update_column_by_conversation_id($conversation_id, 'status', 'seen');
        include(QNAC_PATH.'includes/admin/blocks/single_message.php');
    }
}

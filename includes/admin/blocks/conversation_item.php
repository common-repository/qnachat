<?php
/**
 * Admin - conversation item
 */

if (!defined('ABSPATH')) {
    exit;
}

$conversation = unserialize(qnac_get_value_by_conversation_id('conversation', $conversation_id));
$last_message = end($conversation);
$saved_data = unserialize(qnac_get_value_by_conversation_id('data', $conversation_id));
if(isset($saved_data['name'])){
    $saved_name = $saved_data['name'];
}else{
    $saved_name = '';
}
$saved_email = isset($saved_data['email']) ? $saved_data['email'] : '';

$logged_in = qnac_get_value_by_conversation_id('logged_in', $conversation_id);

if ($last_message['by'] == 'request') {
    $time = $last_message['date'];
    if ($last_message['status'] == 'waiting') {
        $message = esc_html__('Info request sent', 'qnachat');
    } elseif ($last_message['status'] == 'provided') {
        $message = esc_html__('The user has submitted info', 'qnachat');
    }
} else {
    $time = $last_message['date'];
    $message = $last_message['message'];
}

if ($logged_in) {
    if (!empty($saved_name)) {
        $name = $saved_name;
        $pic = qnac_get_picname($name);
    } else {
        $user = get_user_by('email', $saved_email);
        if ($user) {
            $name = get_the_author_meta('display_name', $user->ID);
            $pic = qnac_get_picname($name);
        } else {
            $name = esc_html__('Guest', 'qnachat');
            $pic = '<img width="50" src="' . esc_url(QNAC_URL) . 'assets/images/user.png">';
        }
    }
} else {
    if (!empty($saved_name)) {
        $name = $saved_name;
        $pic = qnac_get_picname($name);
    } else {
        $name = esc_html__('Guest', 'qnachat');
        $pic = '<img width="50" src="' . esc_url(QNAC_URL) . 'assets/images/user.png">';
    }
}
?>

<div class="item" value="<?php echo esc_attr($conversation_id); ?>">
    <div class="left">
        <?php echo wp_kses_post($pic); ?>
    </div>
    <div class="center">
        <h5><?php echo esc_html($name); ?><span><?php echo esc_attr(qnac_format_message_sent_time($time)); ?></span></h5>
        <p><?php echo esc_html(stripslashes($message)); ?></p>
    </div>
    <div class="right">
        <?php
        if (qnac_get_value_by_conversation_id('status', $conversation_id) == 'new') {
            echo '<span class="unread">' . esc_html($saved_data['unseen']) . '</span>';
        }
        ?>
    </div>
</div>
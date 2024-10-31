<?php
/**
 * Front-end chat widget.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<input type="text" id="fname" name="fname">
<?php
$conversation_id = QNAC_CONV_ID;
$conversation = unserialize(qnac_get_value_by_conversation_id('conversation', $conversation_id));
if (is_array($conversation)) {
    $fc = '15000';
    $count = count($conversation);
} else {
    $fc = '';
    $count ='0';
}
$settings = get_option('qnac_settings');
$adv_settings = get_option('qnac_advanced_settings');
$color = get_option('qnac_settings')['theme-color'];
$today = strtolower(date('D'));
$saved_days = explode(',', $settings['availability']['days']);
$set_offline = $settings['offline'];
if ($set_offline) {
    $status = '';
} elseif (in_array($today, $saved_days)) {
    $available = qnac_time_availability($settings['availability']['time']);
    if ($available) {
        $status = esc_html__('Online', 'qnachat');
    } else {
        $status = $settings['availability']['offline-label'];
    }
} else {
    $status = $settings['availability']['offline-label'];
}

if (empty($color)) {
    $color = '#2196F3';
}
if (empty($adv_settings['chat-icon'])) {
    $chat_icon = QNAC_URL . 'assets/images/bot.png';
} else {
    $chat_icon = $adv_settings['chat-icon'];
}
if (empty($adv_settings['icon-size'])) {
    $icon_size = '40';
} else {
    $icon_size = $adv_settings['icon-size'];
}

$position = explode(',', $adv_settings['position']);
$horiz = ($position['0'] == 'l') ? 'left' : 'right';
$verti = ($position['1'] == 't') ? 'top' : 'bottom';

$ft_position = $adv_settings['fine-tune-position'];
$ftx = (!empty($ft_position['x'])) ? $ft_position['x'] : '30';
$fty = (!empty($ft_position['y'])) ? $ft_position['y'] : '30';
?>
<style>
.qnac-open-chat{
    <?php echo esc_attr($horiz); ?>: <?php echo esc_attr($ftx); ?>px;
    <?php echo esc_attr($verti); ?>: <?php echo esc_attr($fty); ?>px;
}

.qnac-container .qnac-submit-info button{
    background-color:<?php echo esc_attr($color); ?> !important;
}
.qnac-container .request button, .qnac-container .inner-content .qnac-head, .qnac-chats .user-input, .qnac-bottom a, .qnac-bottom .tags>*:hover, .qnac-container .submit-details button{
    background-color:<?php echo esc_attr($color); ?> !important;
    border-color:<?php echo esc_attr($color); ?> !important;
}
.qnac-chats .user-input.tag {
    background-color: <?php echo esc_attr($color); ?>0f !important;
    color:<?php echo esc_attr($color); ?> !important;
}
.qnac-chats .reply.question-item {
    color: <?php echo esc_attr($color); ?> !important;
}
.qnac-container .reply.question-item::before {
    background-color: <?php echo esc_attr($color); ?>5c !important;
}
</style>
<div class="qnac-open-chat" fc="<?php echo esc_attr($fc); ?>">
    <img width="<?php echo esc_attr($icon_size); ?>" src="<?php echo esc_url($chat_icon); ?>">
</div>
<div class="qnac-container" id="<?php echo esc_attr($conversation_id); ?>" c="<?php echo absint($count); ?>">
    <div class="inner-content">
        <div class="qnac-head">
            <div class="left">
                <img width="40" src="<?php echo esc_url($chat_icon); ?>">
                <div>
                    <h4 class="n-message"><?php echo esc_html__('Welcome', 'qnachat'); ?></h4>
                    <p><?php echo esc_attr($status); ?></p>
                </div>
            </div>
            <div class="right">
                <div class="qnac-close">×</div>
            </div>
        </div>
        <div class="qnac-chats">
            <?php include(QNAC_PATH . 'includes/front/blocks/conversation.php'); ?>
        </div>
        <div class="qnac-bottom">
            <?php 
            $faqs = get_option('qnac_faq_sets');
            if(is_array($faqs) && count($faqs) > 0 && $adv_settings['enable-faqs']){
                ?>
                <div class="tags">
                    <?php
                    $keys = array_keys($faqs);
                    foreach ($faqs as $tag => $value) {
                        if(is_array($value) && count($value) > 0){
                            echo '<div value="' . esc_attr(array_search($tag, $keys)) . '">' . esc_html($tag) . '</div>';
                        }
                    }
                    ?>
                </div> 
                <?php
            }
            ?>
            <div class="input">
                <textarea class="qnac-input-field" placeholder="<?php echo esc_attr__('Type your message...', 'qnachat'); ?>"></textarea>
                <a><?php echo esc_html__('Send', 'qnachat'); ?></a>
            </div>
        </div>
    </div>
</div>
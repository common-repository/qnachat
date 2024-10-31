<?php
/**
 * Admin - request message.
 */

if (!defined('ABSPATH')) {
    exit;
}

if ($message['by'] == 'user') {
    echo '<div class="user-input">' . esc_html(stripslashes($message['message'])) . '</div>';
} elseif ($message['by'] == 'admin') {
    echo '<div class="reply">' . esc_html(stripslashes($message['message'])) . '</div>';
} elseif (is_array($message)) {
    $status = $message['status'];
    ?>
    <div class="request reply">
        <div class="items">
            <?php
            if ($status == 'waiting') {
                ?><h5><?php echo esc_html__('Info request sent', 'qnachat'); ?><span><?php echo esc_attr($status); ?></span></h5><?php
                foreach ($message['data'] as $key => $value) {
                    if ($key == 'phone') {
                        echo '<p>'.esc_html__('phone number', 'qnachat').'</p>';
                    } else {
                        echo '<p>' . esc_html($key) . '</p>';
                    }
                }
            } elseif ($status == 'provided') {
                ?><h5><?php echo esc_html__('User submitted info', 'qnachat'); ?><span><?php echo esc_attr($status); ?></span></h5><?php
                foreach ($message['data'] as $key => $value) {
                    if ($key == 'phone') {
                        echo '<p><b>'. esc_html(__('phone number', 'qnachat')).'</b> : '. esc_html($value) .'</p>';
                    } else {
                        echo '<p><b>' . esc_html($key).'</b> : '. esc_html($value).'</p>';
                    }
                }
            }
            ?>
        </div>
    </div>
<?php
}

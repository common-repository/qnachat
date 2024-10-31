<?php
/**
 * Front-end chat widget single message.
 */

if (!defined('ABSPATH')) {
    exit;
}

if ($message['by'] == 'request') {
    if($message['status'] == 'waiting'){
        ?>
        <div class="request" data-value="<?php echo esc_attr($key); ?>">
            <p><?php echo esc_html($message['message']); ?></p>
            <?php
            foreach ($message['data'] as $input => $attribute) {
                if ($attribute == 'r') {
                    $attribute = 'required';
                } else {
                    $attribute = '';
                }
                if ($input == 'name') {
                    echo '<input class="name" type="text" name="name" placeholder="'. esc_html__('Full name', 'qnachat').'" ' . esc_attr($attribute) . '>';
                } elseif ($input == 'email') {
                    echo '<input class="email" type="text" name="email" placeholder="'. esc_html__('Email', 'qnachat').'" ' . esc_attr($attribute) . '>';
                } elseif ($input == 'phone') {
                    echo '<input class="phone" type="text" name="phone" placeholder="'. esc_html__('Phone number', 'qnachat').' " ' . esc_attr($attribute) . '>';
                }
            }
            ?>
            <button><?php echo esc_html__('Submit', 'qnachat'); ?></button>
        </div>
    <?php   
    }elseif($message['status'] == 'provided'){
        ?>
        <div class="reply request-provided">
            <p><?php echo esc_html__('We have received your info.', 'qnachat'); ?></p>
            <div class="info">
            <?php 
            foreach($message['data'] as $key => $value){
                if(!empty($value)){
                    if($key == 'phone'){
                        echo '<p><b>'.esc_html__('phone number', 'qnachat').'</b> : '.esc_attr($value).'</p>'; 
                    }else{
                        echo '<p><b>'.esc_attr($key).'</b> : '.esc_attr($value).'</p>';
                    }
                }
            }
            ?>
            </div>
        </div>
        <?php
    }
} elseif ($message['by'] == 'user') {
    echo '<div class="user-input">' . stripslashes($message['message']) . '</div>';
} elseif ($message['by'] == 'admin') {
    echo '<div class="reply">' . stripslashes($message['message']) . '</div>';
}
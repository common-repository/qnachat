<?php 
/**
 * Advanced settings page.
 */

if (!defined('ABSPATH')) {
    exit;
}

function qnac_chats_get_check_status($value) {
    return $value === true ? 'checked' : '';
}
$adv_settings = get_option('qnac_advanced_settings');

?>

<div class="section settings-container advanced-sets">
    <h5><?php echo esc_html__('Advanced Settings', 'qnachat'); ?></h5>
    <div class="set" value="chat-icon">
        <div>
            <h6><?php echo esc_html__('Chat Widget Icon', 'qnachat'); ?></h6>
            <p><?php echo esc_html__('Select a custom chat bubble icon.', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-img">
                <input class="set-value" type="text" value="<?php echo esc_attr($adv_settings['chat-icon']); ?>" hidden>
                <img class="select-chat-icon" width="40" src="<?php echo esc_attr($adv_settings['chat-icon']); ?>">
            </div>
        </div>    
    </div>
    <div class="set" value="icon-size">
        <div>
            <h6><?php echo esc_html__('Icon size (px)', 'qnachat'); ?></h6>
        </div>
        <div>
            <div class="qnac-input">
                <input class="set-value" min="0" max="200" type="number" value="<?php echo esc_attr($adv_settings['icon-size']); ?>">
            </div>
        </div>    
    </div>     
    <div class="set" value="position">
        <div>
            <h6><?php echo esc_html__('Chat icon position', 'qnachat'); ?></h6>
        </div>
        <div>
            <div class="qnac-select position">
                <input class="set-value" type="text" value="<?php echo esc_attr($adv_settings['position']); ?>" hidden>
                <?php
                $positions = $adv_settings['position'];

                $verticalOptions = array(
                    't' => esc_html__('Top', 'qnachat'),
                    'b' => esc_html__('Bottom', 'qnachat')
                );

                $horizontalOptions = array(
                    'l' => esc_html__('Left', 'qnachat'),
                    'r' => esc_html__('Right', 'qnachat')
                );

                $explodedValues = explode(',', $positions);

                echo '<div class="horizontal">';
                foreach ($horizontalOptions as $key => $label) {
                    $isActive = in_array($key, $explodedValues) ? ' active' : '';
                    echo '<span class="item' . esc_attr($isActive) . '" data-value="' . esc_attr($key) . '">' . esc_attr($label) . '</span>';
                }
                echo '</div>';
                
                echo '<div class="vertical">';
                foreach ($verticalOptions as $key => $label) {
                    $isActive = in_array($key, $explodedValues) ? ' active' : '';
                    echo '<span class="item' . esc_attr($isActive) . '" data-value="' . esc_attr($key) . '">' . esc_attr($label) . '</span>';
                }
                echo '</div>';                
                ?>
            </div>
        </div>    
    </div> 
    <div class="set multiple" value="fine-tune-position">
        <div>
            <h6><?php echo esc_html__('Fine-tune position (px)', 'qnachat'); ?></h6>
        </div>
        <div>
            <div class="qnac-input">
                <div class="vertical">
                    <span class=""><?php echo esc_html__('X', 'qnachat'); ?></span>
                    <input key="x" class="input" min="0" max="500" type="number" value="<?php echo esc_attr($adv_settings['fine-tune-position']['x']); ?>">
                </div>
                <div class="horizontal">
                    <span class=""><?php echo esc_html__('Y', 'qnachat'); ?></span>
                    <input key="y" class="input" min="0" max="500" type="number" value="<?php echo esc_attr($adv_settings['fine-tune-position']['y']); ?>">
                </div>                
            </div>
        </div>    
    </div>     
    <div class="set <?php echo esc_attr(qnac_chats_get_check_status($adv_settings['request-info']['main'])); ?>" value="request-info">
        <div>
            <h6><?php echo esc_html__('Auto Request Info', 'qnachat'); ?></h6>
            <p><?php echo esc_html__('Require providing info before starting a conversation.', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-toggle">
                <input class="set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($adv_settings['request-info']['main'])); ?>>
                <div class="knobs">
                    <span></span>
                </div>
                <div class="layer"></div>
            </div>
        </div>
        <div class="sub-set" value="exclude-registered">
            <div>
                <p><?php echo esc_html__('Exclude registered users.', 'qnachat'); ?></p>
            </div>
            <div>
                <input class="sub-set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($adv_settings['request-info']['exclude-registered'])); ?>>
            </div>    
        </div> 
        <div class="sub-set" value="r-message">
            <div>
                <p><?php echo esc_html__('Message', 'qnachat'); ?></p>
            </div>
            <div class="f-space">
                <input class="sub-set-value" type="text" placeholder="<?php echo esc_attr__('Request info message', 'qnachat'); ?>" value="<?php echo esc_attr($adv_settings['request-info']['r-message']); ?>">
            </div>    
        </div> 
        <div class="sub-set" value="r-notice">
            <div>
                <p><?php echo esc_html__('Info required notice', 'qnachat'); ?></p>
            </div>
            <div class="f-space">
                <input class="sub-set-value" type="text" placeholder="<?php echo esc_attr__('Submitting info required notice', 'qnachat'); ?>" value="<?php echo esc_attr($adv_settings['request-info']['r-notice']); ?>">
            </div>    
        </div>         
        <div class="sub-set" value="r-info">
            <div>
                <p><?php echo esc_html__('Info', 'qnachat'); ?></p>
            </div>
            <div class="r-info qnac-select">
                <input class="sub-set-value r-info" type="text" value="<?php echo esc_attr($adv_settings['request-info']['r-info']); ?>" hidden>
                <?php 
                $r_info = $adv_settings['request-info']['r-info'];
                if(empty($r_info)){
                    echo '<span class="item" data-value="name">' . esc_html__('Name', 'qnachat') . '</span><span class="active item" data-value="email">' . esc_html__('Email', 'qnachat') . '</span><span class="active item" data-value="phone">' . esc_html__('Phone Number', 'qnachat') . '</span>';
                }else{
                    $info_array = explode(',', $r_info);

                    $info_values = array(
                        'name' => esc_html__('Name', 'qnachat'),
                        'email' => esc_html__('Email', 'qnachat'),
                        'phone' => esc_html__('Phone Number', 'qnachat')
                    );

                    foreach ($info_values as $key => $label) {
                        if (in_array($key, $info_array)) {
                            echo '<span class="item active" data-value="' . esc_attr($key) . '">' . esc_attr($label) . '</span>';
                        } else {
                            echo '<span class="item" data-value="' . esc_attr($key) . '">' . esc_attr($label) . '</span>';
                        }
                    }
                }
                ?>
                
            </div>    
        </div>        
    </div>
    <div class="set <?php echo esc_attr(qnac_chats_get_check_status($adv_settings['email-notifications']['main'])); ?>" value="email-notifications">
        <div>
            <h6><?php echo esc_html__('Enable E-mail notifications', 'qnachat'); ?></h6>
            <p><?php echo esc_html__('Send E-mail notifications for new conversations', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-toggle">
                <input class="set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($adv_settings['email-notifications']['main'])); ?>>
                <div class="knobs">
                    <span></span>
                </div>
                <div class="layer"></div>
            </div>
        </div>
        <div class="sub-set" value="email">
            <div>
                <p><?php echo esc_html__('E-mail', 'qnachat'); ?></p>
            </div>
            <div class="email">
                <input class="sub-set-value" type="text" placeholder="<?php echo esc_attr__('Enter the recipient E-mail', 'qnachat'); ?>" value="<?php echo esc_attr($adv_settings['email-notifications']['email']) ?>">
            </div>    
        </div>        
        <div class="sub-set" value="email-noti-guests">
            <div>
                <p><?php echo esc_html__('Disable for unregistered users', 'qnachat'); ?></p>
            </div>
            <div>
                <input class="sub-set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($adv_settings['email-notifications']['email-noti-guests'])); ?>>
            </div>    
        </div>        
    </div> 
    <div class="set <?php echo esc_attr(qnac_chats_get_check_status($adv_settings['enable-faqs']['main'])); ?>" value="enable-faqs">
        <div>
            <h6><?php echo esc_html__('Enable FAQ Tags', 'qnachat'); ?></h6>
            <p><?php echo esc_html__('Display FAQ tags in the chat widget.', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-toggle">
                <input class="set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($adv_settings['enable-faqs'])); ?>>
                <div class="knobs">
                    <span></span>
                </div>
                <div class="layer"></div>
            </div>
        </div>
    </div>     
    <div class="sets-actions">
        <span class="adv-settings-save"><?php echo esc_html__('Save', 'qnachat'); ?></span>
    </div>
</div>

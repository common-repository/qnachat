<?php 
/**
 * Settings page.
 */

if (!defined('ABSPATH')) {
    exit;
}

function qnac_get_days_html(){
    $settings = get_option('qnac_settings');

    $days = explode(',',$settings['availability']['days']);
    ob_start();
    $totalDays = array('sun','mon','tue','wed','thu','fri','sat');
    foreach($totalDays as $day){
        $firstLetter = strtoupper(substr($day, 0, 1));
        if(in_array($day,$days)){
            echo '<span data-value="'.esc_attr($day).'" class="active">'.esc_html($firstLetter).'</span>';
        }else{
            echo '<span data-value="'.esc_attr($day).'">'.esc_html($firstLetter).'</span>';
        }
    }
    return ob_get_clean();
}
function qnac_chats_get_check_status($value){
    if ($value === false) {
        return '';
    }
    if ($value === true) {
        return 'checked';
    }        
}
$settings = get_option('qnac_settings');
?>
<div class="section settings-container general-sets">
    <div class="sets-actions">
        <span class="close">×</span>
        <span class="save"><?php esc_html_e('Save and close', 'qnachat'); ?></span>
    </div>
    <h5><?php esc_html_e('General Settings', 'qnachat'); ?></h5>
    <div class="set" value="offline">
        <div>
            <h6><?php esc_html_e('Offline mode', 'qnachat'); ?></h6>
            <p><?php esc_html_e('Hide the \'Online\' status from the chat widget.', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-toggle">
                <input class="set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($settings['offline'])); ?>>
                <div class="knobs">
                    <span></span>
                </div>
                <div class="layer"></div>
            </div>
        </div>    
    </div>
    <div class="set" value="disable">
        <div>
            <h6><?php esc_html_e('Disable the chat widget', 'qnachat'); ?></h6>
            <p><?php esc_html_e('Temporarily hide the chat widget', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-toggle">
                <input class="set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($settings['disable'])); ?>>
                <div class="knobs">
                    <span></span>
                </div>
                <div class="layer"></div>
            </div>
        </div>    
    </div> 
    <div class="set" value="auto-insert">
        <div>
            <h6><?php esc_html_e('Auto user info insertion', 'qnachat'); ?></h6>
            <p><?php esc_html_e('Auto update user info with submitted data.', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-toggle">
                <input class="set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($settings['auto-insert'])); ?>>
                <div class="knobs">
                    <span></span>
                </div>
                <div class="layer"></div>
            </div>
        </div>    
    </div>    
    <div class="set <?php echo esc_attr(qnac_chats_get_check_status($settings['guests'])); ?>" value="guests">
        <div>
            <h6><?php esc_html_e('Enable for Guests', 'qnachat'); ?></h6>
            <p><?php esc_html_e('Allow unregistered users to use the live chat.', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-toggle">
                <input class="set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($settings['guests'])); ?>>
                <div class="knobs">
                    <span></span>
                </div>
                <div class="layer"></div>
            </div>
        </div>
    </div>
    <div class="set <?php echo esc_attr(qnac_chats_get_check_status($settings['availability']['main'])); ?>" value="availability">
        <div>
            <h6><?php esc_html_e('Set availability', 'qnachat'); ?></h6>
            <p><?php esc_html_e('Define the schedule for displaying the \'Online\' status', 'qnachat'); ?></p>
        </div>
        <div>
            <div class="qnac-toggle">
                <input class="set-value" type="checkbox" <?php echo esc_attr(qnac_chats_get_check_status($settings['availability']['main'])); ?>>
                <div class="knobs">
                    <span></span>
                </div>
                <div class="layer"></div>
            </div>
        </div>        
        <div class="sub-set" value="time">
            <div>
                <p><?php esc_html_e('Time', 'qnachat'); ?></p>
            </div>
            <div class="time">
                <input class="sub-set-value" type="text" placeholder="08:00-18:00" value="<?php echo esc_attr($settings['availability']['time']) ?>">
            </div>    
        </div>
        <div class="sub-set" value="days">
            <div>
                <p><?php esc_html_e('Days', 'qnachat'); ?></p>
            </div>
            <div class="days">
                <input class="sub-set-value" type="text" value="<?php echo esc_attr($settings['availability']['days']) ?>" hidden>                
                <?php echo wp_kses_post(qnac_get_days_html()); ?>
            </div>    
        </div>
        <div class="sub-set" value="offline-label">
            <div>
                <p><?php esc_html_e('Offline label', 'qnachat'); ?></p>
            </div>
            <div class="avail-label">
                <input class="sub-set-value" type="text" placeholder="Mon-Fri 08AM-6PM" value="<?php echo esc_attr($settings['availability']['offline-label']) ?>">
            </div>    
        </div>        
    </div>
    <div class="set color-picker" value="theme-color">
        <div>
            <h6><?php esc_html_e('Chat widget theme color', 'qnachat'); ?></h6>
        </div>
        <div class="color">
            <input class="color-picker-input set-value" value="<?php echo esc_attr($settings['theme-color']) ?>" hidden>
        </div>        
    </div> 
    <div class="advanced-settings-link">
        <a href="admin.php?page=qnac_advanced_settings" target="_blank"><?php esc_html_e('Advanced Settings', 'qnachat'); ?> →</a>
    </div>
</div>

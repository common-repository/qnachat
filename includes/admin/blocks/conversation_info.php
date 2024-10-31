<?php
/**
 * Admin - conversations info.
 */

if (!defined('ABSPATH')) {
    exit;
}

$conv_data = unserialize(qnac_get_value_by_conversation_id('data', $conversation_id));
$tags = isset($conv_data['tags']) ? stripslashes($conv_data['tags']) : '';
$saved_name = isset($conv_data['name']) ? $conv_data['name'] : '';
$saved_email = isset($conv_data['email']) ? $conv_data['email'] : '';
$saved_phone = isset($conv_data['phone']) ? $conv_data['phone'] : '';
$note = isset($conv_data['note']) ? $conv_data['note'] : '';
$logged_in = qnac_get_value_by_conversation_id('logged_in', $conversation_id);

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

$last_seens = get_option('qnac_user_last_seen');
if (is_array($last_seens)) {
    $last_seen = $last_seens[$conversation_id];
    if (empty($last_seen)) {
        $last_seen = esc_html__('Offline', 'qnachat');
    } elseif (qnac_format_message_sent_time($last_seen) == esc_html__('Just now', 'qnachat')) {
        $last_seen = esc_html__('Online', 'qnachat');
    } else {
        $last_seen = qnac_format_message_sent_time($last_seen);
    }
} else {
    $last_seen = esc_html__('Offline', 'qnachat');
}

update_option('qnac_user_last_seen', $last_seens);
?>

<div class="top">
    <div class="user-per">
        <?php echo wp_kses_post($pic); ?>
        <h4><?php echo esc_html($name); ?></h4>
        <p class="last-seen"><?php echo esc_html($last_seen); ?></p>
    </div>
    <div class="per-info">
        <h4><?php esc_html_e('Personal Info', 'qnachat'); ?></h4>
        <div class="contact-items">
            <div class="item phone">
                <img width="20" src="<?php echo esc_url(QNAC_URL . 'assets/images/phone.svg'); ?>">
                <?php
                $saved_phone = preg_replace('/[^+\-\d]/', '', $saved_phone);
                if (empty($saved_phone)) {
                    echo '<a><span>' . esc_html__('Add phone number', 'qnachat') . '</span></a>';
                } else {
                    echo '<a href="tel:' . esc_attr($saved_phone) . '">' . esc_html($saved_phone) . '</a>';
                }
                ?>
                <input class="phone" placeholder="<?php esc_attr_e('Tel', 'qnachat'); ?>" value="<?php echo esc_attr(preg_replace('/[^+\-\d]/', '', $saved_phone)); ?>" type="tel">
                <div class="action">
                    <img class="edit" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/edit.svg'); ?>">
                    <img class="action confirm" value="phone" action="s" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/done.svg'); ?>">
                </div>
            </div>
            <div class="item email">
                <img width="20" src="<?php echo esc_url(QNAC_URL . 'assets/images/email.svg'); ?>">
                <?php
                if (empty($saved_email)) {
                    echo '<a><span>' . esc_html__('Add E-mail', 'qnachat') . '</span></a>';
                } else {
                    echo '<a href="mailto:' . esc_html($saved_email) . '">' . esc_html($saved_email) . '</a>';
                }
                ?>
                <input class="email" placeholder="<?php esc_attr_e('Email', 'qnachat'); ?>" value="<?php echo esc_attr(sanitize_email($saved_email)); ?>" type="email">
                <div class="action">
                    <img class="edit" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/edit.svg'); ?>">
                    <img class="action confirm" value="email" action="s" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/done.svg'); ?>">
                </div>
            </div>
            <div class="item request">
                <img width="20" src="<?php echo esc_url(QNAC_URL . 'assets/images/request.png'); ?>"><span><?php esc_html_e('Request info', 'qnachat'); ?></span>
            </div>
        </div>
    </div>
    <div class="tags">
        <div class="tag">
            <div class="items">
                <img width="20" src="<?php echo esc_url(QNAC_URL . 'assets/images/tag-1.svg'); ?>">
                <?php
                if (!empty($tags)) {
                    $tags_array = explode(',', $tags);
                    foreach ($tags_array as $tag) {
                        echo '<p>' . esc_html($tag) . '</p>';
                    }
                } else {
                    echo '<h5 class="edit">' . esc_html__('Add a tag', 'qnachat') . '</h5>';
                }
                ?>
                <img class="edit" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/edit.svg'); ?>">
                <img class="action confirm" action="s" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/done.svg'); ?>">
            </div>
            <div class="tag-edit">
                <p><?php esc_html_e('Separate tags by a comma', 'qnachat'); ?></p>
                <textarea class="t-field" value="<?php echo esc_attr($tags) ?>"><?php echo esc_textarea($tags) ?></textarea>
            </div>
        </div>
    </div>
    <div class="notes">
        <?php
        if (!empty($conv_data['note'])) {
            ?>
            <div class="note active">
                <img width="20" src="<?php echo esc_url(QNAC_URL . 'assets/images/note.svg'); ?>"><a><?php esc_html_e('Add a note', 'qnachat'); ?></a>
            </div>
            <div class="note-content">
                <div class="top">
                    <h5><?php esc_html_e('Note', 'qnachat'); ?></h5>
                    <div class="note-actions">
                        <img class="action delete" action="d" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/cross.svg'); ?>">
                        <img class="edit" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/edit.svg'); ?>">
                        <img class="action confirm" action="s" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/done.svg'); ?>">
                    </div>
                </div>
                <div class="bottom">
                    <p><?php echo esc_textarea(stripslashes($note)); ?></p>
                    <textarea class="n-field" value="<?php echo esc_attr(stripslashes($note)); ?>"><?php echo esc_textarea($note); ?></textarea>
                </div>
            </div>
        <?php
        } else {
            ?>
            <div class="note waiting">
                <img width="20" src="<?php echo esc_url(QNAC_URL . 'assets/images/note.svg'); ?>"><a><?php esc_html_e('Add a note', 'qnachat'); ?></a>
            </div>
            <div class="note-content waiting">
                <div class="top">
                    <h5><?php esc_html_e('Note', 'qnachat'); ?></h5>
                    <div class="note-actions">
                        <img class="action delete" action="d" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/cross.svg'); ?>">
                        <img class="edit" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/edit.svg'); ?>">
                        <img class="action confirm" action="s" width="15" src="<?php echo esc_url(QNAC_URL . 'assets/images/done.svg'); ?>">
                    </div>
                </div>
                <div class="bottom">
                    <p></p>
                    <textarea rows="4" class="n-field" value=""></textarea>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="actions">
        <?php 
        $spams = get_option('qnac_spams');
        if(is_array($spams)){
           if(in_array($conversation_id, $spams)){
                ?><a class="block" action="u"><?php esc_html_e('Unblock this user', 'qnachat'); ?></a><?php
           }else{
                ?><a class="block" action="b"><?php esc_html_e('Block this user', 'qnachat'); ?></a><?php
           } 
        }else{
            ?><a class="block" action="b"><?php esc_html_e('Block this user', 'qnachat'); ?></a><?php
        } 
        ?>
        <a class="delete" action="d"><?php esc_html_e('Delete this chat', 'qnachat'); ?></a>
    </div>
</div>

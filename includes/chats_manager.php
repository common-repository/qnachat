<?php
/**
 * QnAChat Conversations Page.
 */

if (!defined('ABSPATH')) {
    exit;
}

include_once( QNAC_PATH . 'includes/admin/custom_fonts.php' );
$settings = get_option( 'qnac_settings' );
$mute = ( $settings['mute'] == 1 ) ? 'active' : '';
?>
<div class="qnac-manager-admin">
    <div class="cg-head">
        <div class="left"><img width="35" src="<?php echo esc_url( QNAC_URL ); ?>assets/images/qnachat.png"> <?php esc_html_e( 'QnAChat', 'qnachat' ); ?></div>
        <div class="right">
            <audio id="notification-sound" src="<?php echo esc_url( QNAC_URL ); ?>assets/sounds/qnac_notice.mp3";></audio>
            <div class="qnac-notice cg-hide"><img width="20" src="<?php echo esc_url( QNAC_URL ); ?>assets/images/no-wifi.png"> <?php esc_html_e( 'No internet connection', 'qnachat' ); ?></div>
            <div class="actions-buttons">
                <div class="item mute <?php echo esc_attr( $mute ) ?>">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path d="M9 17V18C9 18.394 9.0776 18.7841 9.22836 19.1481C9.37913 19.512 9.6001 19.8427 9.87868 20.1213C10.1573 20.3999 10.488 20.6209 10.8519 20.7716C11.2159 20.9224 11.606 21 12 21C12.394 21 12.7841 20.9224 13.1481 20.7716C13.512 20.6209 13.8427 20.3999 14.1213 20.1213C14.3999 19.8427 14.6209 19.512 14.7716 19.1481C14.9224 18.7841 15 18.394 15 18V17M18 9C18 12 20 17 20 17H4C4 17 6 13 6 9C6 5.732 8.732 3 12 3C15.268 3 18 5.732 18 9Z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </g>
                    </svg>
                </div>
                <a href="admin.php?page=qnac_faqs" target="_blank" class="item faqs btn"><?php esc_html_e( 'FAQs', 'qnachat' ); ?></a>
                <div class="item settings btn"><?php esc_html_e( 'Settings', 'qnachat' ); ?></div>
            </div>
        </div>
    </div>
    <div class="left">
        <div class="bottom">
            <div class="conversations">
                <?php
                delete_option( 'qnac_cu' );
                $conversation_ids = qnac_get_all_conversation_ids();
                if ( ! empty( $conversation_ids ) && is_array( $conversation_ids ) ) {
                    $conversation_order = array();

                    foreach ( $conversation_ids as $conversation_id ) {
                        $conversation       = unserialize( qnac_get_value_by_conversation_id( 'conversation', $conversation_id ) );
                        $last_message      = end( $conversation );
                        if(isset($last_message['request'])){
                            if ( is_array( $last_message['request'] ) ) {
                                $timestamp = strtotime( $last_message['request']['date'] );
                            } else {
                                $timestamp = strtotime( $last_message['date'] );
                            }
                        }else{
                            $timestamp = '';
                        }
                        $conversation_order[ $conversation_id ] = $timestamp;
                    }
                    arsort( $conversation_order );
                    foreach ( $conversation_order as $conversation_id => $time ) {
                        include( QNAC_PATH . 'includes/admin/blocks/conversation_item.php' );
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="center">
        <div class="top"></div>
        <div class="center">
            <div class="conversation-content">
                <?php
                if(empty($conversation_ids) || count($conversation_ids) == 0){
                    ?><h4><?php esc_html_e( 'No chats available. Stay tuned for incoming messages.', 'qnachat' ); ?></h4><?php
                }else{
                    ?><h4><?php esc_html_e( 'Select a conversation', 'qnachat' ); ?></h4><?php
                }
                ?>
            </div>
        </div>
        <div class="bottom cg-hide">
            <div class="input">
                <textarea class="qnac-message" placeholder="<?php esc_attr_e( 'Type your message...', 'qnachat' ); ?>"></textarea>
                <a class="qnac-send"><?php esc_html_e( 'Send', 'qnachat' ); ?></a>
            </div>
        </div>
    </div>
    <div class="right conversation-info">

    </div>
    <div class="qnac-request-info">
        <span class="close">×</span>
        <div class="inner-content">
            <h5><?php esc_html_e( 'Message', 'qnachat' ); ?></h5>
            <textarea placeholder="" value=""><?php esc_html_e( 'Please provide us with your email address for further assistance.', 'qnachat' ); ?></textarea>
            <div class="select">
                <div data-value="name" class="item active">
                    <h5 class="info"><?php esc_html_e( 'Name', 'qnachat' ); ?></h5>
                    <span class="required" data-value="r"><input type="checkbox"> <?php esc_html_e( 'Required', 'qnachat' ); ?></span>
                </div>
                <div data-value="email" class="item active">
                    <h5 class="info"><?php esc_html_e( 'Email', 'qnachat' ); ?></h5>
                    <span class="required" data-value="r"><input type="checkbox" checked> <?php esc_html_e( 'Required', 'qnachat' ); ?></span>
                </div>
                <div data-value="phone" class="item">
                    <h5 class="info"><?php esc_html_e( 'Phone', 'qnachat' ); ?></h5>
                    <span class="required" data-value="r"><input type="checkbox"> <?php esc_html_e( 'Required', 'qnachat' ); ?></span>
                </div>
            </div>
            <h5 class="send-request"><?php esc_html_e( 'Send Request', 'qnachat' ); ?></h5>
        </div>
    </div>
    <div class="settings-container">
        <?php include_once( QNAC_PATH . 'includes/settings.php' ); ?>
    </div>
</div>
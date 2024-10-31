<?php
/**
 * Admin side functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

/*FAQs Settings*/

function qnac_save_faq_settings(){
    if(isset($_REQUEST['S'])){
        $settings = sanitize_text_field(stripslashes($_REQUEST['S']));
        $faqs = json_decode($settings, true);
        if(is_array($faqs)){
            update_option('qnac_faq_sets',$faqs);
        }
    }
    die();
}
add_action('wp_ajax_nopriv_qnac_submit_faq_settings', 'qnac_save_faq_settings');
add_action('wp_ajax_qnac_submit_faq_settings', 'qnac_save_faq_settings');

/*Conversations pages*/

function qnac_admin_nmc(){
    $nm_updates = get_option('qnac_cu');
    if(!is_array($nm_updates)){
        update_option('qnac_cu', array());
    }
    if(isset($_REQUEST['A'])){
        $action = sanitize_text_field($_REQUEST['A']);
        if($action == '1'){
            update_option('qnac_cu', array());
        }
        else{
            if(is_array($nm_updates)){
                if(empty($nm_updates)){
                    echo false;
                }else{
                    $conversations = array();
                    foreach($nm_updates as $conversation_id){
                        
                        ob_start();
                        include(QNAC_PATH.'includes/admin/blocks/conversation_item.php');
                        $last_messages_content = ob_get_clean();
                        
                        $conversations[$conversation_id] = $last_messages_content;
                    }
                    echo wp_json_encode($conversations);
                }
            }else{
                echo false;
            } 
        }
    }    
    die();
}
add_action('wp_ajax_qnac_admin_nmc', 'qnac_admin_nmc');
add_action('wp_ajax_nopriv_qnac_admin_nmc', 'qnac_admin_nmc');

function qnac_admin_get_conversation() {
    if (isset($_REQUEST['id'])) {
        $conversation_id = sanitize_key($_REQUEST['id']);

        if (preg_match('/^[a-f0-9]{13}$/i', $conversation_id)) {
            $conv_data = unserialize(qnac_get_value_by_conversation_id('data', $conversation_id));

            if (is_array($conv_data)) {
                $conv_data['unseen'] = 0;
            } else {
                $conv_data = array('unseen' => 0);
            }

            qnac_update_column_by_conversation_id($conversation_id, 'data', serialize($conv_data));

            ob_start();
            include_once(QNAC_PATH . 'includes/admin/blocks/single_conversation.php');
            $messages = ob_get_clean();

            ob_start();
            include_once(QNAC_PATH . 'includes/admin/blocks/conversation_info.php');
            $conversation_info = ob_get_clean();

            $data = array('M' => $messages, 'I' => $conversation_info, 'C' => count($conversation));
            echo wp_json_encode($data);
        }

        die();
    }
}
add_action('wp_ajax_qnac_admin_get_conversation', 'qnac_admin_get_conversation');

function qnac_get_conversation_items(){
    if(isset($_REQUEST['I']) && isset($_REQUEST['C']) ){
        ob_start();
        $conversation_id = sanitize_key($_REQUEST['I']);
        $conversation = unserialize(qnac_get_value_by_conversation_id('conversation', $conversation_id));
        if(!empty($conversation) && is_array($conversation)){
            $totalCount = count($conversation);
            $lastCount = (int) $_REQUEST['C'];
            $loadCount = $totalCount - $lastCount;
            if($loadCount > 0){
                $loadArray = array_slice($conversation, -$loadCount);
                foreach($loadArray as $message){
                    qnac_update_column_by_conversation_id($conversation_id, 'status','seen');
                    include(QNAC_PATH.'includes/admin/blocks/single_message.php');
                }
            }
        }
        $messages = ob_get_clean();
        $data = array('M'=>$messages,'C' => count($conversation));
        echo wp_json_encode($data);
        die();
    }
}
add_action('wp_ajax_qnac_get_conversation_items', 'qnac_get_conversation_items');

function qnac_mute_setting(){
    if(isset($_REQUEST['S'])){
        $mute = sanitize_text_field($_REQUEST['S']);
        $settings = get_option('qnac_settings');
        if(is_array($settings)){
            $settings['mute'] = $mute;
            update_option('qnac_settings',$settings);
        }
    }
    die();
}
add_action('wp_ajax_nopriv_qnac_mute_setting', 'qnac_mute_setting');
add_action('wp_ajax_qnac_mute_setting', 'qnac_mute_setting');

function qnac_admin_request_info(){
    if(isset($_REQUEST['M']) && isset($_REQUEST['C']) && isset($_REQUEST['S'])){
        $conversation_id = sanitize_key($_REQUEST['C']);
        $message = sanitize_text_field($_REQUEST['M']);
        $info = json_decode(stripslashes($_REQUEST['S']),true);
        qnac_admin_request_handle($conversation_id, $message, $info);
    }
    die();
}
add_action('wp_ajax_nopriv_qnac_admin_request_info', 'qnac_admin_request_info');
add_action('wp_ajax_qnac_admin_request_info', 'qnac_admin_request_info');

function qnac_admin_request_handle($conversation_id, $message, $info){
    $requestID = 'r'.mt_rand(1000, 9999);
    $message = array(
        'status' => 'waiting',
        'date'=>date('Y-m-d H:i:s'),
        'by'=> 'request',
        'message'=>$message,
        'data'=>$info
    );
    qnac_admin_reply($conversation_id, $message, $requestID);
    ob_start();
    include(QNAC_PATH.'includes/admin/blocks/single_message.php');
    echo ob_get_clean();
}

function qnac_conversation_info($conversation_id = '') {
    if(isset($_REQUEST['T'])){
        $conversation_id = sanitize_key($_REQUEST['T']);
    }    
    if(!empty($conversation_id)){
        include_once(QNAC_PATH.'includes/admin/blocks/conversation_info.php');
    }
}

function qnac_admin_ajax_reply(){
    if(isset($_REQUEST['message']) && isset($_REQUEST['id'])){
        $message = sanitize_text_field($_REQUEST['message']);
        $conversation_id = sanitize_text_field($_REQUEST['id']);
        $message_array = array(
            'date'=>date('Y-m-d H:i:s'),
            'by'=> 'admin',
            'message'=>$message,
        );
        qnac_admin_reply($conversation_id, $message_array);
        $inner_msg = '<div class="reply">' .stripslashes($message). '</div>';
        
        ob_start();
        include(QNAC_PATH.'includes/admin/blocks/conversation_item.php');
        $last_messages_content = ob_get_clean();
        
        $response = array('IM'=> $inner_msg ,'LM'=> $last_messages_content);
        echo wp_json_encode($response);
        die();
    }
}

add_action('wp_ajax_nopriv_qnac_admin_reply', 'qnac_admin_ajax_reply');
add_action('wp_ajax_qnac_admin_reply', 'qnac_admin_ajax_reply');

function qnac_admin_manage_chat(){
    if(isset($_REQUEST['A']) && isset($_REQUEST['C'])){
        $action = sanitize_text_field($_REQUEST['A']);
        $conversation_id = sanitize_key($_REQUEST['C']);
        if($action == 'd'){
            $result = qnac_delete_conversation($conversation_id);
            if(!$result){
                echo 'Something went wrong.';
            }
        }elseif($action == 'b'){
            $spams = get_option('qnac_spams');
            if(is_array($spams)){
                $spams[] = $conversation_id;
            }else{
                $spams = array($conversation_id);
            }
            update_option('qnac_spams',$spams);
            $response = array('V'=>'u','T'=>'Unblock this user');
            echo wp_json_encode($response);
        }elseif($action == 'u'){
            $spams = get_option('qnac_spams');
            if(is_array($spams)){
                $key = array_search($conversation_id,$spams);
                if($key !== false){
                    unset($spams[$key]);
                    update_option('qnac_spams',$spams);
                }
            }else{
                $spams = array();
            }
            update_option('qnac_spams',$spams);
            $response = array('V'=>'b','T'=>'Block this user');
            echo wp_json_encode($response);            
        }
    }
    die();
}
add_action('wp_ajax_qnac_admin_manage_chat', 'qnac_admin_manage_chat');
add_action('wp_ajax_nopriv_qnac_admin_manage_chat', 'qnac_admin_manage_chat');

function qnac_admin_note_manage(){
    if(isset($_REQUEST['A']) && isset($_REQUEST['C'])){
        $action = sanitize_text_field($_REQUEST['A']);
        $conversation_id = sanitize_key($_REQUEST['C']);
        $conversationData = unserialize(qnac_get_value_by_conversation_id('data', $conversation_id));
        if($action == 'd'){
            $conversationData['note'] = '';
            qnac_update_column_by_conversation_id($conversation_id, 'data', serialize($conversationData));
        }elseif($action == 's'){
            if(isset($_REQUEST['E'])){
               $note = sanitize_text_field($_REQUEST['E']);
               $conversationData['note'] = $note;
               qnac_update_column_by_conversation_id($conversation_id, 'data', serialize($conversationData));
            }
        }
    }
    die();
}
add_action('wp_ajax_qnac_admin_note_manage', 'qnac_admin_note_manage');
add_action('wp_ajax_nopriv_qnac_admin_note_manage', 'qnac_admin_note_manage');

function qnac_admin_edit_tags(){
    if(isset($_REQUEST['V']) && isset($_REQUEST['C'])){
        $tags = sanitize_text_field($_REQUEST['V']);
        $conversation_id = sanitize_key($_REQUEST['C']);
        $conversationData = unserialize(qnac_get_value_by_conversation_id('data', $conversation_id));
        $conversationData['tags'] = $tags;
        qnac_update_column_by_conversation_id($conversation_id, 'data', serialize($conversationData));
        if(!empty($tags)){
            $tags_array = explode(',',stripslashes($tags));
            foreach($tags_array as $tag){
                echo '<p>'.esc_attr($tag).'</p>';
            }
        }
    }
    die();
}
add_action('wp_ajax_qnac_admin_edit_tags', 'qnac_admin_edit_tags');
add_action('wp_ajax_nopriv_qnac_admin_edit_tags', 'qnac_admin_edit_tags');

function qnac_admin_contacts_manage(){
    if(isset($_REQUEST['T']) && isset($_REQUEST['C']) && isset($_REQUEST['V'])){
        $dataType = sanitize_text_field($_REQUEST['T']);
        $conversation_id = sanitize_key($_REQUEST['C']);
        $conversationData = unserialize(qnac_get_value_by_conversation_id('data', $conversation_id));
        if($dataType == 'phone'){
            $conversationData['phone'] = preg_replace('/[^+\-\d]/', '', sanitize_text_field($_REQUEST['V']));
            qnac_update_column_by_conversation_id($conversation_id, 'data', serialize($conversationData));
        }elseif($dataType == 'email'){
            if(isset($_REQUEST['V'])){
               $email = sanitize_email($_REQUEST['V']);
               $conversationData['email'] = $email;
               qnac_update_column_by_conversation_id($conversation_id, 'data', serialize($conversationData));
            }
        }
    }
    die();
}
add_action('wp_ajax_qnac_admin_contacts_manage', 'qnac_admin_contacts_manage');
add_action('wp_ajax_nopriv_qnac_admin_contacts_manage', 'qnac_admin_contacts_manage');

function qnac_admin_last_seen(){
    if(isset($_REQUEST['C'])){
        $conversation_id = sanitize_text_field($_REQUEST['C']);
        $last_seens = get_option('qnac_user_last_seen');
        if(is_array($last_seens)){
            $last_seen = $last_seens[$conversation_id];
            if(empty($last_seen)){
                $last_seen = 'Offline';
            }elseif(qnac_format_message_sent_time($last_seen) == 'Just now'){
                $last_seen = 'Online';
            }else{
                $last_seen = qnac_format_message_sent_time($last_seen);
            }
        }else{
            $last_seen = 'Offline';
        }
        echo esc_attr($last_seen);
        die();
    }
}

add_action('wp_ajax_nopriv_qnac_admin_last_seen', 'qnac_admin_last_seen');
add_action('wp_ajax_qnac_admin_last_seen', 'qnac_admin_last_seen');

/*General Settings*/

function qnac_save_settings(){
    if(isset($_REQUEST['S'])){
        $settings = json_decode(stripslashes($_REQUEST['S']), true);
        if(is_array($settings)){
            update_option('qnac_settings', $settings);
        }
    }
    die();
}
add_action('wp_ajax_nopriv_qnac_save_settings', 'qnac_save_settings');
add_action('wp_ajax_qnac_save_settings', 'qnac_save_settings');

function qnac_save_advanced_settings(){
    if(isset($_REQUEST['S'])){
        $settings = json_decode(stripslashes($_REQUEST['S']), true);
        if(is_array($settings)){
            update_option('qnac_advanced_settings', $settings);
        }
    }
    die();
}
add_action('wp_ajax_nopriv_qnac_save_advanced_settings', 'qnac_save_advanced_settings');
add_action('wp_ajax_qnac_save_advanced_settings', 'qnac_save_advanced_settings');

/*
* To be removed

$qnac_tags = array(
    'contact' => array('How can I contact your customer support?'=>'You can contact our customer support team by emailing support@example.com or by calling our toll-free number at 1-800-123-4567.'
        ,'What are your customer support hours?' => 'Our customer support team is available Monday to Friday, from 9 AM to 5 PM (local time). We aim to respond to all inquiries within 24 hours.'
        ,'Do you have a live chat option for customer support?' => 'Yes, we offer a live chat option on our website. Simply click on the chat icon in the bottom right corner to initiate a chat with one of our representatives.'
        ,'Can I visit your office in person?' => 'We do not have a physical storefront for customer visits. However, you can reach out to us via email or phone, and we will be happy to assist you.'
    ),
    'marketing' => array('What marketing strategies do you employ?' => 'We utilize various marketing strategies such as social media advertising, email marketing campaigns, search engine optimization (SEO), and influencer partnerships to promote our products/services.'
        ,'How can I stay updated with your latest promotions?' => 'To stay informed about our latest promotions and offers, you can subscribe to our newsletter or follow us on social media platforms like Facebook, Instagram, and Twitter.'
        ,'Do you offer any discounts for first-time customers?' => 'Yes, we have special discounts and offers for first-time customers. You can check our website or contact our customer support for more information.'
        , 'Can I advertise my business on your platform?' => 'We have advertising opportunities available for businesses interested in promoting their products or services. Please reach out to our marketing team for further details.'
    ),
    'shipping' => array('How long does shipping typically take?' => 'Shipping times can vary depending on the destination and shipping method chosen. Generally, standard shipping within the country takes 3-5 business days, while international shipping can take anywhere from 7-21 business days.'
        ,'Do you offer express shipping options?' => 'Yes, we offer express shipping options for customers who require faster delivery. With our express shipping service, you can expect to receive your order within 1-3 business days.'
        ,'How much does shipping cost?' => 'Shipping costs are calculated based on various factors such as the weight and dimensions of the package, the shipping destination, and the selected shipping method. During the checkout process, you will be able to see the shipping cost before finalizing your order.'
        ,'Can I track my shipment?' => 'Yes, we provide order tracking for most shipments. Once your order is processed and shipped, you will receive a tracking number via email. You can use this tracking number to monitor the progress of your shipment online.'
    )
);
*/
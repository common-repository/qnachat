<?php
/**
 * QnAChat Functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (isset($_COOKIE['qnac_id'])) {
    $conversation_id = sanitize_key($_COOKIE['qnac_id']);
    setcookie('qnac_id', $conversation_id, time() + (2 * 24 * 60 * 60), '/');
} else {
    $conversation_id = sanitize_key(uniqid());
    setcookie('qnac_id', $conversation_id, time() + (2 * 24 * 60 * 60), '/');
}

if (preg_match('/^[a-f0-9]{13}$/i', $conversation_id)) {
    $_SESSION['qnac_id'] = $conversation_id;
    define('QNAC_CONV_ID', $conversation_id);
} else {
    unset($_SESSION['qnac_id']);
    define('QNAC_CONV_ID', '');
}

function qnac_init() {
    $qnac_settings = get_option('qnac_settings');
    $spams = get_option('qnac_spams');
    if(!is_array($spams)){
       $spams = array();
    }
    if(!in_array(QNAC_CONV_ID,$spams)){
        if(!$qnac_settings['disable']){
            if($qnac_settings['guests']){
                add_action( 'wp_footer', 'qnac_chatform_append' );
            }else{
                if (is_user_logged_in()) {
                    add_action( 'wp_footer', 'qnac_chatform_append' );
                }
            }
        }
    }
}
add_action('init', 'qnac_init');

function qnac_chatform_append() {
    include_once( QNAC_PATH . 'includes/front/chat_widget.php' );
}

function qnac_get_tag_data(){
    if(isset($_REQUEST['tag'])){
        $index = sanitize_text_field($_REQUEST['tag']);
        $tags_values = array_values(get_option('qnac_faq_sets'));
        $qaArray = $tags_values[$index];
        $keys = array_keys($qaArray);
        foreach($keys as $Qindex=>$q){
            echo '<div class="reply question-item" tag="'.esc_attr($index).'" value="'.esc_attr($Qindex).'">'.esc_attr($q).'</div>';
        }
        die();
    }
}

add_action('wp_ajax_nopriv_qnac_get_tag_data', 'qnac_get_tag_data');
add_action('wp_ajax_qnac_get_tag_data', 'qnac_get_tag_data');

function qnac_get_tag_qa(){
    if(isset($_REQUEST['tag']) && isset($_REQUEST['index'])){
        $tag = sanitize_text_field($_REQUEST['tag']);
        $index = absint($_REQUEST['index']);
        $tag_values = array_values(get_option('qnac_faq_sets'))[$tag];
        $qa_values = array_values($tag_values);
        echo '<div class="reply">'.esc_attr($qa_values[$index]).'</div>';
    }
    die();
}
add_action('wp_ajax_nopriv_qnac_get_tag_qa', 'qnac_get_tag_qa');
add_action('wp_ajax_qnac_get_tag_qa', 'qnac_get_tag_qa');

function qnac_handel_user_message($message, $conversation_id){
    $spams = get_option('qnac_spams');
    if(!is_array($spams)){
       $spams = array();
    }
    if(!in_array($conversation_id,$spams)){
        $message_array = array(
            'date'=>date('Y-m-d H:i:s'),
            'by'=> 'user',
            'message'=>$message,
            'seen'=> false,
        );
        qnac_add_new_conversation('new', $message_array);
        $response = '<div class="user-input">' .esc_html(stripslashes($message)). '</div>';
        $conv_update = get_option('qnac_cu');
        if(is_array($conv_update)){
            $conv_update[] = $conversation_id;
            update_option('qnac_cu', $conv_update); 
        }else{
           $conv_update = array($conversation_id); 
           update_option('qnac_cu', $conv_update); 
        }       
    }else{
        $response = '<p class="error">Something went wrong!</p>';
    }
    return $response;
}

function qnac_user_auto_request_info($conversation_id, $message, $info){
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
    $key = $requestID;
    include(QNAC_PATH.'includes/front/blocks/single_message.php');
    return ob_get_clean();
}

function qnac_add_converstion_item(){
    $adv_settings = get_option('qnac_advanced_settings');
    $require_info = $adv_settings['request-info']['main'];
    $email_noti = $adv_settings['email-notifications']['email'];
    $exclude_registered = $adv_settings['request-info']['exclude-registered'];
    $r_message = $adv_settings['request-info']['r-message'];
    $r_notice = $adv_settings['request-info']['r-notice'];
    $info = explode(',',$adv_settings['request-info']['r-info']);
    $r_info = array();
    foreach ($info as $key) {
        $r_info[$key] = 'r';
    }
    if(isset($_REQUEST['message']) && isset($_REQUEST['id']) && !empty($_REQUEST['message'])){
        $message = sanitize_text_field($_REQUEST['message']);
        $conversation_id = sanitize_text_field($_REQUEST['id']);
        $conversation = unserialize(qnac_get_value_by_conversation_id('conversation', $conversation_id));
        $response = array();
        if($require_info){
            if (is_user_logged_in() && $exclude_registered) {
                $response['M'] = qnac_handel_user_message($message, $conversation_id);
                if(count($conversation) == 1){
                    if(!empty(sanitize_email($email_noti))){
                        $email_content = qnac_generate_email_content(stripslashes($message));
                        qnac_mail($email_noti, $email_content, 'New Conversation Started.');
                    }
                }
            }else{
                $conversationData = unserialize(qnac_get_value_by_conversation_id('data', $conversation_id));
                if(empty($conversation)){
                $response['M'] = qnac_handel_user_message($message, $conversation_id);
                qnac_user_auto_request_info($conversation_id, $r_message, $r_info);
                }elseif($conversationData['auto_request']){
                $response['M'] = qnac_handel_user_message($message, $conversation_id);
                }else{
                    $response['N'] =  wp_kses_post('<p class="r-notice">'.$r_notice.'</p>');
                }
            }
            
        }else{
            $response['M'] = qnac_handel_user_message($message, $conversation_id);
            if(count($conversation) == 1){
                if(!empty(sanitize_email($email_noti))){
                    $email_content = qnac_generate_email_content(stripslashes($message));
                    qnac_mail($email_noti, $email_content, 'New Conversation Started.');
                }
            }   
        }
        echo wp_json_encode($response);
    }
    die();
}
add_action('wp_ajax_nopriv_qnac_add_converstion_item', 'qnac_add_converstion_item');
add_action('wp_ajax_qnac_add_converstion_item', 'qnac_add_converstion_item');


function qnac_get_value_by_conversation_id($column_name, $conversation_id) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'qnac_chats';

  $query = $wpdb->prepare("SELECT $column_name FROM $table_name WHERE conversation_id = %s", $conversation_id);
  $result = $wpdb->get_var($query);

  return $result;
}

function qnac_front_nmc(){
    if(isset($_REQUEST['T']) && isset($_REQUEST['C'])){
        $cId = sanitize_key($_REQUEST['T']);
        $conversation = unserialize(qnac_get_value_by_conversation_id('conversation', $cId));
        if(!empty($conversation) && is_array($conversation)){
            $lastCount = (int) $_REQUEST['C'];
            $totalCount = count($conversation);
            $loadCount = $totalCount - $lastCount; 
            if($loadCount > 0){
                $loadArray = array_slice($conversation, -$loadCount);
                ob_start();
                foreach($loadArray as $key => $message){
                    include(QNAC_PATH.'includes/front/blocks/single_message.php');
                }
                $messages = ob_get_clean();
                $response = array('C'=> $totalCount, 'M'=> $messages);
                echo wp_json_encode($response);
            }
        }
        $last_seens = get_option('qnac_user_last_seen');
        if(!is_array($last_seens)){
            $last_seens = array();
        }
        $last_seens[$cId] = date('Y-m-d H:i:s');
        update_option('qnac_user_last_seen',$last_seens);
    }
    die();
}
add_action('wp_ajax_qnac_front_nmc', 'qnac_front_nmc');
add_action('wp_ajax_nopriv_qnac_front_nmc', 'qnac_front_nmc');


function qnac_user_submit_info(){
    $adv_settings = get_option('qnac_advanced_settings');
    $email_noti = $adv_settings['email-notifications']['email'];
    if(isset($_REQUEST['D']) && isset($_REQUEST['C']) && isset($_REQUEST['R'])){
        $cId = sanitize_key($_REQUEST['C']);
        $data = json_decode(stripslashes($_REQUEST['D']), true);
        $requestID = sanitize_key($_REQUEST['R']);
        $conversation = unserialize(qnac_get_value_by_conversation_id('conversation', $cId));
        $conversationData = unserialize(qnac_get_value_by_conversation_id('data', $cId));
        $request = $conversation[$requestID];
        $additionalData = array();
        if(is_array($data)){
            $name = $data['name'];
            $email = $data['email'];
            $phone = $data['phone'];
            $request['status'] = 'provided';
            $data = array();
            if(!empty($name)){
                $request['data']['name'] = esc_attr($name);
                $additionalData['name'] = esc_attr($name);
            }else{
                unset($request['data']['name']);
            }
            if(!empty($email)){
                $request['data']['email'] = sanitize_email($email);
                $additionalData['email'] = sanitize_email($email);
            }else{
                unset($request['data']['email']);
            }
            if(!empty($phone)){
                $request['data']['phone'] = esc_attr($phone);
                $additionalData['phone'] = esc_attr($phone);
            }else{
                unset($request['data']['phone']);
            }
            unset($conversation[$requestID]);
            $conversation[$requestID] = $request;
            qnac_update_column_by_conversation_id($cId, 'conversation', serialize($conversation));
            $cIds = get_option('qnac_cu');
            if(is_array){
                $cIds[] = $cId;
                update_option('qnac_cu',$cIds);
            }else{
                $cIds= array($cId);
                update_option('qnac_cu',$cIds);
            }
            $settings = get_option('qnac_settings');
            if($settings['auto-insert']){
                $newData = array_merge($conversationData, $additionalData, array('auto_request'=>true));
                qnac_update_column_by_conversation_id($cId, 'data', serialize($conversationData));
            }else{
                $newData = array_merge($conversationData, array('auto_request'=>true));
            }
            
            qnac_update_column_by_conversation_id($cId, 'data', serialize($newData));
            
            if(!empty(sanitize_email($email_noti))){
                $email_content = qnac_generate_request_email_content($additionalData);
                qnac_mail($email_noti, $email_content, 'User submitted info.');
            }
        }
        $message = $request;
        $key = $requestID;
        include(QNAC_PATH.'includes/front/blocks/single_message.php');
    }
    die();
}
add_action('wp_ajax_qnac_user_submit_info', 'qnac_user_submit_info');
add_action('wp_ajax_nopriv_qnac_user_submit_info', 'qnac_user_submit_info');

function qnac_front_get_messages($conversation_id = ''){
    if(isset($_REQUEST['T'])){
        $conversation_id = sanitize_key($_REQUEST['T']);
        include(QNAC_PATH.'includes/front/blocks/conversation.php');
        die();
    }
}
add_action('wp_ajax_qnac_front_get_messages', 'qnac_front_get_messages');
add_action('wp_ajax_nopriv_qnac_front_get_messages', 'qnac_front_get_messages');


function qnac_get_picname($name){
    $colors = array('#FF6384','#36A2EB','#FFCE56','#4BC0C0','#FFA726','#9370DB','#FF9F40','#56CCF2','#FF6384', '#6FCF97');
    for ($i = 0; $i < strlen($name); $i++) {
        $letter = substr($name, 0, 1);
        $color = $colors[$i % count($colors)];
        $pic = '<span class="pic-names" style="background-color:'.$color.';">'.substr($letter, 0, 1).'</span>';;
    }
    return $pic;
}

function qnac_count_conversations_with_status_new() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'qnac_chats';

    $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE status = %s", 'new');
    $count = $wpdb->get_var($query);

    return $count;
}

function qnac_get_conversation_id_by_email($email) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'qnac_chats';

  $query = $wpdb->prepare("SELECT conversation_id FROM $table_name WHERE email = %s", $email);
  $result = $wpdb->get_var($query);

  return $result;
}

function qnac_get_all_conversation_ids() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'qnac_chats';

  $query = "SELECT conversation_id FROM $table_name";
  $results = $wpdb->get_results($query);

  $conversation_ids = array();
  foreach ($results as $result) {
    $conversation_ids[] = $result->conversation_id;
  }

  return $conversation_ids;
}

function qnac_add_new_conversation($status, $conversation) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'qnac_chats';
  
  $existing_conversation = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE conversation_id = %s", QNAC_CONV_ID));

  $unique_id = uniqid();
  $current_user = wp_get_current_user();

  if (is_user_logged_in()) {
      $logged = true;
      $user_email = $current_user->user_email;
  } else {
      $logged = false;
      $user_email = '';
  }

  if ($existing_conversation) {
    $saved_conversation = unserialize($existing_conversation->conversation);
    $saved_conversation[] = $conversation;
    $saved_data = unserialize($existing_conversation->data);
    if(is_array($saved_data)){
        if(!empty($saved_data['unseen'])){
            $saved_data['unseen'] = $saved_data['unseen'] + 1;
        }else{
            $saved_data['unseen'] = 1;
        }
        $saved_data['last_user'] = date('Y-m-d H:i:s');
    }else{
        $saved_data = array('last_user'=>date('Y-m-d H:i:s'), 'unseen' => 1);
    }
    $data = array(
      'logged_in' => $logged,
      'status' => $status,
      'data' => serialize($saved_data),
      'conversation' => serialize($saved_conversation),
    );
    $wpdb->update($table_name, $data, array('conversation_id' => QNAC_CONV_ID));
  } else {
    $new_conversation = array();
    $new_conversation[] = $conversation;
    $data = array(
      'conversation_id' => QNAC_CONV_ID,
      'logged_in' => $logged,
      'status' => $status,
      'data' => serialize(array('email'=> $user_email,'unseen' => 1, 'last_user'=> date('Y-m-d H:i:s'),'auto_request'=>false)),
      'conversation' => serialize($new_conversation),
    );

    $wpdb->insert($table_name, $data);
  }
}

function qnac_admin_reply($conversation_id, $message, $key = '') {
  global $wpdb;
  $table_name = $wpdb->prefix . 'qnac_chats';

  $existing_conversation = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE conversation_id = %s", $conversation_id));
  if ($existing_conversation) {
    $saved_conversation = unserialize($existing_conversation->conversation);
    $saved_data = unserialize($existing_conversation->data);
    if(is_array($saved_data)){
        $saved_data['last_admin'] = date('Y-m-d H:i:s');
    }else{
        $saved_data = array('last_admin'=>date('Y-m-d H:i:s'),);
    }
    if(empty($key)){
        $saved_conversation[] = $message;
    }else{
        $saved_conversation[$key] = $message;
    }
    
    
    $data = array(
      'data'=> serialize($saved_data),
      'conversation' => serialize($saved_conversation),
    );

    $wpdb->update($table_name, $data, array('conversation_id' => $conversation_id));
  }
}

function qnac_update_column_by_conversation_id($conversation_id, $column_name, $new_value) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'qnac_chats';

  $existing_conversation = $wpdb->get_row(
    $wpdb->prepare("SELECT * FROM $table_name WHERE conversation_id = %s", $conversation_id)
  );

  if ($existing_conversation) {
    $wpdb->update(
      $table_name,
      array($column_name => $new_value),
      array('conversation_id' => $conversation_id)
    );
  }
}
function qnac_delete_conversation($conversation_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'qnac_chats';

    $result = $wpdb->delete($table_name, array('conversation_id' => $conversation_id));

    return ($result !== false);
}

function qnac_time_availability($availability){
    $availabilityParts = explode('-', $availability);

    if (count($availabilityParts) === 2) {
        $startTime = strtotime(trim($availabilityParts[0]));
        $endTime = strtotime(trim($availabilityParts[1]));
        
        if ($startTime !== false && $endTime !== false) {
            $currentTime = time();
            if ($currentTime >= $startTime && $currentTime <= $endTime) {
                return true;
            } else {
                return false;
            }
        }
    }
}

function qnac_format_message_sent_time($sent_time) {
  $now = time();
  $sent_timestamp = strtotime($sent_time);
  $time_diff = $now - $sent_timestamp;
  
  if ($time_diff < 60) {
    return 'Just now';
  } elseif ($time_diff < 3600) {
    $minutes = floor($time_diff / 60);
    return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
  } elseif ($time_diff < 86400) {
    $hours = floor($time_diff / 3600);
    return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
  } else {
    $days = floor($time_diff / 86400);
    return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
  }
}

function qnac_mail($email, $message, $subject){
    $domain_name = sanitize_text_field($_SERVER['SERVER_NAME']);
    $headers = 'From: QnAChat <qnachat@'.$domain_name.'>' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    wp_mail( $email, $subject, $message, $headers );
}

function qnac_generate_email_content($message) {
    ob_start();

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_info = 'Logged-in User Information:' . "<br><br>" .
            'Username: ' . $current_user->user_login . "<br>" .
            'Email: ' . $current_user->user_email . "<br>" .
            'First Name: ' . $current_user->first_name . "<br>" .
            'Last Name: ' . $current_user->last_name . "<br>";
    } else {
        $user_info = 'Non-logged-in User' . "<br>";
    }

    $current_time = current_time('Y-m-d H:i:s');
    $current_domain = sanitize_text_field($_SERVER['HTTP_HOST']);

    $email_content = ob_get_clean() .
        'Time: ' . $current_time . "<br>" .
        'Domain: ' . $current_domain . "<br>" .
        $user_info .
        '<br>Message:' . "<br>" . $message;

    return $email_content;
}

function qnac_generate_request_email_content($request) {
    ob_start();
    $user_info = '';
    foreach($request as $key => $value){
        $user_info.= $key.': '.$value.'<br>';
    }

    $current_time = current_time('Y-m-d H:i:s');
    $current_domain = sanitize_text_field($_SERVER['HTTP_HOST']);

    $email_content = ob_get_clean() .
        'Time: ' . $current_time . "<br>" .
        'Domain: ' . $current_domain . "<br><br>" .
        $user_info;

    return $email_content;
}

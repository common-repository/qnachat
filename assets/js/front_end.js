jQuery(document).ready(function($) {
    
    if($('.qnac-open-chat').attr('fc') != ''){
        qnac_front_nmc(0);
    }
    
    function validateEmail(email) {
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailPattern.test(email);
    }

    function validatePhone(phone) {
        var phonePattern = /^[+0-9]{7,15}$/;
        return phonePattern.test(phone);
    }
    
    $('.qnac-container .qnac-input-field').click(function(){
        $('.qnac-container .n-message').removeClass('active').text('Welcome');
        $('.qnac-open-chat').removeClass('active');
    });
    function qnac_user_submit_info(){
        $('.qnac-container .request button').off('click').on('click', function(){
            thisButton = $(this);
            thisButton.addClass('loading');
            var thisRequest = $(this).closest('.request');
            var inputs = {};
            var isValid = true;
            $(this).closest('.qnac-container .request').find('input').each(function(){
                var value = $(this).val();
                var field = $(this).attr('name');
                var isRequired = $(this).attr("required") !== undefined;
                if( field == 'name'){
                    if(value == '' && isRequired){
                        alert('Please enter your name.');
                        isValid = false;
                        return false;
                    }
                }
                else if(field == 'email'){
                    if(value == '' && isRequired){
                        alert('Please enter your E-mail address.');
                        isValid = false;
                        return false;
                    }else if(value != '' && isRequired && !validateEmail(value)){
                        alert('Please enter a valid E-mail address.');
                        isValid = false;
                        return false;                        
                    }
                }else if(field == 'phone'){
                    if(value == '' && isRequired){
                        alert('Please enter your phone number.');
                        isValid = false;
                        return false;
                    }else if(value != '' && isRequired && !validatePhone(value)){
                        alert('Please enter a valid Phone number.');
                        isValid = false;
                        return false;                        
                    }                    
                } 
                inputs[field]= value;
            });
            if(isValid){
                $.ajax({
                    url:'/wp-admin/admin-ajax.php',
                    data:{
                        action:'qnac_user_submit_info',
                        'C': $('.qnac-container').attr('id'),
                        'R': thisRequest.attr('data-value'),
                        'D': JSON.stringify(inputs)
                    },success:function(response){
                        thisRequest.replaceWith(response);
                        thisButton.removeClass('loading');
                    },error:function(xhr,error){
                        thisButton.removeClass('loading');
                    }
                });
            }
        });
    }
    qnac_user_submit_info();
    
    $('.qnac-container .qnac-head .right .actions').click(function(){
        $('.qnac-container').toggleClass('pinfo');
    });
    $('.qnac-open-chat').click(function(){
        $('.qnac-container').addClass('active');
        $(this).addClass('hide').attr('fc','5000');
        var chatElement = $('.qnac-container .inner-content .qnac-chats');
        chatElement.scrollTop(chatElement[0].scrollHeight);
    });
    $('.qnac-container .qnac-head .qnac-close').click(function(){
        $('.qnac-container').removeClass('active');
        $('.qnac-open-chat').removeClass('hide').attr('fc','15000');
    });    

    $('textarea.qnac-input-field').on('input change', function() {
        this.style.height = '27px';
        this.style.height = this.scrollHeight + 'px';
    });
    $('.qnac-submit-info button').click(function(e){
        e.preventDefault();
        var name = $('.qnac-container .qnac-submit-info .name').val();
        var email = $('.qnac-container .qnac-submit-info .email').val();
        var phone = $('.qnac-container .qnac-submit-info .phone').val();
        $(this).addClass('loading');
        $.ajax({
            url:'/wp-admin/admin-ajax.php',
            data:{
                'action': 'qnac_submit_user_info',
                'C': $('.qnac-container').attr('id'),
                'N': name,
                'E': email,
                'P': phone,
            },
            success:function(response){
                $('.qnac-submit-info').removeClass('loading');
            },
            error: function(xhr, status, error) {
                $('.qnac-submit-info').removeClass('loading');
                console.log(error);
            }
        });
    });
    
var timeoutID;

function qnac_front_nmc(count) {
    if ($('.qnac-open-chat').attr('fc') == '') {
        var freq_check = 1000 * 120;
    } else {
        var freq_check = $('.qnac-open-chat').attr('fc');
    }
    
    clearTimeout(timeoutID);

    $.ajax({
        url: '/wp-admin/admin-ajax.php',
        data: {
            'action': 'qnac_front_nmc',
            'T': $('.qnac-container').attr('id'),
            'C': $('.qnac-container').attr('c'),
        },
        success: function(response) {
            if (response !== '') {
                try {
                    var data = JSON.parse(response);
                    $('.qnac-container .n-message').addClass('active').text('New Message');
                    $('.qnac-open-chat').addClass('active');
                    $('.qnac-container .qnac-chats .reply.typing').before(data.M);
                    $('.qnac-container').attr('c', data.C);
                    qnac_user_submit_info();
                    timeoutID = setTimeout(function() {
                        qnac_front_nmc(response);
                    }, freq_check);
                } catch (error) {
                    timeoutID = setTimeout(function() {
                        qnac_front_nmc(response);
                    }, freq_check);
                }
            } else {
                timeoutID = setTimeout(function() {
                    qnac_front_nmc(response);
                }, freq_check);
            }
        },
        error: function(xhr, status, error) {
            timeoutID = setTimeout(function() {
                qnac_front_nmc(count);
            }, freq_check);
        }
    });
}


    function qnac_front_get_messages(){
        var chatElement = $('.qnac-container .inner-content .qnac-chats');
        $('.qnac-container .qnac-chats .reply.typing').removeClass('hidden');
        chatElement.scrollTop(chatElement[0].scrollHeight);
        $.ajax({
            url:'/wp-admin/admin-ajax.php',
            data:{
                'action':'qnac_front_get_messages',
                'T': $('.qnac-container').attr('id'),
            },
            success:function(response){
                $('.qnac-container .qnac-chats').html(response);
                chatElement.scrollTop(chatElement[0].scrollHeight);
            }
        });
    }    

    function qnac_chatform_tag_handle() {
        $('.qnac-container .inner-content .qnac-bottom .tags>div').click(function() {
          var tagText = $(this).text();
          var tagValue = $(this).attr('value');
          var chatElement = $('.qnac-container .inner-content .qnac-chats');
          var userInputElement = $('<div class="user-input tag">' + tagText + '</div>');
          $('.qnac-container .inner-content .qnac-chats .reply.typing').before(userInputElement).removeClass('hidden');
          chatElement.scrollTop(chatElement[0].scrollHeight);
          $.ajax({
              url:'/wp-admin/admin-ajax.php',
              data:{
                  'action':'qnac_get_tag_data',
                  'tag': tagValue,
              },
              success:function(response){
                  $('.qnac-container .inner-content .qnac-chats .reply.typing').before(response).addClass('hidden');
                  chatElement.scrollTop(chatElement[0].scrollHeight);
                  $('.qnac-container .inner-content .qnac-bottom .tags>div').off('click');
                  qnac_chatform_tag_handle();
              }
          });
        });
        
        $('.qnac-container .reply.question-item').click(function() {
          var tag = $(this).attr('tag');
          var index = $(this).attr('value');
          var tagValue = $(this).attr('value');
          var chatElement = $('.qnac-container .inner-content .qnac-chats');
          $('.qnac-container .inner-content .qnac-chats .reply.typing').removeClass('hidden');
          chatElement.scrollTop(chatElement[0].scrollHeight);
          $.ajax({
              url:'/wp-admin/admin-ajax.php',
              data:{
                  'action':'qnac_get_tag_qa',
                  'tag': tag,
                  'index': index,
              },
              success:function(response){
                  $('.qnac-container .inner-content .qnac-chats .reply.typing').before(response).addClass('hidden');
                  chatElement.scrollTop(chatElement[0].scrollHeight);
              }
          });
        });
        
        $('.qnac-container .inner-content .qnac-chats').scrollTop($('.qnac-container .inner-content .qnac-chats')[0].scrollHeight);
    }
    qnac_chatform_tag_handle();
    
    function qnac_add_converstion_item(){
        $('.qnac-bottom .input a').off('click').on('click',function(e){
            e.preventDefault();
            $(this).addClass('loading');
            var id = $('.qnac-container').attr('id');
            var message = $('textarea.qnac-input-field').val();
            var userInputElement = $('<div class="user-input temp">' + message + '</div>');
            $('.qnac-container .inner-content .qnac-chats .reply.typing').before(userInputElement);
            $('.qnac-bottom .input .qnac-input-field').val('').empty();
            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                data:{
                    'action':'qnac_add_converstion_item',
                    'id': id,
                    'message': message,
                },
                success:function(response){
                    try{
                        var data = JSON.parse(response);
                        if(data.hasOwnProperty('M')){
                            $('.qnac-container p.r-notice').remove();
                            $('.qnac-container .inner-content .qnac-chats .user-input.temp').replaceWith(data.M);
                            var count = parseInt($('.qnac-container').attr('c'));
                            $('.qnac-container').attr('c',count + 1);
                            $('.qnac-bottom .input a').removeClass('loading');
                            $('.qnac-open-chat').attr('fc','5000');
                            qnac_user_submit_info();
                            qnac_front_nmc(0);
                        }
                        if(data.hasOwnProperty('N')){
                            $('.qnac-container p.r-notice').remove();
                            $('.qnac-container .inner-content .qnac-chats .user-input.temp').replaceWith(data.N);
                            $('.qnac-bottom .input a').removeClass('loading');
                        }

                    }catch(error){
                        $('.qnac-container .inner-content .qnac-chats .user-input.temp').remove();
                        $('.qnac-bottom .input a').removeClass('loading');
                        alert('Something went wrong. Please refresh your page.');
                    }
                },error: function(xhr,error){
                    $('.qnac-bottom .input a').removeClass('loading');
                }
            });
        });
    }
    qnac_add_converstion_item();
    
    function ajax_dev_form_submission() {
        $('.settings-sidebar form input[type="submit"]').click(function(e) {

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                data: {
                    'action': 'ajax_dev_form_submission',
                    'actionName': actionName,
                    'email': email,
                    'content': content
                },
                success: function(response) {
                    clickedButton.attr('value', 'Submitted');
                    clickedButton.closest('form').replaceWith(response);
                }
            });
        });
    }

});
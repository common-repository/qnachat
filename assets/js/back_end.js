jQuery(document).ready(function($) {
    
    $('input.set-value[type="checkbox"]').change(function() {
        var mainElement = $(this).closest('.set');
        if ($(this).is(':checked')) {
            mainElement.addClass('checked');
        } else {
            mainElement.removeClass('checked');
        }
    });


    $('.qnac-manager-admin .cg-head .item.mute').off('click').on('click', function(){
        var mute = 1;
        if($(this).hasClass('active')){
            mute = 0;
        }
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: {
              'action': 'qnac_mute_setting',
              'S':mute
            },
            success:function(response){
                $('.qnac-manager-admin .cg-head .item.mute').toggleClass('active');
            },error:function(error){
                
            }
        });
    });
    $('.qnac-manager-admin .cg-head .item.settings').off('click').on('click', function(){
        $('.qnac-manager-admin .settings-container').addClass('active');
    });  
    $('.qnac-manager-admin .general-sets .close').off('click').on('click', function(){
        $('.qnac-manager-admin .settings-container').removeClass('active');
    }); 
    
    var timeoutID;
    
    function qnac_admin_nmc(action) {
        action = action || "";
        clearTimeout(timeoutID);
      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        data: {
          'action': 'qnac_admin_nmc',
          'A': action
        },
        success: function(response) {
            $('.qnac-notice').addClass('cg-hide');
          if (response !== '') {
            try {
                var conversationData = JSON.parse(response);
                try{playNotificationSound();}catch(error){console.log(error);}
                for (var conversationId in conversationData) {
                    if(conversationId === $('a.qnac-send').attr('value')){
                        var count = $('.qnac-manager-admin .conversation-info').attr('c');
                        var dividerExist = $('.qnac-manager-admin .conversation-content .divider').length;
                        if(!dividerExist){
                            $('.qnac-manager-admin .conversation-content').append('<div class="divider">New</div>');
                        }
                        qnac_get_conversation_items(conversationId,count);
                        $('.item[value="' + conversationId + '"]').addClass('active');
                    }
                    var conversationHtml = conversationData[conversationId];
                    var existingItem = $('.item[value="' + conversationId + '"]');
                    if (existingItem.length) {
                        var updatedItem = $(conversationHtml);
                        if(existingItem.hasClass('active')){
                            updatedItem.addClass('active');
                        }
                        existingItem.remove();
                        $('.qnac-manager-admin>.left .conversations').prepend(updatedItem);
                        qnac_admin_get_conversation();
                    } else {
                        var newElement = $(conversationHtml);
                        newElement.addClass('new');
                        $('.qnac-manager-admin>.left .conversations').prepend(newElement);
                        qnac_admin_get_conversation();
                        qnac_admin_nmc('1');
                    }
                }
                timeoutID = setTimeout(function() {qnac_admin_nmc('1');}, 5000);
            } 
            catch (error) {
                timeoutID = setTimeout(function() {qnac_admin_nmc('1');}, 5000);
            }
          } 
          else {
            timeoutID = setTimeout(function() {qnac_admin_nmc();}, 5000);
          }
        },
        error: function(xhr, status, error) {
            timeoutID = setTimeout(function() {qnac_admin_nmc();}, 10000);
            if (xhr.status === 0) {
              $('.qnac-notice').removeClass('cg-hide');
            }            
        }
      });
    }
    qnac_admin_nmc();

    var lastSeenTimeout;
    function qnac_admin_last_seen(conversationId){
        clearTimeout(lastSeenTimeout);
        $.ajax({
            url:'/wp-admin/admin-ajax.php',
            data:{
                'action':'qnac_admin_last_seen',
                'C': conversationId,
            },
            success:function(response){
                $('.qnac-manager-admin .right p.last-seen').html(response);
               lastSeenTimeout = setTimeout(function() {qnac_admin_last_seen(conversationId);}, 30000);
            }
        });    
    }
    function qnac_conversations_items(conversationIds){
        $.ajax({
            url:'/wp-admin/admin-ajax.php',
            data:{
                'action':'qnac_conversations_items',
                'C': JSON.stringify(conversationIds),
            },
            success:function(response){
                $('.qnac-manager-admin>.left .conversations').html(response);
                qnac_admin_get_conversation();
            }
        });
    }
    
    function qnac_get_conversation_items(conversationId,count){
        $.ajax({
            url:'/wp-admin/admin-ajax.php',
            data:{
                'action':'qnac_get_conversation_items',
                'I': conversationId,
                'C': count
            },
            success:function(response){
                var data = JSON.parse(response);
                $('.qnac-manager-admin .conversation-content').append(data.M);
                $('.qnac-manager-admin .conversation-info').attr('c',data.C);
            }
        });
    }    
    function qnac_admin_get_conversation(){
        $('.qnac-manager-admin .conversations .item').off('click').on('click', function(){
            var chatElement = $('.qnac-manager-admin>.center .conversation-content');
            $('.qnac-manager-admin .conversations .item').removeClass('active');
            var conversation = $(this).attr('value');
            var thisItem = $(this);
            thisItem.addClass('loading').removeClass('new');
            $('.qnac-manager-admin .conversation-content').empty().addClass('loading');
            $('.qnac-manager-admin .center h4').text('Loading the conversation');
            $.ajax({
                url:'/wp-admin/admin-ajax.php',
                data:{
                    'action': 'qnac_admin_get_conversation',
                    'id': conversation,
                },
                success:function(response){
                    var data = JSON.parse(response);
                    $('.qnac-manager-admin .conversation-content').html(data.M).removeClass('loading');
                    $('.qnac-manager-admin .conversation-info').html(data.I).attr('c',data.C);
                    $('.qnac-manager-admin>.center .cg-hide').removeClass('cg-hide');
                    $('.bottom .qnac-send').attr('value', conversation);
                    thisItem.removeClass('loading').addClass('active').find('span.unread').remove();
                    qnac_admin_last_seen($('.bottom .qnac-send').attr('value'));
                    qnac_admin_chat_actions();
                    qnac_admin_contacts_manage();
                    qnac_note_manage();
                    qnac_admin_chat_tags();
                    qnac_admin_request_info();
                    chatElement.scrollTop(chatElement[0].scrollHeight);
                    $('.qnac-notice').addClass('cg-hide');
                },error: function(xhr, error) {
                    if (xhr.status === 0) {
                      $('.qnac-notice').removeClass('cg-hide');
                    }
                    thisItem.removeClass('loading');
                    $('.qnac-manager-admin .conversation-content').removeClass('loading');
                }
            });
            
        });
    }
    qnac_admin_get_conversation();

    $('.qnac-manager-admin .qnac-message').on('input change', function() {
        this.style.height = '36px';
        this.style.height = this.scrollHeight + 'px';
    });    
    
    function qnac_admin_request_info(){
        $('.qnac-request-info .info').off('click').on('click', function(){
            $(this).closest('.item').toggleClass('active');
        });
        
        $('.qnac-manager-admin .contact-items .item.request').off('click').on('click', function(){
            $('.qnac-request-info').addClass('active');
        });
        
        $('.qnac-request-info .close').off('click').on('click', function(){
            $(this).closest('.qnac-request-info').removeClass('active');
        });        
        
        $('.qnac-request-info .send-request').off('click').on('click', function(){
            $(this).addClass('loading');
            var selected = {};
            $('.qnac-request-info .select .item.active').each(function() {
                if ($(this).find('.required input').prop('checked')) {
                    var value = 'r';
                }else{
                    var value = 'o';
                }
                selected[$(this).attr('data-value')] = value;
            });
            var message = $('.qnac-request-info textarea').val();
            $.ajax({
                url:'/wp-admin/admin-ajax.php',
                data:{
                    action:'qnac_admin_request_info',
                    'S':JSON.stringify(selected),
                    'M': message,
                    'C': $('.bottom .qnac-send').attr('value'),
                },success:function(response){
                    $('.qnac-manager-admin>.center .conversation-content').append(response);
                    $('.qnac-request-info .send-request').removeClass('loading');
                    $('.qnac-request-info').removeClass('active');
                },error:function(xhr, error){
                    alert('Something went wrong.');
                    $('.qnac-request-info .send-request').removeClass('loading');
                    $('.qnac-request-info').removeClass('active');
                }
            });
        });
    }
    
    function qnac_admin_reply(){
        $('.bottom .qnac-send').off('click').on('click', function(){
            var conversation = $(this).attr('value');
            var thisButton = $(this);
            var message = $('.qnac-manager-admin>.center .bottom .qnac-message').val();
            $('.qnac-manager-admin .qnac-message').val('').empty();
            if(message !== ''){
                $.ajax({
                    url:'/wp-admin/admin-ajax.php',
                    data:{
                        'action':'qnac_admin_reply',
                        'id': conversation,
                        'message': message,
                    },
                    success:function(response){
                        var data = JSON.parse(response);
                        $('.qnac-manager-admin>.center .conversation-content').append(data.IM);
                        var $newElement = $(data.LM).addClass('active');
                        $('.qnac-manager-admin .conversations .item[value="' + conversation + '"]').replaceWith($newElement);
                        var count = parseInt($('.qnac-manager-admin .conversation-info').attr('c'));
                        $('.qnac-manager-admin .conversation-info').attr('c',count + 1);
                        qnac_admin_get_conversation();
                    }
                });
            }
        });
        
        $('.qnac-message').keyup(function(event) {
          if (event.keyCode === 13) {
            $('.qnac-send').click();
          }
        });
    }
    qnac_admin_reply();
    
    function qnac_admin_chat_actions(){
        $('.qnac-manager-admin>.right .actions a').off('click').on('click', function(e){
            e.preventDefault();
            var action = $(this).attr('action');
            var thisButton = $(this);
            $(this).addClass('loading');
            $.ajax({
                url:'/wp-admin/admin-ajax.php',
                data:{
                    'action':'qnac_admin_manage_chat',
                    'A': action,
                    'C': $('.bottom .qnac-send').attr('value'),
                },
                success:function(response){
                    $('.qnac-manager-admin>.right .actions a').removeClass('loading');
                    if(action === 'd'){
                        if(response !== ''){
                            alert(response);
                        }
                        $('.right.conversation-info').html('');
                        $('.qnac-manager-admin>.center .bottom').addClass('cg-hide'); 
                        $('.qnac-manager-admin>.center .qnac-send').removeAttr('value'); 
                        $('.qnac-manager-admin .conversation-content').html('<h4>Select a conversation</h4>');
                        $('.qnac-manager-admin .conversations .item.active').remove();
                    }else if(action === 'b' || action === 'u'){
                        try{
                            var data = JSON.parse(response);
                            thisButton.text(data.T);
                            thisButton.attr('action',data.V);
                        }catch(error){
                            
                        }
                    }    
                }, error:function(){
                    $('.qnac-manager-admin>.right .actions a').removeClass('loading');
                    alert('Something went wrong.');
                }
            });
        });
    }
    
    function qnac_note_manage(){
        $('.qnac-manager-admin .note').off('click').on('click', function(){
            $(this).removeClass('waiting').addClass('active');
            $('.qnac-manager-admin .note-content').removeClass('waiting');
            $('.qnac-manager-admin .note-content').addClass('active');
        });        
        $('.qnac-manager-admin .note-content .edit').off('click').on('click', function(){
            $('.qnac-manager-admin .note-content').addClass('active');
        });
        $('.qnac-manager-admin .note-content img.action').off('click').on('click', function(){
            var action = $(this).attr('action');
            var cId = $('a.qnac-send').attr('value');
            var note = $('.qnac-manager-admin .note-content textarea.n-field').val();
            $.ajax({
                url:'/wp-admin/admin-ajax.php',
                data:{
                    action:'qnac_admin_note_manage',
                    'A': action,
                    'C': cId,
                    'E': note,
                },
                success:function(response){
                    $('.qnac-manager-admin .note-content').removeClass('active');
                    if(action === 's'){
                        $('.qnac-manager-admin .note-content p').text(note);
                    }else if(action === 'd'){
                        $('.qnac-manager-admin .note-content p').text('');
                        $('.qnac-manager-admin .note-content').addClass('waiting');
                        $('.qnac-manager-admin .note').removeClass('active').addClass('waiting');
                        $('.qnac-manager-admin .note-content textarea.n-field').val('').empty();
                    }
                },
                error:function(){
                    alert('Something went wrong.');
                    $('.qnac-manager-admin .note-content').removeClass('active');
                }
            });
        });        
    }
    function qnac_admin_contacts_manage(){
        $('.qnac-manager-admin>.right .contact-items .action .edit').off('click').on('click', function(){
            $(this).closest('.contact-items .item').addClass('active');
        });
        $('.qnac-manager-admin .contact-items .action.confirm').off('click').on('click', function(){
            var thisButton = $(this);
            var inputType = $(this).attr('value');
            var phonePattern = /^[+\-\d]+$/;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var value = $(this).closest('.item').find('input').val();
            if(inputType === 'phone'){
                if(!phonePattern.test(value) && value !== ''){
                    alert('Invalid phone number');
                    return false;
                }
            }else if(inputType === 'email'){
                if(!emailPattern.test(value) && value !== ''){
                    alert('Invalid email format');
                    return false;
                }                
            }
            var cId = $('a.qnac-send').attr('value');
            $.ajax({
                url:'/wp-admin/admin-ajax.php',
                data:{
                    action:'qnac_admin_contacts_manage',
                    'T': inputType,
                    'C': cId,
                    'V': value,
                },
                success:function(response){
                    thisButton.closest('.item').removeClass('active');
                    $('.qnac-manager-admin .note-content').removeClass('active');
                    if(inputType === 'phone'){
                        if(value === ''){
                            thisButton.closest('.item').find('a').replaceWith('<a><span>Add phone number</span></a>');
                        }else{
                            thisButton.closest('.item').find('a').text(value).attr('href','tel:'+ value);
                        }                         
                    }else if(inputType === 'email'){
                        if(value === ''){
                            thisButton.closest('.item').find('a').replaceWith('<a><span>Add E-mail</span></a>');
                        }else{
                            thisButton.closest('.item').find('a').text(value).attr('href','mailTo:' + value);
                        }                        
                    }
                },
                error:function(){
                    alert('Something went wrong.');
                    thisButton.closest('.item').removeClass('active');
                }
            });
        });          
    }
    function qnac_admin_chat_tags(){
        $('.qnac-manager-admin .tags .edit').off('click').on('click', function(){
            $('.qnac-manager-admin .tags').addClass('active');
        });
        $('.qnac-manager-admin .tags .confirm').off('click').on('click', function(){
            var value = $('.qnac-manager-admin .tags .t-field').val();
            $(this).addClass('loading');
            $.ajax({
                url:'/wp-admin/admin-ajax.php',
                data:{
                    'action':'qnac_admin_edit_tags',
                    'V': value,
                    'C': $('.bottom .qnac-send').attr('value'),
                },
                success:function(response){
                    $('.qnac-manager-admin .tags .items p').remove();
                    $('.qnac-manager-admin .tags .items h5').remove();
                    if(response !== ''){
                        $('.qnac-manager-admin .tags .edit').before(response);
                    }else{
                        $('.qnac-manager-admin .tags .edit').before('<h5>Add a tag</h5>');
                    }
                    $('.qnac-manager-admin .tags').removeClass('active');
                }, error:function(){
                    $('.qnac-manager-admin .tags').removeClass('active');
                    alert('Something went wrong.');
                }
            });
        });
    }
    qnac_admin_chat_tags();
    function playNotificationSound() {
        var mute = $('.qnac-manager-admin .cg-head .item.mute').hasClass('active');
        if(!mute){
            var audioElement = $('.qnac-manager-admin #notification-sound')[0];
            audioElement.play();
        }
    }
});
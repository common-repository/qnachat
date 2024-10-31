jQuery(document).ready(function($) {
    function qnac_prepare_tag(){
        var hasWaitingElement = $('.tag.waiting').length > 0;
        if (!hasWaitingElement) {
            var tElement = $('.tag.cg-hide');
            var duplicatedTElement = tElement.clone();
            duplicatedTElement.toggleClass('cg-hide waiting');
            $('.qnac-faqs .inner-content').append(duplicatedTElement);
            qnac_add_tag();
            qnac_faq_new_prepare();
        }        
    }
    function qnac_add_tag(){
        $('.tag-set').click(function(){
            if($(this).hasClass('waiting')){
                $(this).removeClass('waiting').addClass('active');
                $(this).closest('.tag').removeClass('waiting').addClass('active');
            }
        });
        $('.tag-set .confirm').click(function(){
            var tagVal = $(this).closest('.tag-set').find('.t-field').val();
            var parent = $(this).closest('.tag');
            if(tagVal === ''){
                alert("The tag name cannot be empty.");
            }else{
                parent.find('.tag-set h5 span').text(tagVal);
                parent.find('.tag-set').removeClass('active').addClass('done');
                parent.removeClass('active').addClass('done').attr('value',tagVal);
                qnac_prepare_tag();
            }
        });
        $('.tag-set .edit').click(function(){
            $(this).closest('.tag-set').removeClass('done').addClass('active');
        });
        $('.tag-set .delete').click(function(){
            $(this).closest('.tag').remove();
            qnac_prepare_tag();
        });        
    }
    qnac_add_tag();
    function qnac_prepare_element(tag){
        var hasWaitingElement = tag.find('.qa.waiting').length > 0;
        if (!hasWaitingElement) {
            var qaElement = tag.find('.qa.cg-hide');
            var duplicatedQaElement = qaElement.clone();
            duplicatedQaElement.toggleClass('cg-hide waiting');
            tag.append(duplicatedQaElement);
            qnac_faq_new_prepare();
        }        
    }
    function qnac_faq_new_prepare(){
        $('.qnac-faqs .tag .question.add-new').click(function(){
            var tagVal = $(this).closest('.tag').attr('value');
            if(!tagVal ){
                alert('Please add a tag name first.');
            }
            else if($(this).hasClass('add-new')){
                $(this).toggleClass('add-new active');
                $(this).find('.q-field').focus();
                $(this).closest('.qa').toggleClass('active waiting');
                $(this).closest('.qnac-faqs').addClass('focus');
                qnac_faq_new_confirm();
            }
        });
        $('.qnac-faqs .tag .answer.add-new').click(function(){
            if($(this).hasClass('add-new')){
                $(this).toggleClass('add-new active');
                $(this).find('.a-field').focus();
            }
        });
        
        $('.qnac-faqs .tag .actions .delete').click(function(){
            target = $(this).closest('.qnac-faqs .qa');
            if($(this).hasClass('q')){
                qnac_prepare_element($(this).closest('.tag'));
                $(this).closest('.qnac-faqs').removeClass('focus');
                target.remove();
            }
        });
    }
    qnac_faq_new_prepare();
    
    function qnac_faq_new_confirm(){
        $('.qnac-faqs .tag .confirm').click(function(){
            if($(this).hasClass('q')){
                var parentQ = $(this).closest('.question');
                var inputQ = parentQ.find('input.q-field');
                var labelQ = parentQ.find('p.label');
                if (inputQ.val() === ''){
                    alert('The question field cannot be empty.');
                }else{
                    parentQ.attr('value',inputQ.val()).removeClass('active').addClass('done');
                    labelQ.text(inputQ.val());
                    parentQ.closest('.qa').find('.answer.add-new').removeClass('cg-hide');
                    qnac_faq_new_prepare();
                }                
            }else if($(this).hasClass('a')){
                var parentA = $(this).closest('.qnac-faqs .answer');
                var inputA = parentA.find('input.a-field');
                var labelA = parentA.find('p.label'); 
                if (inputA.val() === ''){
                    alert('Please provide an answer to the question above.');
                }else if($(this).closest('.qnac-faqs .qa').find('.q-field').val() === ''){
                    alert('The question field cannot be empty.');
                }else{
                    parentA.attr('value',inputA.val()).removeClass('active').addClass('done');
                    labelA.text(inputA.val());
                }                
            }
            if($(this).closest('.qa').find('.answer').hasClass('done') && $(this).closest('.qa').find('.question').hasClass('done')){
                $(this).closest('.qa').removeClass('active').addClass('done');
                $(this).closest('.qnac-faqs').removeClass('focus');
                qnac_prepare_element($(this).closest('.tag'));
                qnac_faq_new_prepare();
            }
            qnac_faq_edit();
            console.log(qnac_generate_faq_array());
        });
    }
    qnac_faq_new_confirm();
    function qnac_faq_edit(){
        $('.qnac-faqs .tag .edit').click(function(){
            targetQ = $(this).closest('.question');
            targetA = $(this).closest('.answer');
            if($(this).hasClass('q')){
                targetQ.removeClass('done').addClass('active');
                targetQ.closest('.qa').removeClass('done').addClass('active');
                $(this).closest('.qnac-faqs').addClass('focus');
            }else if($(this).hasClass('a')){
                targetA.removeClass('done').addClass('active');
                targetA.closest('.qa').removeClass('done').addClass('active');
                $(this).closest('.qnac-faqs').addClass('focus');
            }
        });    
    }
    qnac_faq_edit();
    
function qnac_generate_faq_array() {
    var Tags = $('.qnac-faqs .tag');
    var tagsArray = {};

    Tags.each(function() {
        var tagVal = $(this).attr('value');
        if (tagVal && tagVal.trim() !== '') {
            var qaArray = {};

            var QAs = $(this).find('.qa.done');
            QAs.each(function() {
                var qVal = $(this).find('.question.done').attr('value');
                var aVal = $(this).find('.answer.done').attr('value');
                
                qaArray[qVal] = aVal;
            });

            tagsArray[tagVal] = qaArray;
        }
    });

    return tagsArray;
}

    function qnac_submit_faq_settings(){
        $('.qnac-faq-set').click(function(e){
            e.preventDefault();
            $(this).addClass('loading ');
            var button = $(this);
            var settings = qnac_generate_faq_array();
            $.ajax({
                url:'/wp-admin/admin-ajax.php',
                data:{
                    'action': 'qnac_submit_faq_settings',
                    'S': JSON.stringify(settings)
                },
                success:function(response){
                    button.removeClass('loading').text('Saved');
                }
            });
        });
    }
    qnac_submit_faq_settings();

});
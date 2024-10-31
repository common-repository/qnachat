jQuery(document).ready(function($) {
    $('input.set-value[type="checkbox"]').change(function() {
        var mainElement = $(this).closest('.set');
        if ($(this).is(':checked')) {
            mainElement.addClass('checked');
        } else {
            mainElement.removeClass('checked');
        }
    });
    
    $('.select-chat-icon').click(function(e) {
        e.preventDefault();
        var thisImage = $(this);
        var image = wp.media({ 
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            thisImage.closest('.qnac-img').find('input').val(image_url);
            thisImage.attr("src",image_url);
        });
    });
    
    $('.qnac-select.position .item').click(function(){
        $(this).closest('div').find('.item').removeClass('active');
        $(this).addClass('active');
        var values = $('.qnac-select.position .item.active').map(function() {
          return $(this).attr('data-value');
        }).get().join(',');
        $('.qnac-select.position .set-value').val(values);
    });
    
    $('.qnac-select.r-info .item').click(function(){
        $(this).toggleClass('active');
        var values = $('.r-info.qnac-select .item.active').map(function() {
          return $(this).attr('data-value');
        }).get().join(',');
        $('.qnac-select .sub-set-value.r-info').val(values);        
    });
    
  function qnac_save_settings() {
      
    var settings = {};
    
    $('.set').each(function() {
        var $set = $(this);
        var setKey = $set.attr('value');
        var setValue;
        if($set.hasClass('multiple')){
            var mSets = {};
            $set.find('.input').each(function() {
                var key = $(this).attr('key');
                mSets[key] = $(this).val();
            });
            setValue = mSets;
        }else if ($set.find('.set-value').attr('type') === 'checkbox') {
          setValue = $set.find('.set-value').prop('checked');
        } else {
          setValue = $set.find('.set-value').val();
        }

      var $subSet = $set.find('.sub-set');

      if ($subSet.length > 0) {
        var subSettings = { 'main': setValue };

        $subSet.each(function() {
          var $subSet = $(this);
          var subSetKey = $subSet.attr('value');
          var subSetValue;

          if ($subSet.find('.sub-set-value').attr('type') === 'checkbox') {
            subSetValue = $subSet.find('.sub-set-value').prop('checked');
          } else {
            subSetValue = $subSet.find('.sub-set-value').val();
          }

          subSettings[subSetKey] = subSetValue;
        });

        settings[setKey] = subSettings;
      } else {
        settings[setKey] = setValue;
      }
    });
    return settings;
  }  
    $('.adv-settings-save').click(function(){
        var settings = qnac_save_settings();
        var thisButton = $(this);
        $(this).addClass('loading');
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: {
              'action': 'qnac_save_advanced_settings',
              'S': JSON.stringify(settings)
            },
            success:function(response){
                thisButton.removeClass('loading');
            },
            error:function(error){
                alert('Something went wrong');
                thisButton.removeClass('loading');
            }
        }); 
    });
  
});

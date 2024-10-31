jQuery(document).ready(function($) {
  $('.color-picker-input').wpColorPicker();
  function qnac_save_settings() {
    var settings = {};

    $('.set').each(function() {
      var $set = $(this);
      var setKey = $set.attr('value');
      var setValue;

      if ($set.find('.set-value').attr('type') === 'checkbox') {
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

  $('.qnac-manager-admin .general-sets .save').click(function() {
        var settings = qnac_save_settings();
        $(this).addClass('loading');
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            data: {
              'action': 'qnac_save_settings',
              'S': JSON.stringify(settings)
            },
            success:function(response){
                $('.qnac-manager-admin .general-sets .save').removeClass('loading');
                $('.qnac-manager-admin .settings-container').removeClass('active');
            },
            error:function(error){
                alert('Something went wrong');
                $('.qnac-manager-admin .general-sets .save').removeClass('loading');
            }
        });    
  });
  
   $('.qnac-manager-admin .general-sets .pick-color').click(function() {
       $('.wp-picker-open').click();
  }); 

  $('.qnac-manager-admin .general-sets .days span').click(function() {
    $(this).toggleClass('active');
    var values = $('.qnac-manager-admin .general-sets .days span.active').map(function() {
      return $(this).attr('data-value');
    }).get().join(',');
    $('.qnac-manager-admin .general-sets .days input.sub-set-value').val(values);
  });


  $('.time .sub-set-value').keyup(function() {
    var input = $(this).val();
    var formattedInput = formatTimeInput(input);
    $(this).val(formattedInput);
  });

function formatTimeInput(input) {
  var digits = input.replace(/\D/g, '');

  if (digits.length === 0) {
    return '';
  }

  var formattedInput = '';
  var shours = digits.substr(0, 2);
  var sminutes = digits.substr(2, 2);
  var ehours = digits.substr(4, 2);
  var eminutes = digits.substr(6, 2);

  if (shours.length > 0) {
    formattedInput += shours;

    if (shours.length === 2) {
        if((isNaN(parseInt(shours)) || parseInt(shours) < 0 || parseInt(shours) > 23)){
            formattedInput = formattedInput.substr(0, 1);
        }else{
           formattedInput += ':' + sminutes; 
        }
    }

    if (sminutes.length > 0) {

      if (sminutes.length === 2) {
        if(isNaN(parseInt(sminutes)) || parseInt(sminutes) < 0 || parseInt(sminutes) > 59){
            formattedInput = formattedInput.substr(0, 3);
        }else{
           formattedInput += '-' + ehours; 
        }
      }

      if (ehours.length > 0) {
        if (ehours.length === 2) {
            if(isNaN(parseInt(ehours)) || parseInt(ehours) < 0 || parseInt(ehours) > 23){
                formattedInput = formattedInput.substr(0, 6);
            }else{
               formattedInput += ':' + eminutes; 
            }
        }

        if (eminutes.length > 0) {
          if (eminutes.length === 2 && (isNaN(parseInt(eminutes)) || parseInt(eminutes) < 0 || parseInt(eminutes) > 59)) {
            formattedInput = formattedInput.substr(0, 9);
          }
        }
      }
    }
  }

  return formattedInput;
}



});

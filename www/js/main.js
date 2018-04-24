/** Override Nette forms Validation **/
Nette.showFormErrors = function(form, errors) {
  $(form).find('.error').remove();
  $(form).find('.form-group').removeClass('has-error');
  $(form).find('.help-block').remove();
  console.log(errors);
  var messages = [],
      focusElem;

  for (var i = 0; i < errors.length; i++) {
    var elem = errors[i].element,
        message = errors[i].message;

    if (!Nette.inArray(messages, message)) {
      messages.push(message);
      $(elem).closest('.form-group').addClass('has-error');
      var errorTitle = $('<div class="help-block">' + errors[i].message +  '</div>')
      errorTitle.insertAfter($(elem));

      if (!focusElem && elem.focus) {
        focusElem = elem;
      }
    }
  }

  if (messages.length) {

    if (focusElem) {
      focusElem.focus();
    }
  }
};

$(document).ready(function(){

  $.nette.init();
  $.nette.ext("modals", {
    before : function(jqXHR, settings) {
      console.log('ajax');
      var id = $(settings.nette.e.target).closest('tr').attr('id');

      if (id) {
        settings.url = settings.url + '/?rowId=' + id + '&test=test';
      }
    },
    success: function(payload) {
      if (payload.redirect) {
        $("#page-modal").modal("hide");
      } else if(payload.isModal) {
        $('#page-modal').modal('show');
      }
    }
  });

  $.nette.ext("ajaxRedirect", {
    success: function (payload) {
      if (payload.redirect) {
        $.nette.ajax(payload.redirect);
      }
    }
  });
  /** Side menu opened submanu triger class adjustment */
  $('#side-menu li ul li a.active').parents('li').addClass('active');

});

function animateClick(element, animation) {
  var element =$(element).find('button');
  element.removeClass('vivify ' + animation);
  setTimeout(function(){
  element.addClass('vivify ' + animation);
  },100);
}
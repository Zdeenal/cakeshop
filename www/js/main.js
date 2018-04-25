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

  showFlashes();

  $.nette.init();
  $.nette.ext("modals", {
    before : function(jqXHR, settings) {
      var id = $(settings.nette.e.target).closest('tr').attr('id');

      if (id) {
        var separator = (settings.url.match(/\?/) ? '&' : '?');
        settings.url = settings.url + separator + 'rowId=' + id;
      }
    },
    success: function(payload) {
      $("#page-modal").on("hidden.bs.modal", function () {
        reloadDatables();
      });
      if (payload.redirect || payload.closeModal) {
        $("#page-modal").modal("hide");
      } else if(payload.isModal) {
        $('#page-modal').modal('show');
      }
    }
  });

  $.nette.ext("flashes", {
    success: function(payload) {
     if (payload.messages) {
       showFlashes(payload.messages)
     }
    }
  });

  $.nette.ext("prompt", {
    success: function(payload, status, jqXHR, settings) {

      if (payload.prompt) {
        callPrompt(
          payload.prompt['title'] ? payload.prompt['title'] : '',
          payload.prompt['message'] ? payload.prompt['message'] : '',
          function (success) {
          if (success) {
            $.ajax({
                url: settings.url + '&rowId=' + payload.id + '&confirmed=true',
                success : function(payload) {
                  if(payload.success) {
                    reloadDatables();
                  }
                }
              }
            );
          }
        });
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

function reloadDatables () {
  var tableId = $('.dataTable').first().attr('id');
  $('#' + tableId).dataTable().api().ajax.reload(undefined,false);
}

function animateClick(element, animation) {
  var element =$(element).find('button');
  element.removeClass('vivify ' + animation);
  setTimeout(function(){
  element.addClass('vivify ' + animation);
  },100);
}

function goBack(element, backTo) {
  var modal = $(element).closest('.modal');
  if (modal.length) {
    modal.modal('hide');
  } else {
    location.replace(backTo);
  }
}

function showFlashes(messages) {
  if (messages) {
    for (var idx in messages) {
      toast(messages[idx]['message'], messages[idx]['type']);
    }
  } else {
    $('.flash-message').each(function (index, item) {
      var message = $(item).data('message');
      var type    = $(item).data('type') !== undefined ? $(item).data('type') : 'info';
      toast(message, type);
    });
  }
}

function callPrompt(title,message, callback) {
  bootbox.confirm({
    title: title,
    message: message,
    buttons: {
      confirm: {
            label: 'Ano',
        className: 'btn-theme'
      },
      cancel: {
        label: 'Ne',
        className: 'btn-theme-invert'
      }
    },
    callback: function (result) {
      callback(result)
    }
  });
}
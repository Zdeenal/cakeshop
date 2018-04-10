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
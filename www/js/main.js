/** Override Nette forms Validation **/
Nette.showFormErrors = function(form, errors) {
  console.log(errors);
  var messages = [],
      focusElem;

  for (var i = 0; i < errors.length; i++) {
    var elem = errors[i].element,
        message = errors[i].message;

    if (!Nette.inArray(messages, message)) {
      messages.push(message);

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
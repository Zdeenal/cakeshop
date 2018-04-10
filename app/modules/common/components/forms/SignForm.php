<?php
  namespace App\Common\Components\Forms;
  use Nette\Forms\Controls;
  use Nette\Application\UI\Form;


  /**
   * Bootstrap 3 Sign Form Component
   *
   * @author  Zdeněk Houdek
   * @created 06.04.2018
   */
  class SignForm extends Form
  {
  
    public function render(...$args) {
      $renderer = $this->getRenderer();
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['pair']['container'] = 'div class=form-group';
      $renderer->wrappers['pair']['.error'] = 'has-error';
      $renderer->wrappers['control']['container'] = NULL;
      $renderer->wrappers['label']['container'] = NULL;
      $renderer->wrappers['control']['description'] = 'span class=help-block';
      $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
      $this->getElementPrototype()->setAttribute('class', 'form-no-labels')->setAttribute('role', 'form');
      foreach ($this->getControls() as $control) {
        if ($control instanceof Controls\Button) {
          $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-lg btn-success btn-block' : 'btn btn-default');
          $usedPrimary = TRUE;
        } elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
          $control->getControlPrototype()->addClass('form-control');
        } elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
          $control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
        }
      }
      parent::render($args);
    }
  
  }
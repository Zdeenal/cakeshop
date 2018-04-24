<?php
  namespace App\Common\Components\Forms;
  use Nette\Application\UI\Form,
      Nette\Forms\Controls;
  use Tracy\Dumper;


  /**
   * Bootstrap 3 Form Component
   *
   * @author  Zdeněk Houdek
   * @created 06.04.2018
   */
  class BSForm extends Form
  {
  
    public function render(...$args) {
      $renderer = $this->getRenderer();
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['pair']['container'] = 'div class=form-group';
      $renderer->wrappers['pair']['.error'] = 'has-error';
      $renderer->wrappers['control']['container'] = 'div class=col-sm-9';
      $renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
      $renderer->wrappers['control']['description'] = 'span class=help-block';
      $renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';
      foreach ($this->getControls() as $control) {
        if ($control instanceof Controls\Button) {
          $control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-theme' : 'btn btn-theme-invert');
          $usedPrimary = TRUE;
          if ($control->getName() == 'cancel') {
            $control->setAttribute('onClick','goBack(this, "' . $this->getPresenter()->link(':') . '")');
          }
        } elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
          $control->getControlPrototype()->addClass('form-control');
        } elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
          $control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
        }
      }
      parent::render($args);
    }
    
    public function isAjax() {
      $this->getElementPrototype()->setAttribute('class', 'ajax');
    }
  
  }
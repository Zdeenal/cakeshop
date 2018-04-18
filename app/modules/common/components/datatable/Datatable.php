<?php
  
  namespace App\Common\Components\Layout;
  use Nette\Application\UI\Control;
  use Nette\Utils\Arrays;
  use Tracy\Dumper;

  /**
   * Class Datatable  ...
   *
   * @author  Zdeněk Houdek
   * @created 17.04.2018
   */
  class Datatable extends Control
  {
    public function render($args) {
      $template = $this->template;
      $template->id = Arrays::get($args, 'id', 'datatable' . time());
      $template->columns = Arrays::get($args, 'columns', '');
      $template->setFile(__DIR__. '/controll.latte');
      $template->render();
    }
    
    public function renderScript($args) {
      $template = $this->template;
      $id = Arrays::get($args, 'id', 'datatable' . time());
      $template->id = substr($id,0,1) !== '#' ? '#' . $id : $id;
      $template->setFile(__DIR__. '/script.latte');
      $template->render();
    }
  
    public function handleGetData() {
      return $this->presenter->actionGetData();
    }
  }
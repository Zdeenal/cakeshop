<?php
  
  namespace App\Common\Components\Layout;
  use Nette\Application\Responses\JsonResponse;
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
    public function render() {
      $template = $this->template;
      $template->id = $this->presenter->getId();
      $template->columns = implode(',' , $this->presenter->getDataColumns(TRUE));
      $template->buttons = $this->presenter->getButtons();
      $template->headers = $this->presenter->getHeaders();
      $template->setFile(__DIR__. '/controll.latte');
      $template->render();
    }
    
    public function renderScript() {
      $template = $this->template;
      $template->id = $this->presenter->getId(TRUE);
      $columns = [];
      foreach ($this->presenter->getDataColumns(TRUE, TRUE) as $column) {
        $columns[] = [
          'data' => $column
        ];
      }
      $template->columns = $columns;
      $template->actions = $this->presenter->getActions();
      $template->setFile(__DIR__. '/script.latte');
      $template->render();
    }
  
    public function handleGetData() {
      return $this->presenter->actionGetData();
    }
    
  }
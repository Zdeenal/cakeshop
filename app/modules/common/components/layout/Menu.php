<?php
  namespace App\Common\Components\Layout;
  use Nette\Application\UI\Control;
  


  /**
   * Menu Component
   *
   * @author  Zdeněk Houdek
   * @created 06.04.2018
   */
  class Menu extends Control
  {
    protected  $items = [];
    protected $templateFile = '/templates/Menu/menu.latte';
    
    public function __construct($items) {
      $this->items = $items;
    }
  
    public function render(...$args) {
      $template = $this->template;
      $template->setFile(__DIR__ . $this->templateFile);
      $template->items = $this->items;
      $template->render();
    }
  
  }
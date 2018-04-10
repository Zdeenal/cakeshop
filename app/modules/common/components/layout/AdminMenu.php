<?php
  namespace App\Common\Components\Layout;
  use Tracy\Dumper;


  /**
   * AdminMenu Component
   *
   * @author  Zdeněk Houdek
   * @created 06.04.2018
   */
  class AdminMenu extends Menu
  {
    protected  $items = [];
    protected $templateFile = '/templates/Menu/adminMenu.latte';
    
    public function __construct($items) {
      parent::__construct($items);
    }
  
    public function render(...$args) {
      parent::render(...$args);
    }
  
  }
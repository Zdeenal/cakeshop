<?php
  
  namespace App\Common\Factory;
  use Nette\Database\Context;

  /**
   * Class MenuFactory  ...
   *
   * @author  Zdeněk Houdek
   * @created 10.04.2018
   */
  class MenuFactory
  {
    /** @var Context */
    private $db;
    
    public function __construct( Context $db) {
      $this->db = $db;
    }
  
    
    public function create($module  = NULL, $component = 'Menu', $items = []){
      if ($module) {
        $items =
          $this->db->table('menu_items')
            ->select('*, menu.component')
            ->where('menu.module.name = ?', $module)
            ->where('active = 1')
        ;
      }
      $component = ($result = $items->fetchField('component')) ? $result : $component;
      $className = "App\Common\Components\Layout\\" . $component;
      return new $className($items);
    }
  
  }
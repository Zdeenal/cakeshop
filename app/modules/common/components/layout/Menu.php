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
      $template->items = $this->createItemsTree($this->items->fetchAll());
      $template->render();
    }
  
    public function handleClick($presenter , $action) {
      $this->presenter->redirect($presenter . ':' . $action);
      $this->redrawControl();
    }
  
    private function createItemsTree($items, $parent = NULL) {
      if ($parent === NULL) {
        $items = array_map(function(&$item){
          $item = $item->toArray();
          $item['link'] =
            $item['presenter'] || $item['action'] ?
            $this->presenter->link($item['presenter'] . ':' . $item['action']) : '#';
          return $item;
        }, $items);
        $menus = array_filter($items, function($item){
            return !$item['parent_menu_item_id'];
        });
      } else {
        $menus = array_filter($items, function($item) use ($parent){
          return $item['parent_menu_item_id'] == $parent;
        });
      }
  
      foreach ($menus as &$menu) {
        /* @var $menu MenuEntity */
        $children = $this->createItemsTree($items , $menu['menu_item_id']);
        if ($children) {
          $menu['children'] = $children;
        }
      }
      return $menus;
    }
  
  }
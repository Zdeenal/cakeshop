<?php
  namespace App\Back\Components\Layout;

  use App\Back\Factories\NavBarLinksFactory;
  use Nette\Application\UI\Control;

  /**
   * Class NavBarLinks  ...
   *
   * @author  Zdeněk Houdek
   * @created 12.04.2018
   */
  class NavBarLinks extends Control
  {
    /** @var NavBarLinksFactory */
    private $factory;
    
    public function __construct(NavBarLinksFactory $facory) {
      $this->factory = $facory;
    }
  
    public function render(...$args) {
      $items = $args && array_key_exists('items', $args[0]) ? $args[0]['items'] : [];
      $template = $this->template;
      $template->setFile(__DIR__ .'/templates/navBarLink.latte');
      $template->components = $items;
      $template->render();
    }
    
    protected function createComponentUser() {
      return $this->factory->create('user');
    }
  
    protected function createComponentAlerts() {
      return $this->factory->create('alerts');
    }
  
  }
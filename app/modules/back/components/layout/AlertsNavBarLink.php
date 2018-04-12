<?php
  
  namespace App\Back\Components\Layout;
  use Nette\Application\UI\Control;

  /**
   * Class AlertsNavBarLink  ...
   *
   * @author  Zdeněk Houdek
   * @created 12.04.2018
   */
  class AlertsNavBarLink extends Control
  {
    
  
    public function render(...$args) {
      $template = $this->template;
      $template->setFile(__DIR__ .'/templates/alertsNavBarLink.latte');
      $template->alerts = [];
      $template->render();
    }
    
  }
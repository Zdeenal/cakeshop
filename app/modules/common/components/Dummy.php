<?php
  namespace App\Common\Components\Layout;
  use Nette\Application\UI\Control;
  
  
  
  /**
   * Dummy component
   *
   * @author  Zdeněk Houdek
   * @created 06.04.2018
   */
  class Dummy extends Control
  {
    
    public function render(...$args) {
      $template = $this->template;
      $template->setFile(__DIR__. '/dummy.latte');
      $template->render();
    }
    
  }
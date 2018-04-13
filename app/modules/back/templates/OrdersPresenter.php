<?php
  
  namespace App\Back\Presenters;
  
  use Nette;
  
  /**
   * Class OrdersPresenter
   *
   * @author  Zdeněk Houdek
   * @created 13.04.2018
   */
  class OrdersPresenter extends BasePresenter
  {
    
    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database) {
      $this->database = $database;
    }
    
  }
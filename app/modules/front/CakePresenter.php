<?php
  
  namespace App\Front\Presenters;
  
  
  use Nette;
  
  /**
   * Class CakePresenter
   *
   * @author  Zdeněk Houdek
   * @created 04.04.2018
   */
  class CakePresenter extends BasePresenter
  {
    
    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database) {
      $this->database = $database;
    }
    
  }
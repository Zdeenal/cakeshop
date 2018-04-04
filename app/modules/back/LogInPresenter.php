<?php
  
  namespace App\Back\Presenters;
  
  use Nette;
  
  /**
   * Class LogIn
   *
   * @author  Zdeněk Houdek
   * @created 28.03.2018
   */
  class LogInPresenter extends BasePresenter
  {
    
    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database) {
      $this->database = $database;
    }
    
  }
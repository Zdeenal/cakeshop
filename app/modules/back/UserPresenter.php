<?php
  
  namespace App\Back\Presenters;
  
  use Nette;
  
  /**
   * Class UserPresenter
   *
   * @author  Zdeněk Houdek
   * @created 04.04.2018
   */
  class UserPresenter extends BasePresenter
  {
    
    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database) {
      $this->database = $database;
    }
    
  }
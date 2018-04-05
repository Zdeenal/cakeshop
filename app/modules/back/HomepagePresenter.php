<?php
  
  namespace App\Back\Presenters;
  
  use Nette;
  use Tracy\Debugger;
  use Tracy\Dumper;

  /**
   * Class Homepage
   *
   * @author  Zdeněk Houdek
   * @created 28.03.2018
   */
  class HomepagePresenter extends BasePresenter
  {
    
    
    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database) {
      $this->database = $database;
    }
  
  
  }
<?php
  
  namespace App\Front\Presenters;
  
  use Nette;
  
  /**
   * Class HomepagePresenter
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
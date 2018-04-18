<?php
  
  namespace App\Back\Presenters;
  
  use App\Traits\DatatableTrait;
  use Nette;
  
  /**
   * Class UserPresenter
   *
   * @author  Zdeněk Houdek
   * @created 04.04.2018
   */
  class UserPresenter extends BasePresenter
  {
    use DatatableTrait;
    /** @var bool Do use Datatables plugin */
    protected $datatables = TRUE;
    
    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database) {
      $this->database = $database;
    }
  
    public function renderRights() {
    
    }
    
  }
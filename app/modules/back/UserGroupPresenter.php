<?php
  
  namespace App\Back\Presenters;
  
  use App\Traits\DatatableTrait;
  use Nette;
  
  /**
   * Class UserGroup
   *
   * @author  Zdeněk Houdek
   * @created 13.04.2018
   */
  class UserGroupPresenter extends BasePresenter
  {
    use DatatableTrait;
  
    /** @var bool Do use Datatables plugin */
    protected $datatables = TRUE;
    
    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database) {
      $this->database = $database;
    }
    
    protected function startup() {
      parent::startup();
      $this->setDTColumns([
        'Id skupiny'  => ['column' => 'user_group_id', 'prefixTableName' => TRUE],
        'Název'       => ['column' => 'name', 'prefixTableName' => TRUE],
        'Rodič'       => 'parent_group.name',
       ]
      );
    }
  
  
  }
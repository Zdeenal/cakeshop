<?php
  
  namespace App\Back\Presenters;
  
  use App\Traits\DatatableTrait;
  use Nette;
  use Tracy\Dumper;

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
        'Id skupiny'  => ['column' => 'user_group_id', 'prefixTableName' => TRUE, 'operator' => '='],
        'Název'       => ['column' => 'name', 'prefixTableName' => TRUE ],
        'Rodič'       => 'parent_group.name'
       ]
      );
      $this->setDTActions([
        'edit' => [
          'button' => '<button class="btn btn-theme"><i class="fa fa-pencil"></i></button>',
          'action' => $this->link('edit')
        ],
        'delete' => [
          'button' => '<button class="btn btn-warning"><i class="fa fa-trash"></i></button>',
          'action' => $this->link('delete')
        ]
      ]);
    }
  
    public function actionEdit() {
      $id = $this->getParameter('rowId');
        if ($this->isAjax()) {
          $this->payload->isModal = TRUE;
          $this->template->group = $this->database->table('user_groups')->get($id);
          $this->redrawControl('modal');
        }
      }
  
    public function actionDelete() {
      if ($this->isAjax()) {
        $this->payload->isModal = TRUE;
        $this->redrawControl('modal');
      }
    }
  
  }
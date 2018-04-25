<?php
  
  namespace App\Back\Presenters;
  
  use App\Common\Components\Forms\BSForm;
  use App\Helpers\Strings;
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
    
    const _SUCCESS_MESSAGE = ['message' => 'Uživatel {NAME} byl uložen.', 'type' => 'success'];
    const _FAIL_MESSAGE = ['message' => 'Uživatel {NAME} nebyl uložen!', 'type' => 'error'];
    
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
        'Název'       => ['column' => 'name', 'prefixTableName' => TRUE ],
        'Rodič'       => 'parent_group.name'
       ]
      );
      $this->setDTActions([
        'edit' => [
          'button' => '<button title="Upravit skupinu" class="btn btn-theme-datatable btn-sm"><i class="fa fa-pencil"></i></button>',
          'action' => $this->link('edit')
        ],
        'delete' => [
          'button' => '<button title="Smazat skupinu" class="btn btn-theme-datatable btn-sm"><i class="fa fa-trash"></i></button>',
          'action' => $this->link('delete!')
        ]
      ]);
      
      $this->setDTButtons([
        'add' => [
          'class' => 'btn btn-circle btn-theme',
          'title' => 'Přidat skupinu',
          'text' => 'Přidat skupinu',
          'action' => '',
          'icon' => 'plus',
          'action' => $this->link('edit')
        ]
      ]);
    }
    
    protected function createComponentUserGroupForm() {
        $form = new BSForm();
        $form->isAjax();
        $form->addHidden('user_group_id');
        $form->addText('name','Název')->setRequired('Musíte vyplnit název');
        $form->onSuccess[] = [$this, 'userGroupFormSubmit'];
        return $form;
        
    }
  
    public function actionEdit() {
      $id = $this->getParameter('rowId');
      $group = $this->database->table('user_groups')->get($id);
      $this->template->title = $group ?
        "Editovat skupinu" :
        "Přidat skupinu";
      $this->template->group = $group;
      $this->template->modal = FALSE;
      
      $groups = $this->database->table('user_groups')->select('user_group_id, name');
      if ($group) {
        $groups->where('user_group_id != ?', $group->user_group_id);
      }
      $this['userGroupForm']->addSelect('parent_group_id','Rodič',
        [NULL => ''] + $groups->fetchPairs('user_group_id', 'name'));
      if ($group) {
        $this['userGroupForm']->setDefaults($group);
      }
  
  
      $this['userGroupForm']->addSubmit('submit','Uložit');
      $this['userGroupForm']->addButton('cancel','Zrušit')->setOmitted(TRUE);
      
      if ($this->isAjax()) {
        $this->template->modal = TRUE;
        $this->payload->isModal = TRUE;
        $this->redrawControl('modal');
      }
    }
  
    public function handledelete() {
      $id = $this->getParameter('rowId');
      if (!$this->isAjax() || $this->getParameter('confirmed')) {
        if ($this->isAjax()) {
            $this->payload->success = TRUE;
          $this->sendPayload();
        }
      } else {
        if ($this->isAjax()) {
          $message = ' ';
          $group = $this->database->table('user_groups')->get($id)->toArray();
          $childGroups = $this->database->table('user_groups')->where('parent_group_id = ?', $id);
          if ($childGroups->count()) {
            $message .= '<strong>Je rodičem pro tyto skupiny:</strong><ul class="list-inline">';
              foreach ( $childGroups as $childGroup) {
                $message .= '<li>' . $childGroup->name .'</li>';
              }
            $message .= '</ul>';
          }
          
          $this->payload->id = $id;
          $this->payload->prompt = [
            'title'   => Strings::placeholders($group,'Opravdu chcete smazat skupinu {NAME} ?'),
            'message' => $message
          ];
          $this->sendPayload();
        }
      }
    }
  
    public function userGroupFormSubmit(Nette\Application\UI\Form $form, \stdClass $values) {
      $values = array_map(function($item){return $item ? $item : NULL;},(array)$values);
      try {
        if ($id = Nette\Utils\Arrays::get($values, 'user_group_id')) {
          $this->database->table('user_groups')->where('user_group_id = ?', $id)->update($values);
        } else {
          $this->database->table('user_groups')->insert($values);
        }
      } catch (Exception $e) {
        if ($this->isAjax()) {
          $this->payload->closeModal = TRUE;
          $this->payload->messages[] = Strings::placeholders($values,self::_FAIL_MESSAGE);
          $this->sendPayload();
        } else {
          $this->flashMessage(...Strings::placeholders($values,self::_FAIL_MESSAGE));
          $this->redirect(301, ':');
        }
      }
      
      if ($this->isAjax()) {
        $this->payload->closeModal = TRUE;
        $this->payload->messages[] = Strings::placeholders($values,self::_SUCCESS_MESSAGE);
        $this->sendPayload();
      } else {
        $this->flashMessage(...Strings::placeholders($values,self::_SUCCESS_MESSAGE));
        $this->redirect(301, ':');
      }
    }
  
  }
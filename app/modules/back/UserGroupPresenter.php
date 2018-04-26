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
    
    const _SUCCESS_MESSAGE = ['Skupina {NAME} byla uložena.','success'];
    const _FAIL_MESSAGE = ['Chyba! Skupina {NAME} nebyla uložena!', 'error'];
  
    const _SUCCESS_DELETE_MESSAGE = ['Skupina {NAME} byla odstraněna.','success'];
    const _FAIL_DELETE_MESSAGE = ['Chyba! Skupina {NAME} nebyla odstraněna!', 'error'];
    
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
      
      /** CONFIRMED */
      if (!$this->isAjax() || $this->getParameter('confirmed')) {
        $group = $this->database->table('user_groups')->get($id);
        try {
          $this->database->table('user_groups')
            ->where('parent_group_id = ?', $group->user_group_id)
            ->update(['parent_group_id' => NULL]);

          $this->database->table('users')
            ->where('user_group_id = ?', $group->user_group_id)
            ->update(['user_group_id' => NULL]);

          $this->database->table('user_groups')
            ->where('user_group_id = ?', $group->user_group_id)
            ->delete();
          
          
        } catch (Exception $e) {
          $this->flashMessage(...Strings::placeholders($group->toArray(),self::_FAIL_DELETE_MESSAGE));
          if ($this->isAjax()) {
            $this->payload->success = FALSE;
            $this->sendPayload();
          } else {
            $this->redirect(301, ':');
          }
        }
  
        $this->flashMessage(...Strings::placeholders($group->toArray(),self::_SUCCESS_DELETE_MESSAGE));
        if ($this->isAjax()) {
            $this->payload->success = TRUE;
          $this->sendPayload();
        } else {
          $this->redirect(301, ':');
        }
      
        /** PROMPT*/
      } else {
        if ($this->isAjax()) {
          $message = '';
          $group = $this->database->table('user_groups')->get($id)->toArray();
          $childGroups = $this->database->table('user_groups')->where('parent_group_id = ?', $id);
          if ($childGroups->count()) {
            $message .= '<div><strong>Je rodičem pro tyto skupiny:</strong><ul class="list-no-marks">';
              foreach ( $childGroups as $childGroup) {
                $message .= '<li>' . $childGroup->name .'</li>';
              }
            $message .= '</ul></div>';
          }
  
          $users = $this->database->table('users')->where('user_group_id = ?', $id);
          if ($users->count()) {
            $message .= '<div><strong>Je použita u těchto uživatelů:</strong><ul class="list-no-marks">';
            foreach ( $users as $user) {
              $message .= '<li>' . $user->username .'</li>';
            }
            $message .= '</ul></div>';
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
        $this->flashMessage(...Strings::placeholders($values,self::_FAIL_MESSAGE));
        if ($this->isAjax()) {
          $this->payload->closeModal = TRUE;
          $this->sendPayload();
        } else {
          $this->redirect(301, ':');
        }
      }
      
      $this->flashMessage(...Strings::placeholders($values,self::_SUCCESS_MESSAGE));
      if ($this->isAjax()) {
        $this->payload->closeModal = TRUE;
        $this->sendPayload();
      } else {
        $this->redirect(301, ':');
      }
    }
  
  }
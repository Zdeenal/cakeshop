<?php
  
  namespace App\Back\Presenters;
  
  use App\Back\Models\UserGroupModel;
  use App\Back\Models\UserModel;
  use App\Common\Components\Forms\BSForm;
  use App\Helpers\Strings;
  use App\Traits\DatatableTrait;
  use Nette;
  
  /**
   * Class UserGroup
   * Administration of user groups
   *
   * @author  Zdeněk Houdek
   * @created 13.04.2018
   */
  class UserGroupPresenter extends BasePresenter
  {
    /** datatable extension for presenter*/
    use DatatableTrait;
    
    /** @var UserGroupModel */
    private $model;
    /** @var UserModel */
    private $userModel;
    /** @var bool Do use Datatables plugin */
    protected $datatables = TRUE;
    
    /**
     * UserGroupPresenter constructor.
     *
     * @param UserGroupModel $model
     * @param UserModel      $userModel
     */
    public function __construct(UserGroupModel $model, UserModel $userModel) {
      $this->model     = $model;
      $this->userModel = $userModel;
    }
    
    /**
     * Set up datatable properties
     *
     * @throws Nette\Application\UI\InvalidLinkException
     */
    protected function startup() {
      parent::startup();
      $this->setDTColumns([
          'Název' => ['column' => 'name', 'prefixTableName' => TRUE],
          'Rodič' => 'parent_group.name'
        ]);
      $this->setDTActions([
        'edit'   => [
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
          'class'  => 'btn btn-circle btn-theme',
          'title'  => 'Přidat skupinu',
          'text'   => 'Přidat skupinu',
          'action' => '',
          'icon'   => 'plus',
          'action' => $this->link('edit')
        ]
      ]);
    }
    
    /**
     * Edit/Add Action
     */
    public function actionEdit() {
      $id                    = $this->getParameter('rowId');
      $group                 = $this->model->getItemById($id);
      $this->template->title = $group ? "Editovat skupinu" : "Přidat skupinu";
      $this->template->group = $group;
      $this->template->modal = FALSE;
      
      $this['userGroupForm']->addSelect('parent_group_id', 'Rodič', $this->model->getPairsForSelect('user_group_id', 'name', $id));
      if ($group) {
        $this['userGroupForm']->setDefaults($group);
      }
      $this['userGroupForm']->addSubmit('submit', 'Uložit');
      $this['userGroupForm']->addButton('cancel', 'Zrušit')->setOmitted(TRUE);
      
      if ($this->isAjax()) {
        $this->template->modal  = TRUE;
        $this->payload->isModal = TRUE;
        $this->redrawControl('modal');
      }
    }
    
    /**
     * Delete action with prompt
     */
    public function handledelete() {
      $id = $this->getParameter('rowId');
      $this->actionDelete(
        'Opravdu chcete smazat skupinu {NAME} ?',
        UserGroupModel::_SUCCESS_DELETE_MESSAGE,
        UserGroupModel::_FAIL_DELETE_MESSAGE,
        ['message' => $this->model->getDeleteMessage($id) . $this->userModel->getDeleteMessageForUserGroup($id)]);
    }
    
    
    /**
     * Edit/Add form
     *
     * @return BSForm
     */
    protected function createComponentUserGroupForm() {
      $form = new BSForm();
      $form->isAjax();
      $form->addHidden('user_group_id');
      $form->addText('name', 'Název')->setRequired('Musíte vyplnit název');
      $form->onSuccess[] = [$this, 'userGroupFormSubmit'];
      return $form;
    }
    
    /**
     *
     * Edit/Add form submited action
     *
     * @param Nette\Application\UI\Form $form
     * @param \stdClass                 $values
     *
     * @throws Nette\Application\AbortException
     */
    public function userGroupFormSubmit(Nette\Application\UI\Form $form, \stdClass $values) {
      $values = array_map(function ($item) {
        return $item ? $item : NULL;
      }, (array)$values);
      
      /** Unique name*/
      if (!$this->model->checkUniqueValue($values, 'name')) {
        $this->flashMessage(...Strings::placeholders($values, UserGroupModel::_FAIL_DUPLICITY_NAME_MESSAGE));
        $this->finishWithPayload();
      }
      try {
        $this->model->store($values);
      } catch (Exception $e) {
        $this->flashMessage(...Strings::placeholders($values, UserGroupModel::_FAIL_MESSAGE));
        $this->finishWithPayload(['closeModal' => TRUE]);
      }
      $this->flashMessage(...Strings::placeholders($values, UserGroupModel::_SUCCESS_MESSAGE));
      $this->finishWithPayload(['closeModal' => TRUE]);
    }
  }

  
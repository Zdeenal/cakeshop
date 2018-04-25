<?php
  
  namespace App\Back\Presenters;
  
  use App\Common\Components\Forms\SignForm;
  use App\Common\Factory\SignFormFactory;
  use App\Back\Services\Authenticator;
  use Nette\Security\User;
  use Nette;
  
  /**
   * Class LogIn
   *
   * @author  Zdeněk Houdek
   * @created 28.03.2018
   */
  class LoginPresenter extends BasePresenter
  {
    protected $includeMenu = FALSE;
    /** @var Nette\Database\Context */
    private $database;
    
    /** @var SignFormFactory */
    private $factory;
    
    /** @var Authenticator */
    private $authenticator;
  
    /** @var User */
    private $user;
    
    
    public function __construct(User $user ,Nette\Database\Context $database, SignFormFactory $factory, Authenticator $authenticator) {
      $this->factory = $factory;
      $this->user = $user;
      $this->database = $database;
      $this->authenticator = $authenticator;
    }
  
    protected function checkAuth()
    {
      $this->getUser()->setAuthenticator($this->authenticator);
    }
    
    public function renderIn($key) {
      if ($this->getUser()->isLoggedIn()) {
        $this->redirect('Homepage:default');
      }
    }
  
  
    protected function createComponentSignInForm()
    {
      $form = $this->factory->create();
      $form->onSuccess[] = [$this, 'formSucceeded'];
      $form->onSuccess[] = function () {
        $key = $this->getParameter('key');
        $this->flashMessage('Přihlášení proběhlo úspěšně', 'success');
        $this->restoreRequest($key);
        $this->redirect('Homepage:');
      };
      
      return $form;
    }
  
  
    public function actionOut()
    {
      $this->getUser()->logout();
      $this->flashMessage('Odhlášení proběhlo v pořádku', 'success');
      $this->redirect(':in');
    }
  
  
    public function formSucceeded(SignForm $form, $values)
    {
      if ($values->remember) {
        $this->user->setExpiration('14 days', FALSE);
      } else {
        $this->user->setExpiration('20 minutes', TRUE);
      }
    
      try {
        $this->user->login($values->username, $values->password);
      } catch (Nette\Security\AuthenticationException $e) {
        $form->addError($e->getMessage());
      }
    }
  }
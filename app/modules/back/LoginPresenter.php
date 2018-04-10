<?php
  
  namespace App\Back\Presenters;
  
  use App\Common\Factory\SignFormFactory;
  use App\Back\Services\Authenticator;
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
    
    public function __construct(Nette\Database\Context $database, SignFormFactory $factory, Authenticator $authenticator) {
      $this->factory = $factory;
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
      $form->onSuccess[] = function () {
        $key = $this->getParameter('key');
        $this->restoreRequest($key);
        $this->redirect('Homepage:');
      };
      return $form;
    }
  
  
    public function actionOut()
    {
      $this->getUser()->logout();
      $this->redirect(':in');
    }
    
  }
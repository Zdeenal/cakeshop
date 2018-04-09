<?php
  
  namespace App\Front\Presenters;
  use Nette;
  use App\Common\Factory\SignFormFactory;
  use App\Front\Services\Authenticator;

  /**
   * Class LoginPresenter  ...
   *
   * @author  Zdeněk Houdek
   * @created 09.04.2018
   */
  class LoginPresenter extends BasePresenter
  {
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
  
    public function renderIn($key) {
      if ($this->getUser()->isLoggedIn()) {
        $this->redirect('Homepage:');
      }
    }
  
  
    protected function createComponentSignInForm()
    {
      $this->getUser()->setAuthenticator($this->authenticator);
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
      $this->redirect('Homepage:');
    }
  }
<?php

namespace App\Front\Presenters;

use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
  protected $module = 'front';
  protected $secured = FALSE;
  
  protected function startup() {
    parent::startup();
    if ($this->secured) {
      $this->checkAuth();
    }
  }
  
  protected function checkAuth() {
    if (!$this->getUser()->isLoggedIn()) {
      $this->flashMessage('Abyste mohl pokračovat, přihlaste se.');
      $this->redirect('Login:in', $this->storeRequest());
    }
  }
  
  protected function beforeRender() {
    parent::beforeRender();
    $this->template->className = $this->getName();
  }
  
  public function checkRequirements($element) {
    $this->getUser()->getStorage()->setNamespace('front');
    parent::checkRequirements($element);
  }

}

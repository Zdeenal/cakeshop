<?php

namespace App\Back\Presenters;


use Nette;
use App\Model;
use Tracy\Debugger;
use Tracy\Dumper;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
  protected function startup() {
    parent::startup();
    $this->checkAuth();
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
    $this->getUser()->getStorage()->setNamespace('back');
    parent::checkRequirements($element);
  }
  
}

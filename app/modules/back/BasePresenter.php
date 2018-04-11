<?php

namespace App\Back\Presenters;


use App\Common\Components\Layout\Dummy;
use App\Common\Factory\MenuFactory;
use Nette;
use App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
  protected $module = 'back';
  protected $includeMenu = TRUE;
  
  /** @var MenuFactory @inject*/
  public $menuFactory;
  
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
  
  protected function createComponentMenu() {
    return $this->includeMenu ? $this->menuFactory->create($this->module) : new Dummy();
  }
  
  protected function beforeRender() {
    parent::beforeRender();
    $this->template->className = $this->getName();
  }
  
  public function checkRequirements($element) {
    $this->getUser()->getStorage()->setNamespace('back');
    parent::checkRequirements($element);
  }
  
  /**
   * @return bool
   */
  public function isIncludeMenu() {
    return $this->includeMenu;
  }
}

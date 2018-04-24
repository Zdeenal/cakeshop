<?php

namespace App\Back\Presenters;


use App\Back\Components\Layout\NavBarLinks;
use App\Back\Factories\NavBarLinksFactory;
use App\Common\Components\Layout\Dummy;
use App\Common\Components\Layout\Modal;
use App\Common\Factory\MenuFactory;
use App\Services\WebDir;
use Nette;
use App\Model;
use Tracy\Dumper;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
  protected $module = 'back';
  protected $includeMenu = TRUE;
  
  /** @var NavBarLinksFactory @inject*/
  public $navBarLinksFactory;
  
  /** @var MenuFactory @inject*/
  public $menuFactory;
  
  /** @var WebDir @inject*/
  public $webDir;
  
  protected $datatables = FALSE;
  
  protected $scripts = [];
  
  protected $styles = [];
  
  protected $preloads = [];
  
  
  protected function startup() {
    parent::startup();
    $this->addDefaultPreloads();
    $this->addDefaultStyles();
    $this->addDefaultScripts();
    if ($this->datatables) {
      $this->useDatatables();
    }
    $this->addPresenterStyles();
    $this->addPresenterScripts();
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
  
  public function createComponentNavBarLinks() {
    return $this->includeMenu ? new NavBarLinks($this->navBarLinksFactory) : new Dummy();
  }
  
  public function createComponentModal() {
    return new Modal();
  }
  
  protected function beforeRender() {
    parent::beforeRender();
    $this->template->className = $this->getName();
    $this->template->styles= $this->styles;
    $this->template->scripts= $this->scripts;
    $this->template->preloads= $this->preloads;
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
  
  /** SCRIPTS , STYLES , PRELOADS */
  
  protected function addDefaultPreloads() {
    $defaultPreloads = [
    ];
    foreach ($defaultPreloads as $preload) {
      $this->preloads[] = $preload;
    }
  
  }
  
  protected function getBasePath() {
    return $this->getHttpRequest()->getUrl()->getBasePath();
  }
  
  protected function addDefaultStyles() {
    $defaultStyles = [
      ['href' => 'http://fonts.googleapis.com/css?family=Didact+Gothic&subset=latin,latin-ext', 'preload' => FALSE],
      ['href' => $this->getBasePath() . '/admin_theme/vendor/bootstrap/css/bootstrap.min.css', 'preload' => TRUE],
      ['href' => '//cdnjs.cloudflare.com/ajax/libs/metisMenu/2.7.1/metisMenu.min.css', 'preload' => TRUE],
      ['href' => $this->getBasePath() . '/admin_theme/less/sb-admin-2.css', 'preload' => TRUE],
      ['href' => $this->getBasePath() . '/admin_theme/vendor/font-awesome/css/font-awesome.min.css', 'preload' => FALSE],
      ['href' => $this->getBasePath() . '/css/libs/vivify.css', 'preload' => FALSE],
      ['href' => $this->getBasePath() . '/css/style.css', 'preload' => TRUE],
    ];
   foreach ($defaultStyles as $style) {
     $this->addStyle($style);
   }
  }
  
  protected function addStyle($style) {
    if (array_key_exists('preload', $style) && $style['preload']) {
      $this->preloads[] = ['href' => $style['href'], 'as' => 'style'];
    }
    $this->styles[] = $style;
  }
  
  protected function addDefaultScripts() {
    $defaultScripts = [
      ['src' => $this->getBasePath() . '/admin_theme/vendor/jquery/jquery.min.js', 'preload' => TRUE],
      ['src' => $this->getBasePath() . '/admin_theme/vendor/bootstrap/js/bootstrap.min.js', 'preload' => TRUE],
      ['src' => '//cdnjs.cloudflare.com/ajax/libs/metisMenu/2.7.1/metisMenu.min.js', 'preload' => TRUE],
      ['src' => $this->getBasePath() . '/admin_theme/vendor/raphael/raphael.min.js', 'preload' => TRUE],
      ['src' => $this->getBasePath() . '/admin_theme/dist/js/sb-admin-2.js', 'preload' => TRUE],
      ['src' => '//nette.github.io/resources/js/netteForms.min.js', 'preload' => TRUE],
      ['src' => $this->getBasePath() . '/js/libs/nette.ajax.js', 'preload' => TRUE],
      ['src' => $this->getBasePath() . '/js/main.js', 'preload' => TRUE],
    ];
    foreach ($defaultScripts as $script) {
      $this->addScript($script);
    }
  
  }
  
  protected function addScript($script) {
    if (array_key_exists('preload', $script) && $script['preload']) {
      $this->preloads[] = ['href' => $script['src'], 'as' => 'script'];
    }
    $this->scripts[] = $script;
  }
  
  
  protected function useDatatables() {
    $this->styles[] = ['href' => $this->getBasePath() . '/admin_theme/vendor/datatables-plugins/dataTables.bootstrap.css', 'preload' => TRUE];
    $this->styles[] = ['href' => $this->getBasePath() . '/admin_theme/vendor/datatables-responsive/dataTables.responsive.css', 'preload' => TRUE];
    $this->scripts[] = ['src' => $this->getBasePath() . '/admin_theme/vendor/datatables/js/jquery.dataTables.min.js', 'preload' => TRUE];
    $this->scripts[] = ['src' => $this->getBasePath() . '/admin_theme/vendor/datatables-plugins/dataTables.bootstrap.min.js', 'preload' => TRUE];
    $this->scripts[] = ['src' => $this->getBasePath() . '/admin_theme/vendor/datatables-responsive/dataTables.responsive.js', 'preload' => TRUE];
  }
  
  private function addPresenterStyles() {
    $path = strtolower(str_replace(':', '/', $this->getName())) . '/';
    foreach (glob($this->webDir->getPath() . 'js/' . $path . '*') as $script) {
      if(pathinfo($script)['extension'] == 'js') {
        $source = str_replace($this->webDir->getPath(), $this->getBasePath(), $script);
        $this->addScript(['src' => $source, 'preload' => TRUE]);
      }
    }
    
  }
  
  private function addPresenterScripts() {
    $path = strtolower(str_replace(':', '/', $this->getName())) . '/';
    foreach (glob($this->webDir->getPath() . 'css/' . $path . '*') as $script) {
      if(pathinfo($script)['extension'] == 'css') {
        $source = str_replace($this->webDir->getPath(), $this->getBasePath(), $script);
        $this->addStyle(['href' => $source, 'preload' => TRUE]);
      }
    }
  }
  
}

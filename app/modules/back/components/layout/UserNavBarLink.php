<?php
  
  namespace App\Back\Components\Layout;
  use Nette\Application\UI\Control;
  use Nette\Database\Context;
  use Tracy\Dumper;

  /**
   * Class UserNavBarLink  ...
   *
   * @author  Zdeněk Houdek
   * @created 12.04.2018
   */
  class UserNavBarLink extends Control
  {
    /** @var Context */
    private $db;
    
    public function __construct(Context $db) {
      $this->db = $db;
    }
  
    public function render(...$args) {
      $template = $this->template;
      $userId = $this->presenter->getUser()->getIdentity()->id;
      $template->profile = $this->db->table('user_profiles')->where('user_id = ?', $userId)->fetch();
      $template->setFile(__DIR__ .'/templates/userNavBarLink.latte');
      $template->render();
    }
  
    public function handleLogout() {
    
      $this->presenter->redirect('Login:out');
    
    }
    
  }
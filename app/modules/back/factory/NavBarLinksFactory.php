<?php
  namespace App\Back\Factories;
  use App\Common\Components\Layout\Dummy;
  use Nette\Database\Context;
  use Tracy\Dumper;

  /**
   * Class NavBarLinksFactory  ...
   *
   * @author  Zdeněk Houdek
   * @created 12.04.2018
   */
  class NavBarLinksFactory
  {
    private $withConnection = ['user'];
    /** @var Context */
    private $db;
    
    public function __construct(Context $db) {
      $this->db = $db;
    }
  
    public function create($type) {
      $className = 'App\Back\Components\Layout\\' . ucfirst($type) .'NavBarLink';
      if (in_array($type, $this->withConnection)) {
        $component = new $className($this->db);
      } else {
        $component = new $className();
      }
      return  $component;
    }
    
  }
<?php
  
  namespace App\Presenters;
  
  use Nette;
  
  /**
   * Class PostPresenter
   *
   * @author  Zdeněk Houdek
   * @created 27.03.2018
   */
  class PostPresenter extends Nette\Application\UI\Presenter
  {
    
    /** @var Nette\Database\Context */
    private $database;
    
    public function __construct(Nette\Database\Context $database) {
      $this->database = $database;
    }
  
    public function renderShow($postId) {
      
      $post = $this->database->table('posts')->get($postId);
      if (!$post) {
        $this->error('Stránka nebyla nalezena');
      }
      $this->template->post = $post;
    }
    
  }
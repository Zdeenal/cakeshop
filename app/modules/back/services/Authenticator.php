<?php
  namespace App\Back\Services;
  use App\Back\Models\UserGroupModel;
  use Nette\Database\Context;
  use Nette\Security\AuthenticationException;
  use Nette\Security\IAuthenticator;
  use Nette\Security\Identity;
  use Nette\Security\Passwords;
  use Tracy\Dumper;

  /**
   * Class Authenticator  ...
   *
   * @author  Zdeněk Houdek
   * @created 05.04.2018
   */
  class Authenticator implements IAuthenticator
  {
    /** @var Context */
    public $database;
    
    /** @var UserGroupModel */
    private $userGroupModel;
    
    
    public function __construct(Context $database, UserGroupModel $userGroupModel) {
      $this->database = $database;
      $this->userGroupModel = $userGroupModel;
    }
    
    function authenticate(array $credentials) {
      list($username, $password) = $credentials;
      $row = $this->database->table('users')
        ->where('username', $username)->fetch();
  
      
      if (!$row) {
        throw new AuthenticationException('Neexistující uživatel ' . $username . '.');
      }
  
      elseif (!Passwords::verify($password, $row->password)) {
        throw new AuthenticationException('Chybné heslo pro uživatele ' . $username . '.');
      }

      elseif (!in_array($row->module->name, ['back','common'])) {
        throw new AuthenticationException('Uživatel patří do modulu ' . $row->module->description . '.');
      }
      
      elseif (Passwords::needsRehash($row->password)) {
        $row->update(array(
          'password' => Passwords::hash($password),
        ));
      }
      $groups = $this->userGroupModel->getRoles($row->user_group_id);
      return new Identity($row->user_id, $groups ? $groups : 'guest', ['username' => $row->username]);
    }
  
  }
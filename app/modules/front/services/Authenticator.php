<?php
  namespace App\Front\Services;
  use Nette\Database\Context;
  use Nette\Security\AuthenticationException;
  use Nette\Security\IAuthenticator;
  use Nette\Security\Identity;
  use Nette\Security\Passwords;

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
    
    public function __construct(Context $database) {
      $this->database = $database;
    }
    
    function authenticate(array $credentials) {
      list($username, $password) = $credentials;
      $row = $this->database->table('users')
        ->where('username', $username)->fetch();
  
      if (!$row) {
        throw new AuthenticationException('User not found.');
      }
  
      elseif (!Passwords::verify($password, $row->password)) {
        throw new AuthenticationException('Invalid password.');
      }

      elseif (!in_array($row->module->name, ['front','common'])) {
        throw new AuthenticationException('Invalid user module.');
      }
      
      elseif (Passwords::needsRehash($row->password)) {
        $row->update(array(
          'password' => Passwords::hash($password),
        ));
      }
  
      return new Identity($row->user_id, $row->user_group->name, ['username' => $row->username]);
    }
  
  }
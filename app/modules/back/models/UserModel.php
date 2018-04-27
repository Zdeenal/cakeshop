<?php
  namespace App\Back\Models;
  use App\Traits\DatatableModelTrait;
  use Nette;
  /**
   * Class UserModel  ...
   *
   * @author  Zdeněk Houdek
   * @created 26.04.2018
   */
  class UserModel
  {
    use DatatableModelTrait;
  
  
    const _SUCCESS_DELETE_MESSAGE = ['Uživatel {USERNAME} byl odstraněn.','success'];
    const _FAIL_DELETE_MESSAGE = ['Chyba! Uživatel {USERNAME} nebyl odstraněn!', 'error'];
  
    /**
     *
     * @param $id
     *
     * @return string
     */
    public function getDeleteMessageForUserGroup($id) {
      $message = '';
      $users = $this->getAllItems()->where('user_group_id = ?', $id);
      if ($users->count()) {
        $message .= '<div><strong>Je použita u těchto uživatelů:</strong><ul class="list-no-marks">';
        foreach ( $users as $user) {
          $message .= '<li>' . $user->username .'</li>';
        }
        $message .= '</ul></div>';
      }
      return $message;
    }
  }
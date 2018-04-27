<?php
  namespace App\Back\Models;
  use App\Traits\DatatableModelTrait;
  /**
   * Class UserModel
   *
   * @author  Zdeněk Houdek
   * @created 26.04.2018
   */
  class UserModel
  {
    use DatatableModelTrait;
  
    const _SUCCESS_MESSAGE = ['Uživatel {USERNAME} byl uložen.','success'];
    const _FAIL_MESSAGE = ['Chyba! Uživatel {USERNAME} nebyl uložen!', 'error'];
    
    const _SUCCESS_DELETE_MESSAGE = ['Uživatel {USERNAME} byl odstraněn.','success'];
    const _FAIL_DELETE_MESSAGE = ['Chyba! Uživatel {USERNAME} nebyl odstraněn!', 'error'];
  
    const _FAIL_DUPLICITY_NAME_MESSAGE = ['Chyba! Uživatel s loginem `{USERNAME}` již existuje!', 'error'];
  
    /**
     * Get message of all users for given user group id
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
  
    /**
     * Overrides DatatableModelTrait->delete() function
     */
    public function delete() {
      
    }
  }
<?php
  namespace App\Back\Models;
  use App\Traits\DatatableModelTrait;
  use Nette;
  /**
   * Class UserGroupModel  ...
   *
   * @author  Zdeněk Houdek
   * @created 26.04.2018
   */
  class UserGroupModel
  {
    use DatatableModelTrait;
  
    const _SUCCESS_MESSAGE = ['Skupina {NAME} byla uložena.','success'];
    const _FAIL_MESSAGE = ['Chyba! Skupina {NAME} nebyla uložena!', 'error'];
  
    const _SUCCESS_DELETE_MESSAGE = ['Skupina {NAME} byla odstraněna.','success'];
    const _FAIL_DELETE_MESSAGE = ['Chyba! Skupina {NAME} nebyla odstraněna!', 'error'];
  
    const _FAIL_DUPLICITY_NAME_MESSAGE = ['Chyba! Skupina {NAME} již existuje!', 'error'];
  
    /**
     * Overrides DatatableModelTrait->delete() function
     * @param Nette\Database\Table\ActiveRow $group
     */
    public function delete(Nette\Database\Table\ActiveRow $group) {
      $this->db->table('user_groups')
        ->where('parent_group_id = ?', $group->user_group_id)
        ->update(['parent_group_id' => NULL]);
  
      $this->db->table('users')
        ->where('user_group_id = ?', $group->user_group_id)
        ->update(['user_group_id' => NULL]);
  
      $this->db->table('user_groups')
        ->where('user_group_id = ?', $group->user_group_id)
        ->delete();
    }
  
    /**
     * Return message with child groups of given user group id
     * @param $id
     *
     * @return string
     */
    public function getDeleteMessage($id) {
      $message = '';
  
      $childGroups = $this->getAllItems()->where('parent_group_id = ?', $id);
      if ($childGroups->count()) {
        $message .= '<div><strong>Je rodičem pro tyto skupiny:</strong><ul class="list-no-marks">';
        foreach ( $childGroups as $childGroup) {
          $message .= '<li>' . $childGroup->name .'</li>';
        }
        $message .= '</ul></div>';
      }
      return $message;
    }
  
    public function getRoles($userGroupId, $roles = []) {
      if ($group = $this->db->table('user_groups')->get($userGroupId)) {
        $roles[] = $group->name;
        $children =$this->db->table('user_groups')->where('parent_group_id = ?', $group->user_group_id);
        foreach ( $children as $childGroup) {
          $roles = $this->getRoles($childGroup->user_group_id, $roles);
        }
      }
      return $roles;
    }
  }
<?php
  
  namespace App\Common\Factory;
  
  use App\Common\Components\Forms\BSForm;
  use App\Common\Components\Forms\SignForm;
  use Nette;
  use Nette\Application\UI\Form;
  use Nette\Security\User;
  
  
  class SignFormFactory
  {
    use Nette\SmartObject;
    /** @var User */
    private $user;
    
    
    public function __construct(User $user)
    {
      $this->user = $user;
    }
    
    
    /**
     * @return Form
     */
    public function create()
    {
      $form = new SignForm();
      $form->addText('username')
        ->setRequired('Zadejte uživatelské jméno.')
        ->controlPrototype->setAttribute('placeholder','Jméno');
      
      $form->addPassword('password')
        ->setRequired('Zadejte heslo.')
        ->controlPrototype->setAttribute('placeholder','Heslo');
      
      $form->addCheckbox('remember', 'Zapamatovat přihlášení');
      
      $form->addSubmit('send', 'Přihlásit');
      
      $form->onSuccess[] = array($this, 'formSucceeded');
      return $form;
    }
    
    
    public function formSucceeded(Form $form, $values)
    {
      if ($values->remember) {
        $this->user->setExpiration('14 days', FALSE);
      } else {
        $this->user->setExpiration('20 minutes', TRUE);
      }
      
      try {
        $this->user->login($values->username, $values->password);
      } catch (Nette\Security\AuthenticationException $e) {
        $form->addError($e->getMessage());
      }
    }
    
  }

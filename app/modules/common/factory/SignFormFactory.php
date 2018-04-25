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
      
      return $form;
    }
    
    
  }

<?php

namespace App\View\Helper;

use Cake\View\Helper;

class CustomHelper extends Helper
{
    public $helpers = ['Html','Url'];

    public function profileImage($id)
    {
        $userImage = WWW_ROOT . 'img' . DS . 'profile' . DS . 'user_' . $id . '.png'; 
        
        
        if(file_exists($userImage)){
            
            return $this->Html->image('profile/user_' . $id . '.png');
            
        }else{
            
            return $this->Html->image('icon.png');
            
        }
        
        
    }
    
    public function hasImage($id){
        
        $userImage = WWW_ROOT . 'img' . DS . 'profile' . DS . 'user_' . $id . '.png'; 
        
        return(file_exists($userImage));
    }
}


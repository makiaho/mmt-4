<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class NotesTable extends Table {
    
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('notes');
        $this->displayField('content');
        $this->primaryKey('id');
    }
    
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('content', 'create')
            ->notEmpty('content');

        $validator
            ->notEmpty('project_role');
        
        $validator
            ->notEmpty('created_on');
        
        $validator
            ->allowEmpty('email');
        
        $validator
            ->allowEmpty('contact_user');
        
        $validator
            ->allowEmpty('note_read');

        return $validator;
    }  
}
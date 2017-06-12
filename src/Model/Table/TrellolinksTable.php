<?php
namespace App\Model\Table;

use App\Model\Entity\Trellolink;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TrellolinksTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('trellolinks');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Trello', [
            'foreignKey' => 'trello_id',
            'joinType' => 'INNER'
        ]);

    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');
        
        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['trello_id'], 'Trello'));
        return $rules;
    }
}



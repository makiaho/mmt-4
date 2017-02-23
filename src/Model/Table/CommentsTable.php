<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CommentsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('comments');
        $this->displayField('content');
        $this->primaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
		$this->belongsTo('Weeklyreports', [
            'foreignKey' => 'weeklyreport_id'
        ]);
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
            ->notEmpty('date_created');

        $validator
            ->notEmpty('date_modified');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['weeklyreport_id'], 'Weeklyreports'));
        return $rules;
    }
}

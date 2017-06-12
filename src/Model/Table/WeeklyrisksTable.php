<?php
namespace App\Model\Table;

use App\Model\Entity\Weeklyrisk;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class WeeklyrisksTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('weeklyrisks');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Weeklyreports', [
            'foreignKey' => 'weeklyreport_id',
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
        return $rules;
    }
}

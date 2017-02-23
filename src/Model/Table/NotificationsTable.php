<?php
namespace App\Model\Table;

use App\Model\Entity\Weeklyreport;
use App\Model\Entity\Comment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class NotificationsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('notifications');
        $this->primaryKey('comment_id', 'member_id');

        $this->belongsTo('Comments', [
            'foreignKey' => 'comment_id'
        ]);
		$this->hasMany('Members', [
            'foreignKey' => 'member_id'
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['comment_id'], 'Comments'));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        return $rules;
    }
}

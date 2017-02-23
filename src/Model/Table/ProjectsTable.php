<?php
namespace App\Model\Table;

use App\Model\Entity\Project;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

class ProjectsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('projects');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->hasMany('Members', [
            'foreignKey' => 'project_id'
        ]);
        $this->hasMany('Metrics', [
            'foreignKey' => 'project_id'
        ]);
        $this->hasMany('Weeklyreports', [
            'foreignKey' => 'project_id'
        ]);
    }
    
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('project_name', 'create')
            ->notEmpty('project_name')
            ->add('project_name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->add('created_on', 'valid', ['rule' => 'date'])
            ->requirePresence('created_on', 'create')
            ->notEmpty('created_on');

        $validator
            ->add('updated_on', 'valid', ['rule' => 'date'])
            ->allowEmpty('updated_on');

        // because of jQuery UI datepicker used in edit.ctp
        $validator
            //->add('finished_date', 'valid', ['rule' => 'date'])
            ->allowEmpty('finished_date');

        $validator
            ->allowEmpty('description');

        $validator
            ->add('is_public', 'valid', ['rule' => 'boolean'])
            ->requirePresence('is_public', 'create')
            ->notEmpty('is_public');

        return $validator;
    }
    
    // return a list of all the public projects
    public function getPublicProjects(){  
        $projects = TableRegistry::get('Projects');
        $query = $projects
            ->find()
            ->select(['id', 'project_name', 'finished_date'])
            ->where(['is_public' => 1])
            ->toArray();
        $publicProjects = array();
        foreach($query as $temp){
            $project = array();
            $project['id'] = $temp->id;
            $project['project_name'] = $temp->project_name;
            $project['finished_date'] = $temp->finished_date;
            $publicProjects[] = $project;
        }
        return $publicProjects;
    }
    
    // get the total workinghours of a project
    public function getHoursDuration($project_id) {
        $members = TableRegistry::get('Members');
        $queryM = $members
                ->find()
                ->select(['id'])
                ->where(['project_id' => $project_id])
                ->toArray();
                $ids = array();
        foreach($queryM as $temp){
            $ids[] = $temp->id;
        }
        
        $workinghours = TableRegistry::get('Workinghours');
        $sum = 0;
        if(!empty($ids)) {
            $queryW = $workinghours
                    ->find()
                    ->select(['duration'])
                    ->where(['member_id IN' => $ids])
                    ->toArray();       
            if(!empty($queryW)) {
                foreach($queryW as $result) {
                    $sum += $result->duration;
                }
            }
        } 
        return $sum;
    }
    
    // new version of the function
    public function getWeeklyhoursDuration($project_id){
        $weeklyreports = TableRegistry::get('Weeklyreports'); 
        $query = $weeklyreports
            ->find()
            ->select(['id'])
            ->where(['project_id' => $project_id])
            ->toArray();
        $ids = array();
        foreach($query as $temp){
            $ids[] = $temp->id;
        }
        
        $weeklyhours = TableRegistry::get('Weeklyhours');
        $duration = 0;
        if (!empty($ids)) {
            $query = $weeklyhours
                ->find()
                ->select(['duration'])
                ->where(['weeklyreport_id IN' => $ids])
                ->toArray();            
            if(!empty($query)) {
                foreach($query as $temp){
                    $duration += $temp->duration;
                }
            }
        }    
        return $duration;
    } 
    
    // get a list with 'X', 'L' or ' ' for the weeks based on the limits
    // 'X' if that weeks report was returned
    // 'L' if that weeks report should have been returned but its not
    // ' ' if there is no report but its still not late
    public function getWeeklyreportWeeks($project_id, $min, $max, $year){
        $weeklyreports = TableRegistry::get('Weeklyreports'); 
        /* BUG FIX: editing weekly limits now works fine
         */
        $query = $weeklyreports
            ->find()
            ->select(['week', 'created_on'])
            ->where(['project_id' => $project_id, 'week >=' => $min, 'week <=' => $max, 'year' => $year])
            ->toArray();

        $weeks = array();
		$createdates = array();
        foreach($query as $temp){
            $weeks[] = $temp->week;
			$createdates[] = $temp->created_on;
        }
        $time = Time::now();
        // with the weeks when the report has not been filled
        $completeList = array();
		
		// iterator for weeks and createdates array, resets to zero after "finishing" with one project's reports
		$i = 0;
        for($count = $min; $count <= $max; $count++){
            // if the week is found
            if(in_array($count, $weeks)){
				// fetch the weekday when report was sent; protip: Sunday = 0 (so Monday = 1)
				$weekday = date( "w", strtotime($createdates[$i]));
				// also fetch the weeknumber when report was sent
				$weekno = date( "W", strtotime($createdates[$i]));

				// weeklyreport is late
				// IF a) it was created on next week AND the weekday was after monday
				// OR b) it was created several weeks later
                if( ($weeks[$i] +1 == $weekno && $weekday > 1) || ($weeks[$i] +1 < $weekno) ) {
					$completeList[] = 'L';
                } else {
					$completeList[] = 'X';
				}
				$i++;
            }
            else {
				// if its not late, but there is no report
				$completeList[] = '-';
            }
        }
        return $completeList;
    }
}

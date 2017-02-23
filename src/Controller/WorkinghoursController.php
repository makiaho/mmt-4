<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\Routing\Router;

class WorkinghoursController extends AppController
{
    public function index()
    {
        // only load workinghours from current project
        // ordered by date
        $project_id = $this->request->session()->read('selected_project')['id'];
        $this->paginate = [
            'contain' => ['Members', 'Worktypes'],
            'conditions' => array('Members.project_id' => $project_id),
            'order' => ['date' => 'DESC']
        ];
        
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);

        //$now = Time::now();
        $members = $this->Workinghours->Members->find('list',[ 
              'conditions' => ['Members.project_id' => $project_id, 
                                'Members.project_role !=' => 'supervisor',
                                'and' => array('Members.project_role !=' => 'client')],                                 
                               //'Members.project_role !=' => 'supervisor', 
                               //'or' => array(''Members.ending_date >' => $now,'Members.ending_date IS' => NULL)],
              'contain' => ['Users'], 
              'keyField' => 'id', 
              'valueField' => 'user.full_name',
              'limit' => 200]);
        
        $this->set('workinghours', $this->paginate($this->Workinghours));
        $this->set(compact('memberlist', 'members'));               
        $this->set('_serialize', ['workinghours']);
    }
    
    // For listing member's workinghours
    public function tasks($id = null)
    {
        // only load workinghours from current project
        // ordered by date
        $project_id = $this->request->session()->read('selected_project')['id'];
        $this->paginate = [
            'contain' => ['Members', 'Worktypes'],
            'conditions' => array('Members.project_id' => $project_id, 'Members.id' => $id),
            'order' => ['date' => 'DESC']
        ];
        
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);

        //$now = Time::now();
        $members = $this->Workinghours->Members->find('list',[ 
              'conditions' => ['Members.project_id' => $project_id, 
                                'Members.id' => $id],                                 
                               //'Members.project_role !=' => 'supervisor', 
                               //'or' => array(''Members.ending_date >' => $now,'Members.ending_date IS' => NULL)],
                ]);
        
        $this->set('workinghours', $this->paginate($this->Workinghours));
        $this->set(compact('memberlist', 'members'));               
        $this->set('_serialize', ['workinghours']);
    }
    public function view($id = null)
    {
        $project_id = $this->request->session()->read('selected_project')['id'];
        $workinghour = $this->Workinghours->get($id, [
            'contain' => ['Members', 'Worktypes'],
            'conditions' => array('Members.project_id' => $project_id)
        ]);
        
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);

        foreach($memberlist as $member){
            if($workinghour->member->id == $member['id']){
                // if the id's match add the name for the member
                $workinghour->member['member_name'] = $member['member_name'];
            }
        }
        $this->set('workinghour', $workinghour);
        $this->set('_serialize', ['workinghour']);
    }
    
    
    public function add()
    {
        $workinghour = $this->Workinghours->newEntity();
        if ($this->request->is('post')) {
            // get data from the form
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->data);  
            // only allow members to add workinghours for themself
            $workinghour['member_id'] = $this->request->session()->read('selected_project_memberid');
            
            if ($this->Workinghours->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }
        $project_id = $this->request->session()->read('selected_project')['id'];
        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $members = $this->Workinghours->Members->find('list', ['limit' => 200, 'conditions' => array('Members.project_id' => $project_id)]);
        $this->set(compact('workinghour', 'members', 'worktypes'));
        $this->set('_serialize', ['workinghour']);
    }
    
    public function addlate()
    {
        $workinghour = $this->Workinghours->newEntity();
        if ($this->request->is('post')) {
            // get data from the form
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->data);  
            // only allow members to add workinghours for themself
            $workinghour['member_id'] = $this->request->session()->read('selected_project_memberid');
            
            if ($this->Workinghours->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                // redirect back to the weeklyreport
                echo "<script>
                        window.history.go(-2);
                </script>";
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }
        $project_id = $this->request->session()->read('selected_project')['id'];
        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $members = $this->Workinghours->Members->find('list', ['limit' => 200, 'conditions' => array('Members.project_id' => $project_id)]);
        $this->set(compact('workinghour', 'members', 'worktypes'));
        $this->set('_serialize', ['workinghour']);
    }
    
    // managers supervisors and admins can add workinghours for the developers
    public function adddev()
    {
        $workinghour = $this->Workinghours->newEntity();
        if ($this->request->is('post')) {
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->data);  
            if ($this->Workinghours->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }
        $project_id = $this->request->session()->read('selected_project')['id'];
        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $now = Time::now();
        $members = $this->Workinghours->Members->find('list',[ 
              'conditions' => ['Members.project_id' => $project_id,
                                'Members.project_role !=' => 'supervisor',
                                'and' => array('Members.project_role !=' => 'client')],  
                                //'Members.project_role !=' => 'supervisor',
                               //'or' => array('Members.ending_date >' => $now,'Members.ending_date IS' => NULL)],
              'contain' => ['Users'], 
              'keyField' => 'id', 
              'valueField' => 'user.full_name',
              'limit' => 200]);

        $this->set(compact('workinghour', 'members', 'worktypes'));
        $this->set('_serialize', ['workinghour']);
    }
    
    public function edit($id = null)
    {
        // only allow editing workinghours from the current project
        $project_id = $this->request->session()->read('selected_project')['id'];
        $workinghour = $this->Workinghours->get($id, [
            'contain' => ['Members', 'Worktypes'],
            'conditions' => array('Members.project_id' => $project_id)
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $workinghour = $this->Workinghours->patchEntity($workinghour, $this->request->data);
            if ($this->Workinghours->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }
        $worktypes = $this->Workinghours->Worktypes->find('list', ['limit' => 200]);
        $now = Time::now();
        $members = $this->Workinghours->Members->find('list', ['limit' => 200])
                                                ->where(['Members.project_id' => $project_id, 'Members.project_role !=' => 'supervisor', 'Members.ending_date >' => $now])
                                                ->orWhere(['Members.project_id' => $project_id, 'Members.project_role !=' => 'supervisor', 'Members.ending_date IS' => NULL]);
        $this->set(compact('workinghour', 'members', 'worktypes', 'memberlist'));
        $this->set('_serialize', ['workinghour']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $workinghour = $this->Workinghours->get($id);
        if ($this->Workinghours->delete($workinghour)) {
            $this->Flash->success(__('The workinghour has been deleted.'));
        } else {
            $this->Flash->error(__('The workinghour could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function isAuthorized($user)
    {   
        // admins can do anything
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        
        $project_role = $this->request->session()->read('selected_project_role');
        
        if  ($this->request->action === 'add')
        {
            // supervisor cannot have workinghours, and the add function simply takes the member_id of the current user
            if($project_role != "notmember" && $project_role != "supervisor" && $project_role != "client"){
                return True;
            }
            return False;
        }
        // developers can only edit and delete their own workinghours
        if ($this->request->action === 'edit' || $this->request->action === 'delete') 
        {
            if($project_role == "developer") {
                $query = $this->Workinghours
                    ->find()
                    ->select(['member_id'])
                    ->where(['id =' => $this->request->pass[0]])
                    ->toArray();
                
                //Bug fix 8.3.2016 : members were unable to edit themselves because of false comparison (userID vs. memberID)
                //   Requirement ID = 17
                
                // fetching the Members-table from database
                $members = TableRegistry::get('Members');
                
                // querying Members-table for member ID:s that correspond to the current user's ID
                $query2 = $members
                	->find()
                	->select(['id'])
                	->where(['user_id =' => $user['id']])
                	->toArray();

                foreach ($query2 as $temp) {
                    
                    if($query[0]->member_id == $temp->id ){
                        return True;
                    }
                }
                return False;
            }
        }

        //special rule for workinghours controller.
        
        // supervisors cannot log time late
        if ($this->request->action === 'addlate')
        {
            //if($project_role == "manager" || $project_role == "developer"){
            //    return True;
            //}
            return False;
        }
        if ($this->request->action === 'tasks') 
        {
            if($project_role == "manager" || $project_role == "supervisor" || $project_role == "developer" || $project_role == "client"){
                return True;
            }
        }
        //all members can add edit and delete workinghours
        if ($this->request->action === 'adddev' || $this->request->action === 'edit'
            || $this->request->action === 'delete') 
        {
            if($project_role == "manager" || $project_role == "supervisor"){
                return True;
            }
            return False;
        }
        // if not trying to add edit delete
        return parent::isAuthorized($user);
    }
}

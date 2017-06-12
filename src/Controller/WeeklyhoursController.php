<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

class WeeklyhoursController extends AppController
{
    public function index()
    {
        // only get weeklyhours from the current project
        $project_id = $this->request->session()->read('selected_project')['id'];
        $this->paginate = [
            'contain' => ['Weeklyreports', 'Members'],
            'conditions' => array('Members.project_id' => $project_id)
        ];
        
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);
        
        $this->set('weeklyhours', $this->paginate($this->Weeklyhours));
        $this->set(compact('memberlist'));
        $this->set('_serialize', ['weeklyhours']);
    }

    public function view($id = null)
    {
        // only allow viewing weeklyhours from the current project
        $project_id = $this->request->session()->read('selected_project')['id'];
        $weeklyhour = $this->Weeklyhours->get($id, [
            'contain' => ['Weeklyreports', 'Members'],
            'conditions' => array('Members.project_id' => $project_id)
        ]);
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);

        foreach($memberlist as $member){
            if($weeklyhour->member->id == $member['id']){
               $weeklyhour->member['member_name'] = $member['member_name'];
            }
        }
        $this->set('weeklyhour', $weeklyhour);
        $this->set('_serialize', ['weeklyhour']);
    }

    public function add()
    {
        $weeklyhour = $this->Weeklyhours->newEntity();
        if ($this->request->is('post')) {
            $weeklyhour = $this->Weeklyhours->patchEntity($weeklyhour, $this->request->data);
            if ($this->Weeklyhours->save($weeklyhour)) {
                $this->Flash->success(__('The weeklyhour has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The weeklyhour could not be saved. Please, try again.'));
            }
        }
        $project_id = $this->request->session()->read('selected_project')['id'];
        $weeklyreports = $this->Weeklyhours->Weeklyreports->find('list', ['limit' => 200, 'conditions' => array('Weeklyreports.project_id' => $project_id)]);
        $now = Time::now();
        $members = $this->Weeklyhours->Members->find('list', ['limit' => 200])
                                                ->where(['Members.project_id' => $project_id, 'Members.project_role !=' => 'supervisor', 'Members.ending_date >' => $now])
                                                ->orWhere(['Members.project_id' => $project_id, 'Members.project_role !=' => 'supervisor', 'Members.ending_date IS' => NULL]);
        $this->set(compact('weeklyhour', 'weeklyreports', 'members'));
        $this->set('_serialize', ['weeklyhour']);
    }
    
    // used in the weelkyreport form
    public function addmultiple()
    {   
        $project_id = $this->request->session()->read('selected_project')['id'];
        $current_weeklyreport = $this->request->session()->read('current_weeklyreport');
        // create a list of key valuepairs where the value is their member id and key is the members email + role
        $membersTable = TableRegistry::get('Members');
        // list of members so we can display usernames instead of id's
        $memberlist = $membersTable->getMembers($project_id);
        //count the workinghours for members so they can be translated to weeklyhours 
        $hourlist = $this->Weeklyhours->getHours($memberlist, $current_weeklyreport['week']);
        
        $weeklyhours = $this->Weeklyhours->newEntity();
        if ($this->request->is('post')) {
            // the last key in the data is "submit", the value tells what button the user pressed 
            $formdata = $this->request->data;
            $entities = array();

            for($count = 0; $count < count($memberlist); $count++){
                $temp = array();
                // the id does not exist yet
                //$temp['weeklyreport_id'] = $current_weeklyreport['id'];
                $temp['member_id'] = $memberlist[$count]['id'];
                $temp['duration'] = $formdata[$count]['duration'];
                
                $entities[] = $temp;
            }
            
            $weeklyhours = $this->Weeklyhours->newEntities($entities);
            
            $dataok = True;
            foreach($weeklyhours as $temp){
                if($temp->errors()){
                    $dataok = False;
                }
            }
            
            if($dataok){
                $this->request->session()->write('current_weeklyhours', $weeklyhours);
                if($this->request->data['submit'] == "submit"){
                    
                    // save all the parts of the weeklyreport that are saved in the session
                    $current_metrics = $this->request->session()->read('current_metrics');
                    $current_risks = $this->request->session()->read('current_risks');
					// fetch all supervisors
					$svquery = TableRegistry::get('Members')->find()
								->select(['user_id'])
								->distinct(['user_id'])
								->where(['project_role =' => 'supervisor'])
								->toArray();
                    
                    if($result = $this->Weeklyhours->saveSessionReport($current_weeklyreport, $current_metrics, $current_risks, $weeklyhours)){
						// ID of last weeklyreport
						// when a comment is added, also add a notification to database; so for that we'll fetch the maximum (= most recent) comment id
						$query = TableRegistry::get('Weeklyreports')->find();
						$query->select(['max'=>$query->func()->max('id')]);
						$query = $query->toArray();

						$maxid = $query[0]->max;

						// add data about newly saved reports
                                                $connection = \Cake\Datasource\ConnectionManager::get("default");
//						
                                                for ($i = 0; $i < sizeof($svquery); $i++) {
							
                                                       $connection->insert('newreports',['user_id' => $svquery[$i]->user_id, 'weeklyreport_id' => $maxid]);
                                                    
						}
			
                        //This is for sending the report notification to slack                        
                        $slackController = new SlackController();

                        $slackData = ['type' => 'report','details' => ['report_id' => $maxid]]; 

                        $slackResult = $slackController->sendMessage($project_id, $slackData);                        
                                                
                                                
                        $this->Flash->success(__('Weeklyreport saved'));
                        
                        if($slackResult == 'success'){
                            $this->Flash->success(__('The report notification has been sent to slack.'));
                        }else if ($slackResult == 'fail'){
                            $this->Flash->error(__('The report notification could not be sent to slack.'));
                        }
                        
                        //This is for sending report as email to clients
                        $report = TableRegistry::get('Weeklyreports')->get($maxid);
                        
                        $clients = TableRegistry::get('Members')->find('all',[
                            'conditions' => ['project_id' => $report->project_id, 'project_role' => 'client'],
                            'contain' => ['Users']
                        ]);

                        $emailReport = (new WeeklyreportsController())->preparemail($maxid);
                        $projectName = $this->request->session()->read('selected_project')['project_name'];
                        $reportDesc = 'Report for week '.$report->week;
                        
                        foreach($clients as $client){
                            
                            $clientEmail = $client->user->email;
                            
                            $sendMail = new Email();
                
                            $sendMail->from(['mmt@uta.fi' => 'MMT']);
                            $sendMail->to($clientEmail);
                            $sendMail->subject($projectName.' - '.$reportDesc);
                            $sendMail->emailFormat('html');
                            
                            $sendMail->send($emailReport);
                            
                        }
                        
                        
                        
                        $this->request->session()->delete('current_weeklyreport');
                        $this->request->session()->delete('current_metrics');
                        $this->request->session()->delete('current_weeklyhours');
                        $this->request->session()->delete('current_risks');
                        
                        return $this->redirect(
                            ['controller' => 'Weeklyreports', 'action' => 'index']
                        );
                    }
                    else{
                        $this->Flash->success(__('Saving weeklyreport failed'));
                    }
                    
                }
                else{
                    return $this->redirect(
                        ['controller' => 'Metrics', 'action' => 'addmultiple']
                    ); 
                }
                  
            }
            else{
                $this->Flash->success(__('Weeklyhours failed validation'));
            }
        }
        $weeklyreports = $this->Weeklyhours->Weeklyreports->find('list', ['limit' => 200, 'conditions' => array('Weeklyreports.project_id' => $project_id)]);
        $members = $this->Weeklyhours->Members->find('list', ['limit' => 200, 'conditions' => array('Members.project_id' => $project_id)]);
        $this->set(compact('weeklyhours', 'weeklyreports', 'members', 'memberlist', 'hourlist'));
        $this->set('_serialize', ['weeklyhour']);
    }
    
    public function edit($id = null)
    {
        // only allow editing if the weeklyreport is from the current project
        $project_id = $this->request->session()->read('selected_project')['id'];
        $weeklyhour = $this->Weeklyhours->get($id, [
            'contain' => ['Members'],
            'conditions' => array('Members.project_id' => $project_id)
        ]);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $weeklyhour = $this->Weeklyhours->patchEntity($weeklyhour, $this->request->data);
            // can edit without changing weeklyreport id
            if ($this->Weeklyhours->save($weeklyhour)) {
                $this->Flash->success(__('The weeklyhour has been saved.'));
                // return the user back a page
                echo "<script>
                        window.history.go(-2);
                </script>";
            } else {
                $this->Flash->error(__('The weeklyhour could not be saved. Please, try again.'));
            }
        }
        $weeklyreports = $this->Weeklyhours->Weeklyreports->find('list', ['limit' => 200, 'conditions' => array('Weeklyreports.project_id' => $project_id)]);
        $now = Time::now();
        $members = $this->Weeklyhours->Members->find('list', [
            'conditions' => ['Members.project_id' => $project_id, 
                             'Members.project_role !=' => 'supervisor', 
                             'or' => array('Members.ending_date >' => $now,'Members.ending_date IS' => NULL)],
            'contain' => ['Users'],
            'keyField' => 'id',
            'valueField' => 'user.full_name',
            'limit' => 200]);
        $this->set(compact('weeklyhour', 'weeklyreports', 'members'));
        $this->set('_serialize', ['weeklyhour']);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $weeklyhour = $this->Weeklyhours->get($id);
        if ($this->Weeklyhours->delete($weeklyhour)) {
            $this->Flash->success(__('The weeklyhour has been deleted.'));
        } else {
            $this->Flash->error(__('The weeklyhour could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
    
    public function isAuthorized($user)
    {   
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        // managers supervisors and developers cannot add or delete weeklyhours
        if ($this->request->action === 'add' || $this->request->action === 'delete') 
        {
            return False;
        }
        
        return parent::isAuthorized($user);
    }
}

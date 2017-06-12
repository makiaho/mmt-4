<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Network\Http\Client;
use Cake\Routing\Router;

class SlackController extends AppController {

    public function index() {
        
        $project_id = $this->request->session()->read('selected_project')['id'];
        
        $slack = $this->Slack->find()->where(['project_id' => $project_id])->first();
        
        if($slack === null){
            $slack = $this->Slack->newEntity();
        }
        
        //For adding or updating slack webhook url
        if ($this->request->is(['patch', 'post', 'put'])) {
            // get data from the form
            $slack = $this->Slack->patchEntity($slack, $this->request->data);  
            
            $slack['project_id'] = $project_id;
            
            if ($this->Slack->save($slack)) {
                $this->Flash->success(__('The slack info has been added.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The slack info could not be added. Please, try again.'));
            }
        }
        
        $this->set('slack',$slack);
        
    }
    
    public function about() {
        
    }
    
    public function sendMessage($projectId,$slackData){
        
        $result = '';
        
        $url = $this->Slack->find()->where(['project_id' => $projectId])->first()['webhookurl'];
        
        //Proceed only if there is a web hook url
        if($url != null){
            
            $http = new Client();
            
            //If this is a comment message
            if($slackData['type'] == 'comment'){
                
                $userId = $slackData['details']['user_id'];
                $reportId = $slackData['details']['report_id'];
                $content = $slackData['details']['text'];
                
                $userName = TableRegistry::get('Users')->get($userId)->fullName;
                $reportName = TableRegistry::get('Weeklyreports')->get($reportId)->title;
                $projectName = TableRegistry::get('Projects')->get($projectId)->project_name;
                
                $message = '*'.$userName.'* commented on report *'.$reportName.'* of project *'.$projectName.'*: _'.$content.'_';
                 
            //If this is a report message   
            }else if ($slackData['type'] == 'report'){
                
                $reportId = $slackData['details']['report_id'];
                
                $projectName = TableRegistry::get('Projects')->get($projectId)->project_name;
                
                $reportLink = Router::url(['controller' => 'Weeklyreports', 'action' => 'view',$reportId],true);
                
                $message = 'New report on project *'.$projectName.'*: <'.$reportLink.'|Link>';
                
            }
            
            
        
            $data = array('text' => $message);

            $response = $http->post($url,
            json_encode($data),
              ['headers' => ['Content-Type' => 'application/json']]
            );
            
            if($response->code == 200){//This means message is sent successfully
                $result = 'success';
            }else{
                $result = 'fail';
            }
            
        }
        
        

        return $result;
    }
    
    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
               
        $project_role = $this->request->session()->read('selected_project_role');
        
        if($this->request->action === 'index'){
            //Only admmin and manager can edit slack information
            return ($project_role == 'manager' || $project_role == 'admin');
        }else{
            return true;
        }
        
        
        
    }
 
    
    

}

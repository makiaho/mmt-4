<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;

class RisksController extends AppController {

    public function index() {
        
        $project_id = $this->request->session()->read('selected_project')['id'];
        
        $editable = array();
        
        $risks = $this->Risks->find()->where(['project_id' => $project_id]);
        
        $types = $this->getImpactProbTypes();
        
        foreach($risks as $risk){
            
            $editable[$risk->id] = $this->checkEditable($risk->id);
            
        }
        
        $this->set(compact('risks', 'types', 'editable'));
        
    }
    
    public function add() {
        
        $risk = $this->Risks->newEntity();
        
        if ($this->request->is('post')) {
            // get data from the form
            $risk = $this->Risks->patchEntity($risk, $this->request->data);  
            
            $risk['project_id'] = $this->request->session()->read('selected_project')['id'];
            
            if ($this->Risks->save($risk)) {
                $this->Flash->success(__('The risk has been added.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The risk could not be added. Please, try again.'));
            }
        }
        
        $types = $this->getImpactProbTypes();
        
        $this->set(compact('risk', 'types'));
        $this->set('_serialize', ['risk']);
        
    }
    
    public function delete($id = null) {
        
        $risk = $this->Risks->get($id);
        
        if($this->checkEditable($id)){
        
            if ($this->Risks->delete($risk)) {
                $this->Flash->success(__('The risk has been deleted.'));
            } else {
                $this->Flash->error(__('The risk could not be deleted. Please, try again.'));
            }
        }else{
            $this->Flash->error(__('This risk is already contained in a weekly report, and thus can not be deleted.'));
        }
        return $this->redirect(['action' => 'index']);
        
    }
    
    public function edit($id = null) {
        
        $risk = $this->Risks->get($id);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $risk = $this->Risks->patchEntity($risk, $this->request->data);
            if ($this->Risks->save($risk)) {
                $this->Flash->success(__('The risk has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The risk could not be saved. Please, try again.'));
            }
        }
        
        $types = $this->getImpactProbTypes();
        
        $editable = $this->checkEditable($id);
        
        $this->set(compact('risk', 'types','editable'));
        $this->set('_serialize', ['risk']);
        
    }
    
    public function addweekly(){
        
        $project_id = $this->request->session()->read('selected_project')['id'];
        
        $risks = $this->Risks->find()->where(['project_id' => $project_id])->toArray();
        
        $types = $this->getImpactProbTypes();
        
        
        
        if ($this->request->is('post')) {
            
            $temp = array();
            
            $data = $this->request->data;
            
            //Puts each risk value to the array
            foreach($data as $key => $value){
                
                if (strpos($key, 'prob') !== false){
                    
                    $riskId = str_replace('prob-', '', $key);
                    
                    $temp[$riskId]['probability'] = $value;
                    
                }
                
                if (strpos($key, 'impact') !== false){
                    
                    $riskId = str_replace('impact-', '', $key);
                    
                    $temp[$riskId]['impact'] = $value;
                    
                }
                
            }
            
            //Writes the selected values to the session
            $this->request->session()->write('current_risks', $temp);
            
            return $this->redirect(['controller' => 'Weeklyhours', 'action' => 'addmultiple']);
            
        }
        
        //If there are values previously selected, it will get them form session
        if($this->request->session()->check('current_risks') && !empty($this->request->session()->read('current_risks'))){
            
            $current_risks = $this->request->session()->read('current_risks');
            
        }else{
            
            //If no prevously selected values exist, it look for the latest week
            $current_risks = $this->getLatestRisks($project_id);
            
        }
        
        
        $this->set(compact('risks', 'types', 'current_risks'));
        
    }
    
    public function getImpactProbTypes(){
        
        $types = array();
        
        $types[0] = 'None';
        $types[1] = 'Very Low';
        $types[2] = 'Low';
        $types[3] = 'Medium';
        $types[4] = 'High';
        $types[5] = 'Very High';
        
        return $types;
        
    }
    
    public function checkEditable($riskId){
        
        $weekly = TableRegistry::get('Weeklyrisks')->find()->where(['risk_id' => $riskId])->toArray();
        
        return empty($weekly);
        
    }
    
    //This is for getting the latest probability values for project risks
    public function getLatestRisks($projectId){
        
        $latestRisks = array();
        
        $risks = $this->Risks->find()->where(['project_id' => $projectId]);
        
        foreach($risks as $risk){
            
            //Looks for the latest value for this risk
            $weeklyRisk = TableRegistry::get('Weeklyrisks')->find('all',[
                'conditions' => ['risk_id' => $risk['id']],
                'order' => ['weeklyreport_id' => 'DESC']    
            ])->first();
            
            if($weeklyRisk !== null){
                
                $item = array();
                
                $item['probability'] = $weeklyRisk['probability'];
                $item['impact'] = $weeklyRisk['impact'];
                
                $latestRisks[$risk['id']] = $item;
                
            }else{
                 //If no previous weekly risk is found, it will get the starting impact and probability value   
                
                $item = array();
                
                $item['probability'] = $risk['probability'];
                $item['impact'] = $risk['impact'];
                
                $latestRisks[$risk['id']] = $item;
            }
            
        }
        
        return $latestRisks;
        
    }
    
    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        
        $project_role = $this->request->session()->read('selected_project_role');
        
        if ($this->request->action === 'addweekly'){
            //Only manager and admin can add risks to the report
            return ($project_role == 'manager' || $project_role == 'admin');
          
        }else if (in_array ($this->request->action, ['add','edit','delete'])){    
            //Only manager and admin can add new risk
            $allow = ($project_role == 'manager' || $project_role == 'admin');
            
            if ($allow && ($this->request->action === 'edit' || $this->request->action === 'delete')){
                
                $riskId = $this->request->params['pass'][0];
                
                $projectId = $this->Risks->get($riskId)->project_id;
                
                //One can only edit or delete risks for the current selected project
                $allow = $projectId == $this->request->session()->read('selected_project')['id'];
                
            }
                     
            return $allow;
            
        }else{
            return true;
        }
        
    }

}

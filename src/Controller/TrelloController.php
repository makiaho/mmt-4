<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;


class TrelloController extends AppController {

    public function index() {
        
        $project_id = $this->request->session()->read('selected_project')['id'];
        
        $trello = $this->Trello->find('all',[
            'conditions' => ['project_id' => $project_id],
            'contain' => ['Trellolinks']
        ])->first();
        
        $this->set('trello',$trello);
        
    }
    
    public function about() {
        
    }
    
    public function add(){
        
        $trello = $this->Trello->newEntity();
        
        if ($this->request->is('post')) {
            // get data from the form
            $trello = $this->Trello->patchEntity($trello, $this->request->data);  
            
            $trello['project_id'] = $this->request->session()->read('selected_project')['id'];
            
            if ($this->Trello->save($trello)) {
                $this->Flash->success(__('Trello configuration has been added.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Trello configuration could not be added. Please, try again.'));
            }
            
            return $this->redirect(['action' => 'index']);
        }
        
        
        
    }
    
    public function edit($id = null){
        
        $trello = $this->Trello->get($id);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $trello = $this->Trello->patchEntity($trello, $this->request->data);
            if ($this->Trello->save($trello)) {
                $this->Flash->success(__('Trello configuration has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Trello configuration could not be saved. Please, try again.'));
            }
        }

        $this->set('trello',$trello);
        
    }
    
    public function delete($id = null){
        
        $trello = $this->Trello->get($id);
        
//
//            if ($this->Trello->delete($trello)) {
//                $this->Flash->success(__('Trello configuration has been deleted.'));
//            } else {
//                $this->Flash->error(__('Trello configuration could not be deleted. Please, try again.'));
//            }
        
        return $this->redirect(['action' => 'index']);
        
    }
    
    public function links($id = null){
              
        $reqNames = ['reqNew','reqInProgress','reqClosed','reqRejected'];
        
        if ($this->request->is('post')) {
            
            $trelloLinkTable = TableRegistry::get('Trellolinks');
            
            $currentLinks = $trelloLinkTable->find()->where(['trello_id' => $id])->toArray();
            
            $itemsToAdd = array();
            
            $itemsToRemove = array();
            
            $formItems = array();
            
            foreach($this->request->data as $key => $value){
                
                if(in_array($key, $reqNames)){
                    
                    $link = array();
                    
                    $link['trello_id'] = $id;
                    $link['requirement_type'] = $key;
                    $link['list_id'] = $value;
                    
                    $formItems[] = $link;                    
                }          
            }
            
            
            foreach($formItems as $item){
                
                $add = true;
                
                foreach($currentLinks as $checkItem){           
                    if($checkItem['list_id'] === $item['list_id'] && $checkItem['requirement_type'] === $item['requirement_type']){               
                        $add = false;
                    }              
                }
                
                if($add){
                    $itemsToAdd[] = $item;
                }                
            }
            
            
            foreach($currentLinks as $item){
                
                $remove = true;
                
                foreach($formItems as $checkItem){   
                    if($checkItem['list_id'] === $item['list_id'] && $checkItem['requirement_type'] === $item['requirement_type']){                       
                        $remove = false;
                    }              
                }
                
                if($remove){                   
                    $itemsToRemove[] = $item;
                }         
            }
            
            
     
            $success = true;
            
            $trelloLinksAdd = $trelloLinkTable->newEntities($itemsToAdd);
            
            
            foreach($trelloLinksAdd as $entity){
                    if (!$trelloLinkTable->save($entity)) {
                    $success = false;
                    break;
                }
            }
            
            
            foreach($itemsToRemove as $entity){
                    if (!$trelloLinkTable->delete($entity)) {
                    $success = false;
                    break;
                }
            }
            
            
            
            if($success){                             
                $this->Flash->success(__('Trello configuration has been saved.'));    
            }else{
                $this->Flash->error(__('Trello configuration could not be saved. Please, try again.'));
            }
            
        }
        
        $trello = $this->Trello->get($id, ['contain' => ['Trellolinks']]);
        
        $metricTypes = TableRegistry::get('Metrictypes')->find()->where(['description IN' => $reqNames])->toArray();
        
        $metricNames = (new MetricsController())->getMetricNames();
        
        $this->set(compact('trello','metricTypes','metricNames'));
        
    }
    
    
    public function isAuthorized($user)
    {
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        
        $project_role = $this->request->session()->read('selected_project_role');
        
        $project_id = $this->request->session()->read('selected_project')['id'];
        
        //Only admin and manager can access trello pages
        $allowed = ($project_role == 'manager' || $project_role == 'admin');
        
        if($allowed){
            //One can only access trello pages of current project
            if(in_array ($this->request->action, ['links','edit','delete'])){
                
                $trelloId = $this->request->getParam('pass')[0];
                
                $checkItem = $this->Trello->find()->where(['id' => $trelloId])->first();
                
                if($checkItem != null){
                    return $checkItem->project_id === $project_id;
                }else{
                    return false;
                }
                
            }else{
                return true;
                
            }
            
        }
    }

}

<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;

class MetricsController extends AppController
{

    public function index()
    {
        // only load metrics that are from the current project
        // the metrics are loaded in descending order by date
        $project_id = $this->request->session()->read('selected_project')['id'];
        $this->paginate = [
            'contain' => ['Projects', 'Metrictypes', 'Weeklyreports'],
            'conditions' => array('Metrics.project_id' => $project_id),
            'order' => ['date' => 'DESC']
        ];
        $this->set('metrics', $this->paginate($this->Metrics));
        $this->set('_serialize', ['metrics']);
    }
    
    public function view($id = null)
    {
        // only allow viewing metrics that are in the current project
        $project_id = $this->request->session()->read('selected_project')['id'];
        $metric = $this->Metrics->get($id, [
            'contain' => ['Projects', 'Metrictypes', 'Weeklyreports'],
            'conditions' => array('Metrics.project_id' => $project_id)
        ]);
        $this->set('metric', $metric);
        $this->set('_serialize', ['metric']);
    }

    public function add()
    {
        $project_id = $this->request->session()->read('selected_project')['id'];
        $metric = $this->Metrics->newEntity();
        if ($this->request->is('post')) {
            // data loaded from the form
            $metric = $this->Metrics->patchEntity($metric, $this->request->data);
            // metrics can only be added to the current project
            $metric['project_id'] = $project_id;
            // if metrics are added outside of the weeklyreport the id will be null
            $metric['weeklyreport_id'] = NULL;

            if ($this->Metrics->save($metric)) {
                $this->Flash->success(__('The metric has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The metric could not be saved. Please, try again.'));
            }
        }
        $metrictypes = $this->Metrics->Metrictypes->find('list', ['limit' => 200]);
        //$weeklyreports = $this->Metrics->Weeklyreports->find('list', ['limit' => 200, 'conditions' => array('Weeklyreports.project_id' => $project_id)]);
        $this->set(compact('metric', 'projects', 'metrictypes', 'weeklyreports'));
        $this->set('_serialize', ['metric']);
    }
    
    // admin only function
    // admin is allowed to add metrics to weeklyreports outside the weeklyreport form
    public function addadmin()
    {
        $project_id = $this->request->session()->read('selected_project')['id'];
        $metric = $this->Metrics->newEntity();
        if ($this->request->is('post')) {
            $metric = $this->Metrics->patchEntity($metric, $this->request->data);
            
            $metric['project_id'] = $project_id;

            if ($this->Metrics->save($metric)) {
                $this->Flash->success(__('The metric has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The metric could not be saved. Please, try again.'));
            }
        }
        $metrictypes = $this->Metrics->Metrictypes->find('list', ['limit' => 200]);
        $weeklyreports = $this->Metrics->Weeklyreports->find('list', ['limit' => 200, 'conditions' => array('Weeklyreports.project_id' => $project_id)]);
        $this->set(compact('metric', 'projects', 'metrictypes', 'weeklyreports'));
        $this->set('_serialize', ['metric']);
    }
    
    // function for adding multiple metrics at once
    // used in the weeklyreport form
    public function addmultiple()
    {        
        $project_id = $this->request->session()->read('selected_project')['id'];
        $metric = $this->Metrics->newEntity();
        
        if ($this->request->is('post')) {
            // the last key in the form data is "submit", the value tells what button the user pressed 
            $formdata = $this->request->data;
            
            $entities = array();
            // these keys are the metric types that are added with this function
            $keys = ["phase", "totalPhases", "reqNew", "reqInProgress", "reqClosed", "reqRejected", "commits", "passedTestCases", "totalTestCases"];
            // the project in this session
            $selected_project = $this->request->session()->read('selected_project');
            // rolling counter for the metrictype
            // $keys array must be in same order as the metrictypes are in the database
            $metrictype = 1;
            $current_weeklyreport = $this->request->session()->read('current_weeklyreport');
            // go trough the data from the form and read the data with keys from $keys array
            foreach($keys as $key){
                $temp = array();
                $temp['project_id'] = $selected_project['id'];                      
                $temp['metrictype_id'] = $metrictype;
                // the id does not exist yet
                //$temp['weeklyreport_id'] = $current_weeklyreport['id'];
                $temp['date'] = $current_weeklyreport['created_on'];
                // the value is loaded from the form data with the keys
                $temp['value'] = $formdata[$key];
                
                $entities[] = $temp;
                
                $metrictype += 1;
            }
            // create metrics entities of all the entities
            $metrics = $this->Metrics->newEntities($entities);
            // look for errors
            $dataok = True;
            $continue = True;
            
            // Weekly report form (page 2/3)
			// check that all values exist and are greater than zero
            // at the same time, check that the totals are greater than phases/passed test cases          
            $items1 = $metrics;
            $items2 = $metrics;
			
			// flags to indicate what error message needs to be printed
			$nullFlag = False;
			$phaseFlag = False;
			
            // Totals (metrictype_ids 2 and 9) must be greater
            foreach($items1 as $item1) {
                foreach($items2 as $item2) {
					// check that no nulls or negative values exist
					if ( ($item1['value'] < 0) || ($item2['value'] < 0) ) {
						$continue = False;
						$nullFlag = True;
						break;
					}
                    // total phases must be greater than phases
                    if(($item1['metrictype_id'] == 1) && ($item2['metrictype_id'] == 2)) {
                        if($item1['value'] > $item2['value']) {                           
                            $continue = False;
							$phaseFlag = True;
							break;
                        }
                    }
                    // total test cases must be greater than passed test cases
                    if (($item1['metrictype_id'] == 8) && ($item2['metrictype_id'] == 9)) {
                        if($item1['value'] > $item2['value']) {
                            $continue = False;
							break;
                        }
                    }
                }                
            }
            // check metrics for errors
            foreach($metrics as $temp){
                if($temp->errors()){
                    $dataok = False;
                }
            }
            // write data if there are no errors
            if (!$continue) {
				if ( $nullFlag ) {
					$this->Flash->error(__('Make sure all fields are filled with values greater than zero'));
				} elseif ( $phaseFlag ) {
	                $this->Flash->error(__('Current phase number cannot exceed the total.'));
				} else {
	                $this->Flash->error(__('Passed test cases cannot exceed the total.'));
				}
            }
            else {
                if($dataok){
                    $this->request->session()->write('current_metrics', $metrics);
                    // based on the last form data we either move back or forward in the form
                    if($this->request->data['submit'] == "next"){
                        return $this->redirect(
                            ['controller' => 'Weeklyhours', 'action' => 'addmultiple']
                        );
                    }
                    else{
                        return $this->redirect(
                            ['controller' => 'Weeklyreports', 'action' => 'add']
                        );
                    }

                }
                else{
                    $this->Flash->success(__('Metrics failed validation'));
                }
            }
        }
        $projects = $this->Metrics->Projects->find('list', ['limit' => 200]);
        $metrictypes = $this->Metrics->Metrictypes->find('list', ['limit' => 200]);
        $weeklyreports = $this->Metrics->Weeklyreports->find('list', ['limit' => 200, 'conditions' => array('Weeklyreports.project_id' => $project_id)]);
        $this->set(compact('metric', 'projects', 'metrictypes', 'weeklyreports'));
        $this->set('_serialize', ['metric']);
    }
    
        public function edit($id = null)
    {   
        // the metric can only be edited if its from the current project
        $project_id = $this->request->session()->read('selected_project')['id'];
        $metric = $this->Metrics->get($id, [
            'contain' => [],
            'conditions' => array('Metrics.project_id' => $project_id)
        ]);
       // metrictype_id and weeklyreport_id for the metric in question
        $metric_type = $metric->metrictype_id;
        $wr_id = $metric->weeklyreport_id;
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            // data from the form
            $metric = $this->Metrics->patchEntity($metric, $this->request->data);
            
            $errTooHigh = False;
            $errTooSmall = False;
            
            // phase, totalPhases, passedTestCases, totalTestCases
            if($metric_type == 1 || $metric_type == 2 || $metric_type == 8 || $metric_type == 9) { 
                $query = TableRegistry::get('Metrics')
                        ->find()
                        ->select(['metrictype_id', 'value']) 
                        ->where(['project_id =' => $project_id, 'weeklyreport_id' => $wr_id])
                        ->toArray(); 
                $items = $query;
                
                foreach($items as $item) {
                    if($item != null) {
                        // total phases must be greater than phases
                        if(($metric['metrictype_id'] == 1) && ($item['metrictype_id'] == 2)) {
                            if($metric['value'] > $item['value']) {                           
                                $errTooHigh = True;
                                break;
                            }
                        }
                        if(($metric['metrictype_id'] == 2) && ($item['metrictype_id'] == 1)) {
                            if($metric['value'] < $item['value']) {                           
                                $errTooSmall = True;
                                break;
                            }
                        }
                        // total test cases must be greater than passed test cases
                        if (($metric['metrictype_id'] == 8) && ($item['metrictype_id'] == 9)) {
                            if($metric['value'] > $item['value']) {
                                $errTooHigh = True;
                                break;
                            }
                        }
                        if(($metric['metrictype_id'] == 9) && ($item['metrictype_id'] == 8)) {
                            if($metric['value'] < $item['value']) {                           
                                $errTooSmall = True;
                                break;
                            }
                        }
                    }
                }    
            }

            // it is made sure that the metric stays in the same project
            $metric['project_id'] = $project_id;
           
            if ($errTooHigh) {
                $this->Flash->error(__('The number must be smaller. Please, try again.'));
            }
            elseif ($errTooSmall) {
                $this->Flash->error(__('The number must be higher. Please, try again.'));
            }
            // if no errors found
            else {
                if ($this->Metrics->save($metric)) {
                    $this->Flash->success(__('The metric has been saved.'));
                    // takes the user to the weeklyreport's page
                    return $this->redirect(['controller' => 'weeklyreports', 'action' => 'view', $wr_id]);                    
                    // place the user back where they presed the edit button
                    /*echo "<script>
                        window.history.go(-2);
                    </script>"; */
                } else {
                    $this->Flash->error(__('The metric could not be saved. Please, try again.'));
                }
            }    
        }
        $metrictypes = $this->Metrics->Metrictypes->find('list', ['limit' => 200]);
        $weeklyreports = $this->Metrics->Weeklyreports->find('list', ['limit' => 200, 'conditions' => array('Weeklyreports.project_id' => $project_id)]);
        $this->set(compact('metric', 'projects', 'metrictypes', 'weeklyreports'));
        $this->set('_serialize', ['metric']);
    }

    // normal delete function
    // will not allow deletion of metrics that belong to weeklyreports
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $metric = $this->Metrics->get($id);

        if($metric['weeklyreport_id'] == NULL){
            if ($this->Metrics->delete($metric)) {
                $this->Flash->success(__('The metric has been deleted.'));
            }
            else {
                $this->Flash->error(__('The metric could not be deleted. Please, try again.'));
            } 
        }
        else {
            $this->Flash->error(__('Cannot delete metrics that belong to a weeklyreport'));
        } 
        return $this->redirect(['action' => 'index']);
        
    }
    
    // a admin only delete function, will allow deleting metrics that belong to weeklyreports
    public function deleteadmin($id = null)
    {
        $this->request->allowMethod(['post', 'deleteadmin']);
        $metric = $this->Metrics->get($id);

        if ($this->Metrics->delete($metric)) {
            $this->Flash->success(__('The metric has been deleted.'));
        }
        else {
            $this->Flash->error(__('The metric could not be deleted. Please, try again.'));
        } 

        return $this->redirect(['action' => 'index']);
        
    }
    
    public function isAuthorized($user)
    {   
        // Admin can access every action
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        
        
        return parent::isAuthorized($user);
    }
}

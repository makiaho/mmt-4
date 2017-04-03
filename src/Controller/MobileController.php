<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\I18n\Time;
use Highcharts\Controller\Component\HighchartsComponent;

class MobileController extends AppController 
{

    public function index() {
        
        
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
            }else{
                $this->Flash->error('Your username or password is incorrect.');
            }
            
        }
        
        $myProjects = [];
        if($this->Auth->user()){
            
           $myProjectIds = TableRegistry::get('Members')->find()
                   ->where(['user_id' => $this->Auth->user('id')])->select('project_id')->toArray();
           
           $ids = [];
           
           foreach($myProjectIds as $item){
               $ids[] = $item->project_id;
           }
           
           $myProjects = TableRegistry::get('Projects')->find()->where(['id IN' => $ids])->toArray();


        }

        $this->set('myProjects',$myProjects);
    }
    
    public function addhour() {
        
       
      
        $worktypes = TableRegistry::get('Workinghours')->Worktypes->find('list', ['limit' => 200]);
        
        $workinghour = TableRegistry::get('Workinghours')->newEntity();
        
        if ($this->request->is('post')) {
            // get data from the form
            $workinghour = TableRegistry::get('Workinghours')->patchEntity($workinghour, $this->request->data);  
            // only allow members to add workinghours for themself
            $workinghour['member_id'] = $this->request->session()->read('selected_project_memberid');
            
            if (TableRegistry::get('Workinghours')->save($workinghour)) {
                $this->Flash->success(__('The workinghour has been saved.'));
                return $this->redirect(['action' => 'project']);
            } else {
                $this->Flash->error(__('The workinghour could not be saved. Please, try again.'));
            }
        }
        
        $this->set('worktypes',$worktypes);
        $this->set('workinghour',$workinghour);
    }
    
    public function project($id = null) {
        
        if($id != null){
            
            $project = TableRegistry::get('Projects')->get($id, [
            'contain' => ['Members', 'Metrics', 'Weeklyreports']]);
            
            $this->request->session()->write('selected_project', $project);
        }else{
            $id = $this->request->session()->read('selected_project')['id'];
        }
        
        
       
        $members = TableRegistry::get('Members')->find('all',[
                'conditions' => ['project_id' => $id],
                'contain' => ['Users', 'Projects', 'Workinghours']
                ])->toArray();
        
        $this->set('members', $members);
    }
    
    public function chart() {
        
        
        $this->loadComponent('Highcharts.Highcharts');
        
        $this->helpers = ['Highcharts.Highcharts'];
        
        // When the chart limits are updated this is where they are saved
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $chart_limits['weekmin'] = $data['weekmin'];
            $chart_limits['weekmax'] = $data['weekmax'];
            $chart_limits['yearmin'] = $data['yearmin'];
            $chart_limits['yearmax'] = $data['yearmax'];
			
            $this->request->session()->write('chart_limits', $chart_limits);

        }
        // Set the stock limits for the chart limits
        // They are only set once, if the "chart_limits" cookie is not in the session
        if(!$this->request->session()->check('chart_limits')){
            $time = Time::now();
            // show last year, current year and next year
            $chart_limits['weekmin'] = 1;
            $chart_limits['weekmax'] =  52;
            $chart_limits['yearmin'] = $time->year - 1;
            $chart_limits['yearmax'] = $time->year;
            
            $this->request->session()->write('chart_limits', $chart_limits);
        }
        // Loadin the limits to a variable
        $chart_limits = $this->request->session()->read('chart_limits');
        // The ID of the currently selected project
        $project_id = $this->request->session()->read('selected_project')['id'];
        
        
        $chartController = new ChartsController();
        
        // Get the chart objects for the charts
        // these objects come from functions in this controller
        $phaseChart = $chartController->phaseChart();
        $reqChart = $chartController->reqChart();
        $commitChart = $chartController->commitChart();
        $testcaseChart = $chartController->testcaseChart();
        $hoursChart = $chartController->hoursChart();
        $weeklyhourChart = $chartController->weeklyhourChart();
        $hoursPerWeekChart = $chartController->hoursPerWeekChart();
        $reqPercentChart = $chartController->reqPercentChart();
        $derivedChart = $chartController->derivedChart();
        
        // Get all the data for the charts, based on the chartlimits
        // Fuctions in "ChartsTable.php"
        $weeklyreports = $chartController->Charts->reports($project_id, $chart_limits['weekmin'], $chart_limits['weekmax'], $chart_limits['yearmin'], $chart_limits['yearmax']);
        $phaseData = $chartController->Charts->phaseAreaData($weeklyreports['id']);
        $reqData = $chartController->Charts->reqColumnData($weeklyreports['id']);
        $commitData = $chartController->Charts->commitAreaData($weeklyreports['id']);
        $testcaseData = $chartController->Charts->testcaseAreaData($weeklyreports['id']);
        $hoursData = $chartController->Charts->hoursData($project_id);
        $hoursperweekData = $chartController->Charts->hoursPerWeekData($project_id, $weeklyreports['id'], $weeklyreports['weeks']);
        $weeklyhourData = $chartController->Charts->weeklyhourAreaData($weeklyreports['id']);
        
        // Insert the data in to the charts, one by one
        // phaseChart
        $phaseChart->xAxis->categories = $weeklyreports['weeks'];
        $phaseChart->series[] = array(
            'name' => 'Total phases planned',
            'data' => $phaseData['phaseTotal']
        );
        $phaseChart->series[] = array(
            'name' => 'Phase',
            'data' => $phaseData['phase']
        );
        $phaseChart->chart['width'] = null;
        
        // reqChart
        $reqChart->xAxis->categories = $weeklyreports['weeks'];
        $reqChart->series[] = array(
            'name' => 'New',
            'data' => $reqData['new']
        );
        $reqChart->series[] = array(
            'name' => 'In progress',
            'data' => $reqData['inprogress']
        );
        $reqChart->series[] = array(
            'name' => 'Closed',
            'data' => $reqData['closed']
        );
        $reqChart->series[] = array(
            'name' => 'Rejected',
            'data' => $reqData['rejected']
        );
        $reqChart->chart['width'] = null;
        
        // commitChart
        $commitChart->xAxis->categories = $weeklyreports['weeks'];    
        $commitChart->series[] = array(
            'name' => 'commits',
            'data' => $commitData['commits']
        );
        $commitChart->chart['width'] = null;
        
        // testcaseChart
        $testcaseChart->xAxis->categories = $weeklyreports['weeks'];
        $testcaseChart->series[] = array(
            'name' => 'Total test cases',
            'data' => $testcaseData['testsTotal']
        );
        $testcaseChart->series[] = array(
            'name' => 'Passed test cases',
            'data' => $testcaseData['testsPassed']
        );
        $testcaseChart->chart['width'] = null;
        
        // hoursChart
        $hoursChart->series[] = array(
            'name' => 'Management',
            'data' => array(
                $hoursData['management'],
                $hoursData['code'],
                $hoursData['document'],
                $hoursData['study'],
                $hoursData['other']
            )
        );
        $hoursChart->chart['width'] = null;
        
        // weeklyhourChart 
        $weeklyhourChart->xAxis->categories = $weeklyreports['weeks'];    
        $weeklyhourChart->series[] = array(
            'name' => 'weekly hours',
            'data' => $weeklyhourData
        );
        $weeklyhourChart->chart['width'] = null;
        
        //workinghours per week  
        $hoursPerWeekChart->xAxis->categories = $weeklyreports['weeks'];
        $hoursPerWeekChart->series[] = array(
            'name' => 'Working hours per week',
            'data' => $hoursperweekData
        );
        $hoursPerWeekChart->chart['width'] = null;
        
        // reqPercentChart
        $reqPercentChart->xAxis->categories = $weeklyreports['weeks'];
        $reqPercentChart->series[] = array(
            'name' => 'New',
            'data' => $reqData['new']
        );
        $reqPercentChart->series[] = array(
            'name' => 'In progress',
            'data' => $reqData['inprogress']
        );
        $reqPercentChart->series[] = array(
            'name' => 'Closed',
            'data' => $reqData['closed']
        );
        $reqPercentChart->series[] = array(
            'name' => 'Rejected',
            'data' => $reqData['rejected']
        );
        $reqPercentChart->chart['width'] = null;
              
        // chart for derived metrics
        $derivedChart->xAxis->categories = $weeklyreports['weeks'];
        $derivedChart->series[] = array(
            'name' => 'Total test cases',
            'data' => $testcaseData['testsTotal']
        );
        $derivedChart->series[] = array(
            'name' => 'Passed test cases',
            'data' => $testcaseData['testsPassed']
        );
        $derivedChart->chart['width'] = null;
        
        // This sets the charts visible in the actual charts page "Charts/index.php"
        $this->set(compact('phaseChart', 'reqChart', 'commitChart', 'testcaseChart', 'hoursChart', 'weeklyhourChart', 'hoursPerWeekChart', 'reqPercentChart', 'derivedChart'));

    }
    
    public function logout()
    {
        // remove all session data
        $this->request->session()->delete('selected_project');
        $this->request->session()->delete('selected_project_role');
        $this->request->session()->delete('selected_project_memberid');
        $this->request->session()->delete('current_weeklyreport');
        $this->request->session()->delete('current_metrics');
        $this->request->session()->delete('current_weeklyhours');
        $this->request->session()->delete('project_list');
        $this->request->session()->delete('project_memberof_list');
        $this->request->session()->delete('is_admin');
        $this->request->session()->delete('is_supervisor');
        
        $this->Flash->success('You are now logged out.');
        
        $this->Auth->logout();
        
        return $this->redirect(['action' => 'index']);
    }
    
    // this allows anyone to go to the frontpage
    public function beforeFilter(\Cake\Event\Event $event)
    {   

        $this->Auth->allow(['index']);
        
        if($this->Auth->user()){
            
            $this->Auth->allow(['chart']);
            $this->Auth->allow(['logout']);
        }
        

    }
    
    public function beforeRender(\Cake\Event\Event $event)
    {   

        $this->viewBuilder()->layout('mobile');
        

    }
    
    
    public function isAuthorized($user)
    {      
       
      
        // authorization for the selected project
        if ($this->request->action === 'project') 
        {   
            if(!empty($this->request->pass)){          
                $id = $this->request->pass[0];
            }else{
                $id = $this->request->session()->read('selected_project')['id'];
            }

            $time = Time::now();
            $project_role = "";
            $project_memberid = -1;
            // what kind of member is the user
            $members = TableRegistry::get('Members');    
            // load all the memberships that the user has for the selected project
            $query = $members
                ->find()
                ->select(['project_role', 'id', 'ending_date'])
                ->where(['user_id =' => $this->Auth->user('id'), 'project_id =' => $id])
                ->toArray();

            // for loop goes through all the memberships that this user has for this project
            // its most likely just 1, but since it has not been limited to that we must check for all possibilities
            // the idea is that the highest membership is saved, 
            // so if he or she is a developer and a supervisor, we save the latter
            foreach($query as $temp){
                // if supervisor, overwrite all other memberships     
                if($temp->project_role == "supervisor" && ($temp->ending_date > $time || $temp->ending_date == NULL)){
                    $project_role = $temp->project_role;
                    $project_memberid = $temp->id;
                }
                // if the user is a manager in the project 
                // but we have not yet found out that he or she is a supervisor
                // if dev or null then it gets overwritten
                elseif($temp->project_role == "manager" && $project_role != "supervisor" && ($temp->ending_date > $time || $temp->ending_date == NULL)){
                    $project_role = $temp->project_role;
                    $project_memberid = $temp->id;
                }
                // if we have not found out that the user is a manager or a supervisor
                elseif($project_role != "supervisor" && $project_role != "manager" && ($temp->ending_date > $time || $temp->ending_date == NULL)){
                    $project_role = $temp->project_role;
                    $project_memberid = $temp->id;
                }      
            }
            // if the user is a admin, he is automatically a admin of the project
            if($this->Auth->user('role') == "admin"){
                $project_role = "admin";
            }
            // if the user is not a admin and not a member
            elseif($project_role == ""){
                $project_role = "notmember";
            }


            $this->request->session()->write('selected_project_role', $project_role);
            $this->request->session()->write('selected_project_memberid', $project_memberid);
            // if the user is not a member of the project he can not access it
            // unless the project is public
            if($project_role == "notmember"){  
                $query = TableRegistry::get('Projects')
                    ->find()
                    ->select(['is_public'])
                    ->where(['id' => $this->request->pass[0]])
                    ->toArray();          
                if($query[0]->is_public == 1){
                    return True;
                }
                else{
                    return False;
                }    
            }
            else{
                return True;
            }
        }else if ($this->request->action === 'addhour'){
            
            $project_role = $this->request->session()->read('selected_project_role');
            
            return ($project_role == 'manager' || $project_role == 'developer');
            
        }else{
            // Default allow
            return true;
        }

       

        
    }

}

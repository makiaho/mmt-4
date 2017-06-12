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
        $risksProbChart = $chartController->risksProbChart();
        $risksImpactChart = $chartController->risksImpactChart();
        $risksCombinedChart = $chartController->risksCombinedChart();
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
        $riskData = $chartController->Charts->riskData($weeklyreports['id'], $project_id);
        
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
        
        
        // risksProbChart
        $risksProbChart->xAxis->categories = $weeklyreports['weeks'];
        
        foreach ($riskData as $risk){
            
            $risksProbChart->series[] = array(
                'name' => $risk['name'],
                'data' => $risk['probability']
            );
        
        }
        
        
        // risksImpactChart
        $risksImpactChart->xAxis->categories = $weeklyreports['weeks'];
        
        foreach ($riskData as $risk){
            
            $risksImpactChart->series[] = array(
                'name' => $risk['name'],
                'data' => $risk['impact']
            );
        
        }
        
        
        // risksCombinedChart
        $risksCombinedChart->xAxis->categories = $weeklyreports['weeks'];
        
        foreach ($riskData as $risk){
            
            $risksCombinedChart->series[] = array(
                'name' => $risk['name'],
                'data' => $risk['combined']
            );
        
        }
        
        
              
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
        $this->set(compact('phaseChart', 'reqChart', 'commitChart', 'testcaseChart', 'hoursChart', 'weeklyhourChart', 'hoursPerWeekChart', 'reqPercentChart', 'risksProbChart', 'risksImpactChart', 'risksCombinedChart', 'derivedChart'));

    }
    
    public function report()
    {
        $project_id = $this->request->session()->read('selected_project')['id'];
        
        $report = TableRegistry::get('Weeklyreports')->find('all',[
            'conditions' => ['project_id' => $project_id],
            'order' => ['year' => 'DESC', 'week' => 'DESC'],
            'contain' => ['Projects', 'Metrics', 'Workinghours']
                ])->first();
         
        $members = array();
        
        //get the weekly risks of the report
        $risks = array();
        
        if($report !== null){
            
            $metricNames = (new MetricsController())->getMetricNames();
            
            foreach($report->metrics as $metrics) {
                
                $metrics['metric_description'] = $metricNames[$metrics->metrictype_id];
            }	

            $risksController = new RisksController();

            $riskTypes = $risksController->getImpactProbTypes();

            $currentWeeklyRisks = TableRegistry::get('Weeklyrisks')->find()->where(['weeklyreport_id' => $report['id']]);

            foreach($currentWeeklyRisks as $weeklyRisk){

                $risk = new \stdClass();

                $risk->description = TableRegistry::get('Risks')->get($weeklyRisk['risk_id'])['description'];
                $risk->impact = $riskTypes[$weeklyRisk['impact']];
                $risk->probability = $riskTypes[$weeklyRisk['probability']];

                $risks[] = $risk;

            }
            
            
            
            $membersList = TableRegistry::get('Members')->find('all',[
                'conditions' => ['project_id' => $project_id],
                'contain' => ['Users', 'Projects', 'Workinghours']
                ])->toArray();
            
            
            foreach($membersList as $member){
                
                if($member->project_role === 'manager' || $member->project_role === 'developer'){
                    
                    $name = $member->user->full_name;
                    
                    $hours = 0;
                    
                    $queryForHours = $member->workinghours;
                    
                    foreach ($queryForHours as $key) {
                        if ($report->week == $key->date->format('W')) {
                            if (($report->week == 52 && $key->date->format('m') == 01) ||
                                    ($report->week == 5 && $key->date->format('m') == 01) || 
                                    ($report->week == 1 && $key->date->format('m') == 12) ||
                                    ($report->year == $key->date->format('Y'))) {
                            
                                    $hours += $key->duration;
                                }
                        }
                    } 
                    
                    $obj = new \stdClass();
                    
                    $obj->name = $name;
                    $obj->hours = $hours;
                    $obj->role = $member->project_role;
                    
                    $members[] = $obj;
                }
                
            }
            
        }
        
        $this->set('report', $report);
        $this->set('risks', $risks);
        $this->set('members', $members);
    }
    
    public function stat(){
        
        // get the limits from the sidebar if changes were submitted
        if ($this->request->is('post')) {
            $data = $this->request->data;
            
            /* FIX: editing limits on Public Statistics now behaves like a decent UI
             */
            // fetch values using helpers
            $min = $data['weekmin'];
            $max = $data['weekmax'];
		$year = $data['year'];
            
            // correction for nonsensical values for week numbers
            if ( $min < 1 )  $min = 1;
            if ( $min > 53 ) $min = 53;
            if ( $max < 1 )  $max = 1;
            if ( $max > 53 ) $max = 53;
            if ( $max < $min ) {
            	$temp = $max;
            	$max = $min;
            	$min = $temp;
            }
			
            $statistics_limits['weekmin'] = $min;
            $statistics_limits['weekmax'] = $max;
            $statistics_limits['year'] = $year;
            
            $this->request->session()->write('statistics_limits', $statistics_limits);

        }
        
        // current default settings
        if(!$this->request->session()->check('statistics_limits')){
            $time = Time::now();
            $week = date('W');
            $month = date('m');
            // weekmin will be the current week - 10
            // weekmax will be the current week + 1
            // exceptions when the current week is 1-10 or 53
            
            // weeks 2-10
            if ($week >= 2 && $week <= 10) {
                $weekmin = 1;
                $weekmax = $week+1;               
            }
            // week 1
            elseif ($week == 1) {
                $weekmin = 43;
                $weekmax = 53;        
            }
            elseif ($week == 53) {
                $weekmin = $week-10;
                $weekmax = $week;                
            }
            // weeks 11-52
            else {
                $weekmin = $week-10;
                $weekmax = $week+1;         
            }
            // these initial limits are arbitrary so change freely if needed
            $statistics_limits['weekmin'] = $weekmin;
            $statistics_limits['weekmax'] = $weekmax;
            
            $year = $time->year;
            $diffYear = $year - 1 ; 
            
            if (($week == 1 && $month == 01) || 
                ($week == 52 && $month == 01) || 
                ($week == 53 && $month == 01) ) {
                $statistics_limits['year'] = $diffYear;
            }
            else {
                $statistics_limits['year'] = $time->year;
            }
                    
            $this->request->session()->write('statistics_limits', $statistics_limits);
        }

        // load the limits to a variable
        $statistics_limits = $this->request->session()->read('statistics_limits');
        // function in the projects table "ProjectsTable.php"
        // return the list of public projects
        
        $projectsTable = TableRegistry::get('Projects');
        
        $publicProjects = $projectsTable->getPublicProjects();
        $projects = array();
        // the weeklyreport weeks and the total weeklyhours duration is loaded for all projects
        // functions in "ProjectsTable.php"
        foreach($publicProjects as $project){
            $project['reports'] = $projectsTable->getWeeklyreportWeeks($project['id'], 
            $statistics_limits['weekmin'], $statistics_limits['weekmax'], $statistics_limits['year']);
            $project['duration'] = $projectsTable->getWeeklyhoursDuration($project['id']);
            $project['sum'] = $projectsTable->getHoursDuration($project['id']);
            $projects[] = $project;
        }
        // the projects and their data are made visible in the "statistics.php" page
        $this->set(compact('projects'));
        $this->set('_serialize', ['projects']);
        
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
        $this->Auth->allow(['stat']);
        
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
                    ->where(['id' => $id])
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

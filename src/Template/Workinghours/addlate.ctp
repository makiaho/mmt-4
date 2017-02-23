<?php
echo $this->Html->css('jquery-ui.min');
echo $this->Html->script('jquery');
echo $this->Html->script('jquery-ui.min');
?>

<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="workinghours form large-8 medium- columns content float: left">
    <?= $this->Form->create($workinghour) ?>
    <fieldset>
        <?php 
        // full name for the member
        $first= $this->request->session()->read('Auth.User.first_name');
        $last= $this->request->session()->read('Auth.User.last_name'); ?>
        <legend><?= __('Log time late for ') . $first . " " . $last ?></legend>    

        <?php

            // Req 21: Using jQuery UI datepicker
            echo $this->Form->input('date', ['type' => 'text', 'readonly' => true]);
            ?> </br>
        <?php  
            echo $this->Form->input('description');
            echo $this->Form->input('duration', array('style' => 'width: 35%;'));
            echo $this->Form->input('worktype_id', ['options' => $worktypes]);
        
            $project_id = $this->request->session()->read('selected_project')['id'];
            $query1 = Cake\ORM\TableRegistry::get('Weeklyreports')
                ->find()
           	->select(['year','week']) 
            	->where(['project_id =' => $project_id])
                ->toArray(); 
            
            if (count($query1) >= 2) {
                // picking out the week of the last weekly report from the results
                $max = max($query1);

                $maxYear = $max['year'];
                $maxWeek = $max['week'];
                
                //$minDate: the first day of the new weeklyreport week (monday)
                $monday = new DateTime();
                $monday->setISODate($maxYear,$maxWeek,1);
                $date1 = $monday->format('d M Y');
                $minDate = date('d M Y', strtotime($date1));
            }
            /*
             * If the weeklyreport is the first one
             * The date project was created is fetched from the db.
             */ 
            else {
                $project_id = $this->request->session()->read('selected_project')['id'];
                $query2 = Cake\ORM\TableRegistry::get('Projects')
                    ->find()
                    ->select(['created_on']) 
                    ->where(['id =' => $project_id])
                    ->toArray(); 
                
                foreach($query2 as $result) {
                    $temp = date_parse($result);
                    $year = $temp['year'];
                    $month = $temp['month'];
                    $day = $temp['day'];
                    
                    // $mDate is the date project was created on  
                    $minDate = date("d M Y", mktime(0,0,0, $month, $day, $year));
                }
            }
            
            echo $this->Form->button(__('Submit'));
        ?>    
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<script> 
    /* minDate is either monday of the week of the weeklyreport or the date project was created
     * maxDate is the current day
     * */
    $( "#date" ).datepicker({
        dateFormat: "MM d, yy",
        minDate: new Date('<?php echo $minDate; ?>'),
        maxDate: '0', 
        firstDay: 1,
        showWeek: true,
        showOn: "both",
        buttonImage: "../webroot/img/glyphicons-46-calendar.png",
        buttonImageOnly: true,
        buttonText: "Select date"       
    });
</script>



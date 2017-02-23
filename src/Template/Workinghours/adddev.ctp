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
        <legend><?= __('Log time for another member') ?></legend>
        <?php
            /* Req 10: changing the ID's of entities to their textual names 
             * updated: WorkingHours.addev.ctp, WorkingHoursController.php, User.php, 
             * Weeklyhours.edit.ctp, WeeklyHoursController.php*/
            echo $this->Form->input('member_id', ['options' => $members, 'label' => 'Member Name', 'empty' => ' ', 'required' => true]);
            
            // Req 21: Using jQuery UI datepicker
            echo $this->Form->input('date', ['type' => 'text', 'readonly' => true]);
            ?> </br>
        <?php  
            echo $this->Form->input('description');
            echo $this->Form->input('duration', array('style' => 'width: 35%;'));
            echo $this->Form->input('worktype_id', ['options' => $worktypes, 'empty' => ' ', 'required' => true]);
        
            // Fetching from the db the date when the project was created      
            $project_id = $this->request->session()->read('selected_project')['id'];
            $query = Cake\ORM\TableRegistry::get('Projects')
                ->find()
                ->select(['created_on']) 
                ->where(['id =' => $project_id])
                ->toArray(); 
                
            foreach($query as $result) {
                $temp = date_parse($result);
                $year = $temp['year'];
                $month = $temp['month'];
                $day = $temp['day'];   
                $mDate = date("d M Y", mktime(0,0,0, $month, $day, $year));
            }
			
			echo $this->Form->button(__('Submit'));
        ?>    
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<script> 
    /*
     * Req 21:
     * minDate is the date the project was created
     * maxDate is the current day
     */
    $( "#date" ).datepicker({
        dateFormat: "MM d, yy",
        minDate: new Date('<?php echo $mDate; ?>'),
        maxDate: '0', 
        firstDay: 1,
        showWeek: true,
        showOn: "both",
        buttonImage: "../webroot/img/glyphicons-46-calendar.png",
        buttonImageOnly: true,
        buttonText: "Select date"       
    });
</script>

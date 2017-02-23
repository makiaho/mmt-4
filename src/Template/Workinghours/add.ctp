<?php
echo $this->Html->css('jquery-ui.min');
echo $this->Html->script('jquery');
echo $this->Html->script('jquery-ui.min');
?>

<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="workinghours form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($workinghour) ?>
    <fieldset>
        <legend><?= __('Log time') ?></legend>
        
          <?php 
            /*
             * Req 1
             * Using jQuery UI datepicker
             * Added css and js files for datepicker to webroot
             * Changed settings for validation in WorkingHoursTable.php
             * Readonly turns the text field grey and doesn't allow other input than 
             * the date selected from the calendar
             * Added input[readonly] to cake.css
             */
          
            echo $this->Form->input('date', ['type' => 'text', 'readonly' => true]);
            ?> </br>
        <?php  
            echo $this->Form->input('description');
            echo $this->Form->input('duration', array('style' => 'width: 35%;'));
            echo $this->Form->input('worktype_id', ['options' => $worktypes, 'empty' => ' ', 'required' => true]); 
            
            /*
             * Req 1
             * If there are no weekly reports for the project then the minimum date 
             * in the datepicker's date range is the date the project was created.
             * Otherwise, the minimum date in the date range is the monday after
             * the last weekly report was sent.
             */
         
            $project_id = $this->request->session()->read('selected_project')['id'];
            /*
            $query1 = Cake\ORM\TableRegistry::get('Weeklyreports')
                ->find()
           	->select(['year','week']) 
            	->where(['project_id =' => $project_id])
                ->toArray(); 
            
            if ($query1 != null) {
                // picking out the week of the last weekly report from the results
                $max = max($query1);

                $maxYear = $max['year'];
                $maxWeek = $max['week'];
                
                //$mDate: the first day of the new weeklyreport week (monday)
                $monday = new DateTime();
                $monday->setISODate($maxYear,$maxWeek,8);
                $mDate1 = $monday->format('d M Y');
                $mDate = date('d M Y', strtotime($mDate1));
            }             
            /*
             * The date project was created is fetched from the db.
             */
            // else {  
                $project_id = $this->request->session()->read('selected_project')['id'];
                $queryP = Cake\ORM\TableRegistry::get('Projects')
                        ->find()
                        ->select(['created_on']) 
                        ->where(['id =' => $project_id])
                        ->toArray(); 

                foreach($queryP as $result) {
                    $temp = date_parse($result);
                    $year = $temp['year'];
                    $month = $temp['month'];
                    $day = $temp['day'];
                    // $mDate is the date project was created on      
                    $mDate = date("d M Y", mktime(0,0,0, $month, $day, $year));
                }
            // }    
	    echo $this->Form->button(__('Submit'));
        ?>    
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<script> 
    /*
     * minDate is the date project was created
     * maxDate is the current day
     * both the input field and the icon can be clicked
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
 

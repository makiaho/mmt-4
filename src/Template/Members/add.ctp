<?php
echo $this->Html->css('jquery-ui.min');
echo $this->Html->script('jquery');
echo $this->Html->script('jquery-ui.min');
?>

<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="members form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($member) ?>
    <fieldset>
        <legend><?= __('Add Member') ?></legend>
        <?php
            //echo $this->Form->input('user_id', ['options' => $users, 'empty' => ' ', 'required' => true]);
            echo $this->Form->input('user_id', ['type' => 'hidden']);    
        ?><div class="ui-widget"><?php            
            echo $this->Form->input('email', ['options' => $users, 'type' => 'text', 'id' => 'autocomplete', 
                'required' => true, 'label' => 'Email of the user (enter first few characters)']);
            ?></div><?php
            echo $this->Form->input('project_role', 
                ['options' => array('client' => 'client', 'developer' => 'developer', 'manager' => 'manager', 'supervisor' => 'supervisor'), 'empty' => ' ']);
            
            // jQuery UI datepicker
            echo $this->Form->input('starting_date', ['type' => 'text', 'readonly' => true, 'id' => 'datepicker1']);            
            ?> </br>
            <?php            
            echo $this->Form->input('ending_date', ['type' => 'text', 'label' => 'Ending date (preferably leave this field empty)', 'readonly' => true, 'id' => 'datepicker2']);

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
                        
            $isAdmin = $this->request->session()->read('is_admin');
       ?>    
    </fieldset>
    <?= $this->Form->end() ?>
</div>

<script> 
    // minDate is the date the project was created, for admin there is no min date
    // maxDate is the current day

       $( "#datepicker1" ).datepicker({
        dateFormat: "MM d, yy",
        minDate: <?php if($isAdmin) { ?> null<?php } else { ?> new Date('<?php echo $mDate; ?>') <?php } ?>,
        maxDate: '0', 
        firstDay: 1,
        showWeek: true,
        showOn: "both",
        buttonImage: "../../webroot/img/glyphicons-46-calendar.png",
        buttonImageOnly: true,
        buttonText: "Select date"       
    });
        $( "#datepicker2" ).datepicker({
        dateFormat: "MM d, yy",
        minDate: <?php if($isAdmin) { ?> null<?php } else { ?> new Date('<?php echo $mDate; ?>') <?php } ?>,
        firstDay: 1,
        showWeek: true,
        showOn: "both",
        buttonImage: "../../webroot/img/glyphicons-46-calendar.png",
        buttonImageOnly: true,
        buttonText: "Select date"       
    });
</script>
<script>

    var emails = [ 
           <?php 
           if ($users != null) {
           foreach ($users as $user) {
               echo "\"" . $user . "\",";
           }
           }?>
    ];

    $( "#autocomplete" ).autocomplete({
      source: function( request, response ) {
              var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
              response( $.grep( emails, function( item ){
                  return matcher.test( item );
              }) );
          }
    });
</script>

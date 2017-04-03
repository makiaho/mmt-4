<?php
echo $this->Html->css('jquery-ui.min');
echo $this->Html->script('jquery');
echo $this->Html->script('jquery-ui.min');
?>

<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $project->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $project->id)]
            )
        ?></li>
    </ul>
</nav>
<div class="projects form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($project) ?>
    <fieldset>
        <legend><?= __('Edit Project') ?></legend>
        <?php
            echo $this->Form->input('project_name');
            
            // Req 37: using jQuery UI datepicker
            echo $this->Form->input('finished_date', ['type' => 'text', 'readonly' => true, 'label' => 'Completion date', 'id' => 'datepicker']);
            ?> </br>
            <?php
            echo $this->Form->input('description');
            echo $this->Form->input('is_public', array("checked" => "checked", 'label' => "This project is public"));
            
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
	<?= $this->Form->end(); ?>
</div>

<script> 
    /*
     * Req 37:
     * minDate is the date the project was created, no min date if it is admin
     */
    $( "#datepicker" ).datepicker({
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
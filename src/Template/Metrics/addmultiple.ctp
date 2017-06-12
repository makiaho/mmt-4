<!-- This is the second page in the weeklyreport form.
     $current_metrics is what was previously placed in the form if the user visits this page a second time
-->
<?= $this->Html->script('trello.js') ?>
<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="metrics form large-6 medium-12 columns content float: left">
    <?= $this->Form->create($metric) ?>
    <fieldset>
        <legend><?= __('Add Metrics, Page 2/4') ?></legend>
        <?php
            $current_metrics = $this->request->session()->read('current_metrics');
        
            echo $this->Form->input('phase', 
                array('value' => $current_metrics[0]['value'], 'label' => $metricNames[1],'type' => 'number', 'required'=>true));
            echo $this->Form->input('totalPhases', 
                array('value' => $current_metrics[1]['value'], 'label' => $metricNames[2],'type' => 'number', 'required'=>true));
            ?>
            <div class="boxed">
            <p>
                <?php echo "Current state of the requirements list"; ?>
            </p>
            <?php if($trello != null): ?>
            <div id="trello-requirements"  data-board-id="<?= $trello->board_id ?>" data-app-key="<?= $trello->app_key ?>" data-token="<?= $trello->token ?>">
                <?php foreach($trello->trellolinks as $link): ?>
                <div class="trello-link" data-list-id="<?= $link->list_id ?>" data-req="<?= $link->requirement_type ?>"></div>
                <?php endforeach; ?>
            </div>
            
            <?php endif; ?>
                
                <?php
            echo $this->Form->input('reqNew', 
                array('value' => $current_metrics[2]['value'], 'label' => $metricNames[3],'type' => 'number', 'required'=>true));
            echo $this->Form->input('reqInProgress', 
                array('value' => $current_metrics[3]['value'], 'label' => $metricNames[4],'type' => 'number', 'required'=>true));
            echo $this->Form->input('reqClosed', 
                array('value' => $current_metrics[4]['value'], 'label' => $metricNames[5],'type' => 'number', 'required'=>true));
            echo $this->Form->input('reqRejected', 
                array('value' => $current_metrics[5]['value'], 'label' => $metricNames[6],'type' => 'number', 'required'=>true));
            ?></div><?php
            echo $this->Form->input('commits', 
                array('value' => $current_metrics[6]['value'], 'label' => $metricNames[7],'type' => 'number', 'required'=>true));
            echo $this->Form->input('passedTestCases', 
                array('value' => $current_metrics[7]['value'], 'label' => $metricNames[8],'type' => 'number', 'required'=>true));
            echo $this->Form->input('totalTestCases', 
                array('value' => $current_metrics[8]['value'], 'label' => $metricNames[9],'type' => 'number', 'required'=>true));
		?>
                <div class="report-nav">
	        <?= $this->Form->button('Next page', ['name' => 'submit', 'value' => 'next']);?>
                <?= $this->Html->link('Previous page', ['name' => 'submit', 'value'=>'previous', 'controller' => 'Weeklyreports', 'action' => 'add'],['class' => 'link-button']); ?>
	    	</div>
    	</div>
    </fieldset>
   
    <?= $this->Form->end() ?>
</div>

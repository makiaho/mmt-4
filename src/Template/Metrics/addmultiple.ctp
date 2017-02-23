<!-- This is the second page in the weeklyreport form.
     $current_metrics is what was previously placed in the form if the user visits this page a second time
-->
<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="metrics form large-6 medium-12 columns content float: left">
    <?= $this->Form->create($metric) ?>
    <fieldset>
        <legend><?= __('Add Metrics, Page 2/3') ?></legend>
        <?php
            $current_metrics = $this->request->session()->read('current_metrics');
        
            echo $this->Form->input('phase', 
                array('value' => $current_metrics[0]['value'], 'label' => 'Current phase','type' => 'number', 'required'=>true));
            echo $this->Form->input('totalPhases', 
                array('value' => $current_metrics[1]['value'], 'label' => 'Total number of planned phases','type' => 'number', 'required'=>true));
            ?>
            <div class="boxed">
            <p>
                <?php echo "Current state of the requirements list"; ?>
            </p><?php
            echo $this->Form->input('reqNew', 
                array('value' => $current_metrics[2]['value'], 'label' => 'New requirements','type' => 'number', 'required'=>true));
            echo $this->Form->input('reqInProgress', 
                array('value' => $current_metrics[3]['value'], 'label' => 'Requirements in progress','type' => 'number', 'required'=>true));
            echo $this->Form->input('reqClosed', 
                array('value' => $current_metrics[4]['value'], 'label' => 'Closed requirements','type' => 'number', 'required'=>true));
            echo $this->Form->input('reqRejected', 
                array('value' => $current_metrics[5]['value'], 'label' => 'Rejected requirements','type' => 'number', 'required'=>true));
            ?></div><?php
            echo $this->Form->input('commits', 
                array('value' => $current_metrics[6]['value'], 'label' => 'Total number of commits','type' => 'number', 'required'=>true));
            echo $this->Form->input('passedTestCases', 
                array('value' => $current_metrics[7]['value'], 'label' => 'Passed test cases','type' => 'number', 'required'=>true));
            echo $this->Form->input('totalTestCases', 
                array('value' => $current_metrics[8]['value'], 'label' => 'Total number of test cases','type' => 'number', 'required'=>true));
		?>
		<div style="margin-top: 2em;">
	        <?php
	        	/* REQ ID 27: navigating back now doesn't require fields  to be filled
	        	* Also positions of buttons slightly altered
            	* Navigating back to previous page changed to regular link to avoid confusion
	        	*/
	            // buttons
		        echo $this->Form->button('Next page', ['name' => 'submit', 'value' => 'next', 'style' => 'float: right;']);
		    ?>
		    <!-- for positioning back-link -->
		    <div style="padding-top: 0.7em;">
			    <?php
			        echo $this->Html->link('Previous page', ['name' => 'submit', 'value'=>'previous', 'controller' => 'Weeklyreports', 'action' => 'add']); 
		    	?>
	    	</div>
    	</div>
    </fieldset>
   
    <?= $this->Form->end() ?>
</div>

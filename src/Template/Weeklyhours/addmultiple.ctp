<!-- The third page in the weeklyreport form.
     A input is added for all developers managers.
     Pre calculated workinghours are added automatically and if the user
     goes backwards on the page the current values are saved.
-->
<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="weeklyhours form large-6 medium-12 columns content float: left">
    <?= $this->Form->create($weeklyhours) ?>
    <fieldset>
        <legend><?= __('Preview of working hours, Page 3/3') ?></legend>
        <table>
            <tr>
                <th colspan="2"><?= __('Name - Project role') ?></th> 
                <th><?= __('Working hours') ?></th>
            </tr> 
        <?php
            $current_weeklyhours = $this->request->session()->read('current_weeklyhours');
            // if its not the first time the user visits this page in the same report
            // then previous values are loaded
            // else the pre calculated hours are added
            
            if(!is_null($current_weeklyhours)){

                for($count = 0; $count < count($memberlist); $count++){ ?>
                    <tr>
                        <td colspan="2"><?= h($memberlist[$count]['member_name']) ?></td>
                        <td><?= h($current_weeklyhours[$count]['duration']) ?></td>
                    </tr>        
                <?php }
            }
            else{
                for($count = 0; $count < count($memberlist); $count++){ ?>
                    <tr>
                        <td colspan="2"><?= h($memberlist[$count]['member_name']) ?></td>
                        <td><?= h($hourlist[$count]) ?></td>
                    </tr>        
                <?php }
            }
        ?>
        </table>        
        <?php

            // For the time being, the weeklyhours are merely hidden.

            $current_weeklyhours = $this->request->session()->read('current_weeklyhours');
            // if its not the first time the user visits this page in the same report
            // then previous values are loaded
            // else the pre calculated hours are added
            if(!is_null($current_weeklyhours)){
                //echo "<tr>";
                for($count = 0; $count < count($memberlist); $count++){
                    //print_r($memberlist[$count]['member_name']);
                    //echo "<td>";
                    echo $this->Form->hidden("{$count}.duration", array('value' => $current_weeklyhours[$count]['duration']));
                    //echo "</td>";
                }
                //echo "</tr>";
            }
            else{
                //echo "<tr>";
                for($count = 0; $count < count($memberlist); $count++){
                    //print_r($memberlist[$count]['member_name']);
                    //echo "<td>";
                    echo $this->Form->hidden("{$count}.duration", array('value' => $hourlist[$count]));
                    //echo "</td>";
                }
                //echo "</tr>";
            }
        ?>
        <div style="margin-top: 2em">
        	<?php
        		/* REQ ID 27: navigating back now doesn't require fields  to be filled
        		* Also positions of buttons slightly altered
            	* Navigating back to previous page changed to regular link to avoid confusion
	        	*/
        		echo $this->Form->button('Submit', ['name' => 'submit', 'value' => 'submit', 'style' => 'float: right;']);
        	?>
        	<!-- for positioning back-link -->
		    <div style="padding-top: 0.7em;">
			    <?php
			    	// echo $this->Form->button('Back', ['name' => 'submit', 'value' => 'previous']);
			        echo $this->Html->link('Previous Page', ['controller' => 'Metrics', 'action' => 'addmultiple']); 
		    	?>
	    	</div>
        </div>
    </fieldset>
    <?php 
        
    ?>
    <?= $this->Form->end() ?>
</div>

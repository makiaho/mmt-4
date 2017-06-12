<?php
	$userid = $this->request->session()->read('Auth.User.id');
	$projid = $this->request->session()->read('selected_project')['id'];
	$wrid = $weeklyreport->id;

	// fetch member id of current user in currently chosen project
	$memid = Cake\ORM\TableRegistry::get('Members')->find()
				->select(['id'])
				->where(['user_id =' => $userid, 'project_id =' => $projid])
				->toArray();
	
        $connection = \Cake\Datasource\ConnectionManager::get("default");
        
	if (!empty($memid[0]->id)) {
		$memid = $memid[0]->id;

		// if current weeklyreport's ID is in notifications, remove the row where current member's id is
                $connection->delete('notifications',['member_id' => $memid, 'weeklyreport_id' => $wrid]);
                             
	}
        // let's also remove data about unread weeklyreports
        $supervisor = ($this->request->session()->read('selected_project_role') == 'supervisor') ? 1 : 0;
	$super = $this->request->session()->read('is_supervisor');
	//if ( $this->request->session()->read('selected_project_role') == 'supervisor' ) {
        if ($super || $supervisor) {
            $newreps = Cake\ORM\TableRegistry::get('Newreports')->find()
		->select()
		->where(['user_id =' => $userid, 'weeklyreport_id =' => $wrid])
		->toArray();
		if ( sizeof($newreps) > 0 ) {
                  
                    $connection->delete('newreports',['user_id' => $userid, 'weeklyreport_id' => $wrid]);
                }
	}
	
	// if you're an admin or supervisor, we'll force you to change to the project the weeklyreport is from
	$admin = $this->request->session()->read('is_admin');
	$manager = ( $this->request->session()->read('selected_project_role') == 'manager' ) ? 1 : 0;
        $developer = ( $this->request->session()->read('selected_project_role') == 'developer' ) ? 1 : 0;

	if ( $admin || $supervisor ) {
		// fetch the ID of relevant project
		$query = Cake\ORM\TableRegistry::get('Weeklyreports')
					->find()
					->select(['project_id'])
					->where(['id =' => $weeklyreport['id']])
					->toArray();
		$iidee = $query[0]->project_id;
		
		/* Don't hit me. This code is a modified copy of Projects-controller's view-function.
		 * Essentially it is an unnecessary copy, but it cannot be accessed directly because MVC doesn't
		 * allow using controllers inside other controllers.
		 */
		$project = Cake\ORM\TableRegistry::get('Projects')->get($iidee, [
            'contain' => ['Members', 'Metrics', 'Weeklyreports']
        ]);
        $this->set('project', $project);
        $this->set('_serialize', ['project']);
		
		// if the selected project is a new one
        if($this->request->session()->read('selected_project')['id'] != $project['id']){
            // write the new id 
            $this->request->session()->write('selected_project', $project);
            // remove the all data from the weeklyreport form if any exists
            $this->request->session()->delete('current_weeklyreport');
            $this->request->session()->delete('current_metrics');
            $this->request->session()->delete('current_weeklyhours');
			
        }
	}
?>
<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">	
           <?php
            // link not visible to devs and clients
            if ($admin || $supervisor || $manager) { ?>
                <li><?= $this->Html->link(__('Edit Weeklyreport'), ['action' => 'edit', $weeklyreport->id]) ?> </li>
            <?php }
            // Logging time is allowed for the last weeklyreport
            // link not visible to supervisors and clients
            /*
            $queryForMax = Cake\ORM\TableRegistry::get('Weeklyreports')
		->find()
		->select(['year', 'week'])
		->where(['project_id =' => $weeklyreport['project_id']])
		->toArray();
            if(!empty($queryForMax)) {
                    $lastReport = max($queryForMax);
            }
            if (($weeklyreport->year >= $lastReport['year']) && ($weeklyreport->week >= $lastReport['week'])) {
                if ($admin || $manager || $developer) { ?>
                <li><?= $this->Html->link(__('Log time late'), ['controller' => 'Workinghours', 'action' => 'addlate']) ?></li>            
            <?php } 
            } */ ?>
    </ul>
</nav>
<div class="weeklyreports view large-8 medium-16 columns content float: left">
    <h3><?= h($weeklyreport->title) ?></h3>
	<h5><?= h($selected_project = $this->request->session()->read('selected_project')['project_name']) ?></h5>
    <table class="vertical-table">
        <tr>
            <th><?= __('Title') ?></th>
            <td><?= h($weeklyreport->title) ?></td>
        </tr>
        <tr>
            <th><?= __('Week') ?></th>
            <td><?= h($weeklyreport->week) ?></tr>
        </tr>
        <tr>
            <th><?= __('Year') ?></th>
            <td><?= h($weeklyreport->year) ?></tr>
        </tr>
		<tr>
            <th><?= __('Meetings') ?></th>
            <td><?= h($weeklyreport->meetings) ?></td>
        </tr>
        <tr>
            <th><?= __('Requirements link') ?></th>
            <td><?= h($weeklyreport->reglink) ?></td>
        </tr>
        <tr>
            <th><?= __('Challenges, issues, etc.') ?></th>
            <td><?= h($weeklyreport->problems) ?></td>
        </tr>
        <tr>
            <th><?= __('Additional') ?></th>
            <td><?= h($weeklyreport->additional) ?></td>
        </tr>
        <tr>
            <th><?= __('Created on') ?></th>
            <td><?= h($weeklyreport->created_on->format('d.m.Y')) ?></td>
        </tr>
        <tr>
            <th><?= __('Updated on') ?></th>
        <td><?php 
		if ( $weeklyreport->updated_on != NULL ) {
			echo h($weeklyreport->updated_on->format('d.m.Y'));
		} ?></td>
    </table>
    <div class="related">
        <h4><?= __('Working hours for week ') . $weeklyreport->week ?></h4>
        
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th colspan="2"><?= __('Name') ?></th>
                <th><?= __('Project role') ?></th>                    
                <th><?= __('Working hours') ?></th>
            </tr>

            
            <?php
            // Finding members who are not supervisors or clients
            $p_id = $this->request->session()->read('selected_project')['id'];
            $mlist = Cake\ORM\TableRegistry::get('Members')->find()
				->select()
				->where(['project_id =' => $p_id, 'project_role =' => 'developer'])
                                ->orWhere(['project_id =' => $p_id, 'project_role =' => 'manager'])
				->toArray();
            
            foreach ($mlist as $member): ?>
                <tr>
                <?php 
                $m_id = $member->id;
                $u_id = $member->user_id;
                $queryForHours = Cake\ORM\TableRegistry::get('Workinghours')->find()
				->select()
				->where(['member_id =' => $m_id])
				->toArray();
                $queryForName = Cake\ORM\TableRegistry::get('Users')->find()
     				->select(['first_name', 'last_name'])
				->where(['id =' => $u_id])
				->toArray();                   
                $sum = 0;
                if(!empty($queryForHours)) {
                    $hours = array();
                    // Get member's hours for the week
                    foreach ($queryForHours as $key) {
                        if ($weeklyreport->week == $key->date->format('W')) {
                            if (($weeklyreport->week == 52 && $key->date->format('m') == 01) ||
                                    ($weeklyreport->week == 5 && $key->date->format('m') == 01) || 
                                    ($weeklyreport->week == 1 && $key->date->format('m') == 12) ||
                                    ($weeklyreport->year == $key->date->format('Y'))) {
                            
                                    $hours[] = $key->duration;
                                    $sum = array_sum($hours);
                                }
                        }
                    } 
                } 
                // finding member's full name
                if(!empty($queryForName)) {
                    foreach ($queryForName as $name) {
                        $fullName = $name->first_name . " " . $name->last_name;        
                    }
                }?>
			
                <td colspan="2"><?= h($fullName) ?></td>
                <td><?= h($member->project_role) ?></td> 
                <td><?= h($sum) ?></td> 
            
        </tr>
        <?php endforeach; ?>
        </table>
        
        
        <h4><?= __('Metrics') ?></h4>
            <?php if (!empty($weeklyreport->metrics)): ?>
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <th colspan="2"><?= __('Metrictype') ?></th>                 
                    <th><?= __('Value') ?></th>
                    <th><?= __('Date') ?></th>
                    <?php /*
                    $queryForMax = Cake\ORM\TableRegistry::get('Weeklyreports')
                        ->find()
                        ->select(['year', 'week'])
                        ->where(['project_id =' => $weeklyreport['project_id']])
                        ->toArray();
                    if(!empty($queryForMax)) {
                        $lastReport = max($queryForMax);
                    }
                    */
                    // if (($admin || $supervisor) || ($manager && (($weeklyreport->year >= $lastReport['year']) && ($weeklyreport->week >= $lastReport['week'])))) { 
                    if ( $admin || $supervisor ) { ?> 
                        <th class="actions"><?= __('Actions') ?></th>
                    <?php } ?>
                </tr>
                <?php foreach ($weeklyreport->metrics as $metrics): ?>
                <tr>
                    <td colspan="2"><?= h($metrics->metric_description) ?></td>
                    <td><?= h($metrics->value) ?></td>
                    <td><?= h($metrics->date->format('d.m.Y')) ?></td>                  
                    <?php           
                    // admins and supervisors can edit metrics
                    // managers can edit metrics of the last weeklyreport
                    // if (($admin || $supervisor) || ($manager && (($weeklyreport->year >= $lastReport['year']) && ($weeklyreport->week >= $lastReport['week'])))) { 
                    if ( $admin || $supervisor ) { ?>     
                        <td class="actions">
                            <?= $this->Html->link(__('Edit'), ['controller' => 'Metrics', 'action' => 'edit', $metrics->id]) ?>  
                        </td> 
                    <?php } ?>
                </tr>
                <?php endforeach; ?>
            </table>
        
        <?php endif; ?>
        
        
        <?php if (!empty($risks)): ?>
        
        <h4><?= __('Risks') ?></h4>
        
        <table cellpadding="0" cellspacing="0">
                <tr>
                    <th><?= __('Risk') ?></th>                 
                    <th><?= __('Impact') ?></th>
                    <th><?= __('Probability') ?></th>
                </tr>
                
        <?php foreach($risks as $risk): ?>
          
                <tr>
                    <td><?= h($risk->description) ?></td>
                    <td><?= h($risk->impact) ?></td>
                    <td><?= h($risk->probability) ?></td>
                </tr>   
                
        <?php endforeach; ?>
                
        </table>

        <?php endif; ?>
        
		<h4><?= __('Comments') ?></h4>
		<?php
			// query for comments
			$query = Cake\ORM\TableRegistry::get('Comments')
						->find()
						->select()
						->where(['weeklyreport_id =' => $weeklyreport['id']])
						->toArray();
			
			if (empty( $query )) {
				echo "<p>No comments yet, be the first one!</p>";
			} else {
				// loop every query row
				for ($i=0; $i<sizeof( $query ); $i++ ) {
					// display info about user and time of the comment
					// data into variables
					$userquery = Cake\ORM\TableRegistry::get('Users')
								->find()
								->select(['first_name', 'last_name'])
								->where(['id =' => $query[$i]->user_id])
								->toArray();
					$fullname = $userquery[0]->first_name ." ". $userquery[0]->last_name;
					echo "<div class='messagebox'>";
					echo "<span class='msginfo'>" . $fullname . " left this comment on " . $query[$i]->date_created->format('d.m.Y, H:i') . "</span><br />";
					
                                        
                                        echo "<div class='msg-content'><span>" . $query[$i]->content . "</span></div>";

					// display edit and delete options to owner and admin/SV
					if ( $query[$i]->user_id == $this->request->session()->read('Auth.User.id') || ($admin || $supervisor) ) {
						echo "<div class='msgaction' data-edit-url='" . $this->Url->build(['controller' => 'Comments', 'action' => 'edit', $query[$i]->id]) . "'>";
                                                echo $this->Html->link(__('edit'), '#',['class' => 'edit']);
                                                echo $this->Html->link(__('save'), '#',['class' => 'save']);
                                                echo $this->Html->link(__('cancel'), '#',['class' => 'cancel']);
						echo $this->Html->link(__('delete'), ['controller' => 'Comments', 'action' => 'delete', $query[$i]->id],['class' => 'delete']);
						echo "</div>";
					}
					echo "</div>";
				}
			}
		?>
		<?php
			// current time
			$datetime = date_create()->format('Y-m-d H:i:s');
			
			echo $this->Form->create('Comments', array('url'=>array('controller'=>'Comments', 'action'=>'add')));
		?>
		<fieldset>
			<legend><?= __('New comment') ?></legend>
			<?= $this->Form->textarea('content') ?>
			<?= $this->Form->hidden('user_id', array('type' => 'numeric', 'value' => $this->request->session()->read('Auth.User.id') ) ) ?>
			<?= $this->Form->hidden('weeklyreport_id', array('type' => 'numeric', 'value' => $weeklyreport->id ) ) ?>
			<?php echo $this->Form->button('Submit', ['name' => 'submit', 'value' => 'submit']); ?>
		</fieldset>
		<?= $this->Form->end() ?>
    </div>
</div>
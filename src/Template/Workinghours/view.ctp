<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php
        	$admin = $this->request->session()->read('is_admin');
                $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
		$manager = ( $this->request->session()->read('selected_project_role') == 'manager' ) ? 1 : 0;
		// the week and year of the last weekly report
		$project_id = $this->request->session()->read('selected_project')['id'];
		/*
                $query = Cake\ORM\TableRegistry::get('Weeklyreports')
				->find()
				->select(['year','week']) 
				->where(['project_id =' => $project_id])
				->toArray();
		if ($query != null) {
			// picking out the week of the last weeklyreport from the results
			$max = max($query);
			$maxYear = $max['year'];
			$maxWeek = $max['week'];
		}
                else {
                        $firstWeeklyReport = true;
                }
		// the week and the year of the workinghour
		$week= $workinghour->date->format('W');
		$year= $workinghour->date->format('Y');
		$firstWeeklyReport = false; 
                 */
		/* The next IF looks kinda complicated, but it means this:
		* IF you are the owning user AND workinghour isn't from previous weeks
		* OR you are an admin or a supervisor */
        	//if ( ( ($workinghour->member->user_id == $this->request->session()->read('Auth.User.id') || $manager)
                //        && ($firstWeeklyReport || (($year >= $maxYear) && ($week > $maxWeek)))) 
		//		|| ($admin || $supervisor) ) { 
                if ( ($workinghour->member->user_id == $this->request->session()->read('Auth.User.id')) || $manager || $supervisor || $admin ) { ?>
			<li><?= $this->Html->link(__('Edit logged time'), ['action' => 'edit', $workinghour->id]) ?> </li>
		<?php } ?>
    </ul>
</nav>
<div class="workinghours view large-7 medium-14 columns content float: left">
    <h3><?= h("View logged task") ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Member') ?></th>
            <td colspan="2"><?= $workinghour->has('member') ? $this->Html->link($workinghour->member->member_name, ['controller' => 'Members', 'action' => 'view', $workinghour->member->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Date') ?></th>
            <td colspan="2"><?= h($workinghour->date->format('d.m.Y')) ?></tr>
        </tr>
        <tr>
            <th><?= __('Duration') ?></th>
            <td colspan="2"><?= $this->Number->format($workinghour->duration) ?></td>
        </tr>
        <tr>
            <th><?= __('Worktype') ?></th>
            <td colspan="2"><?= $workinghour->has('worktype') ? $this->Html->link($workinghour->worktype->description, ['controller' => 'Worktypes', 'action' => 'view', $workinghour->worktype->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Description') ?></th>
            <td colspan="2"><?= h(wordwrap($workinghour->description,35,"\n",TRUE)) ?></td>
        </tr>
    </table>
</div>

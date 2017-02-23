<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <?php
        	// more stuff hidden from devs; the edit-option in /weeklyhour/view/
        	$admin = $this->request->session()->read('is_admin');
            $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            // managers are also able to edit weeklyhours
		// $manager = ( $this->request->session()->read('selected_project_role') == 'manager' ) ? 1 : 0;
		// if($admin || $supervisor || $manager) {    
	        if($admin || $supervisor) {
	      ?>
        		<li><?= $this->Html->link(__('Edit Weeklyhour'), ['action' => 'edit', $weeklyhour->id]) ?> </li>
		<?php
			}
		?>
    </ul>
</nav>
<div class="weeklyhours view large-7 medium-14 columns content float: left">
    <h3><?= h("View weeklyhour") ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Weeklyreport') ?></th>
            <td><?= $weeklyhour->has('weeklyreport') ? $this->Html->link($weeklyhour->weeklyreport->title, ['controller' => 'Weeklyreports', 'action' => 'view', $weeklyhour->weeklyreport->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Member') ?></th>
            <td><?= $weeklyhour->has('member') ? $this->Html->link($weeklyhour->member->member_name, ['controller' => 'Members', 'action' => 'view', $weeklyhour->member->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($weeklyhour->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Duration') ?></th>
            <td><?= $this->Number->format($weeklyhour->duration) ?></td>
        </tr>
    </table>
</div>

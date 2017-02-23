<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="weeklyhours index large-9 medium-18 columns content  float: left">
    <h3><?= __('Weeklyhours') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th colspan="2"><?= $this->Paginator->sort('member_id') ?></th>
                <th><?= __('Week') ?></th>
                <th><?= __('Year') ?></th>
                <th colspan="2"><?= $this->Paginator->sort('weeklyreport_id') ?></th>        
                <th><?= $this->Paginator->sort('duration') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($weeklyhours as $weeklyhour): ?>
            <tr>
                <?php
                    foreach($memberlist as $member){
                        if($weeklyhour->member->id == $member['id']){
                           $weeklyhour->member['member_name'] = $member['member_name'];
                        }
                    }
                ?>
                <td colspan="2"><?= $weeklyhour->has('member') ? $this->Html->link($weeklyhour->member->member_name, ['controller' => 'Members', 'action' => 'view', $weeklyhour->member->id]) : '' ?></td>
                <td><?= h($weeklyhour->weeklyreport->week) ?></td>
                <td><?= h($weeklyhour->weeklyreport->year) ?></td>
                <td colspan="2"><?= $weeklyhour->has('weeklyreport') ? $this->Html->link($weeklyhour->weeklyreport->title, ['controller' => 'Weeklyreports', 'action' => 'view', $weeklyhour->weeklyreport->id]) : '' ?></td>
                <td><?= $this->Number->format($weeklyhour->duration) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $weeklyhour->id]) ?>
                    <?php
			            $admin = $this->request->session()->read('is_admin');
			            $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
			            
			            // FIX: managers can also add edit/delete weeklyhours
        			    // $manager = ( $this->request->session()->read('selected_project_role') == 'manager' ) ? 1 : 0;
        			    // if($admin || $supervisor || $manager) {
			            if($admin || $supervisor) {
			        ?>
                 	   <?= $this->Html->link(__('Edit'), ['action' => 'edit', $weeklyhour->id]) ?>
                 	   <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $weeklyhour->id], ['confirm' => __('Are you sure you want to delete # {0}?', $weeklyhour->id)]) ?>
					<?php } ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>

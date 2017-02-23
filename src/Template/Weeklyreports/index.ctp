<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php
            $admin = $this->request->session()->read('is_admin');
            $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            $manager = ( $this->request->session()->read('selected_project_role') == 'manager' ) ? 1 : 0;

            if($admin || $supervisor || $manager) { ?>
        	<li><?= $this->Html->link(__('New Weeklyreport'), ['action' => 'add']) ?></li>
        <?php } 
        // link eventually be weeklyhours should be deleted
        if($admin) { ?>
            <li><?= $this->Html->link(__('Weeklyhours'), ['controller' => 'Weeklyhours', 'action' => 'index']) ?> </li> 
        <?php } ?>
    </ul>
</nav>
<div class="weeklyreports index large-9 medium-18 columns content float: left">
    <h3><?= __('Weekly reports') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th colspan="2"><?= __('Title') ?></th>
                <th style="text-align: center"><?= __('Week') ?></th>
                <th style="text-align: center"><?= __('Year') ?></th>
                <th><?= $this->Paginator->sort('created_on') ?></th>

                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($weeklyreports as $weeklyreport): ?>
            <tr>
                <td colspan="2"><?= h($weeklyreport->title) ?></td>
                <td style="text-align: center"><?= h($weeklyreport->week) ?></td>
                <td style="text-align: center"><?= h($weeklyreport->year) ?></td>		
                <td><?= h($weeklyreport->created_on->format('d.m.Y')) ?></td>
                <!--
                <td><?php 
                if ($weeklyreport->updated_on != NULL)
                    echo h($weeklyreport->updated_on->format('d.m.Y')); ?></td> -->
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $weeklyreport->id]) ?>
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

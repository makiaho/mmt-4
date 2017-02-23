<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php
            $admin = $this->request->session()->read('is_admin');
            $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            
            /* Req33: Only administrator can add or change/delete metrics.
             * Updated view.ctp and index.ctp */   
            if($admin){
        ?>
            <li><?= $this->Html->link(__('New Metric (admin)'), ['action' => 'addadmin']) ?></li>
        <?php
            } 
            if ($admin || $supervisor) {
        ?> 
	        <li><?= $this->Html->link(__('New Metric'), ['action' => 'add']) ?></li>
        <?php 
        	} // endif
        ?>
    </ul>
</nav>
<div class="metrics index large-8 medium-16 columns content float: left">
    <h3><?= __('Metrics') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('metrictype_id') ?></th>
                <th><?= $this->Paginator->sort('value') ?></th>
                <th><?= $this->Paginator->sort('weeklyreport_id') ?></th>
                <th><?= $this->Paginator->sort('date') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($metrics as $metric): ?>
            <tr>
                <td><?= $metric->has('metrictype') ? $this->Html->link($metric->metrictype->description, ['controller' => 'Metrictypes', 'action' => 'view', $metric->metrictype->id]) : '' ?></td>
                <td><?= $this->Number->format($metric->value) ?></td>
                <td><?= $metric->has('weeklyreport') ? $this->Html->link($metric->weeklyreport->title, ['controller' => 'Weeklyreports', 'action' => 'view', $metric->weeklyreport->id]) : '' ?></td>
                <td><?= h($metric->date->format('d.m.Y')) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $metric->id]) ?>
                    <?php
			            $admin = $this->request->session()->read('is_admin');
			            $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
			            if($admin || $supervisor){
			        ?>
	        
	                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $metric->id]) ?>
	                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $metric->id], ['confirm' => __('Are you sure you want to delete # {0}?', $metric->id)]) ?>
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

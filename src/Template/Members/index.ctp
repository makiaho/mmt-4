<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php
            $admin = $this->request->session()->read('is_admin');
            $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            // FIX: managers can also add new members
            $manager = ( $this->request->session()->read('selected_project_role') == 'manager' ) ? 1 : 0;
            
            if($admin || $supervisor || $manager ) {
        ?>
            <li><?= $this->Html->link(__('New Member'), ['action' => 'add']) ?></li>
        <?php } ?>
    </ul>
</nav>
<div class="members index large-9 medium-18 columns content float: left">
    <h3><?= __('Members') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="image-cell"></th>
                <th colspan="2"><?= __('Name') ?></th>
                <th><?= $this->Paginator->sort('project_role') ?></th>
                <th><?= __('Working hours') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0;?>
            <?php foreach ($members as $member): ?>

            <tr>
                <td class="image-cell">
                    <?= $this->Custom->profileImage($member->user_id); ?>
                </td>
                <td colspan="2"><?= $member->has('user') ? $this->Html->link($member->user->first_name . " ". $member->user->last_name, ['controller' => 'Members', 'action' => 'view', $member->id]) : '' ?></td>  
                <td><?= h($member->project_role) ?></td><?php
                // Get the sum of workinghours for a member who has working hours              
                if (!empty($member->workinghours)) {
                    $query = $member->workinghours;
                    $hours = array();
                    $sum = 0;
                    foreach ($query as $key) {
                        $hours[] = $key->duration;
                        $sum = array_sum($hours);   
                    }
                }
                else {
                    $sum = "";
                }
                $total += $sum;?>

                <td><?= h($sum) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $member->id]) ?>
                    <?php
			$admin = $this->request->session()->read('is_admin');
			$supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
			if($admin || $supervisor){
			        ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $member->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $member->id], ['confirm' => __('Are you sure you want to delete # {0}?', $member->id)]) ?>
                    <?php } ?>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (!empty($member->project_id)) { ?>
            <tr style="border-top: 2px solid black;">
                <td></td>
                <td colspan="2"><b><?= __('Total') ?></b></td>
                <td></td>
                <td><b><?= h($total) ?></b></td>
                <td></td>
            </tr> 
            <?php } ?>
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
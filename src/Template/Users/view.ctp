<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user->id]) ?> </li>
    </ul>
</nav>
<div class="users view large-7 medium-14 columns content float: left">
    <h3><?= h($user->first_name ." ". $user->last_name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
		<!-- redundant info
        <tr>
            <th><?= __('First Name') ?></th>
            <td><?= h($user->first_name) ?></td>
        </tr>
        <tr>
            <th><?= __('Last Name') ?></th>
            <td><?= h($user->last_name) ?></td>
        </tr>
		-->
        <tr>
            <th><?= __('Phone') ?></th>
            <td><?= h($user->phone) ?></td>
        </tr>
        <tr>
            <th><?= __('Role') ?></th>
            <td><?= h($user->role) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Member of') ?></h4>
        <?php if (!empty($user->members)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Project') ?></th>
                <th><?= __('Project role') ?></th>
                <th><?= __('Starting date') ?></th>
                <th><?= __('Ending date') ?></th>
            </tr>
            <?php foreach ($user->members as $members): ?>
            <tr>
                <td><?php 
					$query = Cake\ORM\TableRegistry::get('Projects')
							->find()
							->select(['project_name'])
							->where(['id =' => $members->project_id])
							->toArray();
					echo $query[0]->project_name;
				?></td>
                <td><?= h($members->project_role) ?></td>
                <td><?php 
					if ($members->starting_date != NULL)
						echo h($members->starting_date->format('d.m.Y')); 
				?></td>
                <td><?php 
					if ($members->ending_date != NULL)
						echo h($members->ending_date->format('d.m.Y')); 
				?></td>
				
				<!-- Unable to do these because only members of currently chosen project can be accessed
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Members', 'action' => 'view', $members->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Members', 'action' => 'edit', $members->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Members', 'action' => 'delete', $members->id], ['confirm' => __('Are you sure you want to delete # {0}?', $members->id)]) ?>

                </td>
				-->
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>

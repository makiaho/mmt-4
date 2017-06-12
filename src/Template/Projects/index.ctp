<nav class="large-2 medium-4 columns" id="actions-sidebar">    
    <ul class="side-nav">
        <?php
            $admin = $this->request->session()->read('is_admin');
            $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            
            // Get the number of unread feedback for admin
            $unreadNotes = Cake\ORM\TableRegistry::get('Notes')->find()
					->select()
					->where(['note_read IS' => NULL])
					->toArray();
            // link is visible only if there is unread feedback
            if ($admin && (sizeof($unreadNotes)>0)) { ?>
                <li><b><?= $this->Html->link(__('Unread feedback: ' . count($unreadNotes)), ['controller' => 'Notes', 'action' => 'index']) ?> </b></li>
            <?php } 
            // only admins/supervisors can add new projects
            if($admin || $supervisor) { ?>
                <li><?= $this->Html->link(__('New Project'), ['action' => 'add']) ?></li>
            <?php }
            if ($admin) { ?>
                <li><?= $this->Html->link(__('Manage Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
                <li><?= $this->Html->link(__('Metrictypes'), ['controller' => 'Metrictypes', 'action' => 'index']) ?> </li>
                <li><?= $this->Html->link(__('Worktypes'), ['controller' => 'Worktypes', 'action' => 'index']) ?> </li>
            <?php } ?>
            <li><?= $this->Html->link(__('Public statistics'), ['controller' => 'Projects', 'action' => 'statistics']) ?> </li>
            <li><?= $this->Html->link(__('FAQ'), ['controller' => 'Projects', 'action' => 'faq']) ?> </li>
            <li><?= $this->Html->link(__('About MMT 4.0'), ['controller' => 'Projects', 'action' => 'about'],['class' => 'link-highlight']) ?> </li>
            <?php if ($admin) { ?>
                <li><?= $this->Html->link(__('All feedback'), ['controller' => 'Notes', 'action' => 'index']) ?></li> 
            <?php } ?>
    </ul>
</nav>
<div class="projects index large-9 medium-18 columns content float: left">
    <!-- List of the projects the user is a member of -->
    <?php if($this->request->session()->check('Auth.User')){ ?>       
        <h3><?= __('My projects') ?></h3>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('project_name') ?></th>
                    <th><?= __('Desciption') ?></th>
                    <?php // links to unread weekly reports for supervisors
                    $super = $this->request->session()->read('is_supervisor');
                    if ($admin || $super) { ?>
                        <th colspan="2"><?= __('Unread Weekly Reports') ?></th>
                    <?php } ?>
                   <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <?php if(in_array($project->id, $this->request->session()->read('project_memberof_list'))){ ?>
                        <tr>    
                            <td><?= $project->has('project_name') ? $this->Html->link($project->project_name, ['action' => 'view', $project->id]) : '' ?></td>
                            <td><?= h($project->description) ?></td>
                            <?php 
                            // Links to unread weeklyreports are visible to supervisors
                            // admin can only see the column (no links)
                            // code is the same as in Statistics.ctp
                            if ($admin || $super || $supervisor) {
                                
                                $userid = $this->request->session()->read('Auth.User.id');
                                $query = Cake\ORM\TableRegistry::get('Weeklyreports')
					->find()
					->select()
					->where(['project_id =' => $project['id']])
					->toArray(); ?> 
                            <td colspan="2"> 
                                <?php foreach ($query as $key) {
                                    $reportId = $key->id;
                                 
                                    $newreps = Cake\ORM\TableRegistry::get('Newreports')->find()
					->select()
					->where(['user_id =' => $userid, 'weeklyreport_id =' => $reportId])
					->toArray();
                                    if ( sizeof($newreps) > 0 ) { ?>                          
                                        <?= $this->Html->link(__('Week ' . $key->week), ['controller' => 'Weeklyreports', 'action' => 'view', $reportId]) ?>    
                                    <?php }   
                                }?> 
                            </td>
                            <?php } ?>
                            <td class="actions">
                                <?= $this->Html->link(__('Select'), ['action' => 'view', $project->id]) ?>
                            </td> 	   
                        </tr>
                    <?php } ?>
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
    <?php } ?>
    
    <!-- List of the public projects-->
    <div>
        <h3><?= __('Public projects') ?></h3>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('project_name') ?></th>
                    <th><?= $this->Paginator->sort('description') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <?php if($project->is_public){ ?>
                        <tr>
                            <td><?= $project->has('project_name') ? $this->Html->link($project->project_name, ['action' => 'view', $project->id]) : '' ?></td>
                            <td><?= h($project->description) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('Select'), ['action' => 'view', $project->id]) ?>
                            </td>
                        </tr>
                    <?php } ?>
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
</div>



<?php if (!$mobileOptional) { ?>
<script> 
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    window.location = '<?= $this->Url->build(['controller' => 'Mobile', 'action' => 'index']) ?>'; 
}
</script>
<?php } ?>
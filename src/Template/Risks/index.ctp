<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php
            $admin = $this->request->session()->read('is_admin');
            $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
            $manager = ( $this->request->session()->read('selected_project_role') == 'manager' ) ? 1 : 0;
            $developer = ( $this->request->session()->read('selected_project_role') == 'developer' ) ? 1 : 0;
            // link not visible to supervisors and clients
            if ($admin || $manager) {
            ?>
            <li><?= $this->Html->link(__('Add new risk'), ['action' => 'add']) ?></li>
            <?php 
            } 
            // link not visible to devs and clients
            if($admin || $supervisor || $manager) {
            ?>
        <?php } ?>
    </ul>
</nav>
<div class="workinghours index large-9 medium-18 columns content float: left">
    <h3><?= __('Project Risks') ?></h3>
    <?php // the code for the menu is the same as in adddev.ctp 
    //echo $this->Form->input('member_id', ['options' => $members, 'label' => 'Show hours for', 'empty' => '']) . $this->Form->button(__('Submit'));
    ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:130px;"><?= __('Risk') ?></th>
                <th style="width:75px;"><?= __('Impact') ?></th>
                <th style="width:60px;"><?= __('Probability') ?></th>
                <th style="width:70px;" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($risks as $risk): ?>
            <tr>
                <td ><?= $risk->description ?></td>  
                <td><?= $types[$risk->impact] ?></td>
                <td><?= $types[$risk->probability] ?></td>
                <td class="actions">
                    <?php
                    $admin = $this->request->session()->read('is_admin');
                    $supervisor = ( $this->request->session()->read('selected_project_role') == 'supervisor' ) ? 1 : 0;
                    $manager = ( $this->request->session()->read('selected_project_role') == 'manager' ) ? 1 : 0;

                    if (($manager || $supervisor ||  $admin)
                            && $editable[$risk->id]) { ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $risk->id]) ?><br/>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $risk->id], ['confirm' => __('Are you sure you want to delete # {0}?', $risk->id)]) ?> 
                    <?php } ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit profile'), ['action' => 'editprofile']) ?></li>
    </ul>
</nav>
<div class="users form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Change password') ?></legend>
            <?php
            echo $this->Form->input('password', ['label' => 'New password', 'value' => '', 'id' => 'key', 'empty']);
            echo $this->Form->input('checkPassword', array('label' => 'Retype the new password', 'value' => '', 'required' => true, 'type' => 'password', 'empty'));

            echo $this->Form->button(__('Submit'));
            
            ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

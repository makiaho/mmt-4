<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="worktypes form large-9 medium-18 columns content float: left">
    <?= $this->Form->create($worktype) ?>
    <fieldset>
        <legend><?= __('Add Worktype') ?></legend>
        <?php
            echo $this->Form->input('description');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>

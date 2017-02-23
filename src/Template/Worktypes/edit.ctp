<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $worktype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $worktype->id)]
            )
        ?></li>
    </ul>
</nav>
<div class="worktypes form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($worktype) ?>
    <fieldset>
        <legend><?= __('Edit Worktype') ?></legend>
        <?php
            echo $this->Form->input('description');
			echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

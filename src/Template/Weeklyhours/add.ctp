<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
    </ul>
</nav>
<div class="weeklyhours form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($weeklyhour) ?>
    <fieldset>
        <legend><?= __('Add Weeklyhour') ?></legend>
        <?php
            echo $this->Form->input('weeklyreport_id', ['options' => $weeklyreports]);
            echo $this->Form->input('member_id', ['options' => $members]);
            echo $this->Form->input('duration');
			echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

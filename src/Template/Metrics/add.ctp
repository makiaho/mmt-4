<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="metrics form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($metric) ?>
    <fieldset>
        <legend><?= __('Add Metric') ?></legend>
        <?php
            echo $this->Form->input('metrictype_id', ['options' => $metrictypes]);
            echo $this->Form->input('date');
            echo $this->Form->input('value', array('style' => 'width: 30%;'));
			echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

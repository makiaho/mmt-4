<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="projects form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($project) ?>
    <fieldset>
        <legend><?= __('Add Project') ?></legend>
        <?php
            echo $this->Form->input('project_name');
            echo $this->Form->input('description');
            echo $this->Form->input('is_public', array("checked" => "checked", 'label' => "This project is public"));
			echo $this->Form->button(__('Submit'));
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>

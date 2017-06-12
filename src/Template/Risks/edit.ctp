<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php if($editable): ?>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $risk->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $risk->id)]
            )
        ?></li>
        <?php endif; ?>
    </ul>
</nav>
<div class="workinghours form large-8 medium-16 columns content float: left">
    <?php if($editable): ?>
    <?= $this->Form->create($risk) ?>
    <fieldset>
        <legend><?= __('Edit risk') ?></legend>
        
        <?php  
            echo $this->Form->input('description');
            echo $this->Form->input('impact', ['options' => $types, 'empty' => ' ', 'required' => true]); 
            echo $this->Form->input('probability', ['options' => $types, 'empty' => ' ', 'required' => true]);
            

         
            $project_id = $this->request->session()->read('selected_project')['id'];
          
	    echo $this->Form->button(__('Submit'));
        ?>    
    </fieldset>
    <?= $this->Form->end() ?>
    <?php else: ?>
    
    <p>This risk is already contained in a weekly report, and thus can't be edited.</p>
    
    <?php endif; ?>
</div>
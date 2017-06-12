<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<div class="workinghours form large-8 medium-16 columns content float: left">
    <?= $this->Form->create($risk) ?>
    <fieldset>
        <legend><?= __('Add new risk') ?></legend>
        
        <?php  
            echo $this->Form->input('description');
            echo $this->Form->input('impact', ['options' => $types, 'empty' => ' ', 'required' => true]); 
            echo $this->Form->input('probability', ['options' => $types, 'empty' => ' ', 'required' => true]);
            

         
            $project_id = $this->request->session()->read('selected_project')['id'];
          
	    echo $this->Form->button(__('Submit'));
        ?>    
    </fieldset>
    <?= $this->Form->end() ?>
</div>

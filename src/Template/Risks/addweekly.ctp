<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav"></ul>
</nav>
<!-- This is the third page in the weeklyreport form.
-->

<div class="metrics form large-6 medium-12 columns content float: left">
    <?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Add Risks, Page 3/4') ?></legend>
        <?php if(!empty($risks)): ?>
        <table>
            <tr>
                <th>Risk</th>
                <th>Impact</th>
                <th>Probability</th>
            </tr>
        <?php foreach($risks as $risk): ?>
            <tr>
                <td><?= $risk->description ?></td>
                <td><?= $this->Form->input('', ['value' => $current_risks[$risk->id]['impact'], 'name' => 'impact-'.$risk->id, 'options' => $types]);  ?></td>
                <td><?= $this->Form->input('', ['value' => $current_risks[$risk->id]['probability'], 'name' => 'prob-'.$risk->id, 'options' => $types]);  ?></td>
            </tr>
            
 
        
        <?php endforeach; ?>
        </table>
        <?php else: ?>
        <p>This project has no registered risk. Please proceed to next page.</p>
        <?php endif; ?>

        
        
    	<div class="report-nav">
        <?= $this->Html->link('Previous page', ['name' => 'submit', 'value'=>'previous', 'controller' => 'Metrics', 'action' => 'addmultiple'],['class' => 'link-button']); ?>
        <?= $this->Form->button('Next page', ['name' => 'submit', 'value' => 'next']); ?>
        </div>
    </fieldset>
   
    <?= $this->Form->end() ?>
</div>



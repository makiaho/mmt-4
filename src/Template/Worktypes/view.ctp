<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Worktype'), ['action' => 'edit', $worktype->id]) ?> </li>
    </ul>
</nav>
<div class="worktypes view large-9 medium-18 columns content float: left ">
    <h3><?= h($worktype->description) ?></h3>
    <div class="related">
        <h4><?= __('Related Workinghours') ?></h4>
        <?php if (!empty($worktype->workinghours)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Member Id') ?></th>
                <th colspan="3"><?= __('Description') ?></th>
                <th><?= __('Duration') ?></th>
            </tr>
            <?php foreach ($worktype->workinghours as $workinghours): ?>
            <tr>
                <td><?= h($workinghours->id) ?></td>
                <td><?= h($workinghours->member_id) ?></td>
                <td colspan="3" style="font-family:monospace;"><?= h(wordwrap($workinghours->description,28,"\n",TRUE)) ?></td>
                <td><?= h($workinghours->duration) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>

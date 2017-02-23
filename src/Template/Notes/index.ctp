<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Back'), ['controller' => 'Projects', 'action' => 'index']) ?></li>
</ul>
</nav>

<div class="notes index large-9 medium-18 columns content float: left">
    <h3><?= __('Feedback') ?></h3>
    
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('note_read') ?></th>
                <th><?= $this->Paginator->sort('created_on') ?></th>
                <th><?= $this->Paginator->sort('project_role') ?></th>
                <th colspan="3"><?= __('Preview') ?></th>
                <th><?= __('Wants a reply?') ?></th>
                <th class="actions"><?= __('Actions') ?></th>

            </tr>
        </thead>
        <tbody>
             <?php foreach ($notes as $note): ?>
                <tr>
                    <?php 
                    // feedback is read or not
                    if ($note->note_read == NULL || $note->note_read == 0) {
                        $read = "unread";
                    } 
                    else {
                        $read = "read";
                    }
                    // the user wants a reply
                    if ($note->contact_user == 1) {
                        $reply = "Yes";
                    } 
                    else {
                        $reply = "No";
                    }  
                    $preview = substr($note->content, 0, 100);?>
                    <td><?= h($read) ?></td>
                    <td><?= h($note->created_on->format('d.m.Y')) ?></td>
                    <td><?= h($note->project_role) ?></td>
                    <td colspan="3" style="font-family:monospace;"><?= h(wordwrap($preview,25,"\n",TRUE)) ?></td>
                    <td><?= h($reply) ?></td>
                    <td class="Actions"><?= $this->Html->link(__('View'), ['action' => 'view', $note->id]) ?></td>
                </tr>
                <?php endforeach; ?>
        </tbody>
    </table>
</div>
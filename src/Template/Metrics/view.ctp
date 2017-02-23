<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php
           $admin = $this->request->session()->read('is_admin');
           /* Req33: Only administrator can add or change/delete metrics.
           * Updated view.ctp and index.ctp */ 
           if ($admin) {
        ?>
           <li><?= $this->Html->link(__('Edit Metric'), ['action' => 'edit', $metric->id]) ?> </li>
        <?php
           }
         ?>
    </ul>
</nav>
<div class="metrics view large-7 medium-14 columns content float: left">
    <h3><?= h($metric->metrictype->description) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Weeklyreport') ?></th>
            <td><?= $metric->has('weeklyreport') ? $this->Html->link($metric->weeklyreport->title, ['controller' => 'Meeklyreports', 'action' => 'view', $metric->weeklyreport->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Value') ?></th>
            <td><?= $this->Number->format($metric->value) ?></td>
        </tr>
        <tr>
            <th><?= __('Date') ?></th>
            <td><?= h($metric->date) ?></tr>
        </tr>
    </table>
</div>

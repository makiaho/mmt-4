<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Main Slack Page'), ['controller' => 'Slack', 'action' => 'index']) ?> </li>
    </ul>
</nav>
<div class="workinghours form large-8 medium-16 columns content float: left">
    <p>Slack integration allows you to automatically send slack messages for your reports and comments.</p>
    <p>Once you connect your project to a slack channel with a webhook, weekly report notifications and comments will be sent to that slack channel.</p>
</div>

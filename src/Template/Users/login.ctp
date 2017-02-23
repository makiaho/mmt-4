<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li style="color:purple;"><b>Updated on 8.10.2016</b></li>
    <li>Weekly report form (page 2/3) is revamped</li>
    </ul>
</nav>
<div class="users form large-8 medium-16 columns content float: left">
    <h1>Login</h1>
    <?= $this->Form->create() ?>
    <?= $this->Form->input('email') ?>
    <?= $this->Form->input('password') ?>
    <?= $this->Form->button('Login') ?>
    <?= $this->Form->end() ?>
</div>
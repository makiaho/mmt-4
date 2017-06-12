<nav class="large-2 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit profile'), ['action' => 'editprofile']) ?></li>
    </ul>
</nav>
<div class="users form large-8 medium-16 columns content float: left">
    <?= $this->Form->create(null,['type' => 'file']) ?>
    <fieldset>
        <legend><?= __('Upload Photo') ?></legend>       
        <table class="cell-align-top">
            <tr>
                <td>
                    Current Photo:
                </td>
                <td class="portrait">
                    <?= $this->Custom->profileImage($this->request->session()->read('Auth.User')['id']); ?>
                </td>
            </tr>
            <tr>
                <td>
                    Upload File:
                </td>
                <td>
                    <?= $this->Form->input('image', ['label' => '', 'type' => 'file', 'accept' => 'image/*', 'class' => 'preview']); ?>
                </td>
            </tr>
        </table>
        <?= $this->Form->button(__('Submit'),['name' => 'action', 'value' => 'upload']); ?>     
        <?php
        
        if($this->Custom->hasImage($this->request->session()->read('Auth.User')['id'])){
            
            echo $this->Form->button(__('Delete'),['name' => 'action', 'value' => 'delete', 'class' => 'confirm']);
            
        }
        
        ?>
    </fieldset>
    <?= $this->Form->end() ?>
</div>
<script>
    $('button.confirm').click(function(){
        
        if(!confirm('Are you sure you want to delete your image?')){
            
            return false;
            
        }
        
        
    });

</script>

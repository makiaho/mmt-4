<?php 
$this->assign('title','Members');

$total = 0;
?>
<div class="item-list">
<?php foreach ($members as $member): ?>
    <div class="item">
        <h2><?= $member->user->first_name . " ". $member->user->last_name ?></h2>
        <div>
    <?= $member->project_role ?>
        </div>
        <div class="text-right hours">
    <?php
    
    $sum = 0;
    
    $query = $member->workinghours;
    
    foreach ($query as $key) {
        $hours[] = $key->duration;
        $sum += $key->duration;  
    }
    
    $total += $sum;
         
    if($sum == 0){
        echo "";
    }else{
        echo '<div class="number">'.number_format($sum).'</div>';
        echo '<div class="text">'.(number_format($sum) > 1 ? 'hours' : 'hour').'</div>';
    }
            
    ?>
        </div>
</div>
<?php endforeach; ?>
</div>
<div class="total-hours">
    <?= 'Total of '.number_format($total).' hours' ?>
</div>


<?php $statuses = array(0=>'Open',1=>'Contacted',2=>'Qualified',3=>'Unqualified',4=>'Future Lead',5=>'Hot Lead',6=>'Converted',8=>'Interested',7=>'Not Interested'); ?>

<div class="form">
    <h2>New Lead</h2>

    <?php
    echo $this->Form->create('Lead',array('action'=>'add'));
    echo $this->Form->input('name');
    echo $this->Form->input('contact_name');
    echo $this->Form->input('phone');
    echo $this->Form->input('address');
    echo $this->Form->input('email');
    echo $this->Form->input('website');
    echo $this->Form->input('search_term');
    echo $this->Form->input('categories');
    echo $this->Form->input('salesman_id',array('value'=>0));
    echo $this->Form->input('status',array('options'=>$statuses));
    echo $this->Form->end(__d('cake', 'Submit'));
    ?>
</div>
<div class="actions">
    <h3><?php echo __d('cake', 'Actions'); ?></h3>
    <ul>
        <li><a href="javascript:void(0);" onclick="window.history.back();">Back</a></li>
    </ul>
</div>

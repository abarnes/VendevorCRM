<div class="form">
    <h2>Edit Reminder</h2>

    <?php
    echo $this->Form->create('Reminder',array('action'=>'edit'));
    echo $this->Form->input('salesman_id');
    echo $this->Form->input('date');
    echo $this->Form->input('status',array('options'=>array(0=>'Incomplete',1=>'Complete')));
    echo $this->Form->input('notes');
    echo $this->Form->input('id',array('type'=>'hidden'));
    echo $this->Form->end(__d('cake', 'Submit'));
    ?>
</div>
<div class="actions">
    <h3><?php echo __d('cake', 'Actions'); ?></h3>
    <ul>
        <li><a href="javascript:void(0);" onclick="window.history.back();">Back</a></li>
    </ul>
</div>
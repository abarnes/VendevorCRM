<div class="form">
    <h2>Edit Email Campaign</h2>

    <?php
    echo $this->Form->create('EmailCampaign',array('action'=>'edit'));
    echo $this->Form->input('name');
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

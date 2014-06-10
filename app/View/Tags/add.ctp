<div class="form">
    <h2>New Tag</h2>

    <?php
    echo $this->Form->create('Tag',array('action'=>'add'));
    echo $this->Form->input('name');
    echo $this->Form->end(__d('cake', 'Submit'));
    ?>
</div>
<div class="actions">
    <h3><?php echo __d('cake', 'Actions'); ?></h3>
    <ul>
        <li><a href="javascript:void(0);" onclick="window.history.back();">Back</a></li>
    </ul>
</div>

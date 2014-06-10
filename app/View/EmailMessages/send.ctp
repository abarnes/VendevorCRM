<div class="form">
    <h2>Send Message</h2>

    <?php
    echo $this->Form->create('EmailMessage',array('action'=>'send/'.$lead['Lead']['id']));
    echo $this->Form->input('campaign',array('options'=>$campaigns,'value'=>0));
    echo $this->Form->input('from');
    echo $this->Form->input('to',array('value'=>$lead['Lead']['email']));
    echo $this->Form->input('cc');
    echo $this->Form->input('bcc');
    echo $this->Form->input('subject');
    echo $this->Form->input('text',array('type'=>'textarea'));
    echo $this->Form->input('html',array('type'=>'textarea'));
    echo $this->Form->end(__d('cake', 'Submit'));
    ?>
</div>
<div class="actions">
    <h3><?php echo __d('cake', 'Actions'); ?></h3>
    <ul>
        <li><a href="javascript:void(0);" onclick="window.history.back();">Back</a></li>
    </ul>
</div>

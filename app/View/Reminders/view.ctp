<div class="view">
<h2><?php echo __d('cake', 'View Reminder'); ?></h2>
	<dl>
        <dt>ID</dt>
        <dd><?php echo $reminder['Reminder']['id']; ?></dd>

        <dt>Salesman</dt>
        <dd><?php echo empty($reminder['Salesman']) ? '-':$reminder['Salesman']['name']; ?></dd>

        <dt>Date</dt>
        <dd><?php echo date('m/d/Y g:i a',strtotime($reminder['Reminder']['date'])); ?></dd>

        <dt>Status</dt>
        <dd><?php echo $reminder['Reminder']['status']==1 ? 'Complete':'Incomplete'; ?></dd>

        <dt>Type</dt>
        <dd><?php echo $reminder['Reminder']['type']==1 ? 'Automatically Created Reminder':'Manually Created Reminder'; ?></dd>

        <dt>Lead Name</dt>
        <dd><?php echo $this->Html->link($reminder['Lead']['name'],array('controller'=>'leads','action'=>'view',$reminder['Lead']['id'])); ?></dd>

        <dt>Lead Email</dt>
        <dd><?php echo $reminder['Lead']['email']=='' ? '-':$this->Html->link($reminder['Lead']['email'],array('controller'=>'email_messages','action'=>'send',$reminder['Lead']['id'])); ?></dd>

        <dt>Lead Phone</dt>
        <dd><?php echo $reminder['Lead']['phone']=='' ? '-':$reminder['Lead']['phone']; ?></dd>

        <dt>Notes</dt>
        <dd><?php echo $reminder['Reminder']['notes']=='' ? '-':$reminder['Reminder']['notes']; ?></dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __d('cake', 'Actions'); ?></h3>
	<ul>
        <li>
            <a href="javascript:void(0);" onclick="window.history.back();">Back</a>
        </li>

<?php
	echo "\t\t<li>";
	echo $this->Html->link(__d('cake', 'Edit'), array('action' => 'edit', $reminder['Reminder']['id']));
	echo " </li>\n";

	echo "\t\t<li>";
	echo $this->Form->postLink(__d('cake', 'Delete'), array('action' => 'delete', $reminder['Reminder']['id']), array(), __d('cake', 'Are you sure you want to delete # %s?', $reminder['Reminder']['id']));
	echo " </li>\n";
?>
	</ul>
</div>
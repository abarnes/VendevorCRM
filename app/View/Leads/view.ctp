<?php $statuses = array(0=>'Open',1=>'Contacted',2=>'Qualified',3=>'Unqualified',4=>'Future Lead',5=>'Hot Lead',6=>'Converted',8=>'Interested',7=>'Not Interested'); ?>
<?php $e_statuses = array(0=>'unopened',1=>'opened',2=>'failed',3=>'complained',4=>'rejected',5=>'clicked',6=>'unsubscribed'); ?>

<div class="view">
<h2><?php echo __d('cake', 'View Lead'); ?></h2>
	<dl>
        <dt>ID</dt>
        <dd><?php echo $lead['Lead']['id']; ?></dd>

        <dt>Name</dt>
        <dd><?php echo $lead['Lead']['name']; ?></dd>

        <dt>Contact Name</dt>
        <dd><?php echo $lead['Lead']['contact_name']=='' ? '-':$lead['Lead']['contact_name']; ?></dd>

        <dt>Status</dt>
        <dd><?php echo $statuses[$lead['Lead']['status']]; ?></dd>

        <dt>Salesman</dt>
        <dd><?php echo $lead['Lead']['salesman_id']==0 ? '(unclaimed)':$lead['Salesman']['name']; ?></dd>

        <dt>Address</dt>
        <dd><?php echo $lead['Lead']['address']=='' ? '-':'<a href="http://maps.google.com/?q='.urlencode(trim($lead['Lead']['address'])).'" target="_blank">'.$lead['Lead']['address'].'</a>'; ?></dd>

        <dt>Phone</dt>
        <dd><?php echo $lead['Lead']['phone']=='' ? '-':$lead['Lead']['phone']; ?></dd>

        <dt>Email</dt>
        <dd><?php echo $lead['Lead']['email']=='' ? '-':$lead['Lead']['email']; ?></dd>

        <dt>Website</dt>
        <dd><?php echo $lead['Lead']['website']=='' ? '-':$lead['Lead']['website']; ?></dd>

        <dt>Search Term</dt>
        <dd><?php echo $lead['Lead']['search_term']=='' ? '-':$lead['Lead']['search_term']; ?></dd>

        <dt>Categories</dt>
        <dd><?php echo $lead['Lead']['categories']; ?></dd>
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
	echo $this->Html->link(__d('cake', 'Edit'), array('action' => 'edit', $lead['Lead']['id']));
	echo " </li>\n";

	echo "\t\t<li>";
	echo $this->Form->postLink(__d('cake', 'Delete'), array('action' => 'delete', $lead['Lead']['id']), array(), __d('cake', 'Are you sure you want to delete # %s?', $lead['Lead']['id']));
	echo " </li>\n";

	echo "\t\t<li>";
	echo $this->Html->link(__d('cake', 'New Lead'), array('action' => 'add'));
	echo " </li>\n";
?>
	</ul>
</div>

<div class="related">
    <br>
    <h2>Emails</h2>

    <?php if (!empty($lead['EmailMessage'])) { ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Campaign</th>
                <th>Status</th>
                <th></th>
            </tr>
            <?php foreach ($lead['EmailMessage'] as $e) { ?>
                <tr>
                    <td><?php echo date('m/d/Y h:i',strtotime($e['created'])); ?></td>
                    <td><?php if (isset($e['EmailCampaign']) && !empty($e['EmailCampaign'])) echo $this->Html->link($e['EmailCampaign']['name'],array('controller'=>'email_campaigns','action'=>'view',$e['EmailCampaign']['id'])); ?></td>
                    <td><?php echo $e_statuses[$e['status']]; ?></td>
                    <td><?php echo $this->Html->link('More Info',array('controller'=>'email_messages','action'=>'view',$e['id'])); ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No emails sent.</p>
    <?php } ?>

    <?php echo $this->Html->link('Send Email',array('controller'=>'email_messages','action'=>'send/'.$lead['Lead']['id'])); ?>
    <br><br>
</div>

<hr>

<div class="related">
    <br>
    <h2>Reminders</h2>

    <?php if (!empty($lead['Reminder'])) { ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th>Date & Time</th>
                <th>Notes</th>
                <th><?php echo __d('cake', 'Actions'); ?></th>
            </tr>
            <?php
            foreach ($lead['Reminder'] as $l) {
                echo '<tr>';
                if (strtotime($l['date'])<strtotime("now")) {
                    echo '<td style="color:red;">';
                } else {
                    echo '<td>';
                }
                echo date('m/d/Y g:i a',strtotime($l['date'])).'</td>';
                echo '<td>'.$l['notes'].'</td>';
                echo '<td class="actions">';
                echo $this->Html->link(__d('cake', 'View'), array('controller'=>'reminders','action' => 'view', $l['id']));
                echo $this->Html->link(__d('cake', 'Mark Complete'), array('controller'=>'reminders','action' => 'complete', $l['id'],$lead['Lead']['id']));
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </table>
    <?php } else { ?>
        <p>No reminders.</p>
    <?php } ?>

    <h4>New Reminder</h4>
    <?php
    echo $this->Form->create('Reminder',array('action'=>'add/'.$lead['Lead']['id']));
    echo $this->Form->input('salesman_id');
    echo $this->Form->input('date');
    echo $this->Form->input('notes');
    echo $this->Form->end('Submit');
    ?>

</div>

<hr>

<div class="related">

    <br>
    <h2 class="contacted">Comments</h2>

    <?php foreach ($lead['Comment'] as $c) { ?>
        <div class="contacted">
            <p><?php echo nl2br($c['notes']); ?></p>
            <h5><?php echo date("m/d/Y g:i A",strtotime($c['created'])).' - '.$salesmen[$c['salesman_id']]; ?></h5>
            <a href="javascript:void(0)" id="d<?php echo $c['id']; ?>" class="deletecontact">delete</a>
            <br><br><hr><br>
        </div>
    <?php } ?>

    <div class="contact-record">
        <h4>New Comment</h4>
        <?php echo $this->Form->create('Comment',array('action'=>'add')); ?>
        <?php echo $this->Form->input('notes',array('type'=>'textarea','label'=>false,'div'=>false)); ?>
        <?php echo $this->Form->end(array('class'=>'contactsubmit','value'=>'Submit','style'=>'float:right;')); ?>
    </div>

</div>

<script type="text/javascript">
    $('.contactsubmit').click(function(){
        event.preventDefault();
        var formData = $('#CommentAddForm').serialize();
        $.ajax({
            type: 'POST',
            url: '/crm/comments/add/<?php echo $lead['Lead']['id']; ?>',
            data: formData,
            dataType: 'json',
            success: function(data){
                $('.contacted:last').after(
                    '<div class="contacted"><p>'+$('#CommentNotes').val()+'</p><h5>Just Now - You</h5><a href="javascript:void(0)" id="d'+data+'" class="deletecontact">delete</a><br><br><hr><br></div>'
                );
                $('#CommentNotes').val('');
            },
            error: function(message){
                console.log(message.responseText);
                alert(message.responseText);
            }
        });
        return false;
    });

    $('.deletecontact').on('click',function(e){
        var id = e.target.id.substring(1);
        if (window.confirm("Are you sure you want to remove this comment?")) {
            $.ajax({
                type: 'POST',
                url: '/crm/comments/delete/'+id,
                success: function(data){
                    $("#"+e.target.id).closest('div').remove();
                },
                error: function(message){
                    alert(message.responseText);
                }
            });
        }
        return false;
    });
</script>
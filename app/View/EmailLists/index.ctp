<div class="index">
    <h2>Email Lists</h2>

    <p>Set up groups of leads to email at the same time.</p>

    <table cellpadding="0" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Total Leads</th>
            <th>Created</th>
            <th><?php echo __d('cake', 'Actions'); ?></th>
        </tr>
            <?php
            foreach ($email_lists as $l) {
                echo '<tr>';
                    echo '<td>'.$l['EmailList']['id'].'</td>';
                    echo '<td>'.$l['EmailList']['name'].'</td>';
                    echo '<td>'.count($l['Lead']).'</td>';
                    echo '<td>'.date('m/d/Y h:i',strtotime($l['EmailList']['created'])).'</td>';

                    echo '<td class="actions">';
                    echo $this->Html->link(__d('cake', 'Email'), array('controller'=>'email_messages','action' => 'send_to_list', $l['EmailList']['id']));
                    echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $l['EmailList']['id']));
                    echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('action' => 'edit', $l['EmailList']['id']));
                    echo ' ' . $this->Form->postLink(
                            __d('cake', 'Delete'),
                            array('action' => 'delete', $l['EmailList']['id']),
                            array(),
                            __d('cake', 'Are you sure you want to delete this email campaign?')
                        );
                    echo '</td>';
                echo '</tr>';
            }
        ?>
    </table>
    <p><?php
        echo $this->Paginator->counter(array(
            'format' => __d('cake', 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
        ));
        ?></p>
    <div class="paging">
        <?php
        echo $this->Paginator->prev('< ' . __d('cake', 'previous'), array(), null, array('class' => 'prev disabled'));
        echo $this->Paginator->numbers(array('separator' => ''));
        echo $this->Paginator->next(__d('cake', 'next') .' >', array(), null, array('class' => 'next disabled'));
        ?>
    </div>
</div>
<div class="actions">
    <h3><?php echo __d('cake', 'Actions'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__d('cake', 'New Email List'), array('action' => 'add')); ?></li>
    </ul>

</div>
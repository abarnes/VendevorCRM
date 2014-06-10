<div class="index">
    <h2>Reminders</h2>

    <table cellpadding="0" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Salesman</th>
            <th>Lead</th>
            <th>Notes</th>
            <th>Date & Time</th>
            <th><?php echo __d('cake', 'Actions'); ?></th>
        </tr>
            <?php
            foreach ($reminders as $l) {
                echo '<tr>';
                    echo '<td>'.$l['Reminder']['id'].'</td>';
                    echo '<td>'.$l['Salesman']['name'].'</td>';
                    echo '<td>'.$this->Html->link($l['Lead']['name'],array('controller'=>'leads','action'=>'view',$l['Reminder']['lead_id'])).'</td>';
                    echo '<td>'.$l['Reminder']['notes'].'</td>';
                    if (strtotime($l['Reminder']['date'])<strtotime("now")) {
                        echo '<td style="color:red;">';
                    } else {
                        echo '<td>';
                    }
                    echo date('m/d/Y g:i a',strtotime($l['Reminder']['date'])).'</td>';

                    echo '<td class="actions">';
                    echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $l['Reminder']['id']));
                    echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('action' => 'edit', $l['Reminder']['id']));
                    echo ' ' . $this->Form->postLink(
                            __d('cake', 'Delete'),
                            array('action' => 'delete', $l['Reminder']['id']),
                            array(),
                            __d('cake', 'Are you sure you want to delete this reminder?')
                        );
                    echo $this->Html->link(__d('cake', 'Mark Complete'), array('action' => 'complete', $l['Reminder']['id']));
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
        <!--<li><?php echo $this->Html->link(__d('cake', 'New Reminder'), array('action' => 'add')); ?></li>-->
    </ul>

</div>
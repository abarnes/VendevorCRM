<div class="index">
    <h2>Email Campaigns</h2>

    <p>Group the emails you send into campaigns to split test open rates and other statistics.</p>

    <table cellpadding="0" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Salesman</th>
            <th>Name</th>
            <th>Created</th>
            <th><?php echo __d('cake', 'Actions'); ?></th>
        </tr>
            <?php
            foreach ($email_campaigns as $l) {
                echo '<tr>';
                    echo '<td>'.$l['EmailCampaign']['id'].'</td>';
                    echo '<td>'.$l['EmailCampaign']['name'].'</td>';
                    echo '<td>'.$this->Html->link($l['Salesman']['name'],array('controller'=>'salesmen','action'=>'view',$l['Salesman']['id'])).'</td>';
                    echo '<td>'.date('m/d/Y h:i',strtotime($l['EmailCampaign']['created'])).'</td>';

                    echo '<td class="actions">';
                    echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $l['EmailCampaign']['id']));
                    echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('action' => 'edit', $l['EmailCampaign']['id']));
                    echo ' ' . $this->Form->postLink(
                            __d('cake', 'Delete'),
                            array('action' => 'delete', $l['EmailCampaign']['id']),
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
        <li><?php echo $this->Html->link(__d('cake', 'New Email Campaign'), array('action' => 'add')); ?></li>
    </ul>
</div>
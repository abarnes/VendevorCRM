<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Scaffolds
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<?php $statuses = array(0=>'Open',1=>'Contacted',2=>'Qualified',3=>'Unqualified',4=>'Future Lead',5=>'Hot Lead',6=>'Converted',8=>'Interested',7=>'Not Interested'); ?>

<div class="index">
    <h2>Leads</h2>

    <?php echo $this->Form->create('Lead',array('action'=>'index/'.$type.'/'.$subtype.'/'.$search_term)); ?>
    <?php echo $this->Form->input('query',array('value'=>$query)); ?>
    <?php echo $this->Form->end(array('label'=>'Search','style'=>'float:left;')); ?>

    <?php echo $this->Form->end(array('label'=>'Export to Excel','style'=>'float:right;','id'=>'export','div'=>false)); ?>

    <table cellpadding="0" cellspacing="0">
        <tr>
            <th><?php echo $this->Paginator->sort('id', 'ID'); ?></th>
            <th><?php echo $this->Paginator->sort('name', 'Name'); ?></th>
            <th><?php echo $this->Paginator->sort('phone', 'Phone'); ?></th>
            <th><?php echo $this->Paginator->sort('email', 'Email'); ?></th>
            <th><?php echo $this->Paginator->sort('website', 'Website'); ?></th>
            <th><?php echo $this->Paginator->sort('Salesman.name', 'Salesman'); ?></th>
            <th><?php echo $this->Paginator->sort('status', 'Status'); ?></th>
            <th><?php echo $this->Paginator->sort('search_term', 'Search Term'); ?></th>
            <th><?php echo __d('cake', 'Actions'); ?></th>
        </tr>
            <?php
            foreach ($leads as $l) {
                echo '<tr>';
                    echo '<td>'.$l['Lead']['id'].'</td>';
                    echo '<td>'.$l['Lead']['name'].'</td>';
                    echo '<td>'.$l['Lead']['phone'].'</td>';
                    echo '<td>'.$this->Html->link($l['Lead']['email'],array('controller'=>'email_messages','action'=>'send',$l['Lead']['id'])).'</td>';
                    echo '<td>'.$this->Html->link($l['Lead']['website'],array('action'=>'view_website/'.$l['Lead']['id']),array('target'=>'_blank')).'</td>';
                    $sales = $l['Salesman']['name']=='' ? '(unclaimed)<br><a href="javascript:void(0);" onclick="claim('.$l['Lead']['id'].')">claim</a>':$l['Salesman']['name'];
                    echo '<td>'.$sales.'</td>';
                    echo '<td>'.$statuses[$l['Lead']['status']].'</td>';
                    echo '<td>'.$l['Lead']['search_term'].'</td>';

                    echo '<td class="actions">';
                    echo $this->Html->link(__d('cake', 'View'), array('action' => 'view', $l['Lead']['id']));
                    echo ' ' . $this->Html->link(__d('cake', 'Edit'), array('action' => 'edit', $l['Lead']['id']));
                    echo ' ' . $this->Form->postLink(
                            __d('cake', 'Delete'),
                            array('action' => 'delete', $l['Lead']['id']),
                            array(),
                            __d('cake', 'Are you sure you want to delete # %s?', $l['Lead']['id'])
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
        <li><?php echo $this->Html->link(__d('cake', 'New Lead'), array('action' => 'add')); ?></li>
    </ul>

    <br>
    <hr>
    <br>

    <h3>Search Terms</h3>
    <ul>
        <li><?php echo $this->Html->link('(all)',array('action'=>'index')); ?></li>
        <?php foreach($search_terms as $s) { ?>
            <?php foreach($s['leads'] as $n) { ?>
                <li><?php if ($n!='') echo $this->Html->link($n,array('action'=>'index/'.$type.'/'.$subtype,$n)); ?></li>
            <?php } ?>
        <?php } ?>
    </ul>

    <br>

    <h3>Filters</h3>
    <select id="filter">
        <option value="0/0">All Leads</option>
        <option value="1/0">Open Leads</option>
        <option value="1/1">Contacted Leads</option>
        <option value="1/2">Qualified Leads</option>
        <option value="1/3">Unqualified Leads</option>
        <option value="1/4">Future Leads</option>
        <option value="1/5">Hot Leads</option>
        <option value="1/6">Converted Leads</option>
        <option value="1/8">Interested Leads</option>
        <option value="2/0">Leads with Email</option>
        <option value="2/1">Leads w/o Email</option>
        <option value="2/2">Leads with Website</option>
        <option value="2/3">Leads w/o Website</option>
        <option value="2/4">Leads w/ Website & Email</option>
        <option value="2/5">Open Leads w/ Email</option>
        <option value="2/6">Open Leads w/ Email & Web</option>
        <option value="3/0">My Leads</option>
        <option value="4/0">Unclaimed Leads</option>
    </select>

</div>

<script type="text/javascript">
    $(document).ready(function(){
       $('#filter').val('<?php echo $type; ?>/<?php echo $subtype; ?>');
    });

    $('#export').click(function(e){
        e.preventDefault();
        var val = $('#LeadQuery').val();
        $('<form action="/crm/leads/export_xls/<?php echo $search_term; ?>" method="POST"><input name="data[Lead][query]" value="'+val+'" type="text" id="LeadQueryx"></form>').appendTo('body').submit().remove();
        return false;
    });

    $('#filter').change(function(){
        var val = $('#filter').val();
        var q = $('#LeadQuery').val();
        if (q!='') {
            $('<form action="/crm/leads/index/'+val+'/<?php echo urlencode($search_term); ?>" method="POST"><input name="data[Lead][query]" value="'+q+'" type="text" id="LeadQueryx"></form>').appendTo('body').submit().remove();
        } else {
            window.location.href = "/crm/leads/index/"+val+'/<?php echo urlencode($search_term); ?>';
        }
    });

    function del(id) {
        $.ajax({url:"/leads/delete/"+id,success:function(){
            window.location.reload();
        }});
        return false;
    }

    function claim(id) {
        $.ajax({url:"/leads/claim/"+id,success:function(){
            window.location.reload();
        }});
        return false;
    }
</script>
<?php $statuses = array(0=>'Open',1=>'Contacted',2=>'Qualified',3=>'Unqualified',4=>'Future Lead',5=>'Hot Lead',6=>'Converted',8=>'Interested',7=>'Not Interested'); ?>
<div class="form">
    <h2>New Email List</h2>

    <p>Select the leads you want to include in your new list.</p>

    <?php
    echo $this->Form->create('EmailList',array('action'=>'select/true'));
    echo $this->Form->input('name',array('label'=>'List Name'));
    ?>
    <h3>Total Selected: <span id="update">250</span></h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Website</th>
            <th>Status</th>
        </tr>
        <?php
        $count = 0;
        foreach ($leads as $l) {
            $count++;
            echo '<tr>';
            $v = $count<=250;
            echo '<td>'.$this->Form->input('l'.$l['Lead']['id'],array('type'=>'checkbox','div'=>false,'label'=>false,'checked'=>$v)).' '.$l['Lead']['id'].'</td>';
            echo '<td>'.$l['Lead']['name'].'</td>';
            echo '<td><a href="mailto:'.$l['Lead']['email'].'">'.$l['Lead']['email'].'</td>';
            echo '<td>'.$this->Html->link($l['Lead']['website'],array('action'=>'view_website/'.$l['Lead']['id']),array('target'=>'_blank')).'</td>';
            echo '<td>'.$statuses[$l['Lead']['status']].'</td>';
            echo '</tr>';
        } ?>
    </table>

    <?php
    echo $this->Form->end(__d('cake', 'Submit'));
    ?>
</div>
<div class="actions">
    <h3><?php echo __d('cake', 'Actions'); ?></h3>
    <ul>
        <li><a href="javascript:void(0);" onclick="window.history.back();">Back</a></li>
    </ul>
</div>

<script type="text/javascript">
    $('input').change(function(){
        count = $(':checkbox:checked').length;
        $('#update').text(count);
    });
</script>

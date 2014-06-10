<?php $statuses = array(0=>'Open',1=>'Contacted',2=>'Qualified',3=>'Unqualified',4=>'Future Lead',5=>'Hot Lead',6=>'Converted',7=>'Not Interested'); ?>

<div class="form">
    <h2>Edit Tag</h2>

    <?php
    echo $this->Form->create('Tag',array('action'=>'edit'));
    echo $this->Form->input('name');
    ?>
    <h3>Total Selected: <span id="update"><?php echo count($this->request->data['Lead']); ?></span></h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Website</th>
            <th>Status</th>
        </tr>
        <?php foreach ($this->request->data['Lead'] as $l) {
            echo '<tr>';
            echo '<td>'.$this->Form->input('l'.$l['id'],array('type'=>'checkbox','div'=>false,'label'=>false,'checked'=>true)).' '.$l['id'].'</td>';
            echo '<td>'.$l['name'].'</td>';
            echo '<td><a href="mailto:'.$l['email'].'">'.$l['email'].'</td>';
            echo '<td>'.$this->Html->link($l['website'],array('action'=>'view_website/'.$l['id']),array('target'=>'_blank')).'</td>';
            echo '<td>'.$statuses[$l['status']].'</td>';
            echo '</tr>';
        } ?>
    </table>
    <?php
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

<script type="text/javascript">
    $('input').change(function(){
        count = $(':checkbox:checked').length;
        $('#update').text(count);
    });
</script>
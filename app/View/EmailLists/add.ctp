<div class="form">
    <h2>New Email List</h2>

    <p>Narrow down the leads you want to add to your list.  You can select individual leads on the next page.</p>

    <?php
    $options = array('0/0'=>'All Leads','1/0'=>'Open Leads','1/1'=>'Contacted Leads','1/2'=>'Qualified Leads','1/3'=>'Unqualified Leads','1/4'=>'Future Leads','1/5'=>'Hot Leads','1/6'=>'Converted Leads','1/8'=>'Interested Leads','2/2'=>'Leads with Websites','2/3'=>'Leads w/o website','3/0'=>'My Leads','4/0'=>'Unclaimed Leads');

    echo $this->Form->create('EmailList',array('action'=>'select'));
    echo $this->Form->input('search_term',array('options'=>$search_terms));
    echo $this->Form->input('filter',array('options'=>$options));
    echo $this->Form->end(__d('cake', 'Submit'));
    ?>
</div>
<div class="actions">
    <h3><?php echo __d('cake', 'Actions'); ?></h3>
    <ul>
        <li><a href="javascript:void(0);" onclick="window.history.back();">Back</a></li>
    </ul>
</div>

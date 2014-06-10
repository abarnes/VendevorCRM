<div class="form">
    <h2>Send Message</h2>

    <?php
    $options = array('0/0'=>'All Leads','1/0'=>'Open Leads','1/1'=>'Contacted Leads','1/2'=>'Qualified Leads','1/3'=>'Unqualified Leads','1/4'=>'Future Leads','1/5'=>'Hot Leads','1/6'=>'Converted Leads','2/2'=>'Leads with Websites','2/3'=>'Leads w/o website','3/0'=>'My Leads','4/0'=>'Unclaimed Leads');

    echo $this->Form->create('EmailMessage',array('action'=>'send_to_list/'.$list));
    echo $this->Form->input('campaign',array('options'=>$campaigns,'value'=>0));
    echo $this->Form->input('from');
    echo $this->Form->input('subject');
    ?>

    <h4>Replacement Rules</h4>
    <p>
        Use two colons (::) in the tag followed by quotes to specify alternate text, if the personal information is not available.<br>This code generates either "Dear (business name)," or "Dear New Business," depending on info available:<br>
        <code>Dear #name::"New Business"#</code>,
    </p>
    <ul>
        <li>Business Name: <code>#name#</code> or <code>#name::"(alternate text)"#</code></li>
        <li>Website: <code>#website#</code> or <code>#website::"(alternate text)"#</code></li>
        <li>Email: <code>#email#</code> or <code>#email::"(alternate text)"#</code></li>
        <li>Phone: <code>#phone#</code> or <code>#phone::"(alternate text)"#</code></li>
        <li>Address: <code>#address#</code> or <code>#address::"(alternate text)"#</code></li>
    </ul>

    <br>

    <p>
        You can show or hide blocks of content by preceding the tag with a "?" or "!" and adding the toggled content in brackets {}.<br>
        A ? displays a block if the information is available.  A ! displays it if the info is not available.<br>
        Example: show website sentence only if the website is known:<br>
        <code>?#website{ We noticed your website doesn't have a shopping cart yet. }#</code><br>
    </p>

    <?php
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

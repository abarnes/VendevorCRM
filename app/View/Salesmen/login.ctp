<div class="form">
    <h2>Login</h2>

    <?php
    echo $this->Form->create(null,array('url' => array('controller' => 'salesmen', 'action' => 'login')));
    echo $this->Form->input('email');
    echo $this->Form->input('password');
    echo $this->Form->end(__d('cake', 'Submit'));
    ?>
</div>
<div class="actions">
    <h3>Login to the CRM</h3>
</div>

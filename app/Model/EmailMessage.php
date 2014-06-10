<?php
class EmailMessage extends AppModel {
    public $name = 'EmailMessage';
    public $belongsTo = array('EmailCampaign','Lead');
    public $hasMany = array('EmailRecord');
}
?>

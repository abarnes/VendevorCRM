<?php
class EmailCampaign extends AppModel {
    public $name = 'EmailCampaign';
    public $belongsTo = array('Salesman');
    public $hasMany = array('EmailMessage');
}
?>

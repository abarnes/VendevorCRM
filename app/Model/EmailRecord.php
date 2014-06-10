<?php
class EmailRecord extends AppModel {
    public $name = 'EmailRecord';
    public $belongsTo = array('EmailMessage');
}
?>

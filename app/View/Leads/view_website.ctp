<?php $statuses = array(0=>'Open',1=>'Contacted',2=>'Qualified',3=>'Unqualified',4=>'Future',5=>'Hot',6=>'Converted',7=>'Not Interested'); ?>
<style>
    body {
        margin:0;
        overflow: hidden;
        font-family: 'lucida grande',verdana,helvetica,arial,sans-serif;
        font-size: 90%;
    }
    div.viewer-header {
        width:100%;
        background-color:#222;
        height:65px;
    }
    div.viewer-header img.logo {
        float:left;
        height:31px;
        margin-top:17px;
        margin-left:10px;
        border:none;
    }
    div.viewer-header a {
        text-decoration: none;
        border:none;
    }
    iframe {
        width:100%;
        height:100%;
        border:0;
        overflow: scroll;
    }
    .viewer-left {
        padding-top:22px;
        width:640px;
        float:left;
    }
    .viewer-middle {
        width:50%;
        width:calc(100% - 660px);
        float:left;
        text-align: left;
    }
    .viewer-middle h1{
        font-weight: 300;
        color:#ffffff;
        margin:6px 0 3px 0;
        font-size: 22px;
        line-height:26px;
        height:26px;
        overflow: hidden;
        width:auto;
        display: inline-block;
        text-align: left;
    }
    .viewer-middle h2 {
        color:#fafafa;
        margin:0;
        font-size:14px;
        line-height: 15px;;
        font-weight: 200;
        height:20px;
        overflow: hidden;
        width:auto;
        display: inline-block;
        text-align: left;
    }
    .viewer-right {
        position:absolute;
        width:100px;
        line-height:25px;
        padding-top:5px;
        padding-right:5px;
        top:0;
        right:0;
    }
    .viewer-right div {
        float:right;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        margin-left:6px;
        width:25px;
        height:25px;
        margin-top:20px;
    }
    .viewer-right a {
        color:rgb(98,98,98);
        font-weight:200;
        font-size: 14px;
        width:45px;
        float:right;
        clear:right;
    }
    .close:hover {
        color:#ffffff;
    }
    .vendevor-link {
        color:#ffffff;
    }
    .vendevor-link:hover {
        color:#999999;
    }
    .statusbtn {
        margin: 0px 6px;
        padding: 2px 5px;
        font-weight: normal;
        padding: 4px 8px;
        background: #dcdcdc;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#fefefe), to(#dcdcdc));
        background-image: -webkit-linear-gradient(top, #fefefe, #dcdcdc);
        background-image: -moz-linear-gradient(top, #fefefe, #dcdcdc);
        background-image: -ms-linear-gradient(top, #fefefe, #dcdcdc);
        background-image: -o-linear-gradient(top, #fefefe, #dcdcdc);
        background-image: linear-gradient(top, #fefefe, #dcdcdc);
        color: #333;
        border: 1px solid #bbb;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        text-decoration: none;
        text-shadow: #fff 0px 1px 0px;
        min-width: 0;
        -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), 0px 1px 1px rgba(0, 0, 0, 0.2);
        -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), 0px 1px 1px rgba(0, 0, 0, 0.2);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), 0px 1px 1px rgba(0, 0, 0, 0.2);
        -webkit-user-select: none;
        user-select: none;
        font-size:92%;
    }
    .tags {
        clear:both;
        width:100%;
        background-color:#222;
    }
    div.checkbox {
        float:left;
        color:#fff;
        font-size:13px;
        font-weight:200;
        margin-right:5px;
    }
</style>

<div class="viewer-header">
    <div class="viewer-left">
        <?php foreach ($statuses as $k=>$s) {
            if ($lead['Lead']['status']!=$k) echo '<a href="javascript:void(0);" class="statusbtn" onclick="change_status('.$k.');">'.$s.'</a>';
        } ?>
    </div>

    <div class="viewer-middle">
        <div style="float:left;">
            <h1><?php echo $lead['Lead']['name']; ?> - <span style="font-weight:100;color:#9a9a9a;"><?php echo $statuses[$lead['Lead']['status']]; ?></span></h1>
            <br>
            <h2><?php echo $lead['Lead']['search_term']; ?> - <?php echo $lead['Lead']['website']; ?></h2>
        </div>

        <div class="viewer-right">
            <a class="close" href="javascipt:void(0);" onclick="window.close();">Close</a>
            <?php echo $this->Html->link('Email',array('controller'=>'email_messages','action'=>'send',$lead['Lead']['id'])); ?>
        </div>
    </div>

    <div class="tags">
        <?php
        $current_tags = array();
        foreach($lead['Tag'] as $t) {
            $current_tags[$t['id']] = $t['id'];
        }
        foreach($tags as $k=>$t) {
            $check = array_key_exists($k,$current_tags) ? "checked":false;
            echo $this->Form->input('t'.$k,array('label'=>$t,'type'=>'checkbox','checked'=>$check));
        } ?>
        <br style="clear:both;">
    </div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script type="text/javascript">
    window.onunload = refreshParent;
    function refreshParent() {
        window.opener.location.reload();
    }

    function change_status(status) {
        $.ajax({url:"/crm/leads/change_status/<?php echo $lead['Lead']['id']; ?>/"+status,success:function(){
            window.opener.location.reload();
            window.close();
        }});
        return false;
    }

    $('input[type="checkbox"').change(function(){
        var label = $('label[for="' + $(this).prop('id') + '"]');
        label.css('color','#999999');
        if ($(this).prop('checked')) {
            var val = "1";
            var alt = false;
        } else {
            var val = "0";
            var alt = true;
        }
        var tag = $(this).prop('id').substring(1);
        $.ajax({url:"/crm/leads/add_tag/<?php echo $lead['Lead']['id']; ?>/" + tag + "/" + val + "/l",success:function(data){
            if (data!="success") {
                $('#t'+tag).prop('checked',alt);
            }
            label.css('color','#ffffff');
        }});
        return false;
    });
</script>

<iframe src="<?php echo $lead['Lead']['website']; ?>"></iframe>
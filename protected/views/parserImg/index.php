<?php
$this->breadcrumbs=array(
	'Parser Img',
);?>
<h1><?php echo $this->id . '/' . $this->action->id; ?></h1>

<div class="row buttons">
       <?php echo CHtml::ajaxButton ("Собрать информацию",
                              CController::createUrl('parserImg/downloadItem'),
                              array('update' => '#data', 'beforeSend'=>'function(data){$("#data").html(data);}'), array('id'=>'uploadphoto')
);
?>
</div>
<ul id="data"></ul>
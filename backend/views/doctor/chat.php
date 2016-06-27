<?php
/**
 * Created by PhpStorm.
 * User: rexit
 * Date: 6/27/2016
 * Time: 12:47 PM
 */
?>

<!--<script src="--><?//=Yii::getAlias('@backendUrl/')?><!--js/flexible_styles.js"></script>-->
<link rel="stylesheet" href="<?=Yii::getAlias('@backendUrl/')?>css/styles1.css">

<section id="wrap">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Private chat with <span class="opponent"></span></h3>
            <button type="button" id="logout" class="btn tooltip-title" data-toggle="tooltip" data-placement="bottom" title="Exit">
                <span class="glyphicon glyphicon-log-out"></span>
            </button>
        </div>
        <div class="chat panel-body">
            <ul class="chat-user-list list-group"></ul>
            <div class="chat-content">
                <div class="messages"></div>
                <form action="#" class="controls">
                    <div class="input-group">
							<span class="uploader input-group-addon">
								<span class="glyphicon glyphicon-file"></span>
								<input type="file" class="tooltip-title" data-toggle="tooltip" data-placement="right" title="Attach file">
								<div class="attach"></div>
							</span>
                        <input type="text" class="form-control" placeholder="Enter your message here..">
							<span class="input-group-btn">
								<button type="submit" class="sendMessage btn btn-primary">Send</button>
							</span>
                    </div>
                    <div class="file-loading bg-warning">
                        <img src="<?=Yii::getAlias('@backendUrl/')?>img/file-loading.gif" alt="loading">
                        Please wait.. File is loading
                    </div>
                </form>
            </div>
        </div>
    </div><!-- .panel -->
</section><!-- #wrap -->

<section id="loginForm" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">One to One chat<br>
                    <a href="http://quickblox.com" target="_blank"><img src="<?=Yii::getAlias('@backendUrl/')?>img/qb_logo.png" alt="quickblox logo">by QuickBlox</a>
                </h3>
            </div>
            <div class="modal-body">
                <button type="button" value="Quick" class="btn btn-primary btn-lg btn-block">Sign in with Quick</button>
                <button type="button" value="Blox" class="btn btn-success btn-lg btn-block">Sign in with Blox</button>
                <div class="progress progress-striped active">
                    <div class="progress-bar"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>

<script src="<?=Yii::getAlias('@backendUrl/')?>js/libs/quickblox.js"></script>
<script src="<?=Yii::getAlias('@backendUrl/')?>js/libs/quickblox.chat.js"></script>
<script src="<?=Yii::getAlias('@backendUrl/')?>js/libs/jquery.timeago.js"></script>
<script src="<?=Yii::getAlias('@backendUrl/')?>js/libs/jquery.scrollTo-min.js"></script>

<script src="<?=Yii::getAlias('@backendUrl/')?>js/config.js"></script>
<script src="<?=Yii::getAlias('@backendUrl/')?>js/helpers.js"></script>

<script src="<?=Yii::getAlias('@backendUrl/')?>js/chat.js"></script>
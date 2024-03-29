<?php
\backend\assets\ChatAsset::register($this);
?>


<link rel="shortcut icon" href="https://quickblox.com/favicon.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="<?=Yii::getAlias('@backendUrl/')?>js/libs/stickerpipe/css/stickerpipe.min.css">
<link rel="stylesheet" href="<?=Yii::getAlias('@backendUrl/')?>css/style1.css">
<script>
    var project_base_url = '<?=Yii::getAlias('@backendUrl/')?>';
</script>
<div class="container">
    <div id="main_block">

        <div class="panel panel-primary">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="list-header">
                            <h4 class="list-header-title">History</h4>
                        </div>
                        <div class="list-group pre-scrollable nice-scroll" id="dialogs-list">
                            <!-- list of chat dialogs will be here -->
                        </div>
                    </div>
                    <div id="mcs_container" class="col-md-8 nice-scroll">
                        <div class="customScrollBox">
                            <div class="container del-style">
                                <div class="content list-group pre-scrollable nice-scroll" id="messages-list">
                                    <!-- list of chat messages will be here -->
                                </div>
                            </div>
                        </div>
                        <div><img src="images/ajax-loader.gif" class="load-msg"></div>
                        <form class="form-inline" role="form" method="POST" action="" onsubmit="return submit_handler(this)">
                            <div class="input-group">
		                  <span class="input-group-btn input-group-btn_change_load">
			              <input id="load-img" type="file">
			                <button type="button" id="attach_btn" class="btn btn-default" onclick="$('#load-img').click();">
                                <i class="icon-photo"></i>
                            </button>
		                  </span>
		                  <span class="input-group-btn input-group-btn_change_load">
			                <button type="button" id="stickers_btn" class="btn btn-default" onclick="">
                                <i class="icon-sticker"></i>
                            </button>
		                  </span>

		                  <span class="input-group-btn" style="width: 100%;">
			                 <input type="text" class="form-control" id="message_text" placeholder="Enter message">
		                  </span>

		                  <span class="input-group-btn">
			                <button  type="submit" id="send_btn" class="btn btn-default" onclick="clickSendMessage()">Send</button>
		                  </span>
                            </div>
                            <img src="images/ajax-loader.gif" id="progress">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal (login to chat)-->
<div id="loginForm" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Sign In to QuickBlox Chat demo</h3>
            </div>
            <div class="modal-body">
                <button type="button" value="Quick" id="user1" class="btn btn-primary btn-lg btn-block">Sign in with Quick</button>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal (new dialog)-->
<div id="add_new_dialog" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Choose users to create a dialog with</h3>
            </div>
            <div class="modal-body">
                <div class="list-group pre-scrollable for-scroll">
                    <div id="users_list" class="clearfix"></div>
                </div>
                <div class="img-place"><img src="images/ajax-loader.gif" id="load-users"></div>
                <input type="text" class="form-control" id="dlg_name" placeholder="Enter dialog name">
                <button id="add-dialog" type="button" value="Confirm" id="" class="btn btn-success btn-lg btn-block" onclick="createNewDialog()">Create dialog</button>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal (update dialog)-->
<div id="update_dialog" class="modal fade row" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Dialog info</h3>
            </div>
            <div class="modal-body">
                <div class="col-md-12 col-sm-12 col-xs-12 new-info">
                    <h5 class="col-md-12 col-sm-12 col-xs-12">Name:</h5>
                    <input type="text" class="form-control" id="dialog-name-input">
                </div>
                <h5 class="col-md-12 col-sm-12 col-xs-12 push">Add more user (select to add):</h5>
                <div class="list-group pre-scrollable occupants" id="push_usersList">
                    <div id="add_new_occupant" class="clearfix"></div>
                </div>
                <h5 class="col-md-12 col-sm-12 col-xs-12 dialog-type-info"></h5>
                <h5 class="col-md-12 col-sm-12 col-xs-12" id="all_occupants"></h5>
                <button id="update_dialog_button" type="button" value="Confirm" id="" class="btn btn-success btn-ms btn-block"
                        onclick="onDialogUpdate()">Update</button>
                <button id="delete_dialog_button" type="button" value="Confirm" id="for_width" class="btn btn-danger btn-ms btn-block"
                        onclick="onDialogDelete()">Delete dialog</button>
            </div>
        </div>
    </div>
</div>

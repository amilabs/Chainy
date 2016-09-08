<link rel="stylesheet" href="css/add.css">
<div class="container">
    <div style="background:white;margin-top:20px;padding:20px;">
        <?php if(isset($success)): ?>
            <h4 class="text-<?php echo $success ? 'success' : 'danger' ?>"><?php echo $message ?></h4>
            <?php if($success): ?>
                <?php if(isset($chainyJSON)): ?>
                    <textarea id="chainy-data" readonly><?php echo $chainyJSON ?></textarea>
                <?php endif ?>
                <?php if(isset($chainyTransaction)): ?>
                    <textarea id="chainy-tx" readonly><?php echo $chainyTransaction ?></textarea>
                <?php endif ?>
                <?php if(isset($hash)): ?>
                   Transaction: <a href="https://testnet.etherscan.io/tx/<?php echo $hash ?>" target="_blank"><?php echo $hash ?></a>
                <?php endif ?>
            <?php endif ?>
            <div class="text-right">
                <a href="/add" class="btn btn-success btn-lg" onclick="">Back</a>
            </div>
        <?php else: ?>
            <h3>Add new Chainy record</h3>
            <hr>
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#local-filehash" id="firstTab">Local File Hash</a></li>
                <li><a data-toggle="tab" href="#remote-filehash">Remote File Hash</a></li>
                <li><a data-toggle="tab" href="#redirect">Redirect</a></li>
                <li><a data-toggle="tab" href="#text">Text</a></li>
                <li><a data-toggle="tab" href="#data-hash">Hash</a></li>
                <li><a data-toggle="tab" href="#encrypted-text">Encrypted Text</a></li>
            </ul>
            <div class="tab-content">
                <div id="local-filehash" class="tab-pane fade in active">
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="Local file hash">
                        <div class="row">
                            <div id="verifier" class="col-xs-4">
                                <a href="javascript:void(0)" class="store-item">
                                    <div class="store-item-icon"><input type="file" id="select-file" style="display: none;">
                                        <i class="fa fa-cloud-upload themed-color"></i>
                                            <div style="font-size:16px;">Click or drag and drop file here</div>
                                    </div>
                                </a>
                                <div class="form-errors text-danger"></div>
                            </div>
                            <div class="col-xs-8" style="display:none;" id="local-fileinfo">
                                <div class="row">
                                    <div class="col-xs-2 text-right">Filename:</div>
                                    <div class="col-xs-10">
                                        <span id="local-filename"></span>
                                        <input type="hidden" name="filename">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 text-right">Filesize:</div>
                                    <div class="col-xs-10">
                                        <span id="local-filesize"></span>
                                        <input type="hidden" name="filesize">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 text-right">Hash:</div>
                                    <div class="col-xs-10">
                                        <div class="progress" id="local-hash-progress">
                                          <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
                                        </div>
                                        <span id="local-hash"></span>
                                        <input type="hidden" name="hash">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2 text-right">
                                        Descrtiption:
                                    </div>
                                    <div class="col-xs-10">
                                        <textarea name="description" class="check-description"></textarea>
                                        <div class="form-errors text-danger">Description is too big</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="remote-filehash" class="tab-pane fade">
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="File hash">
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                URL:
                            </div>
                            <div class="col-xs-10">
                                <input type="text" name="url" class="trim-on-submit check-url" size="64">
                                <div class="form-errors text-danger"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                Descrtiption:
                            </div>
                            <div class="col-xs-10">
                                <textarea name="description" class="check-description"></textarea>
                                <div class="form-errors text-danger">Description is too big</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="redirect" class="tab-pane fade">
                    <div class="alert alert-info">
                        Please enter a valid URL. Protocol is required (http:// or https://).
                    </div>
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="Redirect">
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                URL:
                            </div>
                            <div class="col-xs-10">
                                <input type="text" name="url" class="trim-on-submit check-url" size="64">
                                <div class="form-errors text-danger"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="text" class="tab-pane fade">
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="Text">
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                Text:
                            </div>
                            <div class="col-xs-10">
                                <textarea name="description" class="check-empty check-description"></textarea>
                                <div class="form-errors text-danger"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="data-hash" class="tab-pane fade">
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="Hash">
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                Data:
                            </div>
                            <div class="col-xs-10">
                                <textarea name="description" class="check-empty check-description"></textarea>
                                <div class="form-errors text-danger"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="encrypted-text" class="tab-pane fade">
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="Encrypted Text">
                        <input type="hidden" name="encrypted">
                        <input type="hidden" name="hash">
                    </form>
                    <div class="row">
                        <div class="col-xs-2 text-right">
                            Text:
                        </div>
                        <div class="col-xs-10">
                            <textarea id="enc-text" class="check-empty check-description"></textarea>
                            <div class="form-errors text-danger"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-2 text-right">
                            Password:
                        </div>
                        <div class="col-xs-10">
                            <input type="password" id="password1">
                            <div class="form-errors text-danger"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-2 text-right">
                            Repeat Password:
                        </div>
                        <div class="col-xs-10">
                            <input type="password" id="password2">
                            <div class="form-errors text-danger"></div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <a class="btn btn-success btn-lg" id="add-btn" onclick="submitAdd(); return false;">ADD</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
var addForm = true;
function submitAdd(){
    $('.trim-on-submit:visible').each(function(){
        this.value = this.value.replace(/^\s+/, '').replace(/\s+$/, '');
    });
    var checked = true;
    $('.check-url:visible').each(function(){
        var regexp = /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)$/;
        if(!regexp.test(this.value)){
            $(this).addClass('has-error');
            $(this).next('.form-errors').text('Invalid URL').show();
            checked = false;
        }
    });
    $('.check-description:visible').each(function(){
        if(this.value && this.value.length > 4500){
            $(this).addClass('has-error');
            $(this).next('.form-errors').text('Text is too long').show();
            checked = false;
        }
    });
    $('.check-empty:visible').each(function(){
        if(!this.value){
            $(this).addClass('has-error');
            $(this).next('.form-errors').text('Required').show();
            checked = false;
        }
    });
    if($('#verifier:visible').length){
        var hash = $('#local-fileinfo input[name=hash]').val();
        if(!hash){
            var fileSelected = $('#local-fileinfo [name=filename]').val();
            $('#verifier a').addClass('has-error');
            $('#verifier .form-errors').text(fileSelected ? 'Hash calculation is not complete, please wait' : 'File is not selected').show();
            checked = false;
        }
    }
    if(checked && $('#password1:visible').length){
        $('#encrypted-text input, #encrypted-text textarea').removeClass('has-error');
        $('#encrypted-text .form-errors').text('');
        var password1 = $('#password1:visible').val();
        var password2 = $('#password2:visible').val();
        if(!password1){
            $('#password1').addClass('has-error');
            $('#password1').next('.form-errors').text('Required').show();
            checked = false;
        }else if(password1 !== password2){
            $('#password1').removeClass('has-error');
            $('#password2').addClass('has-error');
            $('#password2').next('.form-errors').text("Passwords don't match").show();
            checked = false;
        }else{
            var enc = $('#enc-text').val();
            var hash = CryptoJS.SHA256(enc).toString();
            var encrypted = CryptoJS.AES.encrypt(enc, password1).toString();
            $('[name=encrypted]').val(encrypted);
            $('[name=encrypted]').next().val(hash);
        }
    }

    if(checked){
        $('.form-errors').hide();
        $('.add-chainy:visible').submit();
    }
}
function clearLocalFileData(){
    $('#local-hash-progress').hide();
    $('#local-hash-progress .progress-bar').css('width', '0%');
    $('#local-hash-progress .progress-bar').attr('aria-valuenow', 0);
    $('#local-hash-progress .progress-bar').text('');
    $('#local-fileinfo [name=filename]').val('');
    $('#local-fileinfo [name=filesize]').val('');
    $('#local-fileinfo [name=hash]').val('');
    $('#local-filename').text('');
    $('#local-filesize').text('');
    $('#local-fileinfo').hide();
}
</script>

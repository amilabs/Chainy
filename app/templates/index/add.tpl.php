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
                    <a href="https://testnet.etherscan.io/tx/<?php echo $hash ?>" target="_blank"><?php echo $hash ?></a>
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
                <li><a data-toggle="tab" href="#encrypted-text">Encrypted Text</a></li>
            </ul>
            <div class="tab-content">
                <div id="local-filehash" class="tab-pane fade in active">
                    <h2>Comming Soon...</h2>
                </div>
                <div id="remote-filehash" class="tab-pane fade">
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="Filehash">
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                URL:
                            </div>
                            <div class="col-xs-10">
                                <input type="text" name="url" class="trim-on-submit check-url" size="64">
                                <div class="form-errors text-danger">Invalid URL</div>
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
                                <div class="form-errors text-danger">Invalid URL</div>
                            </div>
                        </div>
                    </form>
                    <div class="form-errors text-right text-danger">Invalid URL!</div>
                </div>
                <div id="text" class="tab-pane fade">
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="Text">
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                Text:
                            </div>
                            <div class="col-xs-10">
                                <textarea name="description" class="check-description"></textarea>
                                <div class="form-errors text-danger">Text is too big</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="encrypted-text" class="tab-pane fade">
                    <form class="add-chainy" action="/add" method="POST">
                        <input type="hidden" name="addType" value="Encrypted Text">
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                Text:
                            </div>
                            <div class="col-xs-10">
                                <textarea name="description" class="check-description"></textarea>
                                <div class="form-errors text-danger">Text is too big</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                Password:
                            </div>
                            <div class="col-xs-10">
                                <input type="password" name="password1">
                                <div class="form-errors text-danger">Empty password</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-2 text-right">
                                Repeat Password:
                            </div>
                            <div class="col-xs-10">
                                <input type="password" name="password2">
                                <div class="form-errors text-danger">Passwords don't match</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="text-right">
                    <a class="btn btn-success btn-lg" id="add-btn" onclick="submitAdd(); return false;">ADD</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
function submitAdd(){
    $('.trim-on-submit:visible').each(function(){
        this.value = this.value.replace(/^\s+/, '').replace(/\s+$/, '');
    });
    var checked = true;
    $('.check-url:visible').each(function(){
        var regexp = /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)$/;
        if(!regexp.test(this.value)){
            $(this).addClass('has-error');
            $(this).next('.form-errors').show();
            checked = false;
        }
    });
    $('.check-description:visible').each(function(){
        if(this.value && this.value.length > 4500){
            $(this).addClass('has-error');
            $(this).next('.form-errors').show();
            checked = false;
        }
    });
    if(checked){
        $('.form-errors').hide();
        $('.add-chainy:visible').submit();
    }
}
</script>

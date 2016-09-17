<?php
    $oCfg = \AmiLabs\DevKit\Application::getInstance()->getConfig();
?>
<div class="t408__textwrapper t-width t-width_8">
    <div class="t408__uptitle t-uptitle t-uptitle_md" field="subtitle">
        <br /><br />AEON links + Proof of Existence + Files + Messages
    </div>
    <div class="t408__descr t-descr t-descr_md" field="descr">
        <?php if(isset($success)): ?>
            <?php if($success): ?>
                Succesfully generated!
            <?php else: ?>
                Error occured!
            <?php endif ?>
        <?php else: ?>
            Choose type of data to engrave in Ethereum blockchain
        <?php endif ?>
    </div>
</div>
<div class="t408__blockswrapper">
<link rel="stylesheet" href="css/add.css">
<div class="container">
    <div style="background:#fff;padding:20px;border-radius:16px;opacity:0.9;margin-top:-50px;">
        <?php if(isset($success)): ?>
            <?php if($success): ?>
                <?php if(isset($chainyJSON)): ?>
                    <?php echo $message ?>:
                    <textarea id="chainy-data" readonly><?php echo $chainyJSON ?></textarea>
                <?php endif ?>
                <?php if(isset($chainyTransaction)): ?>
                    <textarea id="chainy-tx" readonly><?php echo $chainyTransaction ?></textarea>
                <?php endif ?>
                <?php if(isset($hash)): ?>
                   Transaction: <a href="https://<?php echo $oCfg->get('testnet', FALSE) ? 'testnet.' : ''; ?>etherscan.io/tx/<?php echo $hash ?>" target="_blank"><?php echo $hash ?></a><br />
                   Shortlink: <span id="shortlink"><i class="fa fa-spinner fa-spin"></i> please wait...</span>
                   <script>
                       var checkTm;
                       var getShort = function(){
                           $.get("getShort/<?php echo $hash ?>", {}, function(data){
                               if(data){
                                   clearInterval(checkTm);
                                   var link = $('<a>');
                                   link.attr('href', data);
                                   link.text(data);
                                   $("#shortlink").empty().append(link);
                               }
                           });
                       }
                       checkTm = setInterval(getShort, 5000);
                    </script>
                <?php endif ?>
                <span id="chainy-contract" class="alert"></span>
            <?php else: ?>
                <?php if($message): ?><h3 class="text-danger">ERROR: <?=$message?></h3><?php endif; ?>
            <?php endif ?>
            <div class="text-right">
                <a href="#" class="btn btn-success btn-lg" onclick="document.location.reload(); return false;">Back</a>
            </div>
        <?php else: ?>
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
                    <div class="alert alert-info text-left">
                        Select a file to calculate its hash (there are no file type or size limitations).
                    </div>
                    <form class="add-chainy" action="" method="POST">
                        <input type="hidden" name="addType" value="Local file hash">
                        <div class="row">
                            <div id="verifier" class="col-xs-12 col-sm-4">
                                <a href="javascript:void(0)" class="store-item">
                                    <div class="store-item-icon"><input type="file" id="select-file" style="display: none;">
                                        <i class="fa fa-cloud-upload themed-color"></i>
                                        <div style="font-size:16px;">Click or drag and drop file here</div>
                                    </div>
                                </a>
                                <div class="form-errors text-danger"></div>
                            </div>
                            <div class="col-xs-12 col-sm-8" style="display:none;" id="local-fileinfo">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-4 col-md-2 col-header">Filename:</div>
                                    <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                                        <span id="local-filename"></span>
                                        <input type="hidden" name="filename">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-4 col-md-2 col-header">Filesize:</div>
                                    <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                                        <span id="local-filesize"></span> bytes
                                        <input type="hidden" name="filesize">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-4 col-md-2 col-header">Hash:</div>
                                    <div class="col-xs-12 col-sm-8 col-md-10 text-left" style="max-width: 80vw; overflow: hidden; text-overflow: ellipsis;">
                                        <div class="progress" id="local-hash-progress">
                                          <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>
                                        </div>
                                        <span id="local-hash"></span>
                                        <input type="hidden" name="hash">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-4 col-md-2 col-header">Descrtiption:</div>
                                    <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                                        <textarea name="description" class="check-description"></textarea>
                                        <div class="form-errors text-danger">Description is too big</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="remote-filehash" class="tab-pane fade">
                    <div class="alert alert-info text-left">
                        Enter a link to a remote file with maximum size of 50 megabytes.
                    </div>
                    <form class="add-chainy" action="" method="POST">
                        <input type="hidden" name="addType" value="File hash">
                        <div class="row">
                            <div class="col-xs-12 col-sm-4 col-md-2 col-header">URL:</div>
                            <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                                <input type="text" name="url" class="trim-on-submit check-url">
                                <div class="form-errors text-danger"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-4 col-md-2 col-header">Descrtiption:</div>
                            <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                                <textarea name="description" class="check-description"></textarea>
                                <div class="form-errors text-danger">Description is too big</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="redirect" class="tab-pane fade">
                    <div class="alert alert-info text-left">
                        Please enter a valid URL. Protocol is required (http:// or https://).
                    </div>
                    <form class="add-chainy" action="" method="POST">
                        <input type="hidden" name="addType" value="Redirect">
                        <div class="row">
                            <div class="col-xs-12 col-sm-4 col-md-2 col-header">URL:</div>
                            <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                                <input type="text" name="url" class="trim-on-submit check-url">
                                <div class="form-errors text-danger"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="text" class="tab-pane fade">
                    <div class="alert alert-info text-left">
                        The text entered below will be stored with its SHA256 hash in the blockchain.<br />
                        Text length is limited to 200 chars due to transaction cost limitations.
                    </div>
                    <form class="add-chainy" action="" method="POST">
                        <input type="hidden" name="addType" value="Text">
                        <div class="row">
                            <div class="col-xs-12 col-sm-4 col-md-2 col-header">Text:</div>
                            <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                                <textarea name="description" class="check-empty check-description"></textarea>
                                <div class="form-errors text-danger"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="data-hash" class="tab-pane fade">
                    <div class="alert alert-info text-left">
                        Only SHA256 hash of the data entered below will be stored in the blockchain.
                    </div>
                    <form class="add-chainy" action="" method="POST">
                        <input type="hidden" name="addType" value="Hash">
                        <div class="row">
                            <div class="col-xs-12 col-sm-4 col-md-2 col-header">Data:</div>
                            <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                                <textarea name="description" class="check-empty check-description"></textarea>
                                <div class="form-errors text-danger"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="encrypted-text" class="tab-pane fade">
                    <div class="alert alert-info text-left">
                        Encrypted text will be stored in blockchain and can be read by only those who have a password.
                    </div>
                    <form class="add-chainy" action="" method="POST">
                        <input type="hidden" name="addType" value="Encrypted Text">
                        <input type="hidden" name="encrypted">
                        <input type="hidden" name="hash">
                    </form>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-2 col-header">Text:</div>
                        <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                            <textarea id="enc-text" class="check-empty check-description"></textarea>
                            <div class="form-errors text-danger"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-2 col-header">Password:</div>
                        <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                            <input type="password" id="password1">
                            <div class="form-errors text-danger"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-2 col-header">Repeat Password:</div>
                        <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                            <input type="password" id="password2">
                            <div class="form-errors text-danger"></div>
                        </div>
                    </div>
                </div>
                <?php
                    if($oCfg->get('captcha', FALSE)):
                ?>
                <div style="padding-left: 15px; margin-top: -15px;">
                    <div class="row">
                        <div class="hidden-xs col-sm-4 col-md-2 col-header">&nbsp;</div>
                        <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                            <input type="checkbox" id="publish">&nbsp;Publish transaction
                        </div>
                    </div>
                    <div class="row" style="min-height: 50px;">
                        <div class="col-xs-12 col-sm-4 col-md-2 col-header">Captcha:</div>
                        <div class="col-xs-12 col-sm-8 col-md-10 text-left">
                            <script src='https://www.google.com/recaptcha/api.js'></script>
                            <script> var hasCaptcha = true; </script>
                            <div class="g-recaptcha" data-sitekey="<?=$oCfg->get('captcha')?>"></div>
                            <div id="captcha-err" class="form-errors text-danger"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="text-right">
                    <a class="btn btn-success btn-lg" id="add-btn" onclick="submitAdd(); return false;">ADD</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
var addForm = true;
var contractAddress = '<?php if(isset($contractAddress)): echo $contractAddress; endif;?>';
var contractABI = [ { "constant": false, "inputs": [ { "name": "json", "type": "string" } ], "name": "addChainyData", "outputs": [], "type": "function" }, { "anonymous": false, "inputs": [ { "indexed": false, "name": "timestamp", "type": "uint256" }, { "indexed": false, "name": "code", "type": "string" } ], "name": "chainyShortLink", "type": "event" }, { "constant": true, "inputs": [ { "name": "_key", "type": "string" } ], "name": "getConfig", "outputs": [ { "name": "_value", "type": "uint256", "value": "0" } ], "type": "function" } ];
$('#chainy-contract').text('');
$('#chainy-contract').removeClass("alert-danger alert-success alert-warning");

var isDapp = function(){
    return (typeof mist !== 'undefined' && typeof web3 !== 'undefined' && contractAddress);
}

function submitAdd(){
    $('#captcha-err').text('');
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
        if(this.value && this.value.length > 200){
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
        if(isDapp()){
            $('.add-chainy:visible').append('<input type="hidden" name="mist" value="1" />');
        }
        $('.form-errors').hide();

        // Check and process captcha
        var capthcaStr = $('#g-recaptcha-response').val();
        if(hasCaptcha && capthcaStr){
            var inp = $('<INPUT name="g-recaptcha-response" type="hidden">');
            inp.val(capthcaStr);
            $('.add-chainy:visible').append(inp);
        }else{
            $('#captcha-err').text('Please check the captcha').show();
            return false;
        }

        if($('#publish:checked').length){
            $('.add-chainy:visible').append($('<INPUT name="publish" type="hidden" value="1">'));
        }

        $('.add-chainy:visible').submit();
    }
}

$(document).ready(function(){
    if(isDapp()){
        $('#publish').parent().hide();
    }
});

function addChainyData(chainyJSON){
    if(isDapp()){
        console.log(contractAddress);
        console.log(contractABI);
        console.log(web3.eth.accounts);
        $('#chainy-contract').addClass("alert-warning");
        $('#chainy-contract').html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i> <b>Please wait..</b>');

        web3.eth.getCode(contractAddress, function(e, r){
            if(!e && r.length > 3){
                var chainyContract = web3.eth.contract(contractABI);
                var chainy = chainyContract.at(contractAddress);

                var chainyShortLinkWatcher = chainy.chainyShortLink();
                chainyShortLinkWatcher.watch(function(error, result){
                    chainyShortLinkWatcher.stopWatching();
                    if(!error && result && result.args && result.args.code){
                        console.log(result.args);
                        var url = result.args.code;
                        $('#chainy-contract').addClass("alert-success");
                        $('#chainy-contract').html('<b>The Chainy short link is <a href="' + url + '">' + url + '</a></b>');
                    }else{
                        $('#chainy-contract').addClass("alert-danger");
                        $('#chainy-contract').html('<b>Error: ' + (error ? error.toString() : 'Unknown error.') + '</b>');
                    }
                });

                // currently use first authorized account
                if(web3.eth.accounts.length > 0){
                    // get fee value
                    var fee = chainy.getConfig('fee', {from: web3.eth.accounts[0]});
                    console.log(fee.toString());
                    // get account balance
                    var balance = web3.eth.getBalance(web3.eth.accounts[0]);
                    console.log(balance.toString());

                    var addChainyDataParams = {from: web3.eth.accounts[0]};
                    if(fee.cmp(0) == 1){
                        if(fee.cmp(balance) == 1){
                            $('#chainy-contract').addClass("alert-danger");
                            $('#chainy-contract').html('<b>Error: Insufficient funds in account.</b>');
                            return;
                        }
                        addChainyDataParams.value = fee.toString();
                    }

                    chainy.addChainyData(chainyJSON, addChainyDataParams);
                }else{
                    $('#chainy-contract').addClass("alert-warning");
                    $('#chainy-contract').html('<b>Please select the account in the upper right corner which you would like to make visible to the Chainy DAPP.</b>');
                }

                /*mist.requestAccount(function(e, address){
                    console.log('Select account');
                    if(e){
                        console.log(e.toString());
                    }else{
                        console.log('Address selected: ' + address);
                        chainy.addChainyData(chainyJSON, {from: address});
                    }
                });*/
            }
        });
    }
}
</script>
<?php if(isset($success)): ?>
    <?php if($success): ?>
        <?php if(isset($chainyJSON)): ?>
            <script>addChainyData('<?php echo $chainyJSON ?>');</script>
        <?php endif ?>
    <?php endif ?>
<?php endif; ?>
</div>

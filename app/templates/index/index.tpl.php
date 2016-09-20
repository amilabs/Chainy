        <link rel="stylesheet" href="css/view.css">
        <!-- Intro -->
        <section class="site-section site-section-light site-section-top">
            <div class="container">
                <h1 class="animation-slideDown"><i class="fa fa-check-square"></i> Blockchain data found</h1>
                <h2 class="date-of-transaction animation-slideUp"><strong>Date of transaction:</strong> <?php if(isset($aTX['date'])): ?><?=$aTX['date']?><?php else: ?>Unknown yet<?php endif; ?></h2>
            </div>
        </section>
        <!-- END Intro -->

        <?php if($aTX["type"] == 'L'): ?>
            <section class="site-content site-section site-slide-content">
                <div class="container">
                    <div class="blue-line"></div>
                    <h2 class="site-heading"><strong>Signed file details</strong></h2>
                    <div class="row visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                        <div class="col-sm-10 col-md-9 site-block">
                            <p>This page contains information about digitally signed file embedded in the Ethereum blockchain. Since transaction is confirmed this file is permanently certified and proven to exist.</p>
                            <div class="grey-line"></div>
                            <p><strong>Hash amount  SHA256</strong></p>
                            <p><span class="long-hash"><?=$aTX['hash']?></span><input type="hidden" id="file-hash" value="<?=$aTX['hash']?>"></p>
                            <?php if(isset($aTX['url'])): ?>
                                <div class="grey-line"></div>
                                <p><strong>Link to the original file is</strong></p>
                                <p><span class="long-hash"><a href="<?=$aTX['url']?>" class="external-link" target="_blank"><?=$aTX['url']?></a></span></p>
                            <?php endif; ?>
                            <div class="grey-line"></div>
                            <p><strong>Transaction date</strong></p>
                            <p><?php if(isset($aTX['date'])): ?><?=$aTX['date']?><?php else: ?>Unknown yet<?php endif; ?></p>
                            <?php if(isset($aTX['tx']) && $aTX['tx']): ?>
                                <div class="grey-line"></div>
                                <p><strong>Transaction id</strong></p>
                                <p><span class="long-hash"><?=$aTX['tx']?></span></p>
                            <?php endif; ?>
                            <?php if(isset($aTX['description']) && $aTX['description']): ?>
                                <div class="grey-line"></div>
                                <p><strong>Description:</strong></p>
                                <p><?=htmlspecialchars($aTX['description']);?></p>
                            <?php endif; ?>
                            <?php
                            /*
                                <p class="promo-content">Look up this transaction on 3rd party services: <a href="http://coinsecrets.org/<?php if($aTX['block']): ?>?to=<?=($aTX['block'] + 1)?>.000000<?php endif; ?>">CoinSecrets</a> and <a href="http://blockchain.info/tx/<?=$aTX['tx']?>">Blockchain.info</a></p>
                             */
                            ?>
                        </div>
                        <div class="col-sm-2 col-md-offset-1 site-block">
                            <a name="verify"></a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="site-content site-section" style="margin-top: 28px;">
                <div class="container">
                    <div class="row store-items">
                        <div id="verifier" class="col-md-8 visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                            <a href="javascript:void(0)" class="store-item">
                                <div class="store-item-info  text-center clearfix">
                                    <span class="store-item-price themed-color">Verify saved file copy</span>
                                    <div>Place your file here to verify it matches the blockchain record.</div>
                                </div>
                                <div class="store-item-icon"><input type="file" id="select-file" style="display: none;">
                                    <i class="fa fa-cloud-upload themed-color"></i>
	                                <div style="font-size:16px;">Click or drag and drop file here</div>
                                </div>
                                <div class="store-item-info clearfix">
                                    <strong>Security note</strong><br>
                                    <span class="text-muted">Your file won't be uploaded.<br>
                                     All SHA256 hash calculations are performed right on your computer.</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4 visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                                <?php if(isset($aTX['url'])): ?>
                                <a href="<?=$aTX['url']?>" target=_blank class="store-item">
                                <?php else: ?>
                                <a class="store-item">
                                <?php endif; ?>
                                <div class="store-item-info  text-center clearfix">
                                    <span class="store-item-price themed-color">Signed file</span>
                                    <div><strong>File name:</strong> <?php if(isset($aTX['filename']) && $aTX['filename']){ echo $aTX['filename']; } elseif(isset($aTX['url'])){ echo substr($aTX['url'], strrpos($aTX['url'], '/') + 1); }?></div>
                                </div>
                                <div class="store-item-icon">
                                    <i class="fa fa-file<?=$aTX['filetype']?'-':''?><?=$aTX['filetype']?>-o themed-color-fire"></i>
                                    <div class="store-item-price themed-color-dark" style="font-size:16px;"><?=$aTX['filesize']?></div>
                                </div>
                                <div class="store-item-info clearfix" style="word-break: break-all">
                                    <?php if(isset($aTX['url'])): ?>
                                    <strong>Link:&nbsp;</strong><?=$aTX['url']?><br>
                                    <?php endif; ?>
                                    <span class="text-muted">This link is saved with the file signature in the same blockchain transaction.</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="site-content site-section">
                <div class="container">
                    <div class="grey-line"></div>
                    <div class="site-block visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                        <h1 class="site-heading"><i class="fa fa-question-circle"></i> <strong>How does it work</strong></h1>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-10 site-block">
                            <p class="remove-margin">Cryptographic digest (or hash amount) has been calculated from this file using SHA256 algorithm and then saved in the blockchain. The original link to this file is also now permanently saved.</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-10 site-block">
                            <div class="grey-line"></div>
                            <p><strong>What does it mean</strong></p>
                            <p class="remove-margin">
                                It means that this confirmed bitcoin transaction contains a file (a document), hash amount of which has been permanently stored inside transaction. It is now impossible to modify or backdate this file based on bitcoin principle (link). If at later date you want to verify this document, and you have a copy of it, you can upload it using form above, calculate its hash amount and compare with the hash amount stored in the blockchain. Matching hash amounts is a 100% probability that the document is authentic.<br>
                                Hash function algorithm of SHA256 has been developed by NSA. More info <a href='http://en.wikipedia.org/wiki/SHA-2' class="external-link" target=_blank>http://en.wikipedia.org/wiki/SHA-2</a>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-10 site-block">
                            <div class="grey-line"></div>
                            <p><strong>How to make sure that file matches the signature </strong></p>
                            <p class="remove-margin">You need to calculate SHA256 hash amount and compare it with the one saved in the blockchain.</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-10 site-block">
                            <div class="grey-line"></div>
                            <p><strong>How to calculate SHA256 hash amount</strong></p>
                            <div class="t419__col t-col t-col_10 ">
                                <div class="t419__numberwrapper">
                                    <div class="t419__number">
                                        <div class="t419__circle" style="width: 40px; height: 40px; border-width: 0px;  background: #b0b0b0;"></div>
                                        <div class="t419__digit t-name t-name_md" field="number" style="font-size: 16px;">1</div>
                                    </div>
                                    <div class="t419__line" style="width: 0px;"></div>
                                </div>
                                <div class="t419__textwrapper" style="">
                                    <div class="t419__descr t-descr t-descr_xs" field="descr">
                                        <p>Upload file into the form on this page. Hash will be calculated inside the browser and never send over internet. Calculated result will be compared with the file previously saved and result will be shown to you.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="t419__col t-col t-col_10 ">
                                <div class="t419__numberwrapper">
                                    <div class="t419__number">
                                        <div class="t419__circle" style="width: 40px; height: 40px; border-width: 0px;  background: #b0b0b0;"></div>
                                        <div class="t419__digit t-name t-name_md" field="number" style="font-size: 16px;">2</div>
                                    </div>
                                    <div class="t419__line" style="width: 0px;"></div>
                                </div>
                                <div class="t419__textwrapper" style="">
                                    <div class="t419__descr t-descr t-descr_xs" field="descr">
                                        <p>If you don't trust our page, feel free to calculate hash amount of your file using independent tools. Just google “calculate sha256 hash”.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="t419__col t-col t-col_10 ">
                                <div class="t419__numberwrapper">
                                    <div class="t419__number">
                                        <div class="t419__circle" style="width: 40px; height: 40px; border-width: 0px;  background: #b0b0b0;"></div>
                                        <div class="t419__digit t-name t-name_md" field="number" style="font-size: 16px;">3</div>
                                    </div>
                                    <div class="t419__line" style="width: 0px;"></div>
                                </div>
                                <div class="t419__textwrapper" style="">
                                    <div class="t419__descr t-descr t-descr_xs" field="descr">
                                        <p>You can also calculate hash by yourself, using open source code.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if(($aTX["type"] == 'T') || ($aTX["type"] == 'H')): ?>
            <section class="site-content site-section site-slide-content">
                <div class="container">
                    <div class="blue-line"></div>
                    <h2 class="site-heading"><strong>Signed text details</strong></h2>
                    <div class="row visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                        <div class="col-sm-10 col-md-9 site-block">
                            <p>This page contains information about digitally signed text embedded in the Ethereum blockchain.</p>
                            <?php if(isset($aTX['description']) && $aTX['description']): ?>
                                <div class="grey-line"></div>
                                <p><strong>Text:</strong></p>
                                <p class="alert alert-heading" style="border:1px solid #999;"><?=htmlspecialchars($aTX['description']);?></p>
                            <?php endif; ?>
                            <div class="grey-line"></div>
                            <p><strong>Hash amount  SHA256</strong></p>
                            <p><span class="long-hash"><?=$aTX['hash']?></span><input type="hidden" id="file-hash" value="<?=$aTX['hash']?>"></p>
                            <div class="grey-line"></div>
                            <p><strong>Transaction date</strong></p>
                            <p><?php if(isset($aTX['date'])): ?><?=$aTX['date']?><?php else: ?>Unknown yet<?php endif; ?></p>
                            <?php if(isset($aTX['tx']) && $aTX['tx']): ?>
                                <div class="grey-line"></div>
                                <p><strong>Transaction id</strong></p>
                                <p><span class="long-hash"><?=$aTX['tx']?></span></p>
                            <?php endif; ?>
                            <?php
                            /*
                                <p class="promo-content">Look up this transaction on 3rd party services: <a href="http://coinsecrets.org/<?php if($aTX['block']): ?>?to=<?=($aTX['block'] + 1)?>.000000<?php endif; ?>">CoinSecrets</a> and <a href="http://blockchain.info/tx/<?=$aTX['tx']?>">Blockchain.info</a></p>
                             */
                            ?>
                        </div>
                    </div>
                    <div class="row visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                        <div class="col-sm-10 col-md-9 site-block text-left">
                            <div class="grey-line"></div>
                            <p><strong>Paste text for verification:</strong></p>
                            <textarea id="checkhash-text" style="width: 100%; height: 100px; color: #000; border-radius: 4px; resize: none; height: 200px;"></textarea>
                        </div>
                        <div class="col-sm-10 col-md-9 site-block text-center">
                            <a id="checkhash" class="btn btn-lg btn-success" style="width: 200px;">Verify</a>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if($aTX["type"] == 'R'): ?>
            <section class="site-content site-section site-slide-content">
                <div class="container">
                    <h2 class="site-heading"><strong>Redirect details</strong></h2>
                    <hr>
                    <div class="row visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                        <div class="col-sm-10 col-md-9 site-block">
                            <p class="promo-content">
                                <?php if(isset($aTX['url'])): ?>
                                <br /><strong>Redirects to</strong>
                                <br /><a href="<?=$aTX['url']?>" target="_blank"><?=$aTX['url']?></a>
                                <?php endif; ?>
                                <br /><strong>Transaction date</strong>
                                <br /> <?php if(isset($aTX['date'])): ?><?=$aTX['date']?><?php else: ?>Unknown yet<?php endif; ?>
                                <?php if(isset($aTX['tx']) && $aTX['tx']): ?>
                                <br /><strong>Transaction id</strong>
                                <br /><span class="long-hash"><?=$aTX['tx']?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
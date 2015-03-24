            <!-- Site Header -->
            <header>
                <div class="container">
                    <!-- Site Logo -->
                    <a href="/" class="site-logo">
                       <i class="gi gi-fishes"></i>  <strong>Chainy</strong>.Link <i class="gi gi-fishes"></i> 
                    </a>
                    <!-- Site Logo -->

                    <!-- Site Navigation -->
                    <nav>
                        <!-- Menu Toggle -->
                        <!-- Toggles menu on small screens -->
                        <a href="javascript:void(0)" class="btn btn-default site-menu-toggle visible-xs visible-sm">
                            <i class="fa fa-bars"></i>
                        </a>
                        <!-- END Menu Toggle -->

                        <!-- Main Menu -->
                        <ul class="site-nav">
                            <!-- Toggles menu on small screens -->
                            <li class="visible-xs visible-sm">
                                <a href="javascript:void(0)" class="site-menu-toggle text-center">
                                    <i class="fa fa-times"></i>
                                </a>
                            </li>
                            <!-- END Menu Toggle -->
                            <li>
                                <a href="http://chainy.info/about">About</a>
                            </li>
                            <li class="active">
                                <a href="verify">Verify</a>
                            </li>
                            <li>
                                <a href="">Sign file</a>
                            </li>
                            <li>
                                <a href="">Eon link</a>
                            </li>
                            <li>
                                <a href="https://github.com/amilabs/chainy">Source code</a>
                            </li>
                            <li>
                                <a href="">API</a>
                            </li>
                            
                        </ul>
                        <!-- END Main Menu -->
                    </nav>
                    <!-- END Site Navigation -->
                </div>
            </header>
            <!-- END Site Header -->

            <!-- Intro -->
            <section class="site-section site-section-light site-section-top themed-background">
                <div class="container">
                    <h1 class="text-center animation-slideDown"><i class="fa fa-check-square"></i> <strong>This file has blockchain signature</strong></h1>
                    <h2 class="h3 text-center animation-slideUp">Date of transaction: <?=$aTX['date']?></h2>
                </div>
            </section>
            <!-- END Intro -->
            
            <section class="site-content site-section site-slide-content">
                <div class="container">
                    <h2 class="site-heading"><strong>Signed file details</strong></h2>
                    <hr>
                    <div class="row visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                        <div class="col-sm-10 col-md-9 site-block">
    					<p class="promo-content">This page contains information about digitally signed file embedded in the Bitcoin blockchain. Since transaction is confirmed this file is permanently certified and proven to exist. 
                            <br><strong>Hash amount  SHA256</strong> <?=$aTX['hash']?> <input type="hidden" id="file-hash" value="<?=$aTX['hash']?>">
                            <br><strong>Link to the original file is</strong><br /><a href="<?=$aTX['link']?>" target="_blank"><?=$aTX['link']?></a>
						<br><strong>Transaction date</strong> <?php if($aTX['block']): ?><?=$aTX['date']?><?php else: ?>Unknown<?php endif; ?>
						<br><strong>Transaction id</strong> <?=$aTX['tx']?>
						</p>
						<p class="promo-content">Look up this transaction on 3rd party services: <a href="http://coinsecrets.org/<?php if($aTX['block']): ?>?to=<?=($aTX['block'] + 1)?>.000000<?php endif; ?>">CoinSecrets</a> and <a href="http://blockchain.info/tx/<?=$aTX['tx']?>">Blockchain.info</a>
						</p>
                            
                        </div>
                        <div class="col-sm-2 col-md-offset-1 site-block">
<!--                            <img src="img/placeholders/screenshots/promo_desktop_right.png" alt="Promo #2" class="img-responsive">
-->
                                                <a name="verify"></a>
                        </div>
                    </div>
                </div>
            </section>
            
            <section class="site-content site-section">
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
                            <a href="<?=$aTX['link']?>" target=_blank class="store-item">
                                <div class="store-item-info  text-center clearfix">
                                    <span class="store-item-price themed-color">Signed file</span>
                                    <div><strong>File name:</strong> <?=$aTX['file_name']?></div>
                                </div>
                                <div class="store-item-icon">
                                    <i class="fa fa-file<?=$aTX['file_type']?'-':''?><?=$aTX['file_type']?>-o themed-color-fire"></i>
                                    <div class="store-item-price themed-color-dark" style="font-size:16px;"><?=$aTX['file_size']?></div>
                                </div>
                                <div class="store-item-info clearfix">
                                    <strong>Link:&nbsp;</strong><?=$aTX['link']?><br>
                                    <span class="text-muted">This link is saved with the file signature in the same blockchain transaction.</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </section>


            <section class="site-content site-section site-section-light themed-background">
                <div class="container">
                    <div class="site-block visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInQuick" data-element-offset="-100">
                        <h1 class="site-heading"><i class="fa fa-question-circle"></i> <strong>How does it work</strong></h1>
                    </div>
                </div>
            </section>

            <section class="site-content site-section">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 col-md-10 site-block">
                            <p class="promo-content remove-margin">Cryptographic digest (or hash amount) has been calculated from this file using SHA256 algorithm and then saved in the blockchain. The original link to this file is also now permanently saved.</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-10 site-block">
                            <h3 class="site-heading"><strong>What does it mean</strong></h3>
                            <p class="remove-margin">It means that this confirmed bitcoin transaction contains a file (a document), hash amount of which has been permanently stored inside transaction. It is now impossible to modify or backdate this file based on bitcoin principle (link). If at later date you want to verify this document, and you have a copy of it, you can upload it using form above, calculate its hash amount and compare with the hash amount stored in the blockchain. Matching hash amounts is a 100% probability that the document is authentic. 
							<br>Hash function algorithm of SHA256 has been developed by NSA. More info <a href='http://en.wikipedia.org/wiki/SHA-2' target=_blank>http://en.wikipedia.org/wiki/SHA-2</a>
							</p>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-12 col-md-10 site-block">
                            <h3 class="site-heading"><strong>How to make sure that file matches the signature </strong></h3>
                            <p class="remove-margin">You need to calculate SHA256 hash amount and compare it with the one saved in the blockchain.</p>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-12 col-md-10 site-block">
                            <h3 class="site-heading"><strong>How to calculate SHA256 hash amount</strong></h3>
                            <p class="remove-margin">
                            <ol>
                            <li>Upload file into the form on this page. Hash will be calculated inside the browser and never send over internet. Calculated result will be compared with the file previously saved and result will be shown to you. </li>
                            <li>If you don't trust our page, feel free to calculate hash amount of your file using independent tools. Just google “calculate sha256 hash”.</li>
                            <li>You can also calculate hash by yourself, using open source code. </li>
							</ol>                            
							</p>
                        </div>
                    </div>
                </div>
            </section>


            <!-- Footer -->
            <footer class="site-footer site-section">
                <div class="container">
                    <!-- Footer Links -->
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <h4 class="footer-heading"><span id="year-copy">2015</span> &copy; <a href="http://amilabs.co/">AmiLabs</a></h4>
                            <ul class="footer-nav list-inline">
                                <li>Crafted with <a href="https://github.com/amilabs/CryptoKit">AmiLabs/CryptoKit</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- END Footer Links -->
                </div>
            </footer>
            <!-- END Footer -->

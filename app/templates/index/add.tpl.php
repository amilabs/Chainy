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
        <?php else: ?>
        <h3>Add new Chainy record</h3>
        <hr>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#local-filehash" id="firstTab">Local File Hash</a></li>
            <li><a data-toggle="tab" href="#remote-filehash">Remote File Hash</a></li>
            <li><a data-toggle="tab" href="#redirect">Redirect</a></li>
            <li><a data-toggle="tab" href="#custom-data">Custom Data</a></li>
        </ul>
        <div class="tab-content">
            <div id="local-filehash" class="tab-pane fade in active">
            </div>
            <div id="remote-filehash" class="tab-pane fade">
                <form class="add-chainy" action="/add" method="POST">
                    <input type="hidden" name="addType" value="filehash">
                    URL: <input type="text" name="url" size="64">
                </form>
            </div>
            <div id="redirect" class="tab-pane fade">
                <form class="add-chainy" action="/add" method="POST">
                    <input type="hidden" name="addType" value="redirect">
                    URL: <input type="text" name="url" size="64">
                </form>
            </div>
            <div id="custom-data" class="tab-pane fade">
            </div>
            <div class="text-right">
                <a class="btn btn-success btn-lg" id="add-btn" onclick="submitAdd(); return false;">ADD</a>
            </div>
        </div>
        <?php endif; ?>

        <!--
        <br />
        <form action="/add" method="POST">
            <input type="hidden" name="addType" value="redirect">
            URL: <input type="text" name="url" size="64">
            <input type="submit" value="ADD REDIRECT TO BLOCKCHAIN">
        </form>
        -->
    </div>
</div>
<script>
    function submitAdd(){
        $('.add-chainy:visible').submit();
    }
</script>

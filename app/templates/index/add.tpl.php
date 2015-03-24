<script>
    
    function getFileInfo(evt){
        if (evt.target.readyState == FileReader.DONE){
            var text = CryptoJS.enc.Latin1.parse(evt.target.result);
            var hash = CryptoJS.SHA256(evt.target.result).toString();
            console.log(hash);
        }
    }
    
    function handleFileLoaded(evt){
        evt.stopPropagation();
        evt.preventDefault();
        var f = evt.target.files[0];
        window.reader = new FileReader();
        reader.onloadend = getFileInfo;
        reader.readAsBinaryString(f);
        console.log(f.name);
        console.log(f.type);
        console.log(f.size);
    }
</script>
<div style="background:white;margin-top:100px;padding:20px;">
    <!-- input type="file" id="sel-file"><br><Br -->
    https://dropbox.com/s/oaib1ejwtpzh7aq/ghost_in_the_shell.jpg<br>
    https://dropbox.com/s/bisyhsix4ap488n/kill_bill.jpg<br>
    <form action="/add" method="POST">
    URL: <input type="text" name="url" size="64"> <input type="submit" value="ADD URL TO BLOCKCHAIN">
</form>

</div>

<script>
    // document.getElementById('sel-file').addEventListener('change', handleFileLoaded, false);
</script>


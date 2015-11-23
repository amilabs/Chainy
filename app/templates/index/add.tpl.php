<div style="background:white;margin-top:100px;padding:20px;">
    <!-- input type="file" id="sel-file"><br><Br -->
    <form action="/add" method="POST">
        <input type="hidden" name="addType" value="filehash">
        URL: <input type="text" name="url" size="64"> 
        <input type="submit" value="ADD FILEHASH TO BLOCKCHAIN">
    </form>
    <br />
    <form action="/add" method="POST">
        <input type="hidden" name="addType" value="redirect">
        URL: <input type="text" name="url" size="64">
        <input type="submit" value="ADD REDIRECT TO BLOCKCHAIN">
    </form>
</div>



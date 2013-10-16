<span class="label label-primary">&nbsp Account&nbsp</span>
<input type="text" id="account" class="form-control" placeholder="Enter your account">
<br />
<span class="label label-primary">Password</span>
<input type="password" id="password" class="form-control" placeholder="Enter your password">
<br />
<input id="login" type="button" class="btn btn-primary" value="Login"/>

<script>
(function(){
    if(opener!=null)
    {
        $("#login").click(function(){
            //測試用,暫時回傳gKey
            opener.key = "<?=$gKey?>";
            if(opener.onLoginSuccess)
                opener.onLoginSuccess();
            window.close(); 
        });
    }
    else
    {
        $("#login").click(function(){
            alert('test');
        });
    }
})()

</script>
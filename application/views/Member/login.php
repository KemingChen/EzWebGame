<span class="label label-primary">&nbsp Account&nbsp</span>
<input type="text" id="account" class="form-control" placeholder="Enter your account">
<br />
<span class="label label-primary">Password</span>
<input type="password" id="password" class="form-control" placeholder="Enter your password">
<br />
<input id="login" type="button" class="btn btn-primary" value="Login"/>

<script>
window.onblur = window.close;
window.onbeforeunload = function(){$.ajax({url: "<?=base_url('/user/cancelLogin/'.$lKey)?>",})};
(function(){
    $("#account").focus();
    if(opener != null && opener.EzWebGame!=null)
    {
        $("#login").click(function(){
            var account = $("#account").val();
            var password = $("#password").val();
            if(account==""||password=="")
            {
                alert("account or password empty");
                return false;
            }
            $.ajax({
        		url: "<?=base_url('/user/login/')?>"+"/<?php echo $lKey?>"+'/'+account+'/'+password,
        	}).done(function(data) {
                window.close();
                data = JSON.parse(data);
                opener.EzWebGame.cKey(data);
        	});
        });
    }
    else
    {
        $("#login").click(function(){
            alert('coming soon');
        });
    }
})()

</script>

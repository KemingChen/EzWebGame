<span class="label label-primary">&nbsp&nbsp&nbsp Name&nbsp&nbsp&nbsp</span>
<input type="text" id="name" class="form-control" placeholder="Enter your name">
<br />
<span class="label label-primary">&nbsp Account&nbsp</span>
<input type="text" id="account" class="form-control" placeholder="Enter your account">
<br />
<span class="label label-primary">Password</span>
<input type="password" id="password" class="form-control" placeholder="Enter your password">
<br />
<input type="button" class="btn btn-primary" value="Sign UP!"/>
<br />
<br />
<script>
$(".header ul li").each(function(){$(this).removeClass('active')});
$(".header ul li:eq(2)").addClass('active');

$("input[value='Sign UP!']").click(function(){
    var name = $("#name").val().trim();
    var account = $("#account").val().trim();
    var password = $("#password").val().trim();
    
    if(name==""||account==""||password=="")
    {
        $('#myModal .modal-title').text('Error');
        $('#myModal .modal-body').html("Please complete game name, account, and password");
        $('#myModal').modal();
    }
    else
    {
        $.ajax({
    		url: '../user/signup'+'/'+name+'/'+account+'/'+password
    	}).done(function(data) {
            $('#myModal .modal-title').text('SignUp Result');
            $('#myModal .modal-body').html(data);
            $('#myModal').modal();
	   });
    }
});
</script>
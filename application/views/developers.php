<input type="button" class="btn btn-primary" value="Register Game"/>
<div id="dataInputDIV">
    <br />
    <form role="form">
        <div class="form-group">
            <label for="exampleInputEmail1">Game Name</label>
            <input id="gameName" type="text" class="form-control" placeholder="Enter your game name">
        </div>
        <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input id="password" type="password" class="form-control" placeholder="Password">
        </div>
        <input type="button" class="btn btn-default" value="Register"/>
        <input type="button" class="btn btn-default" value="Query Key"/>
    </form>
</div>
<br />
<br />
<input type="button" class="btn btn-primary" value="Query GameKey"/>
<br />
<br />
<script type="text/javascript">
console.log('123');
$(".header ul li").each(function(){$(this).removeClass('active')});
$(".header ul li:eq(1)").addClass('active');
$("#dataInputDIV").hide();
$("input[value='Register Game']").on('click',function(){
    if($(dataInputDIV).css('display')=='none')
        $("#dataInputDIV").slideToggle();
    else if($("#gameName").val().trim()!="")
        sendNameAndPasswordTo('./game/create','Register Result');
        
    $("#dataInputDIV input[value='Register']").show();
    $("#dataInputDIV input[value='Query Key']").hide();
});
$("input[value='Query GameKey']").on('click',function(){
    if($(dataInputDIV).css('display')=='none')
        $("#dataInputDIV").slideToggle();
    else if($("#gameName").val().trim()!="")
        sendNameAndPasswordTo('./game/getGameKey','Query Result');
    $("#dataInputDIV input[value='Register']").hide();
    $("#dataInputDIV input[value='Query Key']").show();
});
$("#dataInputDIV input[value='Register']").on('click',function(){
    sendNameAndPasswordTo('./game/create','Register Result');
});
$("#dataInputDIV input[value='Query Key']").on('click',function(){
    sendNameAndPasswordTo('./game/getGameKey','Query Result');
});
function sendNameAndPasswordTo(baseURL,resultModalTitle)
{
    var name = $("#gameName").val().trim();
    var password = $("#password").val().trim();
    if(name==""||password=="")
    {
        $('#myModal .modal-title').text('Error');
        $('#myModal .modal-body').html("Please complete game name and password");
        $('#myModal').modal();
    }
    else
    {
        $.ajax({
    		url: baseURL+'/'+name+'/'+password
    	}).done(function(data) {
    		console.log(data);
            $('#myModal .modal-title').text(resultModalTitle);
            $('#myModal .modal-body').html(data);
            $('#myModal').modal();
	   });
    }
}
</script>
<!DOCTYPE html>  
<html lang="zh-TW">  
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf8">  
<style>  
    div {  
        border: solid 2px #336699;  
        margin: 10px;  
        padding: 5px;  
        border-radius: 5px;  
        vertical-align: top;  
        text-align: center;  
        display: inline-block;  
    }  
    .container {  
        background: #88BBFF;  
        width: 520px;  
    }  
    .msg {  
        background: #99CCFF;  
        border: dotted 1px #4477AA;  
        width: 480px;  
        margin: 0px;  
        text-align: left;  
        font-size: 12px;  
    }  
</style>  
<script>  
function init() {  
    var es = new EventSource('./SSEtest');
    es.onmessage = function (event) {
        var str = '';  
        str += '<li>[' + new Date() + ']: ';  
        str += event.data + '</li>\n';
        document.getElementById('msg1').innerHTML += str;
        console.log(event.data);
        if(event.data=='finish')
        {
            alert('close');
            event.target.close();
        }  
    };
}
</script>  
</head>  
<body>
<a href="./SSETest">SSETest</a>
<button onclick="init()">go</button>
<br />  
<div>
    <div class="container">  
        Returned Messages.  
        <div id="msg1" class="msg"></div>  
    </div>  
</div>  
</body>  
</html>
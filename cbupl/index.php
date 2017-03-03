<?php
header("Access-Control-Allow-Origin:*");
$url  = 'http://'.$_SERVER['HTTP_HOST'];
$file = (isset($_POST["file"])) ? $_POST["file"] : '';
if($file)
{
    $data = base64_decode(str_replace('data:image/png;base64,', '', $file));  //截图得到的只能是png格式图片，所以只要处理png就行了
    $name = 'res/img/default/' . date('Ymd-His') . '.png';
    file_put_contents('../'.$name, $data);
    echo "$url/$name";
    die;
}
?>

<input id="remote" type="hidden" name="img" value="" style="width: 500px;"/>
<div id="area" style="width:500px;height:500px;border:1px solid;" contenteditable></div>
 
<script>
document.querySelector('#area').addEventListener('paste', function(e) {

    //检测有图贴入
    if (e.clipboardData && e.clipboardData.items[0].type.indexOf('image') > -1) {
        var that     = this,
            reader   = new FileReader();
            file     = e.clipboardData.items[0].getAsFile();

        //ajax上传
        reader.onload = function(e) {
            var xhr = new XMLHttpRequest(),
                fd  = new FormData();

            xhr.open('POST', '', true);
            xhr.onload = function () {
                var img = new Image();
                img.src = xhr.responseText;

                document.getElementById("remote").value = img.src;
                document.getElementById("remote").setAttribute('type', 'text');
            };

            //图片base64即显
            fd.append('file', this.result); 
            that.innerHTML = '<img src="'+this.result+'" alt=""/>';
            xhr.send(fd);
        };
        reader.readAsDataURL(file);
    }
}, false);
</script>

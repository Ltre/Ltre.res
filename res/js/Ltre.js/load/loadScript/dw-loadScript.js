/**
 * dw使用的加载js代码，置于head标签
 */
function loadScript(src){
    typeof src == 'string' && (src = [src]);
    src && src.length && src instanceof Array && !function __load(count, src){
        var n = src[count];
        if (n) {
            count ++;
            var o = document.createElement("script");
            o.type = "text/javascript",
                o.setAttribute("async","async"),
                o.onload = o.onreadystatechange = function(){
                    return o.readyState && "complete" !== o.readyState && "loaded" !== o.readyState ? !1 : (o.onload=o.onreadystatechange=null,void __load(count, src));
                };
            var c = new Date;
                c.setSeconds(0),
                c.setMilliseconds(0),
                c = c.getTime(),
                o.async = !0,
                o.src = n + "?_=" + c,
                document.getElementsByTagName("head")[0].appendChild(o);
        }
    }(0, src);
}

module.exports = loadScript;
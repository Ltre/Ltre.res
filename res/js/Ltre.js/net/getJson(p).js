//支持json和jsonp，但不支持非200状态码的情况（从虎牙视频项目拷贝）
function jsonp(url, data, callback){
    var buildParam =  function(object){
        if(typeof object== 'object'){
            var s = [];
            for(var i in object){
                s[ s.length ] = encodeURIComponent( i ) + "=" + encodeURIComponent( object[i] );
            }
            return s.join( "&" ).replace( /%20/g, "+" );	
        }	
    };
    
    var param = "";
    switch(typeof data ){
        case 'string': param = data; break;
        case 'object': param = '&'  +buildParam(data); break;
        case 'function': callback  = data; break;
        default:break;
    }
    
    var callbackName='';
    for(var i = 0; i<100; i++){
        callbackName = 'jsonp' + new Date().getTime() + parseInt(100*Math.random());
        if( 'undefined' == typeof window[callbackName] ){
            window[callbackName] = function(data){
                if( 'function' == typeof callback ){
                    callback(data);
                }
                window[callbackName] = null;
            }
            break;
        }
    }

    url = url.replace(/(\?)$/, callbackName) + param;
    var head = document.getElementsByTagName("head")[0] || document.documentElement;  
    var script = document.createElement("script");   
    head.insertBefore( script, head.firstChild );   
    script.setAttribute("type", "text/javascript");  
    script.setAttribute("src", url); 		       
}

module.exports = jsonp;
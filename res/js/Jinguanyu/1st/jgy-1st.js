undefined != window.Anime || (Anime = {
   'widgets' : [],
   'num' : 0,
   'animemap' : {} //存入控件的id=>interval_id映射
});

/**
 * 金馆鱼一代(版权归折木所有)
 * @author Ltre
 * @since 2015-1-6
 * @link http://miku.us
 */
Anime.Jinguanyu = function(w_start, h_start){
    
    var _this = this;
    var width = document.body.clientWidth;//1200
    var height = document.body.clientHeight;//600
    var wstart = 250;
    var hstart = 150;
    var wnext = 0;
    var hnext = 0;
    var step = 1;
    var speed = 20;
    var widget = null;
    var widget_id = '';
    
    //创建DOM(私有)
    function create(w_start, h_start){
        w_start && (wstart = w_start);
        h_start && (hstart = h_start);
        widget_id = 'anime' + (++Anime.num);
        var src = 'res/biz/other/jinguanyu.gif';
        var img = document.createElement('img'); img.setAttribute('id', widget_id); img.setAttribute('src', src); img.setAttribute('style', 'position: fixed;left:' + wstart + 'px; top: ' + hstart + 'px; z-index: 1');
        var b = document.getElementsByTagName("body");if(b.length) b[0].appendChild(img);  else document.documentElement.appendChild(img);
        widget = document.getElementById(widget_id);
    }
    
    //开始/结束动画(公有)
    this.anime = function(isStart/*1开始0结束*/){
        var itvid = Anime.animemap[widget_id];
        if (undefined == itvid && isStart) {
            var itvid = setInterval(function(){
                //j.css({'left':Math.random()*1300+'px', 'top':Math.random()*600+'px'});
                step = Math.random()*10; //step = 1;
                wnext = widget.style.left;
                wnext=wnext.substring(0,wnext.length-2); Math.round(Math.random()*2-0.5)==0?(wnext-=(-step)):(wnext-=step);
                hnext = widget.style.top;
                hnext = hnext.substring(0,hnext.length-2); Math.round(Math.random()*2-0.5)==0?(hnext-=(-step)):(hnext-=step);
                if (wnext < 50) wnext = 60; if (hnext < 50) hnext = 60;
                if (hnext > height) hnext = width-50; if (hnext > height) hnext = height-50;
                widget.style.left = wnext + 'px';
                widget.style.top = hnext + 'px';
            }, speed);
            Anime.animemap[widget_id] = itvid;
        } else {
            clearInterval(itvid);
            Anime.animemap[widget_id] = undefined;
        }
    };
    
    //注入拖动效果,暂时不能实现按住，只能单击后拖动(公有)
    function injectDrag(){
        var o = document.getElementById(widget_id);
        var moveItvid, x, y, is_clicked = false;//间歇id、x、y、是否按下
        var img_w = 100, img_h = 100;
        o.onclick = function(){
            if (! is_clicked) {
                _this.anime(0);
                o.style.zIndex ++;
                is_clicked = true;
                console.log(['onclick', is_clicked]);
            } else {
                _this.anime(1);
                o.style.zIndex --;
                is_clicked = false;
            }
        };
        o.onmousemove = function(e){
            console.log(['onmousemove', is_clicked]);
            if (! is_clicked) return;//没点击过、或鼠标碰到未绑定拖动的金馆鱼时，不予执行动作
            e = e || window.event;
            console.log(e);
            console.log([e.x,e.y]);
            console.log([(e.x - img_w/2) + 'px', (e.y - img_h/2) + 'px']);
            o.style.left = (e.x - img_w/2) + 'px';
            o.style.top = (e.y - img_h/2) + 'px';
        };
    }
    
    (function init(){
        create(w_start, h_start);
        injectDrag();
    })();
    
};

//试运行
//CONSOLE预览：var doc = document; doc.getElementsByTagName('body')[0].style.zIndex = 0; var je = doc.createElement("script"); je.setAttribute("type", "text/javascript"); je.setAttribute("src", 'http://miku.us/res/lib/anime.js'); var heads = doc.getElementsByTagName("head"); if(heads.length) heads[0].appendChild(je);  else  doc.documentElement.appendChild(je);
//CONSOLE预览：var doc = document; doc.getElementsByTagName('body')[0].style.zIndex = 0; var je = doc.createElement("script"); je.setAttribute("type", "text/javascript"); je.setAttribute("src", 'http://danmu.me/pub/danmu/res/lib/anime.js'); var heads = doc.getElementsByTagName("head"); if(heads.length) heads[0].appendChild(je);  else  doc.documentElement.appendChild(je);
Anime.Jinguanyu.test = function(){
    for(var i = 0; i < 50; i++) {
        var wstart = Math.random() * (window.screen.width - 100) + 50;
        var hstart = Math.random() * (window.screen.height - 50) + 50;
        console.log(wstart, hstart);
        var j = new Anime.Jinguanyu(wstart, hstart);
        j.anime(1);
    }
};
window.Ltrelib = window.Ltrelib || {};//使用一个生僻的名称作为全局变量，以存储自定义的库，防止与其它变量冲突
/**
 * 延迟器
 * @param opt
 * @returns {timing}
 * @example
 * 用法一：
 *      //恒频延迟器（定时器）
 *      Ltrelib.timing({
 *          delay: 100,
 *          onStart: function(opt){
 *              console.log('start');
 *          },
 *          onTiming: function(opt){
 *              console.log('timing');
 *          },
 *          onStop: function(opt){
 *              console.log('stop');
 *          }
 *      });
 *      //变频延迟器
 *      Ltrelib.timing({
 *          delay: 100,
 *          amplTop: +200,
 *          amplBot: -1000,
 *          onStart: function(opt){
 *              console.log('start');
 *          },
 *          onTiming: function(opt){
 *              console.log('timing');
 *          },
 *          onStop: function(opt){
 *              console.log('stop');
 *          }
 *      });
 * 用法二：
 *      //外部干预计时
 *      var timingObj = new Ltrelib.timing({
 *          delay: 100,
 *          onStart: function(opt){
 *              console.log('start');
 *          },
 *          onTiming: function(opt){
 *              console.log('timing');
 *          },
 *          onStop: function(opt){
 *              console.log('stop');
 *          }
 *      });
 *      timingObj.ctrl.goTo = 12;//跳至12
 *      timingObj.ctrl.goPause = true;//暂停计时
 *      timingObj.ctrl.goPause = false;//恢复计时
 *      timingObj.ctrl.goStop = true;//终止
 */
Ltrelib.timing = function(opt){
    opt.a           = opt.a || 0;//开始
    opt.z           = opt.z || 100;//结束
    opt.step        = opt.step || +1;//步长
    opt.delay       = opt.delay || 10;//延迟(毫秒)
    opt.amplTop     = opt.amplTop || +0;//延迟增幅(毫秒，用于设定变频延迟器)
    opt.amplBot     = opt.amplBot || -0;//延迟减幅(毫秒，用于设定变频延迟器)
    opt.onStart     = opt.onStart || function(i){};//启动时
    opt.onTiming    = opt.onTiming || function(i){};//进行时
    opt.onStop      = opt.onStop || function(i){};//结束时
    opt.i = opt.a;
    
    var innerThat = this;
    this.ctrl = {goPause:false, goStop:false, goFirst:false, goLast:false, goPrev:false, goNext:false, goTo:false};
    ~ function f(){
        if (opt.i <= opt.z) {
            //触发延时过程
            var randAmpl = opt.amplBot + Math.random() * (opt.amplTop - opt.amplBot);
            
            setTimeout(f, opt.delay + randAmpl);
            
            //外部干预{innerThat.ctrl实现}
            if (innerThat.ctrl.goPause) {
                return;//暂停，并保证下次可从所停位置继续
            }
            if (innerThat.ctrl.goStop) {
                opt.i = opt.z + opt.step;
                return;//终止，其它任何指令都无法恢复
            }
            if (innerThat.ctrl.goFirst) {
                innerThat.ctrl.goFirst = false;
                opt.i = opt.a;
                return;//跳至开始
            }
            if (innerThat.ctrl.goLast){
                innerThat.ctrl.goLast = false;
                opt.i = opt.z;
                return;//跳至末尾
            }
            if (innerThat.ctrl.goPrev) {
                innerThat.ctrl.goPrev = false;
                opt.i -= opt.step;
                return;//跳至上次
            }
            if (innerThat.ctrl.goNext) {
                innerThat.ctrl.goNext = false;
                opt.i += opt.step;
                return;//跳至下次
            }
            if ('number' == typeof innerThat.ctrl.goTo && opt.a <= innerThat.ctrl.goTo && innerThat.ctrl.goTo <= opt.z) {
                opt.i = innerThat.ctrl.goTo;
                innerThat.ctrl.goTo = false;
                return;//跳至指定位置
            }
            
            //核心执行部分
            opt.a == opt.i && opt.onStart(opt);
            opt.onTiming(opt);
            opt.z == opt.i && opt.onStop(opt);
        }
        opt.i += opt.step;
    }();
};

module.exports = Ltrelib.timing;
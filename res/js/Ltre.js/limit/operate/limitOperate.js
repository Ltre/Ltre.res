window.Ltrelib = window.Ltrelib || {};
~function(){new function(){
    
    /**
     * 控制在某时间范围内，最多的操作次数.(默认10分钟内最多操作5次)
     * @param elem DOM节点
     * @param maxCount 最大次数
     * @param duration 限制次数的时间范围
     * @return {Boolean} 是否可操作
     */
    Ltrelib.limitOperate = function(elem, maxCount, duration){
        if (! elem) return false;
        maxCount = maxCount || 5;
        duration = duration || 600;
        var now = -(-new Date());
        var handle = 'operatehandle';
        var cr = data(elem, handle) || {time:now, count:0};
        data(elem, handle, {time:cr.time, count:++cr.count});
        if (now - cr.time < duration * 1000) {
            if (cr.count > maxCount) {
                return false;
            }
        } else {
            data(elem, handle, {time:now, count:1});
        }
        return true;
    };

    
    /**
     * 控制在某时间范围内，最多的操作次数.(默认10分钟内最多操作5次)【jQuery版】
     * @param selector jQuery选择器，如"#a"
     * @param maxCount 最大次数
     * @param duration 限制次数的时间范围
     * @return {Boolean} 是否可操作
     */
    Ltrelib.$limitOperate = function(selector, maxCount, duration){
        if (! selector) return true;
        maxCount = maxCount || 5;
        duration = duration || 600;
        var now = -(-new Date());
        var handle = 'operatehandle';
        var cr = $(selector).data(handle) || {time:now, count:0};
        $(selector).data(handle, {time:cr.time, count:++cr.count});
        if (now - cr.time < duration * 1000) {
            if (cr.count > maxCount) {
                return false;
            }
        } else {
            $(selector).data(handle, {time:now, count:1});
        }
        return true;
    };

    
    //内部工具：在元素上存储临时数据
    function data(elem, key, value){
        if (! elem) throw new Error('not an element');
        if (! key) throw new Error('key is not valid');
        if ('2' in arguments) {
            elem['data-'+key] = value;
        } else {
            return elem['data-'+key];
        }
    }
    
};}();

module.exports = Ltrelib.limitOperate;
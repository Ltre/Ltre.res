/**
 * 对象多级key一键赋值
 * @params 第1到(n-1)个参数按key由浅至深列出
 * @param 第n个参数为要赋的任意类型值
 * @example
 *      var o = {};
 *      o.multiLevelAssign('a', 'b', 123456); // {a:{b:123456}}
 */
Object.prototype.multiLevelAssign = function(){
    if (arguments.length < 2) throw new Error('args are error, it must be used as multiLevelAssign(key1, [key2,...,keyn,] value)');
    var value = arguments[arguments.length - 1];
    var keys = Array.prototype.slice.call(arguments, 0, -1);
    var curr = this;
    keys.forEach(function(e, i, arr){
        if (i == arr.length - 1) {
            curr[e] = value;
        } else if (!(e in curr) || typeof curr[e] != 'object') {
            curr[e] = {};
        }
        curr = curr[e];
    });
}
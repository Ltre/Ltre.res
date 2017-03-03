if ('forEach' in Array.prototype) {

    var ltreDeCrypt = function(str){
        var table = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@~!*()-_.\''.split('');//@为%的替代符
        var rawList = [];
        var offsetList = [];
        str.split('').reverse().forEach(function(e, i){
            var pos = table.indexOf(e);
            if (parseInt((i + 1) % 2) == 1) {
                offsetList.push(pos);
            } else {
                var rawPos = parseInt(pos - offsetList[(i + 1) / 2 - 1]);
                rawList.push(table[rawPos]);
            }
        });
        var raw = rawList.join('').replace(/@/g, '%');
        return decodeURIComponent(raw);
    };
    
} else {

    var ltreDeCrypt = function(str){
        var indexOf = function(arr, search){
            if (arr.constructor.name == 'Array') {
                for (var i=0; i < arr.length; i++) {
                    if (arr[i]==search) return i;
                }
            }
            return -1;
        };
        var table = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@~!*()-_.\''.split('');//@为%的替代符
        var rawList = [];
        var offsetList = [];
        var tmp = str.split('').reverse();
        for (var i in tmp) {
            var i = parseInt(i);
            var pos = indexOf(table, tmp[i]);
            if (parseInt((i + 1) % 2) == 1) {
                offsetList.push(pos);
            } else {
                var rawPos = parseInt(pos - offsetList[(i + 1) / 2 - 1]);
                rawList.push(table[rawPos]);
            }
        }
        var raw = rawList.join('').replace(/@/g, '%');
        return decodeURIComponent(raw);
    };
    
}

module.exports = ltreDeCrypt;
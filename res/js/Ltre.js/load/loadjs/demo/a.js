window.Ltrelib = window.Ltrelib || {};//使用一个生僻的名称作为全局变量，以存储自定义的库，防止与其它变量冲突var a = function(){    this.hehe = 1;};a.prototype.constructor = Array;Ltrelib.a = a;
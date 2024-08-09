/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
YAHOO.namespace("tool");
YAHOO.tool.Profiler = {
  _container: new Object(),
  _report: new Object(),
  _saveData: function (B, C) {
    var A = this._report[B];
    A.calls++;
    A.points.push(C);
    if (A.calls > 1) {
      A.avg = (A.avg * (A.calls - 1) + C) / A.calls;
      A.min = Math.min(A.min, C);
      A.max = Math.max(A.max, C);
    } else {
      A.avg = C;
      A.min = C;
      A.max = C;
    }
  },
  getAverage: function (A) {
    return this._report[A].avg;
  },
  getCallCount: function (A) {
    return this._report[A].calls;
  },
  getMax: function (A) {
    return this._report[A].max;
  },
  getMin: function (A) {
    return this._report[A].min;
  },
  getFunctionReport: function (A) {
    return this._report[A];
  },
  getFullReport: function (C) {
    C =
      C ||
      function () {
        return true;
      };
    if (YAHOO.lang.isFunction(C)) {
      var A = {};
      for (var B in this._report) {
        if (C(this._report[B])) {
          A[B] = this._report[B];
        }
      }
      return A;
    }
  },
  registerConstructor: function (B, A) {
    this.registerFunction(B, A, true);
  },
  registerFunction: function (name, owner, registerPrototype) {
    var funcName =
      name.indexOf(".") > -1 ? name.substring(name.lastIndexOf(".") + 1) : name;
    if (!YAHOO.lang.isObject(owner)) {
      owner = eval(name.substring(0, name.lastIndexOf(".")));
    }
    var method = owner[funcName];
    var prototype = method.prototype;
    if (YAHOO.lang.isFunction(method) && !method.__yuiProfiled) {
      this._container[name] = method;
      owner[funcName] = function () {
        var start = new Date();
        var retval = method.apply(this, arguments);
        var stop = new Date();
        YAHOO.tool.Profiler._saveData(name, stop - start);
        return retval;
      };
      YAHOO.lang.augmentObject(owner[funcName], method);
      owner[funcName].__yuiProfiled = true;
      owner[funcName].prototype = prototype;
      this._container[name].__yuiOwner = owner;
      this._container[name].__yuiFuncName = funcName;
      if (registerPrototype) {
        this.registerObject(name + ".prototype", prototype);
      }
      this._report[name] = { calls: 0, max: 0, min: 0, avg: 0, points: [] };
    }
    return method;
  },
  registerObject: function (name, object, recurse) {
    object = YAHOO.lang.isObject(object) ? object : eval(name);
    this._container[name] = object;
    for (var prop in object) {
      if (typeof object[prop] == "function") {
        if (prop != "constructor" && prop != "superclass") {
          this.registerFunction(name + "." + prop, object);
        }
      } else {
        if (typeof object[prop] == "object" && recurse) {
          this.registerObject(name + "." + prop, object[prop], recurse);
        }
      }
    }
  },
  unregisterConstructor: function (A) {
    if (YAHOO.lang.isFunction(this._container[A])) {
      this.unregisterFunction(A, true);
    }
  },
  unregisterFunction: function (B, C) {
    if (YAHOO.lang.isFunction(this._container[B])) {
      if (C) {
        this.unregisterObject(B + ".prototype", this._container[B].prototype);
      }
      var A = this._container[B].__yuiOwner;
      var D = this._container[B].__yuiFuncName;
      delete this._container[B].__yuiOwner;
      delete this._container[B].__yuiFuncName;
      A[D] = this._container[B];
      delete this._container[B];
      delete this._report[B];
    }
  },
  unregisterObject: function (B, C) {
    if (YAHOO.lang.isObject(this._container[B])) {
      var A = this._container[B];
      for (var D in A) {
        if (typeof A[D] == "function") {
          this.unregisterFunction(B + "." + D);
        } else {
          if (typeof A[D] == "object" && C) {
            this.unregisterObject(B + "." + D, C);
          }
        }
      }
      delete this._container[B];
    }
  },
};
YAHOO.register("profiler", YAHOO.tool.Profiler, {
  version: "2.7.0",
  build: "1799",
});

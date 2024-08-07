/*
 Copyright (c) 2009, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.7.0
 */
YAHOO.namespace("tool");
(function () {
  var A = 0;
  YAHOO.tool.TestCase = function (B) {
    this._should = {};
    for (var C in B) {
      this[C] = B[C];
    }
    if (!YAHOO.lang.isString(this.name)) {
      this.name = "testCase" + A++;
    }
  };
  YAHOO.tool.TestCase.prototype = {
    resume: function (B) {
      YAHOO.tool.TestRunner.resume(B);
    },
    wait: function (D, C) {
      var B = arguments;
      if (YAHOO.lang.isFunction(B[0])) {
        throw new YAHOO.tool.TestCase.Wait(B[0], B[1]);
      } else {
        throw new YAHOO.tool.TestCase.Wait(
          function () {
            YAHOO.util.Assert.fail(
              "Timeout: wait() called but resume() never called.",
            );
          },
          YAHOO.lang.isNumber(B[0]) ? B[0] : 10000,
        );
      }
    },
    setUp: function () {},
    tearDown: function () {},
  };
  YAHOO.tool.TestCase.Wait = function (C, B) {
    this.segment = YAHOO.lang.isFunction(C) ? C : null;
    this.delay = YAHOO.lang.isNumber(B) ? B : 0;
  };
})();
YAHOO.namespace("tool");
YAHOO.tool.TestSuite = function (A) {
  this.name = "";
  this.items = [];
  if (YAHOO.lang.isString(A)) {
    this.name = A;
  } else {
    if (YAHOO.lang.isObject(A)) {
      YAHOO.lang.augmentObject(this, A, true);
    }
  }
  if (this.name === "") {
    this.name = YAHOO.util.Dom.generateId(null, "testSuite");
  }
};
YAHOO.tool.TestSuite.prototype = {
  add: function (A) {
    if (A instanceof YAHOO.tool.TestSuite || A instanceof YAHOO.tool.TestCase) {
      this.items.push(A);
    }
  },
  setUp: function () {},
  tearDown: function () {},
};
YAHOO.namespace("tool");
YAHOO.tool.TestRunner = (function () {
  function B(C) {
    this.testObject = C;
    this.firstChild = null;
    this.lastChild = null;
    this.parent = null;
    this.next = null;
    this.results = { passed: 0, failed: 0, total: 0, ignored: 0 };
    if (C instanceof YAHOO.tool.TestSuite) {
      this.results.type = "testsuite";
      this.results.name = C.name;
    } else {
      if (C instanceof YAHOO.tool.TestCase) {
        this.results.type = "testcase";
        this.results.name = C.name;
      }
    }
  }

  B.prototype = {
    appendChild: function (C) {
      var D = new B(C);
      if (this.firstChild === null) {
        this.firstChild = this.lastChild = D;
      } else {
        this.lastChild.next = D;
        this.lastChild = D;
      }
      D.parent = this;
      return D;
    },
  };

  function A() {
    A.superclass.constructor.apply(this, arguments);
    this.masterSuite = new YAHOO.tool.TestSuite("YUI Test Results");
    this._cur = null;
    this._root = null;
    var D = [
      this.TEST_CASE_BEGIN_EVENT,
      this.TEST_CASE_COMPLETE_EVENT,
      this.TEST_SUITE_BEGIN_EVENT,
      this.TEST_SUITE_COMPLETE_EVENT,
      this.TEST_PASS_EVENT,
      this.TEST_FAIL_EVENT,
      this.TEST_IGNORE_EVENT,
      this.COMPLETE_EVENT,
      this.BEGIN_EVENT,
    ];
    for (var C = 0; C < D.length; C++) {
      this.createEvent(D[C], { scope: this });
    }
  }

  YAHOO.lang.extend(A, YAHOO.util.EventProvider, {
    TEST_CASE_BEGIN_EVENT: "testcasebegin",
    TEST_CASE_COMPLETE_EVENT: "testcasecomplete",
    TEST_SUITE_BEGIN_EVENT: "testsuitebegin",
    TEST_SUITE_COMPLETE_EVENT: "testsuitecomplete",
    TEST_PASS_EVENT: "pass",
    TEST_FAIL_EVENT: "fail",
    TEST_IGNORE_EVENT: "ignore",
    COMPLETE_EVENT: "complete",
    BEGIN_EVENT: "begin",
    _addTestCaseToTestTree: function (C, D) {
      var E = C.appendChild(D);
      for (var F in D) {
        if (F.indexOf("test") === 0 && YAHOO.lang.isFunction(D[F])) {
          E.appendChild(F);
        }
      }
    },
    _addTestSuiteToTestTree: function (C, F) {
      var E = C.appendChild(F);
      for (var D = 0; D < F.items.length; D++) {
        if (F.items[D] instanceof YAHOO.tool.TestSuite) {
          this._addTestSuiteToTestTree(E, F.items[D]);
        } else {
          if (F.items[D] instanceof YAHOO.tool.TestCase) {
            this._addTestCaseToTestTree(E, F.items[D]);
          }
        }
      }
    },
    _buildTestTree: function () {
      this._root = new B(this.masterSuite);
      this._cur = this._root;
      for (var C = 0; C < this.masterSuite.items.length; C++) {
        if (this.masterSuite.items[C] instanceof YAHOO.tool.TestSuite) {
          this._addTestSuiteToTestTree(this._root, this.masterSuite.items[C]);
        } else {
          if (this.masterSuite.items[C] instanceof YAHOO.tool.TestCase) {
            this._addTestCaseToTestTree(this._root, this.masterSuite.items[C]);
          }
        }
      }
    },
    _handleTestObjectComplete: function (C) {
      if (YAHOO.lang.isObject(C.testObject)) {
        C.parent.results.passed += C.results.passed;
        C.parent.results.failed += C.results.failed;
        C.parent.results.total += C.results.total;
        C.parent.results.ignored += C.results.ignored;
        C.parent.results[C.testObject.name] = C.results;
        if (C.testObject instanceof YAHOO.tool.TestSuite) {
          C.testObject.tearDown();
          this.fireEvent(this.TEST_SUITE_COMPLETE_EVENT, {
            testSuite: C.testObject,
            results: C.results,
          });
        } else {
          if (C.testObject instanceof YAHOO.tool.TestCase) {
            this.fireEvent(this.TEST_CASE_COMPLETE_EVENT, {
              testCase: C.testObject,
              results: C.results,
            });
          }
        }
      }
    },
    _next: function () {
      if (this._cur.firstChild) {
        this._cur = this._cur.firstChild;
      } else {
        if (this._cur.next) {
          this._cur = this._cur.next;
        } else {
          while (this._cur && !this._cur.next && this._cur !== this._root) {
            this._handleTestObjectComplete(this._cur);
            this._cur = this._cur.parent;
          }
          if (this._cur == this._root) {
            this._cur.results.type = "report";
            this._cur.results.timestamp = new Date().toLocaleString();
            this._cur.results.duration =
              new Date() - this._cur.results.duration;
            this.fireEvent(this.COMPLETE_EVENT, { results: this._cur.results });
            this._cur = null;
          } else {
            this._handleTestObjectComplete(this._cur);
            this._cur = this._cur.next;
          }
        }
      }
      return this._cur;
    },
    _run: function () {
      var E = false;
      var D = this._next();
      if (D !== null) {
        var C = D.testObject;
        if (YAHOO.lang.isObject(C)) {
          if (C instanceof YAHOO.tool.TestSuite) {
            this.fireEvent(this.TEST_SUITE_BEGIN_EVENT, { testSuite: C });
            C.setUp();
          } else {
            if (C instanceof YAHOO.tool.TestCase) {
              this.fireEvent(this.TEST_CASE_BEGIN_EVENT, { testCase: C });
            }
          }
          if (typeof setTimeout != "undefined") {
            setTimeout(function () {
              YAHOO.tool.TestRunner._run();
            }, 0);
          } else {
            this._run();
          }
        } else {
          this._runTest(D);
        }
      }
    },
    _resumeTest: function (G) {
      var C = this._cur;
      var H = C.testObject;
      var E = C.parent.testObject;
      if (E.__yui_wait) {
        clearTimeout(E.__yui_wait);
        delete E.__yui_wait;
      }
      var K = (E._should.fail || {})[H];
      var D = (E._should.error || {})[H];
      var F = false;
      var I = null;
      try {
        G.apply(E);
        if (K) {
          I = new YAHOO.util.ShouldFail();
          F = true;
        } else {
          if (D) {
            I = new YAHOO.util.ShouldError();
            F = true;
          }
        }
      } catch (J) {
        if (J instanceof YAHOO.util.AssertionError) {
          if (!K) {
            I = J;
            F = true;
          }
        } else {
          if (J instanceof YAHOO.tool.TestCase.Wait) {
            if (YAHOO.lang.isFunction(J.segment)) {
              if (YAHOO.lang.isNumber(J.delay)) {
                if (typeof setTimeout != "undefined") {
                  E.__yui_wait = setTimeout(function () {
                    YAHOO.tool.TestRunner._resumeTest(J.segment);
                  }, J.delay);
                } else {
                  throw new Error(
                    "Asynchronous tests not supported in this environment.",
                  );
                }
              }
            }
            return;
          } else {
            if (!D) {
              I = new YAHOO.util.UnexpectedError(J);
              F = true;
            } else {
              if (YAHOO.lang.isString(D)) {
                if (J.message != D) {
                  I = new YAHOO.util.UnexpectedError(J);
                  F = true;
                }
              } else {
                if (YAHOO.lang.isFunction(D)) {
                  if (!(J instanceof D)) {
                    I = new YAHOO.util.UnexpectedError(J);
                    F = true;
                  }
                } else {
                  if (YAHOO.lang.isObject(D)) {
                    if (
                      !(J instanceof D.constructor) ||
                      J.message != D.message
                    ) {
                      I = new YAHOO.util.UnexpectedError(J);
                      F = true;
                    }
                  }
                }
              }
            }
          }
        }
      }
      if (F) {
        this.fireEvent(this.TEST_FAIL_EVENT, {
          testCase: E,
          testName: H,
          error: I,
        });
      } else {
        this.fireEvent(this.TEST_PASS_EVENT, { testCase: E, testName: H });
      }
      E.tearDown();
      C.parent.results[H] = {
        result: F ? "fail" : "pass",
        message: I ? I.getMessage() : "Test passed",
        type: "test",
        name: H,
      };
      if (F) {
        C.parent.results.failed++;
      } else {
        C.parent.results.passed++;
      }
      C.parent.results.total++;
      if (typeof setTimeout != "undefined") {
        setTimeout(function () {
          YAHOO.tool.TestRunner._run();
        }, 0);
      } else {
        this._run();
      }
    },
    _runTest: function (F) {
      var C = F.testObject;
      var D = F.parent.testObject;
      var G = D[C];
      var E = (D._should.ignore || {})[C];
      if (E) {
        F.parent.results[C] = {
          result: "ignore",
          message: "Test ignored",
          type: "test",
          name: C,
        };
        F.parent.results.ignored++;
        F.parent.results.total++;
        this.fireEvent(this.TEST_IGNORE_EVENT, { testCase: D, testName: C });
        if (typeof setTimeout != "undefined") {
          setTimeout(function () {
            YAHOO.tool.TestRunner._run();
          }, 0);
        } else {
          this._run();
        }
      } else {
        D.setUp();
        this._resumeTest(G);
      }
    },
    fireEvent: function (C, D) {
      D = D || {};
      D.type = C;
      A.superclass.fireEvent.call(this, C, D);
    },
    add: function (C) {
      this.masterSuite.add(C);
    },
    clear: function () {
      this.masterSuite.items = [];
    },
    resume: function (C) {
      this._resumeTest(C || function () {});
    },
    run: function (C) {
      var D = YAHOO.tool.TestRunner;
      D._buildTestTree();
      D._root.results.duration = new Date().valueOf();
      D.fireEvent(D.BEGIN_EVENT);
      D._run();
    },
  });
  return new A();
})();
YAHOO.namespace("util");
YAHOO.util.Assert = {
  _formatMessage: function (B, A) {
    var C = B;
    if (YAHOO.lang.isString(B) && B.length > 0) {
      return YAHOO.lang.substitute(B, { message: A });
    } else {
      return A;
    }
  },
  fail: function (A) {
    throw new YAHOO.util.AssertionError(
      this._formatMessage(A, "Test force-failed."),
    );
  },
  areEqual: function (B, C, A) {
    if (B != C) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(A, "Values should be equal."),
        B,
        C,
      );
    }
  },
  areNotEqual: function (A, C, B) {
    if (A == C) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(B, "Values should not be equal."),
        A,
      );
    }
  },
  areNotSame: function (A, C, B) {
    if (A === C) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(B, "Values should not be the same."),
        A,
      );
    }
  },
  areSame: function (B, C, A) {
    if (B !== C) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(A, "Values should be the same."),
        B,
        C,
      );
    }
  },
  isFalse: function (B, A) {
    if (false !== B) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(A, "Value should be false."),
        false,
        B,
      );
    }
  },
  isTrue: function (B, A) {
    if (true !== B) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(A, "Value should be true."),
        true,
        B,
      );
    }
  },
  isNaN: function (B, A) {
    if (!isNaN(B)) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(A, "Value should be NaN."),
        NaN,
        B,
      );
    }
  },
  isNotNaN: function (B, A) {
    if (isNaN(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Values should not be NaN."),
        NaN,
      );
    }
  },
  isNotNull: function (B, A) {
    if (YAHOO.lang.isNull(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Values should not be null."),
        null,
      );
    }
  },
  isNotUndefined: function (B, A) {
    if (YAHOO.lang.isUndefined(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Value should not be undefined."),
        undefined,
      );
    }
  },
  isNull: function (B, A) {
    if (!YAHOO.lang.isNull(B)) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(A, "Value should be null."),
        null,
        B,
      );
    }
  },
  isUndefined: function (B, A) {
    if (!YAHOO.lang.isUndefined(B)) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(A, "Value should be undefined."),
        undefined,
        B,
      );
    }
  },
  isArray: function (B, A) {
    if (!YAHOO.lang.isArray(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Value should be an array."),
        B,
      );
    }
  },
  isBoolean: function (B, A) {
    if (!YAHOO.lang.isBoolean(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Value should be a Boolean."),
        B,
      );
    }
  },
  isFunction: function (B, A) {
    if (!YAHOO.lang.isFunction(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Value should be a function."),
        B,
      );
    }
  },
  isInstanceOf: function (B, C, A) {
    if (!(C instanceof B)) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(A, "Value isn't an instance of expected type."),
        B,
        C,
      );
    }
  },
  isNumber: function (B, A) {
    if (!YAHOO.lang.isNumber(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Value should be a number."),
        B,
      );
    }
  },
  isObject: function (B, A) {
    if (!YAHOO.lang.isObject(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Value should be an object."),
        B,
      );
    }
  },
  isString: function (B, A) {
    if (!YAHOO.lang.isString(B)) {
      throw new YAHOO.util.UnexpectedValue(
        this._formatMessage(A, "Value should be a string."),
        B,
      );
    }
  },
  isTypeOf: function (A, C, B) {
    if (typeof C != A) {
      throw new YAHOO.util.ComparisonFailure(
        this._formatMessage(B, "Value should be of type " + expected + "."),
        expected,
        typeof actual,
      );
    }
  },
};
YAHOO.util.AssertionError = function (A) {
  arguments.callee.superclass.constructor.call(this, A);
  this.message = A;
  this.name = "AssertionError";
};
YAHOO.lang.extend(YAHOO.util.AssertionError, Error, {
  getMessage: function () {
    return this.message;
  },
  toString: function () {
    return this.name + ": " + this.getMessage();
  },
  valueOf: function () {
    return this.toString();
  },
});
YAHOO.util.ComparisonFailure = function (B, A, C) {
  arguments.callee.superclass.constructor.call(this, B);
  this.expected = A;
  this.actual = C;
  this.name = "ComparisonFailure";
};
YAHOO.lang.extend(YAHOO.util.ComparisonFailure, YAHOO.util.AssertionError, {
  getMessage: function () {
    return (
      this.message +
      "\nExpected: " +
      this.expected +
      " (" +
      typeof this.expected +
      ")" +
      "\nActual:" +
      this.actual +
      " (" +
      typeof this.actual +
      ")"
    );
  },
});
YAHOO.util.UnexpectedValue = function (B, A) {
  arguments.callee.superclass.constructor.call(this, B);
  this.unexpected = A;
  this.name = "UnexpectedValue";
};
YAHOO.lang.extend(YAHOO.util.UnexpectedValue, YAHOO.util.AssertionError, {
  getMessage: function () {
    return (
      this.message +
      "\nUnexpected: " +
      this.unexpected +
      " (" +
      typeof this.unexpected +
      ") "
    );
  },
});
YAHOO.util.ShouldFail = function (A) {
  arguments.callee.superclass.constructor.call(
    this,
    A || "This test should fail but didn't.",
  );
  this.name = "ShouldFail";
};
YAHOO.lang.extend(YAHOO.util.ShouldFail, YAHOO.util.AssertionError);
YAHOO.util.ShouldError = function (A) {
  arguments.callee.superclass.constructor.call(
    this,
    A || "This test should have thrown an error but didn't.",
  );
  this.name = "ShouldError";
};
YAHOO.lang.extend(YAHOO.util.ShouldError, YAHOO.util.AssertionError);
YAHOO.util.UnexpectedError = function (A) {
  arguments.callee.superclass.constructor.call(
    this,
    "Unexpected error: " + A.message,
  );
  this.cause = A;
  this.name = "UnexpectedError";
  this.stack = A.stack;
};
YAHOO.lang.extend(YAHOO.util.UnexpectedError, YAHOO.util.AssertionError);
YAHOO.util.ArrayAssert = {
  contains: function (E, D, B) {
    var C = false;
    var F = YAHOO.util.Assert;
    for (var A = 0; A < D.length && !C; A++) {
      if (D[A] === E) {
        C = true;
      }
    }
    if (!C) {
      F.fail(
        F._formatMessage(
          B,
          "Value " + E + " (" + typeof E + ") not found in array [" + D + "].",
        ),
      );
    }
  },
  containsItems: function (C, D, B) {
    for (var A = 0; A < C.length; A++) {
      this.contains(C[A], D, B);
    }
  },
  containsMatch: function (E, D, B) {
    if (typeof E != "function") {
      throw new TypeError(
        "ArrayAssert.containsMatch(): First argument must be a function.",
      );
    }
    var C = false;
    var F = YAHOO.util.Assert;
    for (var A = 0; A < D.length && !C; A++) {
      if (E(D[A])) {
        C = true;
      }
    }
    if (!C) {
      F.fail(F._formatMessage(B, "No match found in array [" + D + "]."));
    }
  },
  doesNotContain: function (E, D, B) {
    var C = false;
    var F = YAHOO.util.Assert;
    for (var A = 0; A < D.length && !C; A++) {
      if (D[A] === E) {
        C = true;
      }
    }
    if (C) {
      F.fail(F._formatMessage(B, "Value found in array [" + D + "]."));
    }
  },
  doesNotContainItems: function (C, D, B) {
    for (var A = 0; A < C.length; A++) {
      this.doesNotContain(C[A], D, B);
    }
  },
  doesNotContainMatch: function (E, D, B) {
    if (typeof E != "function") {
      throw new TypeError(
        "ArrayAssert.doesNotContainMatch(): First argument must be a function.",
      );
    }
    var C = false;
    var F = YAHOO.util.Assert;
    for (var A = 0; A < D.length && !C; A++) {
      if (E(D[A])) {
        C = true;
      }
    }
    if (C) {
      F.fail(F._formatMessage(B, "Value found in array [" + D + "]."));
    }
  },
  indexOf: function (E, D, A, C) {
    for (var B = 0; B < D.length; B++) {
      if (D[B] === E) {
        YAHOO.util.Assert.areEqual(
          A,
          B,
          C ||
            "Value exists at index " + B + " but should be at index " + A + ".",
        );
        return;
      }
    }
    var F = YAHOO.util.Assert;
    F.fail(F._formatMessage(C, "Value doesn't exist in array [" + D + "]."));
  },
  itemsAreEqual: function (D, F, C) {
    var A = Math.max(D.length, F.length);
    var E = YAHOO.util.Assert;
    for (var B = 0; B < A; B++) {
      E.areEqual(
        D[B],
        F[B],
        E._formatMessage(C, "Values in position " + B + " are not equal."),
      );
    }
  },
  itemsAreEquivalent: function (E, F, B, D) {
    if (typeof B != "function") {
      throw new TypeError(
        "ArrayAssert.itemsAreEquivalent(): Third argument must be a function.",
      );
    }
    var A = Math.max(E.length, F.length);
    for (var C = 0; C < A; C++) {
      if (!B(E[C], F[C])) {
        throw new YAHOO.util.ComparisonFailure(
          YAHOO.util.Assert._formatMessage(
            D,
            "Values in position " + C + " are not equivalent.",
          ),
          E[C],
          F[C],
        );
      }
    }
  },
  isEmpty: function (C, A) {
    if (C.length > 0) {
      var B = YAHOO.util.Assert;
      B.fail(B._formatMessage(A, "Array should be empty."));
    }
  },
  isNotEmpty: function (C, A) {
    if (C.length === 0) {
      var B = YAHOO.util.Assert;
      B.fail(B._formatMessage(A, "Array should not be empty."));
    }
  },
  itemsAreSame: function (D, F, C) {
    var A = Math.max(D.length, F.length);
    var E = YAHOO.util.Assert;
    for (var B = 0; B < A; B++) {
      E.areSame(
        D[B],
        F[B],
        E._formatMessage(C, "Values in position " + B + " are not the same."),
      );
    }
  },
  lastIndexOf: function (E, D, A, C) {
    var F = YAHOO.util.Assert;
    for (var B = D.length; B >= 0; B--) {
      if (D[B] === E) {
        F.areEqual(
          A,
          B,
          F._formatMessage(
            C,
            "Value exists at index " + B + " but should be at index " + A + ".",
          ),
        );
        return;
      }
    }
    F.fail(F._formatMessage(C, "Value doesn't exist in array."));
  },
};
YAHOO.namespace("util");
YAHOO.util.ObjectAssert = {
  propertiesAreEqual: function (D, G, C) {
    var F = YAHOO.util.Assert;
    var B = [];
    for (var E in D) {
      B.push(E);
    }
    for (var A = 0; A < B.length; A++) {
      F.isNotUndefined(
        G[B[A]],
        F._formatMessage(C, "Property '" + B[A] + "' expected."),
      );
    }
  },
  hasProperty: function (A, B, C) {
    if (!(A in B)) {
      var D = YAHOO.util.Assert;
      D.fail(D._formatMessage(C, "Property '" + A + "' not found on object."));
    }
  },
  hasOwnProperty: function (A, B, C) {
    if (!YAHOO.lang.hasOwnProperty(B, A)) {
      var D = YAHOO.util.Assert;
      D.fail(
        D._formatMessage(
          C,
          "Property '" + A + "' not found on object instance.",
        ),
      );
    }
  },
};
YAHOO.util.DateAssert = {
  datesAreEqual: function (B, D, A) {
    if (B instanceof Date && D instanceof Date) {
      var C = YAHOO.util.Assert;
      C.areEqual(
        B.getFullYear(),
        D.getFullYear(),
        C._formatMessage(A, "Years should be equal."),
      );
      C.areEqual(
        B.getMonth(),
        D.getMonth(),
        C._formatMessage(A, "Months should be equal."),
      );
      C.areEqual(
        B.getDate(),
        D.getDate(),
        C._formatMessage(A, "Day of month should be equal."),
      );
    } else {
      throw new TypeError(
        "DateAssert.datesAreEqual(): Expected and actual values must be Date objects.",
      );
    }
  },
  timesAreEqual: function (B, D, A) {
    if (B instanceof Date && D instanceof Date) {
      var C = YAHOO.util.Assert;
      C.areEqual(
        B.getHours(),
        D.getHours(),
        C._formatMessage(A, "Hours should be equal."),
      );
      C.areEqual(
        B.getMinutes(),
        D.getMinutes(),
        C._formatMessage(A, "Minutes should be equal."),
      );
      C.areEqual(
        B.getSeconds(),
        D.getSeconds(),
        C._formatMessage(A, "Seconds should be equal."),
      );
    } else {
      throw new TypeError(
        "DateAssert.timesAreEqual(): Expected and actual values must be Date objects.",
      );
    }
  },
};
YAHOO.namespace("util");
YAHOO.util.UserAction = {
  simulateKeyEvent: function (F, J, E, C, L, B, A, K, H, N, M) {
    F = YAHOO.util.Dom.get(F);
    if (!F) {
      throw new Error("simulateKeyEvent(): Invalid target.");
    }
    if (YAHOO.lang.isString(J)) {
      J = J.toLowerCase();
      switch (J) {
        case "keyup":
        case "keydown":
        case "keypress":
          break;
        case "textevent":
          J = "keypress";
          break;
        default:
          throw new Error(
            "simulateKeyEvent(): Event type '" + J + "' not supported.",
          );
      }
    } else {
      throw new Error("simulateKeyEvent(): Event type must be a string.");
    }
    if (!YAHOO.lang.isBoolean(E)) {
      E = true;
    }
    if (!YAHOO.lang.isBoolean(C)) {
      C = true;
    }
    if (!YAHOO.lang.isObject(L)) {
      L = window;
    }
    if (!YAHOO.lang.isBoolean(B)) {
      B = false;
    }
    if (!YAHOO.lang.isBoolean(A)) {
      A = false;
    }
    if (!YAHOO.lang.isBoolean(K)) {
      K = false;
    }
    if (!YAHOO.lang.isBoolean(H)) {
      H = false;
    }
    if (!YAHOO.lang.isNumber(N)) {
      N = 0;
    }
    if (!YAHOO.lang.isNumber(M)) {
      M = 0;
    }
    var I = null;
    if (YAHOO.lang.isFunction(document.createEvent)) {
      try {
        I = document.createEvent("KeyEvents");
        I.initKeyEvent(J, E, C, L, B, A, K, H, N, M);
      } catch (G) {
        try {
          I = document.createEvent("Events");
        } catch (D) {
          I = document.createEvent("UIEvents");
        } finally {
          I.initEvent(J, E, C);
          I.view = L;
          I.altKey = A;
          I.ctrlKey = B;
          I.shiftKey = K;
          I.metaKey = H;
          I.keyCode = N;
          I.charCode = M;
        }
      }
      F.dispatchEvent(I);
    } else {
      if (YAHOO.lang.isObject(document.createEventObject)) {
        I = document.createEventObject();
        I.bubbles = E;
        I.cancelable = C;
        I.view = L;
        I.ctrlKey = B;
        I.altKey = A;
        I.shiftKey = K;
        I.metaKey = H;
        I.keyCode = M > 0 ? M : N;
        F.fireEvent("on" + J, I);
      } else {
        throw new Error(
          "simulateKeyEvent(): No event simulation framework present.",
        );
      }
    }
  },
  simulateMouseEvent: function (
    K,
    P,
    H,
    E,
    Q,
    J,
    G,
    F,
    D,
    B,
    C,
    A,
    O,
    M,
    I,
    L,
  ) {
    K = YAHOO.util.Dom.get(K);
    if (!K) {
      throw new Error("simulateMouseEvent(): Invalid target.");
    }
    if (YAHOO.lang.isString(P)) {
      P = P.toLowerCase();
      switch (P) {
        case "mouseover":
        case "mouseout":
        case "mousedown":
        case "mouseup":
        case "click":
        case "dblclick":
        case "mousemove":
          break;
        default:
          throw new Error(
            "simulateMouseEvent(): Event type '" + P + "' not supported.",
          );
      }
    } else {
      throw new Error("simulateMouseEvent(): Event type must be a string.");
    }
    if (!YAHOO.lang.isBoolean(H)) {
      H = true;
    }
    if (!YAHOO.lang.isBoolean(E)) {
      E = P != "mousemove";
    }
    if (!YAHOO.lang.isObject(Q)) {
      Q = window;
    }
    if (!YAHOO.lang.isNumber(J)) {
      J = 1;
    }
    if (!YAHOO.lang.isNumber(G)) {
      G = 0;
    }
    if (!YAHOO.lang.isNumber(F)) {
      F = 0;
    }
    if (!YAHOO.lang.isNumber(D)) {
      D = 0;
    }
    if (!YAHOO.lang.isNumber(B)) {
      B = 0;
    }
    if (!YAHOO.lang.isBoolean(C)) {
      C = false;
    }
    if (!YAHOO.lang.isBoolean(A)) {
      A = false;
    }
    if (!YAHOO.lang.isBoolean(O)) {
      O = false;
    }
    if (!YAHOO.lang.isBoolean(M)) {
      M = false;
    }
    if (!YAHOO.lang.isNumber(I)) {
      I = 0;
    }
    var N = null;
    if (YAHOO.lang.isFunction(document.createEvent)) {
      N = document.createEvent("MouseEvents");
      if (N.initMouseEvent) {
        N.initMouseEvent(P, H, E, Q, J, G, F, D, B, C, A, O, M, I, L);
      } else {
        N = document.createEvent("UIEvents");
        N.initEvent(P, H, E);
        N.view = Q;
        N.detail = J;
        N.screenX = G;
        N.screenY = F;
        N.clientX = D;
        N.clientY = B;
        N.ctrlKey = C;
        N.altKey = A;
        N.metaKey = M;
        N.shiftKey = O;
        N.button = I;
        N.relatedTarget = L;
      }
      if (L && !N.relatedTarget) {
        if (P == "mouseout") {
          N.toElement = L;
        } else {
          if (P == "mouseover") {
            N.fromElement = L;
          }
        }
      }
      K.dispatchEvent(N);
    } else {
      if (YAHOO.lang.isObject(document.createEventObject)) {
        N = document.createEventObject();
        N.bubbles = H;
        N.cancelable = E;
        N.view = Q;
        N.detail = J;
        N.screenX = G;
        N.screenY = F;
        N.clientX = D;
        N.clientY = B;
        N.ctrlKey = C;
        N.altKey = A;
        N.metaKey = M;
        N.shiftKey = O;
        switch (I) {
          case 0:
            N.button = 1;
            break;
          case 1:
            N.button = 4;
            break;
          case 2:
            break;
          default:
            N.button = 0;
        }
        N.relatedTarget = L;
        K.fireEvent("on" + P, N);
      } else {
        throw new Error(
          "simulateMouseEvent(): No event simulation framework present.",
        );
      }
    }
  },
  fireMouseEvent: function (C, B, A) {
    A = A || {};
    this.simulateMouseEvent(
      C,
      B,
      A.bubbles,
      A.cancelable,
      A.view,
      A.detail,
      A.screenX,
      A.screenY,
      A.clientX,
      A.clientY,
      A.ctrlKey,
      A.altKey,
      A.shiftKey,
      A.metaKey,
      A.button,
      A.relatedTarget,
    );
  },
  click: function (B, A) {
    this.fireMouseEvent(B, "click", A);
  },
  dblclick: function (B, A) {
    this.fireMouseEvent(B, "dblclick", A);
  },
  mousedown: function (B, A) {
    this.fireMouseEvent(B, "mousedown", A);
  },
  mousemove: function (B, A) {
    this.fireMouseEvent(B, "mousemove", A);
  },
  mouseout: function (B, A) {
    this.fireMouseEvent(B, "mouseout", A);
  },
  mouseover: function (B, A) {
    this.fireMouseEvent(B, "mouseover", A);
  },
  mouseup: function (B, A) {
    this.fireMouseEvent(B, "mouseup", A);
  },
  fireKeyEvent: function (B, C, A) {
    A = A || {};
    this.simulateKeyEvent(
      C,
      B,
      A.bubbles,
      A.cancelable,
      A.view,
      A.ctrlKey,
      A.altKey,
      A.shiftKey,
      A.metaKey,
      A.keyCode,
      A.charCode,
    );
  },
  keydown: function (B, A) {
    this.fireKeyEvent("keydown", B, A);
  },
  keypress: function (B, A) {
    this.fireKeyEvent("keypress", B, A);
  },
  keyup: function (B, A) {
    this.fireKeyEvent("keyup", B, A);
  },
};
YAHOO.namespace("tool");
YAHOO.tool.TestManager = {
  TEST_PAGE_BEGIN_EVENT: "testpagebegin",
  TEST_PAGE_COMPLETE_EVENT: "testpagecomplete",
  TEST_MANAGER_BEGIN_EVENT: "testmanagerbegin",
  TEST_MANAGER_COMPLETE_EVENT: "testmanagercomplete",
  _curPage: null,
  _frame: null,
  _logger: null,
  _timeoutId: 0,
  _pages: [],
  _results: null,
  _handleTestRunnerComplete: function (A) {
    this.fireEvent(this.TEST_PAGE_COMPLETE_EVENT, {
      page: this._curPage,
      results: A.results,
    });
    this._processResults(this._curPage, A.results);
    this._logger.clearTestRunner();
    if (this._pages.length) {
      this._timeoutId = setTimeout(function () {
        YAHOO.tool.TestManager._run();
      }, 1000);
    } else {
      this.fireEvent(this.TEST_MANAGER_COMPLETE_EVENT, this._results);
    }
  },
  _processResults: function (C, A) {
    var B = this._results;
    B.passed += A.passed;
    B.failed += A.failed;
    B.ignored += A.ignored;
    B.total += A.total;
    B.duration += A.duration;
    if (A.failed) {
      B.failedPages.push(C);
    } else {
      B.passedPages.push(C);
    }
    A.name = C;
    A.type = "page";
    B[C] = A;
  },
  _run: function () {
    this._curPage = this._pages.shift();
    this.fireEvent(this.TEST_PAGE_BEGIN_EVENT, this._curPage);
    this._frame.location.replace(this._curPage);
  },
  load: function () {
    if (parent.YAHOO.tool.TestManager !== this) {
      parent.YAHOO.tool.TestManager.load();
    } else {
      if (this._frame) {
        var A = this._frame.YAHOO.tool.TestRunner;
        this._logger.setTestRunner(A);
        A.subscribe(
          A.COMPLETE_EVENT,
          this._handleTestRunnerComplete,
          this,
          true,
        );
        A.run();
      }
    }
  },
  setPages: function (A) {
    this._pages = A;
  },
  start: function () {
    if (!this._initialized) {
      this.createEvent(this.TEST_PAGE_BEGIN_EVENT);
      this.createEvent(this.TEST_PAGE_COMPLETE_EVENT);
      this.createEvent(this.TEST_MANAGER_BEGIN_EVENT);
      this.createEvent(this.TEST_MANAGER_COMPLETE_EVENT);
      if (!this._frame) {
        var A = document.createElement("iframe");
        A.style.visibility = "hidden";
        A.style.position = "absolute";
        document.body.appendChild(A);
        this._frame = A.contentWindow || A.contentDocument.parentWindow;
      }
      if (!this._logger) {
        this._logger = new YAHOO.tool.TestLogger();
      }
      this._initialized = true;
    }
    this._results = {
      passed: 0,
      failed: 0,
      ignored: 0,
      total: 0,
      type: "report",
      name: "YUI Test Results",
      duration: 0,
      failedPages: [],
      passedPages: [],
    };
    this.fireEvent(this.TEST_MANAGER_BEGIN_EVENT, null);
    this._run();
  },
  stop: function () {
    clearTimeout(this._timeoutId);
  },
};
YAHOO.lang.augmentObject(
  YAHOO.tool.TestManager,
  YAHOO.util.EventProvider.prototype,
);
YAHOO.namespace("tool");
YAHOO.tool.TestLogger = function (B, A) {
  YAHOO.tool.TestLogger.superclass.constructor.call(this, B, A);
  this.init();
};
YAHOO.lang.extend(YAHOO.tool.TestLogger, YAHOO.widget.LogReader, {
  footerEnabled: true,
  newestOnTop: false,
  formatMsg: function (B) {
    var A = B.category;
    var C = this.html2Text(B.msg);
    return (
      '<pre><p><span class="' +
      A +
      '">' +
      A.toUpperCase() +
      "</span> " +
      C +
      "</p></pre>"
    );
  },
  init: function () {
    if (YAHOO.tool.TestRunner) {
      this.setTestRunner(YAHOO.tool.TestRunner);
    }
    this.hideSource("global");
    this.hideSource("LogReader");
    this.hideCategory("warn");
    this.hideCategory("window");
    this.hideCategory("time");
    this.clearConsole();
  },
  clearTestRunner: function () {
    if (this._runner) {
      this._runner.unsubscribeAll();
      this._runner = null;
    }
  },
  setTestRunner: function (A) {
    if (this._runner) {
      this.clearTestRunner();
    }
    this._runner = A;
    A.subscribe(A.TEST_PASS_EVENT, this._handleTestRunnerEvent, this, true);
    A.subscribe(A.TEST_FAIL_EVENT, this._handleTestRunnerEvent, this, true);
    A.subscribe(A.TEST_IGNORE_EVENT, this._handleTestRunnerEvent, this, true);
    A.subscribe(A.BEGIN_EVENT, this._handleTestRunnerEvent, this, true);
    A.subscribe(A.COMPLETE_EVENT, this._handleTestRunnerEvent, this, true);
    A.subscribe(
      A.TEST_SUITE_BEGIN_EVENT,
      this._handleTestRunnerEvent,
      this,
      true,
    );
    A.subscribe(
      A.TEST_SUITE_COMPLETE_EVENT,
      this._handleTestRunnerEvent,
      this,
      true,
    );
    A.subscribe(
      A.TEST_CASE_BEGIN_EVENT,
      this._handleTestRunnerEvent,
      this,
      true,
    );
    A.subscribe(
      A.TEST_CASE_COMPLETE_EVENT,
      this._handleTestRunnerEvent,
      this,
      true,
    );
  },
  _handleTestRunnerEvent: function (D) {
    var A = YAHOO.tool.TestRunner;
    var C = "";
    var B = "";
    switch (D.type) {
      case A.BEGIN_EVENT:
        C = "Testing began at " + new Date().toString() + ".";
        B = "info";
        break;
      case A.COMPLETE_EVENT:
        C =
          "Testing completed at " +
          new Date().toString() +
          ".\nPassed:" +
          D.results.passed +
          " Failed:" +
          D.results.failed +
          " Total:" +
          D.results.total;
        B = "info";
        break;
      case A.TEST_FAIL_EVENT:
        C = D.testName + ": " + D.error.getMessage();
        B = "fail";
        break;
      case A.TEST_IGNORE_EVENT:
        C = D.testName + ": ignored.";
        B = "ignore";
        break;
      case A.TEST_PASS_EVENT:
        C = D.testName + ": passed.";
        B = "pass";
        break;
      case A.TEST_SUITE_BEGIN_EVENT:
        C = 'Test suite "' + D.testSuite.name + '" started.';
        B = "info";
        break;
      case A.TEST_SUITE_COMPLETE_EVENT:
        C =
          'Test suite "' +
          D.testSuite.name +
          '" completed.\nPassed:' +
          D.results.passed +
          " Failed:" +
          D.results.failed +
          " Total:" +
          D.results.total;
        B = "info";
        break;
      case A.TEST_CASE_BEGIN_EVENT:
        C = 'Test case "' + D.testCase.name + '" started.';
        B = "info";
        break;
      case A.TEST_CASE_COMPLETE_EVENT:
        C =
          'Test case "' +
          D.testCase.name +
          '" completed.\nPassed:' +
          D.results.passed +
          " Failed:" +
          D.results.failed +
          " Total:" +
          D.results.total;
        B = "info";
        break;
      default:
        C = "Unexpected event " + D.type;
        C = "info";
    }
    YAHOO.log(C, B, "TestRunner");
  },
});
YAHOO.namespace("tool.TestFormat");
YAHOO.tool.TestFormat.JSON = function (A) {
  return YAHOO.lang.JSON.stringify(A);
};
YAHOO.tool.TestFormat.XML = function (C) {
  var A = YAHOO.lang;
  var B =
    "<" +
    C.type +
    ' name="' +
    C.name.replace(/"/g, "&quot;").replace(/'/g, "&apos;") +
    '"';
  if (A.isNumber(C.duration)) {
    B += ' duration="' + C.duration + '"';
  }
  if (C.type == "test") {
    B += ' result="' + C.result + '" message="' + C.message + '">';
  } else {
    B +=
      ' passed="' +
      C.passed +
      '" failed="' +
      C.failed +
      '" ignored="' +
      C.ignored +
      '" total="' +
      C.total +
      '">';
    for (var D in C) {
      if (A.hasOwnProperty(C, D) && A.isObject(C[D]) && !A.isArray(C[D])) {
        B += arguments.callee(C[D]);
      }
    }
  }
  B += "</" + C.type + ">";
  return B;
};
YAHOO.namespace("tool");
YAHOO.tool.TestReporter = function (A, B) {
  this.url = A;
  this.format = B || YAHOO.tool.TestFormat.XML;
  this._fields = new Object();
  this._form = null;
  this._iframe = null;
};
YAHOO.tool.TestReporter.prototype = {
  constructor: YAHOO.tool.TestReporter,
  _convertToISOString: function (A) {
    function B(C) {
      return C < 10 ? "0" + C : C;
    }

    return (
      A.getUTCFullYear() +
      "-" +
      B(A.getUTCMonth() + 1) +
      "-" +
      B(A.getUTCDate()) +
      "T" +
      B(A.getUTCHours()) +
      ":" +
      B(A.getUTCMinutes()) +
      ":" +
      B(A.getUTCSeconds()) +
      "Z"
    );
  },
  addField: function (A, B) {
    this._fields[A] = B;
  },
  clearFields: function () {
    this._fields = new Object();
  },
  destroy: function () {
    if (this._form) {
      this._form.parentNode.removeChild(this._form);
      this._form = null;
    }
    if (this._iframe) {
      this._iframe.parentNode.removeChild(this._iframe);
      this._iframe = null;
    }
    this._fields = null;
  },
  report: function (A) {
    if (!this._form) {
      this._form = document.createElement("form");
      this._form.method = "post";
      this._form.style.visibility = "hidden";
      this._form.style.position = "absolute";
      this._form.style.top = 0;
      document.body.appendChild(this._form);
      if (YAHOO.env.ua.ie) {
        this._iframe = document.createElement(
          '<iframe name="yuiTestTarget" />',
        );
      } else {
        this._iframe = document.createElement("iframe");
        this._iframe.name = "yuiTestTarget";
      }
      this._iframe.src = "javascript:false";
      this._iframe.style.visibility = "hidden";
      this._iframe.style.position = "absolute";
      this._iframe.style.top = 0;
      document.body.appendChild(this._iframe);
      this._form.target = "yuiTestTarget";
    }
    this._form.action = this.url;
    while (this._form.hasChildNodes()) {
      this._form.removeChild(this._form.lastChild);
    }
    this._fields.results = this.format(A);
    this._fields.useragent = navigator.userAgent;
    this._fields.timestamp = this._convertToISOString(new Date());
    for (var B in this._fields) {
      if (
        YAHOO.lang.hasOwnProperty(this._fields, B) &&
        typeof this._fields[B] != "function"
      ) {
        if (YAHOO.env.ua.ie) {
          input = document.createElement('<input name="' + B + '" >');
        } else {
          input = document.createElement("input");
          input.name = B;
        }
        input.type = "hidden";
        input.value = this._fields[B];
        this._form.appendChild(input);
      }
    }
    delete this._fields.results;
    delete this._fields.useragent;
    delete this._fields.timestamp;
    if (arguments[1] !== false) {
      this._form.submit();
    }
  },
};
YAHOO.register("yuitest", YAHOO.tool.TestRunner, {
  version: "2.7.0",
  build: "1799",
});

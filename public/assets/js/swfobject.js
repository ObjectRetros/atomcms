var swfobject = (function () {
    function B() {
        if (!t && document.getElementsByTagName("body")[0]) {
            try {
                var a,
                    b = U("span");
                (b.style.display = "none"),
                    (a = i.getElementsByTagName("body")[0].appendChild(b)),
                    a.parentNode.removeChild(a),
                    (a = null),
                    (b = null);
            } catch (a) {
                return;
            }
            t = !0;
            for (var c = l.length, d = 0; d < c; d++) l[d]();
        }
    }
    function C(a) {
        t ? a() : (l[l.length] = a);
    }
    function D(b) {
        if (typeof h.addEventListener != a) h.addEventListener("load", b, !1);
        else if (typeof i.addEventListener != a)
            i.addEventListener("load", b, !1);
        else if (typeof h.attachEvent != a) W(h, "onload", b);
        else if ("function" == typeof h.onload) {
            var c = h.onload;
            h.onload = function () {
                c(), b();
            };
        } else h.onload = b;
    }
    function E() {
        var c = i.getElementsByTagName("body")[0],
            d = U(b);
        d.setAttribute("style", "visibility: hidden;"),
            d.setAttribute("type", e);
        var f = c.appendChild(d);
        if (f) {
            var g = 0;
            !(function b() {
                if (typeof f.GetVariable != a)
                    try {
                        var e = f.GetVariable("$version");
                        e &&
                            ((e = e.split(" ")[1].split(",")),
                            (z.pv = [V(e[0]), V(e[1]), V(e[2])]));
                    } catch (a) {
                        z.pv = [8, 0, 0];
                    }
                else if (g < 10) return g++, void setTimeout(b, 10);
                c.removeChild(d), (f = null), F();
            })();
        } else F();
    }
    function F() {
        var b = m.length;
        if (b > 0)
            for (var c = 0; c < b; c++) {
                var d = m[c].id,
                    e = m[c].callbackFn,
                    f = { success: !1, id: d };
                if (z.pv[0] > 0) {
                    var g = T(d);
                    if (g)
                        if (!X(m[c].swfVersion) || (z.wk && z.wk < 312))
                            if (m[c].expressInstall && H()) {
                                var h = {};
                                (h.data = m[c].expressInstall),
                                    (h.width = g.getAttribute("width") || "0"),
                                    (h.height =
                                        g.getAttribute("height") || "0"),
                                    g.getAttribute("class") &&
                                        (h.styleclass =
                                            g.getAttribute("class")),
                                    g.getAttribute("align") &&
                                        (h.align = g.getAttribute("align"));
                                for (
                                    var i = {},
                                        j = g.getElementsByTagName("param"),
                                        k = j.length,
                                        l = 0;
                                    l < k;
                                    l++
                                )
                                    "movie" !=
                                        j[l]
                                            .getAttribute("name")
                                            .toLowerCase() &&
                                        (i[j[l].getAttribute("name")] =
                                            j[l].getAttribute("value"));
                                I(h, i, d, e);
                            } else J(g), e && e(f);
                        else
                            Z(d, !0),
                                e &&
                                    ((f.success = !0),
                                    (f.ref = G(d)),
                                    (f.id = d),
                                    e(f));
                } else if ((Z(d, !0), e)) {
                    var n = G(d);
                    n &&
                        typeof n.SetVariable != a &&
                        ((f.success = !0), (f.ref = n), (f.id = n.id)),
                        e(f);
                }
            }
    }
    function G(c) {
        var d = null,
            e = T(c);
        return (
            e &&
                "OBJECT" === e.nodeName.toUpperCase() &&
                (d =
                    typeof e.SetVariable !== a
                        ? e
                        : e.getElementsByTagName(b)[0] || e),
            d
        );
    }
    function H() {
        return !u && X("6.0.65") && (z.win || z.mac) && !(z.wk && z.wk < 312);
    }
    function I(b, c, d, e) {
        var g = T(d);
        if (
            ((d = S(d)),
            (u = !0),
            (r = e || null),
            (s = { success: !1, id: d }),
            g)
        ) {
            "OBJECT" == g.nodeName.toUpperCase()
                ? ((p = K(g)), (q = null))
                : ((p = g), (q = d)),
                (b.id = f),
                (typeof b.width == a ||
                    (!/%$/.test(b.width) && V(b.width) < 310)) &&
                    (b.width = "310"),
                (typeof b.height == a ||
                    (!/%$/.test(b.height) && V(b.height) < 137)) &&
                    (b.height = "137");
            var j = z.ie ? "ActiveX" : "PlugIn",
                k =
                    "MMredirectURL=" +
                    encodeURIComponent(
                        h.location.toString().replace(/&/g, "%26")
                    ) +
                    "&MMplayerType=" +
                    j +
                    "&MMdoctitle=" +
                    encodeURIComponent(
                        i.title.slice(0, 47) + " - Flash Player Installation"
                    );
            if (
                (typeof c.flashvars != a
                    ? (c.flashvars += "&" + k)
                    : (c.flashvars = k),
                z.ie && 4 != g.readyState)
            ) {
                var l = U("div");
                (d += "SWFObjectNew"),
                    l.setAttribute("id", d),
                    g.parentNode.insertBefore(l, g),
                    (g.style.display = "none"),
                    Q(g);
            }
            O(b, c, d);
        }
    }
    function J(a) {
        if (z.ie && 4 != a.readyState) {
            a.style.display = "none";
            var b = U("div");
            a.parentNode.insertBefore(b, a),
                b.parentNode.replaceChild(K(a), b),
                Q(a);
        } else a.parentNode.replaceChild(K(a), a);
    }
    function K(a) {
        var c = U("div");
        if (z.win && z.ie) c.innerHTML = a.innerHTML;
        else {
            var d = a.getElementsByTagName(b)[0];
            if (d) {
                var e = d.childNodes;
                if (e)
                    for (var f = e.length, g = 0; g < f; g++)
                        (1 == e[g].nodeType && "PARAM" == e[g].nodeName) ||
                            8 == e[g].nodeType ||
                            c.appendChild(e[g].cloneNode(!0));
            }
        }
        return c;
    }
    function L(a, b) {
        var c = U("div");
        return (
            (c.innerHTML =
                "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'><param name='movie' value='" +
                a +
                "'>" +
                b +
                "</object>"),
            c.firstChild
        );
    }
    function O(c, d, f) {
        var g,
            h = T(f);
        if (((f = S(f)), z.wk && z.wk < 312)) return g;
        if (h) {
            var j,
                k,
                l,
                i = U(z.ie ? "div" : b);
            typeof c.id == a && (c.id = f);
            for (l in d)
                d.hasOwnProperty(l) &&
                    "movie" !== l.toLowerCase() &&
                    P(i, l, d[l]);
            z.ie && (i = L(c.data, i.innerHTML));
            for (j in c)
                c.hasOwnProperty(j) &&
                    ((k = j.toLowerCase()),
                    "styleclass" === k
                        ? i.setAttribute("class", c[j])
                        : "classid" !== k &&
                          "data" !== k &&
                          i.setAttribute(j, c[j]));
            z.ie
                ? (n[n.length] = c.id)
                : (i.setAttribute("type", e), i.setAttribute("data", c.data)),
                h.parentNode.replaceChild(i, h),
                (g = i);
        }
        return g;
    }
    function P(a, b, c) {
        var d = U("param");
        d.setAttribute("name", b), d.setAttribute("value", c), a.appendChild(d);
    }
    function Q(a) {
        var b = T(a);
        b &&
            "OBJECT" == b.nodeName.toUpperCase() &&
            (z.ie
                ? ((b.style.display = "none"),
                  (function a() {
                      if (4 == b.readyState) {
                          for (var c in b)
                              "function" == typeof b[c] && (b[c] = null);
                          b.parentNode.removeChild(b);
                      } else setTimeout(a, 10);
                  })())
                : b.parentNode.removeChild(b));
    }
    function R(a) {
        return a && a.nodeType && 1 === a.nodeType;
    }
    function S(a) {
        return R(a) ? a.id : a;
    }
    function T(a) {
        if (R(a)) return a;
        var b = null;
        try {
            b = i.getElementById(a);
        } catch (a) {}
        return b;
    }
    function U(a) {
        return i.createElement(a);
    }
    function V(a) {
        return parseInt(a, 10);
    }
    function W(a, b, c) {
        a.attachEvent(b, c), (o[o.length] = [a, b, c]);
    }
    function X(a) {
        a += "";
        var b = z.pv,
            c = a.split(".");
        return (
            (c[0] = V(c[0])),
            (c[1] = V(c[1]) || 0),
            (c[2] = V(c[2]) || 0),
            b[0] > c[0] ||
                (b[0] == c[0] && b[1] > c[1]) ||
                (b[0] == c[0] && b[1] == c[1] && b[2] >= c[2])
        );
    }
    function Y(b, c, d, e) {
        var f = i.getElementsByTagName("head")[0];
        if (f) {
            var g = "string" == typeof d ? d : "screen";
            if ((e && ((v = null), (w = null)), !v || w != g)) {
                var h = U("style");
                h.setAttribute("type", "text/css"),
                    h.setAttribute("media", g),
                    (v = f.appendChild(h)),
                    z.ie &&
                        typeof i.styleSheets != a &&
                        i.styleSheets.length > 0 &&
                        (v = i.styleSheets[i.styleSheets.length - 1]),
                    (w = g);
            }
            v &&
                (typeof v.addRule != a
                    ? v.addRule(b, c)
                    : typeof i.createTextNode != a &&
                      v.appendChild(i.createTextNode(b + " {" + c + "}")));
        }
    }
    function Z(a, b) {
        if (x) {
            var c = b ? "visible" : "hidden",
                d = T(a);
            t && d
                ? (d.style.visibility = c)
                : "string" == typeof a && Y("#" + a, "visibility:" + c);
        }
    }
    function $(b) {
        return null != /[\\\"<>\.;]/.exec(b) && typeof encodeURIComponent != a
            ? encodeURIComponent(b)
            : b;
    }
    var p,
        q,
        r,
        s,
        v,
        w,
        a = "undefined",
        b = "object",
        c = "Shockwave Flash",
        d = "ShockwaveFlash.ShockwaveFlash",
        e = "application/x-shockwave-flash",
        f = "SWFObjectExprInst",
        g = "onreadystatechange",
        h = window,
        i = document,
        j = navigator,
        k = !1,
        l = [],
        m = [],
        n = [],
        o = [],
        t = !1,
        u = !1,
        x = !0,
        y = !1,
        z = (function () {
            var f =
                    typeof i.getElementById != a &&
                    typeof i.getElementsByTagName != a &&
                    typeof i.createElement != a,
                g = j.userAgent.toLowerCase(),
                l = j.platform.toLowerCase(),
                m = /win/.test(l ? l : g),
                n = /mac/.test(l ? l : g),
                o =
                    !!/webkit/.test(g) &&
                    parseFloat(g.replace(/^.*webkit\/(\d+(\.\d+)?).*$/, "$1")),
                p = "Microsoft Internet Explorer" === j.appName,
                q = [0, 0, 0],
                r = null;
            if (typeof j.plugins != a && typeof j.plugins[c] == b)
                (r = j.plugins[c].description) &&
                    typeof j.mimeTypes != a &&
                    j.mimeTypes[e] &&
                    j.mimeTypes[e].enabledPlugin &&
                    ((k = !0),
                    (p = !1),
                    (r = r.replace(/^.*\s+(\S+\s+\S+$)/, "$1")),
                    (q[0] = V(r.replace(/^(.*)\..*$/, "$1"))),
                    (q[1] = V(r.replace(/^.*\.(.*)\s.*$/, "$1"))),
                    (q[2] = /[a-zA-Z]/.test(r)
                        ? V(r.replace(/^.*[a-zA-Z]+(.*)$/, "$1"))
                        : 0));
            else if (typeof h.ActiveXObject != a)
                try {
                    var s = new ActiveXObject(d);
                    s &&
                        (r = s.GetVariable("$version")) &&
                        ((p = !0),
                        (r = r.split(" ")[1].split(",")),
                        (q = [V(r[0]), V(r[1]), V(r[2])]));
                } catch (a) {}
            return { w3: f, pv: q, wk: o, ie: p, win: m, mac: n };
        })();
    !(function () {
        z.w3 &&
            (((typeof i.readyState != a &&
                ("complete" === i.readyState ||
                    "interactive" === i.readyState)) ||
                (typeof i.readyState == a &&
                    (i.getElementsByTagName("body")[0] || i.body))) &&
                B(),
            t ||
                (typeof i.addEventListener != a &&
                    i.addEventListener("DOMContentLoaded", B, !1),
                z.ie &&
                    (i.attachEvent(g, function a() {
                        "complete" == i.readyState &&
                            (i.detachEvent(g, a), B());
                    }),
                    h == top &&
                        (function a() {
                            if (!t) {
                                try {
                                    i.documentElement.doScroll("left");
                                } catch (b) {
                                    return void setTimeout(a, 0);
                                }
                                B();
                            }
                        })()),
                z.wk &&
                    (function a() {
                        if (!t)
                            return /loaded|complete/.test(i.readyState)
                                ? void B()
                                : void setTimeout(a, 0);
                    })()));
    })();
    l[0] = function () {
        k ? E() : F();
    };
    !(function () {
        z.ie &&
            window.attachEvent("onunload", function () {
                for (var a = o.length, b = 0; b < a; b++)
                    o[b][0].detachEvent(o[b][1], o[b][2]);
                for (var c = n.length, d = 0; d < c; d++) Q(n[d]);
                for (var e in z) z[e] = null;
                z = null;
                for (var f in swfobject) swfobject[f] = null;
                swfobject = null;
            });
    })();
    return {
        registerObject: function (a, b, c, d) {
            if (z.w3 && a && b) {
                var e = {};
                (e.id = a),
                    (e.swfVersion = b),
                    (e.expressInstall = c),
                    (e.callbackFn = d),
                    (m[m.length] = e),
                    Z(a, !1);
            } else d && d({ success: !1, id: a });
        },
        getObjectById: function (a) {
            if (z.w3) return G(a);
        },
        embedSWF: function (c, d, e, f, g, h, i, j, k, l) {
            var m = S(d),
                n = { success: !1, id: m };
            z.w3 && !(z.wk && z.wk < 312) && c && d && e && f && g
                ? (Z(m, !1),
                  C(function () {
                      (e += ""), (f += "");
                      var o = {};
                      if (k && typeof k === b) for (var p in k) o[p] = k[p];
                      (o.data = c), (o.width = e), (o.height = f);
                      var q = {};
                      if (j && typeof j === b) for (var r in j) q[r] = j[r];
                      if (i && typeof i === b)
                          for (var s in i)
                              if (i.hasOwnProperty(s)) {
                                  var t = y ? encodeURIComponent(s) : s,
                                      u = y ? encodeURIComponent(i[s]) : i[s];
                                  typeof q.flashvars != a
                                      ? (q.flashvars += "&" + t + "=" + u)
                                      : (q.flashvars = t + "=" + u);
                              }
                      if (X(g)) {
                          var v = O(o, q, d);
                          o.id == m && Z(m, !0),
                              (n.success = !0),
                              (n.ref = v),
                              (n.id = v.id);
                      } else {
                          if (h && H()) return (o.data = h), void I(o, q, d, l);
                          Z(m, !0);
                      }
                      l && l(n);
                  }))
                : l && l(n);
        },
        switchOffAutoHideShow: function () {
            x = !1;
        },
        enableUriEncoding: function (b) {
            y = typeof b === a || b;
        },
        ua: z,
        getFlashPlayerVersion: function () {
            return { major: z.pv[0], minor: z.pv[1], release: z.pv[2] };
        },
        hasFlashPlayerVersion: X,
        createSWF: function (a, b, c) {
            return z.w3 ? O(a, b, c) : void 0;
        },
        showExpressInstall: function (a, b, c, d) {
            z.w3 && H() && I(a, b, c, d);
        },
        removeSWF: function (a) {
            z.w3 && Q(a);
        },
        createCSS: function (a, b, c, d) {
            z.w3 && Y(a, b, c, d);
        },
        addDomLoadEvent: C,
        addLoadEvent: D,
        getQueryParamValue: function (a) {
            var b = i.location.search || i.location.hash;
            if (b) {
                if ((/\?/.test(b) && (b = b.split("?")[1]), null == a))
                    return $(b);
                for (var c = b.split("&"), d = 0; d < c.length; d++)
                    if (c[d].substring(0, c[d].indexOf("=")) == a)
                        return $(c[d].substring(c[d].indexOf("=") + 1));
            }
            return "";
        },
        expressInstallCallback: function () {
            if (u) {
                var a = T(f);
                a &&
                    p &&
                    (a.parentNode.replaceChild(p, a),
                    q && (Z(q, !0), z.ie && (p.style.display = "block")),
                    r && r(s)),
                    (u = !1);
            }
        },
        version: "2.3",
    };
})();

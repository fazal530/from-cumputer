!(function (e, t) {
  "object" == typeof exports && "undefined" != typeof module ? t(exports) : "function" == typeof define && define.amd ? define(["exports"], t) : t(((e = "undefined" != typeof globalThis ? globalThis : e || self).SlideElement = {}));
})(this, function (e) {
  "use strict";
  let t = { easing: "ease", duration: 250, fill: "backwards", display: "block", overflow: "hidden" },
      n = ["overflow", "display"],
      o = (e, o) => {
          let i = Object.assign({}, t, o),
              d = i.display,
              a = (t) => (e.style.display = t),
              l = () => e.clientHeight + "px",
              r = (t) => (e.style.overflow = t ? i.overflow : ""),
              s = () => e.getAnimations(),
              p = (t, o) => {
                  var d;
                  n.forEach((e) => delete i[e]);
                  let a = l(),
                      r = [a, o].map((e) => ({ height: e, paddingTop: "0px", paddingBottom: "0px" })),
                      { paddingTop: s, paddingBottom: p } = window.getComputedStyle(e);
                  (r[0].paddingTop = s), (r[0].paddingBottom = p), t && ((r[0].height = a), r.reverse()), (null == (d = window.matchMedia("(prefers-reduced-motion: reduce)")) ? void 0 : d.matches) && (i.duration = 0);
                  let u = e.animate(r, i);
                  return (u.id = (+t).toString()), u;
              },
              u = async (e) => {
                  let t = s().map((e) => e.finish());
                  var n;
                  return (
                      await ((n = async (t) => {
                          let n = e ? l() : "0px";
                          e && a(d), r(!0), await p(e, n).finished, r(!1), e || a("none"), t();
                      }),
                      new Promise((e) => {
                          requestAnimationFrame(() => {
                              requestAnimationFrame(() => {
                                  n(e);
                              });
                          });
                      })),
                      t.length ? null : e
                  );
              },
              g = async () => u(!1),
              f = async () => u(!0);
          return {
              up: g,
              down: f,
              toggle: async () => {
                  var t;
                  let n = null == (t = s()[0]) ? void 0 : t.id;
                  return ((n ? "1" === n : e.offsetHeight) ? g : f)();
              },
          };
      };
  (e.down = (e, t = {}) => o(e, t).down()), (e.toggle = (e, t = {}) => o(e, t).toggle()), (e.up = (e, t = {}) => o(e, t).up()), Object.defineProperty(e, "__esModule", { value: !0 }), (e[Symbol.toStringTag] = "Module");
});

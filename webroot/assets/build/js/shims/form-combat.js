webshims.register("form-combat", function(a, b){"use strict"; function c(b){a('select:not(.ui-select-nativeonly), input[type="radio"], input[type="checkbox"]', b).each(c.detectReplacement)}var d = {}, e = function(c, e, f){a.fn[c] && ("object" == typeof e && (f = e, e = c), d[e] = f, b.info("detected use of " + c + " try to add support."))}; a.fn.select2 && e("select2", {shadow:a.fn.select2.amd?"$container":"container", shadowFocus:a.fn.select2.amd?"$selection":"focusser", _create:function(c, d, e, f){var g; "$dropdown"in f?(g = function(g){b.wsPopover.isInElement([c, d, e, a(f.$dropdown)], g.target) || a(c).trigger("updatevalidation.webshims")}, a(d).on("wsallowinstantvalidation", function(b, d){return a(document).off("focusin", g), "focusout" == d.type && d.target != c?(a(document).on("focusin", g), !1):void 0})):"container"in f && a.isFunction(f.opened) && (g = function(g){b.wsPopover.isInElement([c, d, e, a(f.container)], g.target) || a(c).trigger("updatevalidation.webshims")}, a(d).on("wsallowinstantvalidation", function(b, d){return a(document).off("focusin", g), "focusout" == d.type && d.target != c && f.opened()?(a(document).on("focusin", g), !1):void 0}))}}), e("chosen", {shadow:"container", shadowFocus:"search_field"}), e("selectpicker", {shadow:"$newElement", shadowFocus:"$button", _create:function(c, d, e, f){if ("$menu"in f){var g = function(g){b.wsPopover.isInElement([c, d, e, a(f.$menu)], g.target) || a(c).trigger("updatevalidation.webshims")}; a(d).on("wsallowinstantvalidation", function(b, d){return a(document).off("focusin", g), "focusout" == d.type && d.target != c?(a(document).on("focusin", g), !1):void 0})}}}), e("selectBoxIt", {shadow:"dropdownContainer", shadowFocus:"dropdown"}), e("checkboxradio", "mobileCheckboxradio", {shadow:"label", shadowFocus:"element"}); var f = {shadow:"button", shadowFocus:function(a){return a.options.nativeMenu?a.element:a.button}, _create:function(c, d, e, f){var g; if ("menu"in f?g = "menuName":"listbox"in f && (g = "listbox"), g){var h = function(h){b.wsPopover.isInElement([c, d, e, a(f[g]).parent()], h.target) || a(c).trigger("updatevalidation.webshims")}; a(d).on("wsallowinstantvalidation", function(b, d){return"focusout" == d.type && d.target != c && f.isOpen?(setTimeout(function(){a(document).off("focusin", h).on("focusin", h)}, 1), !1):void 0})}}}; e("selectmenu", "mobileSelectmenu", f), e("selectmenu", "uiSelectmenu", f), c.register = function(c, e, f, g){var h = "string" == typeof f.shadow?e[f.shadow]:f.shadow(e, c), i = "string" == typeof f.shadowFocus?e[f.shadowFocus]:f.shadowFocus(e, c); i || (i = h), h && (d.success || a(i).attr("tabindex") || a(i).prop("tabIndex") > - 1)?(b.addShadowDom(c, h, {shadowFocusElement:i}), f._create && f._create(c, h, i, e), d.success = !0):(b.error("webshim could not add support for " + g), g in d && delete d[g])}, c.detectReplacement = function(){var e, f = a(this).data(); if (f && !(b.data(this) || {}).hasShadow)for (e in d)if (f[e]){c.register(this, f[e], d[e], e); break}}, b.addReady(function(a){setTimeout(function(){c(a)}, 4)})});
/*
	jQuery List DragSort v0.3.10
	Website: http://dragsort.codeplex.com/
	License: http://dragsort.codeplex.com/license
*/

(function($) {

	$.fn.dragsort = function(options) {
		var opts = $.extend({}, $.fn.dragsort.defaults, options);
		var lists = new Array();
		var list = null, lastPos = null;
		if (this.selector)
			$("head").append("<style type='text/css'>" + (this.selector.split(",").join(" " + opts.dragSelector + ",") + " " + opts.dragSelector) + " { cursor: pointer; }</style>");

		this.each(function(i, cont) {

			if ($(cont).is("table") && $(cont).children().size() == 1 && $(cont).children().is("tbody"))
				cont = $(cont).children().get(0);

			var newList = {
				draggedItem: null,
				placeHolderItem: null,
				pos: null,
				offset: null,
				offsetLimit: null,
				container: cont,

				init: function() {
					$(this.container).attr("listIdx", i).mousedown(this.grabItem).find(opts.dragSelector).css("cursor", "pointer");
				},

				grabItem: function(e) {
					if (e.button == 2 || $(e.target).is(opts.dragSelectorExclude))
						return;

					var elm = e.target;
					while (!$(elm).is("[listIdx=" + $(this).attr("listIdx") + "] " + opts.dragSelector)) {
						if (elm == this) return;
						elm = elm.parentNode;
					}

					if (list != null && list.draggedItem != null)
						list.dropItem();

					$(e.target).css("cursor", "move");

					list = lists[$(this).attr("listIdx")];
					list.draggedItem = $(elm).closest(opts.itemSelector);
					var mt = parseInt(list.draggedItem.css("marginTop"));
					var ml = parseInt(list.draggedItem.css("marginLeft"));
					list.offset = list.draggedItem.offset();
					list.offset.top = e.pageY - list.offset.top + (isNaN(mt) ? 0 : mt) - 1;
					list.offset.left = e.pageX - list.offset.left + (isNaN(ml) ? 0 : ml) - 1;

					if (!opts.dragBetween) {
						var containerHeight = $(list.container).outerHeight() == 0 ? Math.max(1, Math.round(0.5 + $(list.container).children(opts.itemSelector).size() * list.draggedItem.outerWidth() / $(list.container).outerWidth())) * list.draggedItem.outerHeight() : $(list.container).outerHeight();
						list.offsetLimit = $(list.container).offset();
						list.offsetLimit.right = list.offsetLimit.left + $(list.container).outerWidth() - list.draggedItem.outerWidth();
						list.offsetLimit.bottom = list.offsetLimit.top + containerHeight - list.draggedItem.outerHeight();
					}

					list.draggedItem.css({ position: "absolute", opacity: 0.8, "z-index": 999 }).after(opts.placeHolderTemplate);
					list.placeHolderItem = list.draggedItem.next().css("height", list.draggedItem.height()).attr("placeHolder", true);

					$(lists).each(function(i, l) { l.ensureNotEmpty(); l.buildPositionTable(); });

					list.setPos(e.pageX, e.pageY);
					$(document).bind("selectstart", list.stopBubble); //stop ie text selection
					$(document).bind("mousemove", list.swapItems);
					$(document).bind("mouseup", list.dropItem);
					return false; //stop moz text selection
				},

				setPos: function(x, y) {
					var top = y - this.offset.top;
					var left = x - this.offset.left;

					if (!opts.dragBetween) {
						top = Math.min(this.offsetLimit.bottom, Math.max(top, this.offsetLimit.top));
						left = Math.min(this.offsetLimit.right, Math.max(left, this.offsetLimit.left));
					}

					this.draggedItem.parents().each(function() {
						if ($(this).css("position") != "static" && (!$.browser.mozilla || $(this).css("display") != "table")) {
							var offset = $(this).offset();
							top -= offset.top;
							left -= offset.left;
							return false;
						}
					});

					this.draggedItem.css({ top: top, left: left });
				},

				buildPositionTable: function() {
					var item = this.draggedItem == null ? null : this.draggedItem.get(0);
					var pos = new Array();
					$(this.container).children(opts.itemSelector).each(function(i, elm) {
						if (elm != item) {
							var loc = $(elm).offset();
							loc.right = loc.left + $(elm).width();
							loc.bottom = loc.top + $(elm).height();
							loc.elm = elm;
							pos.push(loc);
						}
					});
					this.pos = pos;
				},

				dropItem: function() {
					if (list.draggedItem == null)
						return;

					$(list.container).find(opts.dragSelector).css("cursor", "pointer");
					list.placeHolderItem.before(list.draggedItem);

					list.draggedItem.css({ position: "", top: "", left: "", opacity: "", "z-index": "" });
					list.placeHolderItem.remove();

					$("*[emptyPlaceHolder]").remove();

					opts.dragEnd.apply(list.draggedItem);
					list.draggedItem = null;
					$(document).unbind("selectstart", list.stopBubble);
					$(document).unbind("mousemove", list.swapItems);
					$(document).unbind("mouseup", list.dropItem);
					return false;
				},

				stopBubble: function() { return false; },

				swapItems: function(e) {
					if (list.draggedItem == null)
						return false;

					list.setPos(e.pageX, e.pageY);

					var ei = list.findPos(e.pageX, e.pageY);
					var nlist = list;
					for (var i = 0; ei == -1 && opts.dragBetween && i < lists.length; i++) {
						ei = lists[i].findPos(e.pageX, e.pageY);
						nlist = lists[i];
					}

					if (ei == -1 || $(nlist.pos[ei].elm).attr("placeHolder"))
						return false;

					if (lastPos == null || lastPos.top > list.draggedItem.offset().top || lastPos.left > list.draggedItem.offset().left)
						$(nlist.pos[ei].elm).before(list.placeHolderItem);
					else
						$(nlist.pos[ei].elm).after(list.placeHolderItem);

					$(lists).each(function(i, l) { l.ensureNotEmpty(); l.buildPositionTable(); });
					lastPos = list.draggedItem.offset();
					return false;
				},

				findPos: function(x, y) {
					for (var i = 0; i < this.pos.length; i++) {
						if (this.pos[i].left < x && this.pos[i].right > x && this.pos[i].top < y && this.pos[i].bottom > y)
							return i;
					}
					return -1;
				},

				ensureNotEmpty: function() {
					if (!opts.dragBetween)
						return;

					var item = this.draggedItem == null ? null : this.draggedItem.get(0);
					var emptyPH = null, empty = true;

					$(this.container).children(opts.itemSelector).each(function(i, elm) {
						if ($(elm).attr("emptyPlaceHolder"))
							emptyPH = elm;
						else if (elm != item)
							empty = false;
					});

					if (empty && emptyPH == null)
						$(this.container).append(opts.placeHolderTemplate).children(":last").attr("emptyPlaceHolder", true);
					else if (!empty && emptyPH != null)
						$(emptyPH).remove();
				}
			};

			newList.init();
			lists.push(newList);
		});

		return this;
	};

	$.fn.dragsort.defaults = {
		itemSelector: "li",
		dragSelector: "li",
		dragSelectorExclude: "input, a[href]",
		dragEnd: function() { },
		dragBetween: false,
		placeHolderTemplate: "<li>&nbsp;</li>"
	};

})(jQuery);

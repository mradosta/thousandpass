thousandpass.onFirefoxLoad = function(event) {
  document.getElementById("contentAreaContextMenu")
          .addEventListener("popupshowing", function (e){ thousandpass.showFirefoxContextMenu(e); }, false);
};

thousandpass.showFirefoxContextMenu = function(event) {
  // show or hide the menuitem based on what the context menu is on
  document.getElementById("context-thousandpass").hidden = gContextMenu.onImage;
};

window.addEventListener("load", thousandpass.init, false);

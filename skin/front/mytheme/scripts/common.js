
var App = App || {};

App.gotoURL = function(newurl) {
    window.location = newurl;
}

App.validateForm = function() {
    if ($('form.validate').length > 0) {
        $('form.validate').validate();
    }
}
App.getCenteredCoords = function(width, height) {
    var xPos = null;
    var yPos = null;
    if (window.ActiveXObject) {
        xPos = window.event.screenX - (width / 2) + 100;
        yPos = window.event.screenY - (height / 2) - 100;
    } else {
        var parentSize = [window.outerWidth, window.outerHeight];
        var parentPos = [window.screenX, window.screenY];
        xPos = parentPos[0] + Math.max(0, Math.floor((parentSize[0] - width) / 2));
        yPos = parentPos[1] + Math.max(0, Math.floor((parentSize[1] - (height * 1.25)) / 2));
    }
    return [xPos, yPos];
}
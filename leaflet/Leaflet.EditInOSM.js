(function (factory) {
    var L;
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['leaflet'], function(L) {
            factory(L, setTimeout);
        });
    } else if (typeof module !== 'undefined') {
        // Node/CommonJS
        L = require('leaflet');
        module.exports = factory(L, setTimeout);
    } else {
        // Browser globals
        if (typeof window.L === 'undefined')
            throw 'Leaflet must be loaded first';
        factory(window.L, setTimeout);
    }
}(function (L, setTimeout) {
    var _controlContainer,
        _map,
        _zoomThreshold,
        _hideClass = 'leaflet-control-edit-hidden',
        _anchorClass = 'leaflet-control-edit-in-swisstopo-toggle',

        _Widgets =  {
            SimpleButton: function (config) {
                var className = (config && config.className) || 'leaflet-control-edit-in-swisstopo-simple',
                    helpText = config && config.helpText,
                    addEditors = (config && config.addEditors) || function (container, editors) {
                        addEditorToWidget(container, editors[0], helpText);
                    };

                return {
                    className: className,
                    helpText: helpText,
                    addEditors: addEditors
                };
            },
            MultiButton: function (config) {
                var className = 'leaflet-control-edit-in-swisstopo',
                    helpText = "Open this map extent in a map editor to provide more accurate data to Swisstopo",
                    addEditors = function (container, editors) {
                        for (var i in editors) {
                            addEditorToWidget(container, editors[i]);
                        }
                    };

                return {
                    className: (config && config.className) || className,
                    helpText: (config && config.helpText) || helpText,
                    addEditors: (config && config.addEditors) || addEditors
                };
            },
            AttributionBox: function (config) {
                var className = 'leaflet-control-attribution',
                    helpText = "Edit in SWISSTOPO",
                    addEditors = function (container, editors) {
                        addEditorToWidget(container, editors[0], helpText);
                    };

                return {
                    className: (config && config.className) || className,
                    helpText: (config && config.helpText) || helpText,
                    addEditors: (config && config.addEditors) || addEditors
                };
            }
        },

        _Editors = {
            Id: function (config) {
                var url = 'https://www.swisstopo.org/edit?editor=id#map=',
                    displayName = "iD",
                    buildUrl = function (map) {
                        return this.url + [
                            map.getZoom(),
                            map.getCenter().wrap().lat,
                            map.getCenter().wrap().lng
                        ].join('/');
                    };
                return {
                    url: (config && config.url) || url,
                    displayName: (config && config.displayName) || displayName,
                    buildUrl: (config && config.buildUrl) || buildUrl
                };
            },
            Jswisstopo: function (config) {
                var url = 'http://127.0.0.1:8111/load_and_zoom',
                    timeout = 1000,
                    displayName = "JSWISSTOPO",
                    buildUrl = function (map) {
                        var bounds = map.getBounds();
                        return this.url + L.Util.getParamString({
                            left: bounds.getNorthWest().lng,
                            right: bounds.getSouthEast().lng,
                            top: bounds.getNorthWest().lat,
                            bottom: bounds.getSouthEast().lat
                        });
                    };

                return {
                    url: (config && config.url) || url,
                    timeout: (config && config.timeout) || timeout,
                    displayName: (config && config.displayName) || displayName,
                    buildUrl: (config && config.buildUrl) || buildUrl
                };
            },
            Potlatch: function (config) {
                var url = 'http://open.mapquestapi.com/dataedit/index_flash.html',
                    displayName = "P2",
                    buildUrl = function (map) {
                        return this.url + L.Util.getParamString({
                            lon: map.getCenter().wrap().lng,
                            lat: map.getCenter().wrap().lat,
                            zoom: map.getZoom()
                        });
                    };
                return {
                    url: (config && config.url) || url,
                    displayName: (config && config.displayName) || displayName,
                    buildUrl: (config && config.buildUrl) || buildUrl
                };
            }
        };


    // Takes an editor, calls their buildUrl method
    // and opens the url in the browser
    function openRemote (editor) {
        var url = editor.buildUrl(_map),
            w = window.open(url);
        if (editor.timeout) {
            setTimeout(function() {w.close();}, editor.timeout);
        }
    }

    function addEditorToWidget(widgetContainer, editor, text) {
        var editorWidget = L.DomUtil.create('a', "swisstopo-editor", widgetContainer);
        editorWidget.href = "#";
        editorWidget.innerHTML = text || editor.displayName;
        L.DomEvent.on(editorWidget, "click", function (e) {
            openRemote(editor);
            L.DomEvent.stop(e);
        });
    }

    // Make the EditInSWISSTOPO widget visible or invisible after each
    // zoom event.
    //
    // configurable by setting the *zoomThreshold* option.
    function showOrHideUI() {
        var zoom = _map.getZoom();
        if (zoom < _zoomThreshold) {
            L.DomUtil.addClass(_controlContainer, _hideClass);
        } else {
            L.DomUtil.removeClass(_controlContainer, _hideClass);
        }
    }

    L.Control.EditInSWISSTOPO = L.Control.extend({

        options: {
            position: "topright",
            zoomThreshold: 0,
            widget: "multiButton",
            editors: ["id", "jswisstopo"]
        },

        initialize: function (options) {
            var newEditors = [],
                widget,
                widgetSmallName,
                editor,
                editorSmallName;

            L.setOptions(this, options);

            _zoomThreshold = this.options.zoomThreshold;

            widget = this.options.widget;
            widgetSmallName = typeof(widget) === 'string' ? widget.toLowerCase() : '';

            // setup widget from string or object
            if (widgetSmallName === "simplebutton") {
                this.options.widget = new _Widgets.SimpleButton(this.options.widgetOptions);
            } else if (widgetSmallName === "multibutton") {
                this.options.widget = new _Widgets.MultiButton(this.options.widgetOptions);
            } else if (widgetSmallName === "attributionbox") {
                this.options.widget = new _Widgets.AttributionBox(this.options.widgetOptions);
            }

            // setup editors from strings or objects
            for (var i in this.options.editors) {
                editor = this.options.editors[i],
                editorSmallName = typeof(editor) === "string" ? editor.toLowerCase() : null;

                if (editorSmallName === "id") {
                    newEditors.push(new _Editors.Id());
                } else if (editorSmallName === "jswisstopo") {
                    newEditors.push(new _Editors.Jswisstopo());
                } else if (editorSmallName === "potlatch") {
                    newEditors.push(new _Editors.Potlatch());
                } else {
                    newEditors.push(editor);
                }
            }
            this.options.editors = newEditors;

        },

        onAdd: function (map) {
            _map = map;
            map.on('zoomend', showOrHideUI);

            _controlContainer = L.DomUtil.create('div', this.options.widget.className);

            _controlContainer.title = this.options.widget.helpText || '';

            L.DomUtil.create('a', _anchorClass, _controlContainer);

            this.options.widget.addEditors(_controlContainer, this.options.editors);
            return _controlContainer;
        },

        onRemove: function (map) {
            map.off('zoomend', this._onZoomEnd);
        }

    });

    L.Control.EditInSWISSTOPO.Widgets = _Widgets;
    L.Control.EditInSWISSTOPO.Editors = _Editors;

    L.Map.addInitHook(function () {
        if (this.options.editInSWISSTOPOControlOptions) {
            var options = this.options.editInSWISSTOPOControlOptions || {};
            this.editInSWISSTOPOControl = (new L.Control.EditInSWISSTOPO(options)).addTo(this);
        }
    });

}));

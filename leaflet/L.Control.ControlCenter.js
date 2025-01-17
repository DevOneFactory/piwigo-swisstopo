L.Control.ControlCenter = L.Control.extend({

	includes: L.Mixin.Events,

    options: {
        position: 'topleft',
        autoPan: true,
    },
	
	onAdd: function (map) {
		//var container = map.zoomControl._container;
		var container = L.DomUtil.create('div', 'leaflet-bar');

		this._createButton({ title: 'Link to this map', href: 'linkToThisMap();'}, "swisstopo-link", container, this.setcontrolcenter, map);
		this._createButton({ title: 'Find my position', href: 'findMyLocation();'}, "swisstopo-direction", container, this.setcontrolcenter, map);
		this._createButton({ title: 'Show all items', href: 'goShowAll();'}, "swisstopo-link-ext", container, this.setcontrolcenter, map);
		//this._createButton({ title: 'Show my items', href: 'goShowMine();'}, "swisstopo-user", container, this.setcontrolcenter, map);
		//this._createButton({ title: 'Show item information', href: 'goShowInfo();'}, "swisstopo-info-circled", container, this.setcontrolcenter, map);

		return container;
	},

	_createButton: function (opts, className, container, fn, context) {
		var link = L.DomUtil.create('a', className, container);
		link.href = 'javascript:'+opts.href;
		link.title = opts.title;

		return link;
	}
});

L.control.controlcenter = function (options) {
	return new L.Control.ControlCenter(options);
};

var GoogleMaps = function( options ) {
    var self     = this;
    var map      = null;
    var markers  = {};
    var windows  = {};
    var defaults = {
        key: '',
        overlay: '',
        callback: function() {},
        center: ['62.5', '90'],
    };
    
    var layers = {
        weather: null,
        traffic: null,
    };
    
    $.extend(defaults, options);
    
    function empty(mixed_var) {
        if (typeof(mixed_var) == 'undefined' || mixed_var === '' || mixed_var === 0
                || mixed_var === '0' || mixed_var === null || mixed_var === false
                || ( is_array(mixed_var) && mixed_var.length === 0 )) {
            return true;
        }

        return false;
    };

    function is_array(mixed_var) { 
        return ( mixed_var instanceof Array ); 
    };
    
    function exists( id ) {
        if( !empty(markers[id]) ) {
            return true;
        }

        return false;
    };
    
    this.init = function() {
        if( typeof(defaults.overlay) != 'undefined' && defaults.overlay != '' && $('#' + defaults.overlay).size() > 0 ) {
            map = new google.maps.Map( document.getElementById( defaults.overlay ), {
                zoom: 4,
                center: new google.maps.LatLng(defaults.center[0], defaults.center[1]),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scaleControl: true,
                scaleControlOptions: {
                    position: google.maps.ControlPosition.BOTTOM_LEFT
                }
            });
            
            if( typeof(defaults.callback) == 'function' ) {
                defaults.callback();
            }
            
        } else {
            console.log('Coogle Maps overlay not found');
        }
    };
    
    function event(id, event_name, callback) {
        if( exists(id) && typeof(callback) == 'function' && !empty(event_name) ) {
            clearEvent(id, event_name);
            
            markers[ id ].addListener(event_name, function(obj, marker) {
                callback(obj, marker);
            });
        }
    };
    
    function clearEvent(id, event_name) {
        if( exists(id) && !empty(event_name) ) {
            google.maps.event.clearListeners(markers[ id ], event_name);
        }
    };
    
    function windowOpen( id ) {
        id = String(id);
        
        if( !empty(windows[id]) && windows[id + '-open'] == false ) {
            windows[ id ].open( map, markers[ id ] );
            windows[ id + '-open' ] = true;
        }
    };
    
    function windowClose( id ) {
        id = String(id);
        
        if( !empty(windows[id]) && windows[id + '-open'] == true ) {
            windows[ id ].close( map, markers[ id ] );
            windows[ id + '-open' ] = false;
        }
    };
    
    function windowCloseAll() {
        var regexp = new RegExp(/^[a-z0-9\-\_]+\-open$/i);
        
        for(var key in windows) {
            if( typeof(key) == 'string' && !regexp.test(key) ) {
                windowClose( key );
            }
        }
    };
    
    function windowUpdate( id, content ) {
        id = String(id);
        
        if( empty(id) && id != '0' ) {
            return false;
        }

        if( !empty(windows[id]) && empty(content) ) {
            windows[ id ].setContent( content );
        }
        
        return true;
    };
    
    this.setMarker = function(id, lat, lng, title, content, icon) {
        id = String(id);

        var params = {
            'position': new google.maps.LatLng(lat, lng),
        }

        if( !empty(icon) ) {
            params.icon = new google.maps.MarkerImage(icon,
                new google.maps.Size(33,33),
                new google.maps.Point(0,0),
                new google.maps.Point(17,17),
                new google.maps.Size(33,33)
            );
        }
        
        if( !exists(id) ) {
            params.map   = map;
            params.title = !empty( title ) ? title : '';
            
            markers[ id ] = new google.maps.Marker( params );
            
        } else {
            if( !empty(title) ) {
                params.title = title;
            }
            
            markers[ id ].setOptions( params );
        }
        
        if( !empty(content) ) {
            if( !empty(windows[id]) ) {
                windows[ id ].setContent( content );
            } else {
                windows[ id ]           = new google.maps.InfoWindow({'content': content});
                windows[ id + '-open' ] = false;
                
                google.maps.event.addListener( windows[id], 'closeclick', function() {
                    windows[ id + '-open' ] = false;
                });
                
                event(id, 'click', function(obj, marker) {
                    windowOpen( id );
                });
            }
        }
    };

    this.getMarker = function( id ) {
        if( exists(id) ) {
            return markers[ id ];
        }
        
        return null;
    };
    
    this.count = function() { 
        var size = 0; 
        $.each(markers, function() { 
            size++; 
        }); 
        
        return size; 
    };

    this.removeMarker = function( id ) {
        id = String(id);
        
        if( exists(id) ) {
            markers[ id ].setMap(null);
            delete markers[ id ];

            if( !empty(windows[ id ]) ) {
                windows[ id ].setMap(null);

                delete windows[ id ];
                delete windows[ id + '-open' ];
            }
        }
        
        return self;
    };

    this.removeAllMarkers = function() {
        for(var key in markers) {
            this.removeMarker( key );
        }
    };

    this.draggable = function(id, dragg) {
        id = String(id);
        
        if( exists(id) ) {
            if( dragg == true || dragg == 1 || dragg == '1' ) {
                markers[ id ].setDraggable( true );
            } else if( dragg == false || dragg == 0 || dragg == '0' ) {
                markers[ id ].setDraggable( false );
            }
        }
        
        return self;
    };

    this.opacity = function(id, num) {
        id = String(id);
        
        if( exists(id) && !empty(num) ) {
            markers[ id ].setOpacity( parseInt(num) );
        }
        
        return self;
    };

    this.hide = function(id) {
        id = String(id);
        
        if( exists(id) && markers[ id ].getVisible() == true ) {
            windowClose( id );
            markers[ id ].setVisible( false );
        }
        
        return self;
    };
    
    this.show = function(id) {
        id = String(id);
        
        if( exists(id) && markers[ id ].getVisible() == false ) {
            markers[ id ].setVisible( true );
        }
        
        return self;
    };

    this.setIcon = function( id, icon, prepare ) {
        id = String(id);

        if( !exists(id) || empty(icon) ) {
            return false;
        }
        
        if( typeof(prepare) == 'undefined' ) {
            prepare = true;
        }
        
        if( prepare && typeof(icon) == 'string' ) {
            markers[ id ].setIcon( new google.maps.MarkerImage(icon,
                new google.maps.Size(33,33),
                new google.maps.Point(0,0),
                new google.maps.Point(17,17),
                new google.maps.Size(33,33)
            ));
            
        } else {
            markers[ id ].setIcon( icon );
        }
        
        return true;
    },
    
    this.getPosition = function(id, as_objects) {
        id = String(id);

        if( typeof(as_objects) == 'undefined' || as_objects == '' || as_objects == 0 || as_objects == '0' ) {
            as_objects = false;
        } else {
            as_objects = true;
        }
        
        if( exists(id) ) {
            var data = markers[ id ].getPosition();
            var out  = [ data.lat().toFixed(6), data.lng().toFixed(6) ];
            
            if( as_objects == true ) {
                out = {lat: data.lat().toFixed(6), lng: data.lng().toFixed(6)};
            }
            
            return out;
        }
        
        return null;
    };
    
    if( typeof(google) == 'undefined' || google == '' ) {
        $.getScript('https://www.google.com/jsapi', function() {
            google.load('maps', '3', {
                key: defaults.key,
                libraries: 'weather,places,drawing',
                callback: self.init,
            });
        });
    }
}

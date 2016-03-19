var GoogleMapsAPI = {
    overlay: '',
    map: null,
    center: ['62.5', '90'],
    init_callback: null,
    initialZoom: true,
    
    self: null,
    initialize: false,
    
    layers: {
        weather: null,
        traffic: null,
    },
    
    is_array: function(mixed_var) { return ( mixed_var instanceof Array ); },
    
    empty: function(mixed_var) {
        if (typeof(mixed_var) == 'undefined' || mixed_var === '' || mixed_var === 0
                || mixed_var === '0' || mixed_var === null || mixed_var === false
                || ( this.is_array(mixed_var) && mixed_var.length === 0 )) {
            return true;
        }

        return false;
    },
    
    /**
    * Инициализация карты
    */
    init: function( element_id, key, callback ) {
        if( typeof(element_id) == 'undefined' || element_id == '' || typeof(key) == 'undefined' || key == '' ) {
            return false;
        }
        
        if( typeof(callback) == 'function' ) {
            this.init_callback = callback;
        }

        if( $('#' + element_id).size() > 0 ) {
            this.overlay = element_id;
            
            if( typeof(google) == 'undefined' || google == '' ) {
                var script = document.createElement('script');
                script.setAttribute('type', 'text/javascript');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=' + key + '&libraries=weather,places,drawing&callback=GoogleMapsAPI.callback';
                document.documentElement.firstChild.appendChild(script);
                
            } else {
                this.polygons.clearAllEvents();
                this.polylines.clearAllEvents();
                this.markers.clearAllEvents();
                this.clearAllEvents();
                this.removeAll();
                
                this.initialize  = false;
                this.map         = null;
                this.toolbox.box = {};
                
                $('#' + GoogleMapsAPI.overlay).html('');
                
                this.callback();
            }
            
            return true;
        }
        
        return false;
    },
    
    callback: function() {
        if( typeof(google) == 'undefined' || google == '' ) {
            console.log('Coogle Maps API not defined');
            return false;
        }
        
        this.map = new google.maps.Map( document.getElementById( GoogleMapsAPI.overlay ), {
            zoom: 4,
            center: new google.maps.LatLng(GoogleMapsAPI.center[0], GoogleMapsAPI.center[1]),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scaleControl: true,
            scaleControlOptions: {
                position: google.maps.ControlPosition.BOTTOM_LEFT
            }
        });
        
        this.initialize = true;
        this.self       = this;

        if( typeof(this.init_callback) == 'function' ) {
            this.init_callback( this );
        }
        
        return true;
    },
    
    /**
    * Проверка инициализирована ли карта
    */
    isInit: function() {
        return this.initialize;
    },

    event: function(event_name, callback) {
        if( this.isInit() && typeof(callback) == 'function' && !this.empty(event_name) ) {
            this.clearEvent(event_name);
            
            this.map.addListener(event_name, function(event) {
                callback( event );
            });
        }
        
        return this;
    },
    
    clearEvent: function(event_name) {
        if( this.isInit() && !this.empty(event_name) ) {
            google.maps.event.clearListeners(this.map, event_name);
        }
        
        return this;
    },

    clearAllEvents: function() {
        if( this.isInit() ) {
            google.maps.event.clearInstanceListeners(this.map);
        }
        
        return this;
    },
    
    removeAll: function() {
        this.markers.removeAll();
        this.polylines.removeAll();
        this.polygons.removeAll();
        this.circles.removeAll();
        
        this.markers.items = {};
        this.markers.infowindows = {};

        this.polylines.items = {};
        this.polygons.items = {};
        this.circles.items = {};
        
        return this;
    },
    
    /**
    * Скрыть карту
    */
    hide: function() {
        $('#' + this.overlay).hide();
        
        return this;
    },

    /**
    * Показать карту
    */
    show: function() {
        $('#' + this.overlay).show();
        
        return this;
    },

    zoom: function( num ) {
        if( this.isInit() && typeof(num) != 'undefined' && num != '' ) {
            this.map.setZoom( parseInt(num) );
        }
        
        return this;
    },
    
    /**
    * Фокусировка на координаты
    * 
    * lat, lng - координаты
    * zoom - приблежение (от 1 до 12)
    */
    focusTo: function( lat, lng, zoom ) {
        if( !this.empty(lat) && !this.empty(lng) && this.isInit() ) {
            this.map.setCenter( 
                new google.maps.LatLng(lat, lng) 
            );
            
            this.zoom( zoom );
        }
        
        return this;
    },

    /**
    * Отображение погоды на карте
    * 
    * status - показать или убрать ( true/false )
    * по умолчанию true
    */
    weather: function( status ) {
        if( typeof(status) == 'undefined' || status == '' || status == 1 || status == true ) {
            status = true;
        } else {
            status = false;
        }
        
        if( this.empty(this.layers.weather) && status == true ) {
            if( this.map.getZoom() > 12 ) {
                this.map.setZoom(12);
            }

            this.layers.weather = new google.maps.weather.WeatherLayer({ temperatureUnits: google.maps.weather.TemperatureUnit.CELSIUS });
            this.layers.weather.setMap( this.map );
            
        } else if( !this.empty(this.layers.weather) && status == false ) {
            this.layers.weather.setMap(null);
            this.layers.weather = null;
        }
        
        return this;
    },

    /**
    * Отображение пробок на карте
    * 
    * status - показать или убрать ( true/false )
    * по умолчанию true
    */
    traffic: function() {
        if( typeof(status) == 'undefined' || status == '' || status == 1 || status == true ) {
            status = true;
        } else {
            status = false;
        }

        if( !this.empty(this.layers.traffic) && status == false ) {
                this.layers.traffic.setMap(null);
                this.layers.traffic = null;
        } else if( this.empty(this.layers.traffic) && status == true ) {
            this.layers.traffic = new google.maps.TrafficLayer();
            this.layers.traffic.setMap( this.map );
        }
        
        return this;
    },
    
    drawing: {
        tool: null,
        
        on: function() {
            this.tool = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.MARKER,
                drawingControl: true,
                
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [
                        google.maps.drawing.OverlayType.MARKER,
                        google.maps.drawing.OverlayType.CIRCLE,
                        google.maps.drawing.OverlayType.POLYGON,
                        google.maps.drawing.OverlayType.POLYLINE,
                        google.maps.drawing.OverlayType.RECTANGLE
                    ]
                },

                circleOptions: {
                    fillColor: '#ff0000',
                    fillOpacity: 0.35,
                    strokeWeight: 1,
                    clickable: false,
                    editable: true,
                    zIndex: 1,
                },
            });
          
            this.tool.setMap( GoogleMapsAPI.map );
        },
        
        off: function() {
            this.tool.setMap(null);
            this.tool = null;
        },
    },
    
    toolbox: {
        box: {},
        
        /**
        * Set toolbox
        * 
        * Position TOP_CENTER - default
        * 
        * @param id
        * @param position - TOP_CENTER, TOP_LEFT, TOP_RIGHT, LEFT_TOP, RIGHT_TOP, LEFT_CENTER, RIGHT_CENTER
        * @param {String} box_class
        * 
        * @returns {Boolean}
        */
        
        set: function( id, position, box_class, style ) {
            if( !GoogleMapsAPI.empty(id) && typeof(this.box[id]) == 'undefined' ) {
                if( GoogleMapsAPI.empty(box_class) ) {
                    box_class = 'googleMapToolBox';
                }
                
                this.box[ id ] = document.createElement('div');
                
                $(this.box[id]).attr({id: 'GoogleMapToolbox_' + position});
                $(this.box[id]).addClass('gmnoprint ' + box_class);
                
                if( typeof(style) == 'object' ) {
                    $(this.box[id]).css( style );
                }
                
                switch(position) {
                    case 'TOP_LEFT'     : position = google.maps.ControlPosition.TOP_LEFT; break;
                    case 'TOP_RIGHT'    : position = google.maps.ControlPosition.TOP_RIGHT; break;

                    case 'LEFT_TOP'     : position = google.maps.ControlPosition.LEFT_TOP; break;
                    case 'RIGHT_TOP'    : position = google.maps.ControlPosition.RIGHT_TOP; break;

                    case 'RIGHT_CENTER' : position = google.maps.ControlPosition.RIGHT_CENTER; break;
                    case 'LEFT_CENTER'  : position = google.maps.ControlPosition.LEFT_CENTER; break;
                    
                    case 'LEFT_BOTTOM'  : position = google.maps.ControlPosition.LEFT_BOTTOM; break;
                    case 'RIGHT_BOTTOM' : position = google.maps.ControlPosition.RIGHT_BOTTOM; break;
                    case 'BOTTOM_CENTER': position = google.maps.ControlPosition.BOTTOM_CENTER; break;
                    case 'BOTTOM_LEFT'  : position = google.maps.ControlPosition.BOTTOM_LEFT; break;
                    case 'BOTTOM_RIGHT' : position = google.maps.ControlPosition.BOTTOM_RIGHT; break;
                    
                    default: position = google.maps.ControlPosition.TOP_CENTER;
                }
                
                
                GoogleMapsAPI.map.controls[ position ].push( this.box[ id ] );
            }
            
            return this;
        },

        /**
        * Add button to toolbox
        * 
        * @param {String} id
        * @param {String} title
        * @param {Function} callback
        * @param {String} button_class
        * @param {Object} style
        */
        button: function(id, title, callback, button_class, style) {
            if( !GoogleMapsAPI.empty(this.box[ id ]) ) {
                if( GoogleMapsAPI.empty(button_class) ) {
                    button_class = 'googleMapToolBoxButton';
                }

                var button = document.createElement('div');
                
                $(button).addClass('gmnoprint');
                $(button).addClass( button_class );
                $(button).html( title );
                
                if( typeof(style) == 'object' ) {
                    $(button).css( style );
                }
                

                if( typeof(callback) == 'function' ) {
                    $(button).click( function() { 
                        return callback( $(button) );
                    });
                }

                $(this.box[ id ]).append( button );
            }
            
            return this;
        },
        
    },
    
    /**
    * Работа с маркерами
    */
    markers: {
        items: {},
        infowindows: {},
        
        /**
        * Проверка наличия маркера
        */
        exists: function( id ) {
            if( !GoogleMapsAPI.empty(this.items[id]) ) {
                return true;
            }

            return false;
        },
        
        count: function() { var size = 0; $.each(this.items, function() { size++; }); return size; },
        
        /**
        * Добавление/изменение маркера на карту
        * 
        * id - идентификатор маркера
        * lat, lng - координаты
        * title - заголовок для маркера
        * content - контент для infowindow
        * icon - иконка маркера
        */
        set: function(id, lat, lng, title, contentString, icon) {
            id = String(id);
            
            if( !GoogleMapsAPI.isInit() || GoogleMapsAPI.empty(id) || GoogleMapsAPI.empty(lat) || GoogleMapsAPI.empty(lng) ) {
                console.log('Incorrect point data (id: ' + id + ', lat: ' + lat + ', lng: ' + lng + ')');
                return this;
            }
            
            var params = {
                'position': new google.maps.LatLng(lat, lng),
            }

            if( !GoogleMapsAPI.empty(icon) ) {
                params.icon = new google.maps.MarkerImage(icon,
                    new google.maps.Size(33,33),
                    new google.maps.Point(0,0),
                    new google.maps.Point(17,17),
                    new google.maps.Size(33,33)
                );
            }
            
            if( !this.exists(id) ) {
                params.map   = GoogleMapsAPI.map;
                params.title = !GoogleMapsAPI.empty( title ) ? title : '';
                
                this.items[ id ] = new google.maps.Marker( params );
                
            } else {
                if( !GoogleMapsAPI.empty(title) ) {
                    params.title = title;
                }
                
                this.items[ id ].setOptions( params );
            }
            
            if( !GoogleMapsAPI.empty(contentString) ) {
                if( !GoogleMapsAPI.empty(this.infowindows[id]) ) {
                    this.infowindows[ id ].setContent( contentString );
                } else {
                    this.infowindows[ id ]           = new google.maps.InfoWindow({content: contentString});
                    this.infowindows[ id + '-open' ] = false;
                    
                    google.maps.event.addListener( this.infowindows[id], 'closeclick', function() {
                        GoogleMapsAPI.markers.infowindows[ id + '-open' ] = false;
                    });
                    
                    this.event(id, 'click', function(obj, marker) {
                        GoogleMapsAPI.markers.windowOpen( id );
                    });
                }
            }
            
            return this;
        },

        focus: function( id, zoom ) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                var position = this.getPosition(1);
                GoogleMapsAPI.map.setCenter( new google.maps.LatLng(position[0], position[1]) );
                
                if( typeof(zoom) != 'undefined' && !_dantser.empty(zoom) ) {
                    GoogleMapsAPI.zoom( zoom );
                } else {
                    GoogleMapsAPI.zoom(9);
                }
            }
        },
        
        /**
        * Добавление события на маркер
        * 
        * event_name - click, dblclick, mouseup, mousedown, mouseover, mouseout
        */
        event: function(id, event_name, callback) {
            if( GoogleMapsAPI.isInit() && this.exists(id) && typeof(callback) == 'function' && !GoogleMapsAPI.empty(event_name) ) {
                this.clearEvent(id, event_name);
                
                this.items[ id ].addListener(event_name, function(obj, marker) {
                    callback(obj, marker);
                });
            }
            
            return this;
        },
        
        clearEvent: function(id, event_name) {
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(event_name) ) {
                google.maps.event.clearListeners(this.items[ id ], event_name);
            }
            
            return this;
        },
        
        clearAllEvents: function(id) {
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                google.maps.event.clearInstanceListeners( this.items[ id ] );
            }
            
            return this;
        },
        
        /**
        * Возвращает ссылку на сам объект Marker
        * или null если не найдено
        * 
        * @param id
        */
        get: function( id ) {
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                return this.items[ id ];
            }
            
            return null;
        },
        
        /**
        * Замена иконки для маркера
        * 
        * id - идентификатор маркера
        * icon - URL иконки
        * prepare - преобразовывать в MarkerImage (по умолчанию true)
        */
        setIcon: function( id, icon, prepare ) {
            id = String(id);

            if( !GoogleMapsAPI.isInit() || !this.exists(id) || GoogleMapsAPI.empty(icon) ) {
                return false;
            }
            
            if( typeof(prepare) == 'undefined' ) {
                prepare = true;
            }
            
            if( prepare && typeof(icon) == 'string' ) {
                this.items[ id ].setIcon( new google.maps.MarkerImage(icon,
                    new google.maps.Size(33,33),
                    new google.maps.Point(0,0),
                    new google.maps.Point(17,17),
                    new google.maps.Size(33,33)
                ));
                
            } else {
                this.items[ id ].setIcon( icon );
            }
            
            return true;
        },
        
        /**
        * Удаление маркера
        * 
        * id - идентификатор маркера
        */
        remove: function( id ) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                this.items[ id ].setMap(null);
                delete this.items[ id ];

                if( !GoogleMapsAPI.empty(this.infowindows[ id ]) ) {
                    this.infowindows[ id ].setMap(null);

                    delete this.infowindows[ id ];
                    delete this.infowindows[ id + '-open' ];
                }
            }
            
            return this;
        },

        /**
        * Удаление всех маркеров
        */
        removeAll: function() {
            for(var key in this.items) {
                this.remove( key );
            }
        },

        draggable: function(id, dragg) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                if( dragg == true || dragg == 1 || dragg == '1' ) {
                    this.items[ id ].setDraggable( true );
                } else if( dragg == false || dragg == 0 || dragg == '0' ) {
                    this.items[ id ].setDraggable( false );
                }
            }
            
            return this;
        },
        
        hide: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && this.items[ id ].getVisible() == true ) {
                this.windowClose( id );
                this.items[ id ].setVisible( false );
            }
        },
        
        show: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && this.items[ id ].getVisible() == false ) {
                this.items[ id ].setVisible( true );
            }
        },
        
        opacity: function(id, num ) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(num) ) {
                this.items[ id ].setOpacity( parseInt(num) );
            }
            
            return this;
        },
        
        /**
        * Отображает infowndow для маркера, если таковой имеется
        * 
        * id - идентификатор маркера
        */
        windowOpen: function( id ) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && !GoogleMapsAPI.empty(this.infowindows[id]) && this.infowindows[id + '-open'] == false ) {
                this.infowindows[ id ].open( GoogleMapsAPI.map, GoogleMapsAPI.markers.items[ id ] );
                this.infowindows[ id + '-open' ] = true;
            }
            
            return this;
        },
        
        /**
        * Скрывает infowndow для маркера, если таковой имеется
        * 
        * id - идентификатор маркера
        */
        windowClose: function( id ) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && !GoogleMapsAPI.empty(this.infowindows[id]) && this.infowindows[id + '-open'] == true ) {
                this.infowindows[ id ].close( GoogleMapsAPI.map, this.items[ id ] );
                this.infowindows[ id + '-open' ] = false;
            }
            
            return this;
        },
        
        /**
        * Закрывает все infowindow
        */
        windowCloseAll: function() {
            if( GoogleMapsAPI.isInit() ) {
                var regexp = new RegExp(/^[a-z0-9\-\_]+\-open$/i);
                
                for(var key in this.infowindows) {
                    if( typeof(key) == 'string' && !regexp.test(key) ) {
                        GoogleMapsAPI.windowClose( key );
                    }
                }
            }
            
            return this;
        },
        
        /**
        * Обновляет контент infowindow для маркера
        * 
        * id - идентификатор маркера
        * content - контент для infowindow
        */
        windowUpdate: function( id, content ) {
            id = String(id);
            
            if( GoogleMapsAPI.empty(id) && id != '0' ) {
                return this;
            }

            if( GoogleMapsAPI.isInit() && !GoogleMapsAPI.empty(this.infowindows[id]) && GoogleMapsAPI.empty(content) ) {
                this.infowindows[ id ].setContent( content );
            }

            return this;
        },
        
        getPosition: function(id, as_objects) {
            id = String(id);

            if( typeof(as_objects) == 'undefined' || as_objects == '' || as_objects == 0 || as_objects == '0' ) {
                as_objects = false;
            } else {
                as_objects = true;
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                var data = this.items[ id ].getPosition();
                var out  = [ data.lat().toFixed(6), data.lng().toFixed(6) ];
                
                if( as_objects == true ) {
                    out = {lat: data.lat().toFixed(6), lng: data.lng().toFixed(6)};
                }
                
                return out;
            }
            
            return null;
        },
    },
    
    /**
    * Работа с линиями
    */
    polylines: {
        items: {},
        
        /**
        * Проверка наличия линии
        */
        exists: function( id ) {
            id = String(id);
            
            if( !GoogleMapsAPI.empty(this.items[id]) ) {
                return true;
            }

            return false;
        },

        count: function() { var size = 0; $.each(this.items, function() { size++; }); return size; },

        countOfPoints: function( id ) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                var item = this.getPath( id );
                return item.length;
            }

            return false;
        },
        
        /**
        * Добавление линии
        * 
        * @param id
        * @param coords = масив из набора точек для линии в виде [ [41.879, -87.624], [41.870, -87.628] ]
        */
        set: function(id, weight, coords) {
            id = String(id);

            if( GoogleMapsAPI.is_array(weight) ) {
                coords = weight;
                weight = 1;
            } else {
                weight = parseInt( weight );
            }
            
            if( GoogleMapsAPI.empty(id) || GoogleMapsAPI.empty(coords) ) {
                console.log('Incorrect data for polyline...'); return this;
            }
            
            var points = [];
            
            $.each(coords, function(ntx, item) {
                if( !GoogleMapsAPI.empty(item) && typeof(item[0]) != 'undefined' && typeof(item[1]) != 'undefined' ) {
                    points.push( new google.maps.LatLng(item[0], item[1]) );
                }
            });
            
            this.remove( id );
            this.items[ id ] = new google.maps.Polyline({
                path: points,
                strokeColor: '#0000FF',
                strokeOpacity: 1.0,
                strokeWeight: weight,
                map: GoogleMapsAPI.map,
            });

            return this;
        },
        
        /**
        * Удаление линии
        * 
        * @param id
        */
        remove: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                this.items[ id ].setMap(null);
                delete this.items[ id ];
            }
            
            return this;
        },

        /**
        * Удаление всех линий
        */
        removeAll: function() {
            for(var key in this.items) {
                this.remove( key );
            }
        },
        
        get: function( id ) {
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                return this.items[ id ];
            }
            
            return null;
        },
        
        event: function(id, event_name, callback) {
            if( GoogleMapsAPI.isInit() && this.exists(id) && typeof(callback) == 'function' && !GoogleMapsAPI.empty(event_name) ) {
                this.clearEvent(id, event_name);
                
                google.maps.event.addListener(this.items[id].getPath(), event_name, function() {
                    callback(this);
                });
            }
            
            return this;
        },
        
        clearEvent: function(id, event_name) {
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(event_name) ) {
                google.maps.event.clearListeners(this.items[id], event_name);
            }
            
            return this;
        },
        
        clearAllEvents: function(id) {
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                google.maps.event.clearInstanceListeners( this.items[id] );
            }
            
            return this;
        },
        
        /**
        * получение списка точек линии
        * 
        * @param id
        */
        getPath: function(id, as_objects) {
            id = String(id);
            
            if( typeof(as_objects) == 'undefined' || as_objects == '' || as_objects == 0 || as_objects == '0' ) {
                as_objects = false;
            } else {
                as_objects = true;
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                var data = this.items[ id ].getPath();
                
                if( data.getLength() > 0 ) {
                    var items = data.getArray();
                    var out   = [];
                    
                    $.each(items, function(ntx, item) {
                        if( as_objects ) {
                            out.push({
                                lat: item.lat().toFixed(6), 
                                lng: item.lng().toFixed(6),
                            });
                        } else {
                            out.push([ item.lat().toFixed(6), item.lng().toFixed(6) ]);
                        }
                    });
                    
                    return out;
                }
            }
            
            return null;
        },

        focus: function( id ) {
            id = String(id);

            var k1 = 0.0025;
            var k2 = 0.0025;
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                bounds = this.getBounds(id);
                bounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(
                        bounds.getSouthWest().lat() - k2, 
                        bounds.getSouthWest().lng() - k2
                    ),
                    
                    new google.maps.LatLng(
                        bounds.getNorthEast().lat() + k2, 
                        bounds.getNorthEast().lng() + k2
                    )
                );

                if( GoogleMapsAPI.map.getCenter() != bounds.getCenter() ) {
                    var zoomChangeListener = google.maps.event.addListener( GoogleMapsAPI.map, 'zoom_changed', function() {
                        var changeBoundsListener = google.maps.event.addListener( GoogleMapsAPI.map, 'bounds_changed', function(event) {
                                if( GoogleMapsAPI.map.getZoom() > 18 && GoogleMapsAPI.initialZoom == true ) {
                                    GoogleMapsAPI.map.setZoom(18);
                                    GoogleMapsAPI.initialZoom = false;
                                }
                                
                                google.maps.event.removeListener( changeBoundsListener );
                            });
                            
                            google.maps.event.removeListener( zoomChangeListener );
                        });
                        
                    GoogleMapsAPI.initialZoom = true;
                    GoogleMapsAPI.map.fitBounds( bounds );
                }            
            }
        },
        
        getBounds: function( id ) {
            id = String(id);

            var bounds = new google.maps.LatLngBounds();
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                this.get(id).getPath().forEach( function(element, index) {
                    bounds.extend( element );
                });
            }

            return bounds;            
        },        
        
        /**
        * Установка цвета линии
        * 
        * @param id
        * @param color
        */
        setColor: function(id, color, opacity) {
            id = String(id);
            
            if( typeof(opacity) == 'undefined' || opacity == '' ) {
                opacity = 1;
            } else {
                opacity = parseFloat(opacity);
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(color) && typeof(color) == 'string' ) {
                this.items[ id ].setOptions({
                    'strokeColor': color,
                    'strokeOpacity': opacity,
                });
            }
            
            return this;
        },
        
        /**
        * Включение возможности редактировать линию на карте
        * 
        * @param id
        * @param edit - true/false, 1/0
        */
        editable: function(id, edit) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                if( edit == true || edit == 1 || edit == '1' ) {
                    this.items[ id ].setEditable( true );
                    this.items[ id ].oldStrokeColor = this.items[ id ].strokeColor;
                    this.setColor(id, '#FF0000');
                } else if( edit == false || edit == 0 || edit == '0' ) {
                    this.items[ id ].setEditable( false );
                    this.setColor(id, this.items[ id ].oldStrokeColor);
                }
            }
            
            return this;
        },
        
        /**
        * скрыть линию на карте
        * 
        * @param id
        */
        hide: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && this.items[ id ].getVisible() == true ) {
                this.items[ id ].setVisible( false );
            }
            
            return this;
        },
        
        /**
        * Показать скрытую линию на карте
        * 
        * @param id
        */
        show: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && this.items[ id ].getVisible() == false ) {
                this.items[ id ].setVisible( true );
            }
            
            return this;
        },
    },
    
    polygons: {
        items: {},
        
        /**
        * Проверка наличия полигона
        */
        exists: function( id ) {
            id = String(id);
            
            if( !GoogleMapsAPI.empty(this.items[id]) ) {
                return true;
            }

            return false;
        },

        count: function() { var size = 0; $.each(this.items, function() { size++; }); return size; },

        countOfPoints: function( id ) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                var item = this.getPath( id );
                return item.length;
            }

            return false;
        },
        
        /**
        * Установка полигона на карту
        * 
        * @param id
        * @param coords = масив из набора точек для линии в виде [ [41.879, -87.624], [8.977263738294123, -68.30017720866238], [41.86803042827033, -13.520437500000071] ]
        */
        set: function(id, weight, coords) {
            id = String(id);

            if( GoogleMapsAPI.is_array(weight) ) {
                coords = weight;
                weight = 1;
            } else {
                weight = parseInt( weight );
            }

            if( GoogleMapsAPI.empty(id) || GoogleMapsAPI.empty(coords) ) {
                console.log('Incorrect data for polygone...'); return this;
            }
            
            var points = [];
            var first  = null;
            var last   = null;
            
            if( $(coords).size() < 3 ) {
                console.log('Need minimum 3 points for polygon (id: ' + id + ')');
                return this;
            }
            
            $.each(coords, function(ntx, item) {
                if( ntx == 0 && first == null ) {
                    first = item;
                }
                
                if( !GoogleMapsAPI.empty(item) && typeof(item[0]) != 'undefined' && typeof(item[1]) != 'undefined' ) {
                    points.push( new google.maps.LatLng(item[0], item[1]) );
//                    points.push({'lat': item[0], 'lng': item[1]});
                }
                
                last = item;
            });
            
//            if( first[0] != last[0] && first[1] != last[1] ) {
//                points.push({'lat': first[0], 'lng': first[1]});
//            }
            
            this.remove();
            this.items[ id ] = new google.maps.Polygon({
                path: points,
                strokeColor: '#0000FF',
                strokeOpacity: 1.0,
                strokeWeight: weight,
                fillColor: '#0000FF',
                fillOpacity: 0.35,
                map: GoogleMapsAPI.map,
            });
            
            return this;
        },

        /**
        * Удаление полигона
        * 
        * @param id
        */
        remove: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                this.items[ id ].setMap(null);
                delete this.items[ id ];
            }
            
            return this;
        },
        
        /**
        * Удаление всех поигонов
        */
        removeAll: function() {
            for(var key in this.items) {
                this.remove( key );
            }
        },

        get: function( id ) {
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                return this.items[ id ];
            }
            
            return null;
        },
        
        event: function(id, event_name, callback) {
            if( GoogleMapsAPI.isInit() && this.exists(id) && typeof(callback) == 'function' && !GoogleMapsAPI.empty(event_name) ) {
                this.clearEvent(id, event_name);
                
                google.maps.event.addListener(this.items[id].getPath(), event_name, function() {
                    callback(this);
                });
            }
            
            return this;
        },
        
        clearEvent: function(id, event_name) {
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(event_name) ) {
                google.maps.event.clearListeners(this.items[ id ], event_name);
            }
            
            return this;
        },
        
        clearAllEvents: function(id) {
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                google.maps.event.clearInstanceListeners( this.items[ id ] );
            }
            
            return this;
        },
        
        /**
        * получение списка точек полигонов
        * 
        * @param id
        */
        getPath: function(id, as_objects) {
            id = String(id);
            
            if( typeof(as_objects) == 'undefined' || as_objects == '' || as_objects == 0 || as_objects == '0' ) {
                as_objects = false;
            } else {
                as_objects = true;
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                var data = this.items[ id ].getPath();
                
                if( data.getLength() > 0 ) {
                    var items = data.getArray();
                    var out   = [];
                    
                    $.each(items, function(ntx, item) {
                        if( as_objects ) {
                            out.push({
                                lat: item.lat().toFixed(6), 
                                lng: item.lng().toFixed(6),
                            });
                        } else {
                            out.push([ item.lat().toFixed(6), item.lng().toFixed(6) ]);
                        }
                    });
                    
                    return out;
                }
            }
            
            return null;
        },

        focus: function( id ) {
            id = String(id);

            var k1 = 0.0025;
            var k2 = 0.0025;
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                GoogleMapsAPI.map.setZoom(18);
                
                bounds = this.getBounds(id);
                bounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(
                        bounds.getSouthWest().lat() - k2, 
                        bounds.getSouthWest().lng() - k2
                    ),
                    
                    new google.maps.LatLng(
                        bounds.getNorthEast().lat() + k2, 
                        bounds.getNorthEast().lng() + k2
                    )
                );

                if( GoogleMapsAPI.map.getCenter() != bounds.getCenter() ) {
                    var zoomChangeListener = google.maps.event.addListener( GoogleMapsAPI.map, 'zoom_changed', function() {
                        var changeBoundsListener = google.maps.event.addListener( GoogleMapsAPI.map, 'bounds_changed', function(event) {
                                if( GoogleMapsAPI.map.getZoom() > 18 && GoogleMapsAPI.initialZoom == true ) {
                                    GoogleMapsAPI.map.setZoom(18);
                                    GoogleMapsAPI.initialZoom = false;
                                }
                                
                                google.maps.event.removeListener( changeBoundsListener );
                            });
                            
                            google.maps.event.removeListener( zoomChangeListener );
                        });
                        
                    GoogleMapsAPI.initialZoom = true;
                    GoogleMapsAPI.map.fitBounds( bounds );
                }            
            }
        },
        
        getBounds: function( id ) {
            id = String(id);

            var bounds = new google.maps.LatLngBounds();
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                this.get(id).getPath().forEach( function(element, index) {
                    bounds.extend( element );
                });
            }

            return bounds;            
        },        

        /**
        * Установка цвета полигона
        * 
        * @param id
        * @param color
        */
        setLineColor: function(id, color, opacity) {
            id = String(id);
            
            if( typeof(opacity) == 'undefined' || opacity == '' ) {
                opacity = 1;
            } else {
                opacity = parseFloat(opacity);
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(color) && typeof(color) == 'string' ) {
                this.items[ id ].setOptions({
                    'strokeColor': color,
                    'strokeOpacity': opacity,
                });
            }
            
            return this;
        },
        
        setFillColor: function(id, color, opacity) {
            id = String(id);
            
            if( typeof(opacity) == 'undefined' || opacity == '' ) {
                opacity = 0.35;
            } else {
                opacity = parseFloat(opacity);
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(color) && typeof(color) == 'string' ) {
                this.items[ id ].setOptions({
                    'fillColor': color,
                    'fillOpacity': opacity,
                });
            }
            
            return this;
        },
        
        /**
        * Включение возможности редактировать полигон на карте
        * 
        * @param id
        * @param edit - true/false, 1/0
        */
        editable: function(id, edit) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                if( edit == true || edit == 1 || edit == '1' ) {
                    this.items[ id ].setEditable( true );
                    this.items[ id ].oldStrokeColor = this.items[ id ].strokeColor;
                    this.setLineColor(id, '#FF0000');
                } else if( edit == false || edit == 0 || edit == '0' ) {
                    this.items[ id ].setEditable( false );
                    this.setLineColor(id, this.items[ id ].oldStrokeColor);
                }
            }
            
            return this;
        },
        
        draggable: function(id, dragg) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                if( dragg == true || dragg == 1 || dragg == '1' ) {
                    this.items[ id ].setDraggable( true );
                    this.items[ id ].oldStrokeColor = this.items[ id ].strokeColor;
                    this.setLineColor(id, '#FFAA00');
                } else if( dragg == false || dragg == 0 || dragg == '0' ) {
                    this.items[ id ].setDraggable( false );
                    this.setLineColor(id, this.items[ id ].oldStrokeColor);
                }
            }
            
            return this;
        },
        
        /**
        * скрыть полигон на карте
        * 
        * @param id
        */
        hide: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && this.items[ id ].getVisible() == true ) {
                this.items[ id ].setVisible( false );
            }
            
            return this;
        },
        
        /**
        * Показать скрытую линию на карте
        * 
        * @param id
        */
        show: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && this.items[ id ].getVisible() == false ) {
                this.items[ id ].setVisible( true );
            }
            
            return this;
        },
    },
    
    circles: {
        items: {},
        
        /**
        * Проверка наличия линии
        */
        exists: function( id ) {
            id = String(id);
            
            if( !GoogleMapsAPI.empty(this.items[id]) ) {
                return true;
            }

            return false;
        },

        count: function() { var size = 0; $.each(this.items, function() { size++; }); return size; },
        
        /**
        * Добавление линии
        * 
        * @param id
        * @param coords = масив из набора точек для линии в виде [ [41.879, -87.624], [8.977263738294123, -68.30017720866238], [41.86803042827033, -13.520437500000071] ]
        */
        set: function(id, lat, lng, radius, weight, color) {
            id = String(id);

            if( GoogleMapsAPI.empty(id) || GoogleMapsAPI.empty(lat) || GoogleMapsAPI.empty(lng) || GoogleMapsAPI.empty(radius) ) {
                console.log('Incorrect data for circle...'); return this;
            }
            
            if( GoogleMapsAPI.empty(weight) ) {
                weight = 1;
            } else {
                weight = parseInt( weight );
            }

            var params = {
                'center': {'lat': lat, 'lng': lng},
                'strokeWeight': weight,
            };
            
            if( GoogleMapsAPI.empty(radius) ) {
                radius = 10000;
            } else {
                radius = parseInt(radius);
            }

            if( GoogleMapsAPI.empty(color) || typeof(color) != 'string' ) {
                color = '#0000FF';
            }
            
            params.strokeColor = color;
            params.fillColor   = color;
            params.radius      = radius;

            if( GoogleMapsAPI.isInit() && !GoogleMapsAPI.empty(this.items[id]) ) {
                this.items[ id ].setOptions( params );
            } else {
                params.strokeOpacity = 1;
                params.fillOpacity   = 0.25;
                params.map           = GoogleMapsAPI.map;
                
                this.items[ id ] = new google.maps.Circle( params );
            }
            
            return this;
        },

        /**
        * Удаление линии
        * 
        * @param id
        */
        remove: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                this.items[ id ].setMap(null);
                delete this.items[ id ];
            }
            
            return this;
        },
        
        /**
        * Удаление всех поигонов
        */
        removeAll: function() {
            for(var key in this.items) {
                this.remove( key );
            }
        },

        get: function( id ) {
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                return this.items[ id ];
            }
            
            return null;
        },
        
        event: function(id, event_name, callback) {
            if( GoogleMapsAPI.isInit() && this.exists(id) && typeof(callback) == 'function' && !GoogleMapsAPI.empty(event_name) ) {
                this.clearEvent(id, event_name);
                
                this.items[ id ].addListener(event_name, function(obj, marker) {
                    callback(obj, marker);
                });
            }
            
            return this;
        },
        
        clearEvent: function(id, event_name) {
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(event_name) ) {
                google.maps.event.clearListeners(this.items[ id ], event_name);
            }
            
            return this;
        },
        
        clearAllEvents: function(id) {
            if( this.isInit() && this.exists(id) ) {
                google.maps.event.clearInstanceListeners( this.items[ id ] );
            }
            
            return this;
        },
        
        /**
        * получение списка точек линии
        * 
        * @param id
        */
        getPosition: function(id, as_objects) {
            id = String(id);
            
            if( typeof(as_objects) == 'undefined' || as_objects == '' || as_objects == 0 || as_objects == '0' ) {
                as_objects = false;
            } else {
                as_objects = true;
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                var item = this.items[ id ];
                var data = item.getCenter();
                
                if( as_objects ) {
                    return {
                        lat: data.lat(), 
                        lng: data.lng(),
                        radius: item.getRadius(),
                    };
                } else {
                    return [ data.lat(), data.lng(), item.getRadius() ];
                }
            }
            
            return null;
        },
        
        /**
        * Установка цвета линии
        * 
        * @param id
        * @param color
        */
        setLineColor: function(id, color, opacity) {
            id = String(id);
            
            if( typeof(opacity) == 'undefined' || opacity == '' ) {
                opacity = 1;
            } else {
                opacity = parseFloat(opacity);
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(color) && typeof(color) == 'string' ) {
                this.items[ id ].setOptions({
                    'strokeColor': color,
                    'strokeOpacity': opacity,
                });
            }
            
            return this;
        },
        
        setFillColor: function(id, color, opacity) {
            id = String(id);
            
            if( typeof(opacity) == 'undefined' || opacity == '' ) {
                opacity = 0.35;
            } else {
                opacity = parseFloat(opacity);
            }
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && !GoogleMapsAPI.empty(color) && typeof(color) == 'string' ) {
                this.items[ id ].setOptions({
                    'fillColor': color,
                    'fillOpacity': opacity,
                });
            }
            
            return this;
        },
        
        /**
        * Включение возможности редактировать линию на карте
        * 
        * @param id
        * @param edit - true/false, 1/0
        */
        editable: function(id, edit) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) ) {
                if( edit == true || edit == 1 || edit == '1' ) {
                    this.items[ id ].setEditable( true );
                    this.items[ id ].oldStrokeColor = this.items[ id ].strokeColor;
                    this.setLineColor(id, '#FF0000');
                } else if( edit == false || edit == 0 || edit == '0' ) {
                    this.items[ id ].setEditable( false );
                    this.setLineColor(id, this.items[ id ].oldStrokeColor);
                }
            }
            
            return this;
        },
        
        /**
        * скрыть линию на карте
        * 
        * @param id
        */
        hide: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && this.items[ id ].getVisible() == true ) {
                this.items[ id ].setVisible( false );
            }
            
            return this;
        },
        
        /**
        * Показать скрытую линию на карте
        * 
        * @param id
        */
        show: function(id) {
            id = String(id);
            
            if( GoogleMapsAPI.isInit() && this.exists(id) && this.items[ id ].getVisible() == false ) {
                this.items[ id ].setVisible( true );
            }
            
            return this;
        },
    },
    
    rectangles: {
        items: {},
        
        count: function() {
            return $(this.items).size();
        },
        
        add: function() {
            
        },

        remove: function() {
            
        },
    },
    
};
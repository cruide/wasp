var wasp = { 
    
    url   : '{$base_url}',
    images: '{$images_url}',
    css   : '{$css_url}',
    fn    : {},

    pre: { now: new Date(), stamp: function(){ return wasp.pre.now.getTime(); }},

    empty: function(mixed_var) { 
        if( mixed_var === "" || mixed_var === 0 || mixed_var === "0" || mixed_var === null || mixed_var === false || ( wasp.is_array(mixed_var) && mixed_var.length === 0 ) ) { return true; } 
        return false;
    },
    
    is_array: function(mixed_var) { return ( mixed_var instanceof Array ); },
    implode: function(glue, pieces) { return ((pieces instanceof Array) ? pieces.join(glue) : pieces); },
    {literal}
    is_url: function(url) {
        var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        if( RegExp.test(url) ) { return true; }
        return false;
    },
    
    is_email: function(email) {
        var RegExp = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if( RegExp.test(email) ) { return true; }
        return false;
    },
    {/literal}
    is_exists: function(selector) {
        if( $(selector).size() > 0 ) {
            return true;
        }
        
        return false;
    },

    function_exists: function( function_name ) {
        if( typeof function_name == 'string' ) { return ( typeof window[function_name] == 'function' );} 
        else { return ( function_name instanceof Function );}
    },

    call_user_func: function (cb) { if( typeof(cb) === 'string' && cb != '' && wasp.function_exists(cb) ) { var func = cb + "();"; eval(func);}},    
    
    redirect: function(url) {
        if( typeof(url) != 'undefined' && url != '' && wasp.is_url(url) == true ) { window.location.href = url;} 
        else { console.log('Incorrect URL redirection: ' + url);}
    },
    
    confirm: function(msg, title, callback) {
    
        if( typeof(callback) != 'function' ) { console.log('wasp.confirm: incorrect callback'); return false;}
        if( typeof(title) == 'undefined' || title == '' ) { title = 'Вы уверены?'; }
        
        wasp.fn.callback = function() {
            jQuery('#wasp-confirm-box-dialog').modal('hide');
            callback();
        };

        var confirm_dialog = '<div class="modal fade" id="wasp-confirm-box-dialog" role="dialog" aria-hidden="true">'
                           + '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">'
                           + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
                           + '<h4 class="modal-title">'+title+'</h4></div><div class="modal-body"><p>'+msg+'</p></div><div class="modal-footer">' 
                           + '<button type="button" class="btn btn-primary" data-dismiss="modal">Нет</button>'
                           + '<button type="button" class="btn btn-default" onclick="wasp.fn.callback()">Да</button>'
                           + '</div></div></div></div>';
        
        jQuery('body').append( confirm_dialog );
        jQuery('#wasp-confirm-box-dialog').on('hidden.bs.modal', function(){ jQuery('#wasp-confirm-box-dialog').remove(); });
        jQuery('#wasp-confirm-box-dialog').modal('show');
    },

    message: function(msg, title, params) {
    
        if( typeof(title) == 'undefined' || title == '' ) { title = 'Message'; }
        if( typeof(params) != 'object' ) { params = { width: 300, height: 150 }}
        if( typeof(params.width) == 'undefined' || params.width ==  "" ) params.width = 300;
        if( typeof(params.height) == 'undefined' || params.height ==  "" ) params.height = 300;
        
        var msg_dialog = '<div class="modal fade" id="wasp-message-box-dialog" role="dialog" aria-hidden="true">'
                       + '<div class="modal-dialog"><div class="modal-content"><div class="modal-header">'
                       + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;'
                       + '</button><h4 class="modal-title">'+title+'</h4></div><div class="modal-body"><p>'+msg+'</p></div><div class="modal-footer">' 
                       + '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button></div></div></div></div>';

        
        jQuery('body').append( msg_dialog ); // 
        jQuery('#wasp-message-box-dialog').on('hidden.bs.modal', function(){ jQuery('#wasp-message-box-dialog').remove(); });
        jQuery('#wasp-message-box-dialog').modal('show');
    },

    preloader: function(sel) {
        if( typeof(jQuery) != 'undefined' && jQuery != ''  ) {
            var now = new Date();

            jQuery(sel).html(
                '<div id="ajax-preloader" style="margin: 0 auto; width: 20px; height: 12px; padding: 5px;">'+
                '<img id="ajax-preloader-image" src="<?=$images_url?>/ajax-loader.gif" style="margin: 0 auto; width: 20px; height: 12px;" title="Загрузка данных ..." />'+
                '</div>'
            );
        }
    },
    
    ajax: function(url, callback, params, type) {
        if( typeof(jQuery) != 'undefined' && jQuery != '' ) {
            if( typeof(url) == 'undefined' || url == '' ) { return false;}
            if( typeof(params) == 'undefined' || wasp.empty(params) ) { 
                params = { timer: wasp.pre.stamp() }; 
            }
            if( typeof(type) == 'undefined' || type == '' ) { type = 'html'; }

            jQuery.ajax({ 
                type: 'POST', data: params, dataType: type, url: url, 
                success: function(result){ if( typeof(callback) == 'function' ) callback(result); }, 
                headers: { 'X-Response-Type': type,'X-Request-Type': 'Expedited' },
                error: function(jqXHR, textStatus, errorThrown) { 
                    if( typeof(console) != 'undefined' && typeof(console) != '' ) { console.log('Request wasp.ajax to `'+url+'` returned an error'); }
                    else { wasp.message('Request wasp.ajax to `'+url+'` returned an error: <i>"'+textStatus+'"</i>', jqXHR.status); }
                },
                timeout: function() { 
                    if( typeof(console) != 'undefined' && typeof(console) != '' ) { console.log('Request wasp.ajax to `'+url+'` has timed out');}
                    else { wasp.message('Request wasp.ajax to `'+url+'` has timed out'); }
                } 
            });
            return true;
        }
        return false;
    },
    
    request: function(url, element_id, params) {
        if( typeof(jQuery) != 'undefined' && jQuery != '' ) {
            if( typeof(url) == 'undefined' || url == '' ) { return false;}
            if( typeof(params) == 'undefined' || params == '' ) { params  = { timer: wasp.pre.stamp() }; }

            jQuery.ajax({
                type: 'POST', data: params, dataType: 'html', cache: false, timeout: 60000,
                url: url, headers: { 'X-Response-Type': 'html','X-Request-Type': 'Expedited' },
                success: function(result) {
                    if( typeof(element_id) != 'undefined' && element_id != '' ) { jQuery(element_id).html(result); }
                },
                error: function( jqXHR, textStatus, errorThrown) {
                    if( typeof(console) != 'undefined' && typeof(console) != '' ) { console.log('Request wasp.ajax to `'+url+'` returned an error'); }
                    if( typeof(wasp.message) == 'undefined' ) { alert('Request wasp.ajax to `'+url+'` returned an error: '+jqXHR.status+', '+textStatus); }
                    else { wasp.message('Request wasp.ajax to `'+url+'` returned an error: <i>"'+textStatus+'"</i>', jqXHR.status); }
                }
            });
            return true;
        }
        return false;
    }
}

if( typeof(jQuery) != 'function' ) {
    alert( 'Need jQuery...' );
}
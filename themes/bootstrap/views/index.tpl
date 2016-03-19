<div class="page-header">
  <h3>Главная</h3>
</div>

<div class="col-xs-9">
    <div id="google-map-overlay" style="height: 400px; width: 100%;">
    
    </div>
</div>

<div class="col-xs-3">
    <strong>Разделы:</strong>
    <ul>
        <li><a href="#directories">Структура директорий</a></li>
        <li><a href="https://github.com/usmanhalalit/pixie#installation" target="_blank">Компонент Pixie</a></li>
        <li><a>{$base_url}</a></li>
    </ul>
</div>
<script type="text/javascript" src="/google.maps.api.js"></script>
<script type="text/javascript">
$(function(){
    $map = new GoogleMaps({
        key: 'AIzaSyDt9u0t2ew3Aw4zLD1MxhvLY6PYnpYfv-E',
        overlay: 'google-map-overlay',
        callback: function(){ 
            $map.setMarker(1, '62.5', '90', 'Marker', 'Hello world!');
        },
    });
});
</script>
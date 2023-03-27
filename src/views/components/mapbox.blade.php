<script src='https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js'></script>
<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css' rel='stylesheet' />        
<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css' type='text/css' />

<div class="form-group">
    <label>{{ ucfirst(trans('messages.'.$component['title'])) }}: {{ $component['required'] ? '*' : '' }}</label>
    <div class="row">
        <div class="col-lg-6">
            <div class="form-group">
                <label>Latitude</label>
                <input id="{{ $component['longitudeField'] }}" name="{{ $component['longitudeField'] }}" readonly=true class="form-control" value="{{ @$item->{$component['longitudeField']} }}" />
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-group">
                <label>Longitude</label>
                <input id="{{ $component['latitudeField'] }}" name="{{ $component['latitudeField'] }}" readonly=true class="form-control" value="{{ @$item->{$component['latitudeField']} }}"/>
            </div>
        </div>
    </div>
    <div class="form-group">                
        <div id='map-{{ $component['field'] }}' style="width:400px; height:300px"></div>
    </div>
</div>
<script>

    $(function() {

        mapboxgl.accessToken = sfwcomponent.mapboxAccessToken;
            
        var latitude = "{{ @$item->{$component['latitudeField']} }}";
        var longitude = "{{ @$item->{$component['longitudeField']} }}";

        if(latitude == '') {
            latitude = 0;
        }
        if(longitude == '') {
            lontiude = 0;
        }

        var map = new mapboxgl.Map({
            container: 'map-{{ $component['field'] }}',
            style: 'mapbox://styles/mapbox/streets-v11'
        });

        const marker = new mapboxgl.Marker({
            draggable: true
        }).setLngLat([longitude, latitude]).addTo(map);

        map.setCenter([longitude, latitude]);
        map.setZoom(5);

        function onDragEnd() {
            const lngLat = marker.getLngLat();
            $("#{{ $component['latitudeField'] }}").val(lngLat.lat);
            $("#{{ $component['longitudeField'] }}").val(lngLat.lng);                
        } 
        
        marker.on('dragend', onDragEnd);

        const geocoder = new MapboxGeocoder({
            // Initialize the geocoder
            accessToken: mapboxgl.accessToken, // Set the access token
            mapboxgl: mapboxgl, // Set the mapbox-gl instance
            marker: false // Do not use the default marker style
        });

        geocoder.on('result', (event) => {    
            marker.setLngLat(event.result.geometry.coordinates);
            $("#{{ $component['latitudeField'] }}").val(event.result.geometry.coordinates[1]);
            $("#{{ $component['longitudeField'] }}").val(event.result.geometry.coordinates[0]);    
        });

        // Add the geocoder to the map
        map.addControl(geocoder);

    });

</script>
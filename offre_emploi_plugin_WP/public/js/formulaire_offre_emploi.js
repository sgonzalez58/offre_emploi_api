var map = L.map('map').setView([46.9881, 3.1568], 16);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

let marker = L.marker([46.9881, 3.1568], {draggable: true}).addTo(map);

function onMapClick(e) {
    marker.setLatLng(e.latlng);
    document.getElementById('formulaire_offre_emploi_latitude').value = e.latlng.lat;
    document.getElementById('formulaire_offre_emploi_longitude').value = e.latlng.lng;
}

function onDragMarker(e) {
    console.log(e)
    document.getElementById('formulaire_offre_emploi_latitude').value = e.target._latlng.lat;
    document.getElementById('formulaire_offre_emploi_longitude').value = e.target._latlng.lng;
}

map.on('click', onMapClick);
marker.on('dragend', onDragMarker);
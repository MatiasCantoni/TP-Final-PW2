function iniciarMap(){
    var coord = {lat:-34.5956145 ,lng: -58.4431949};
    var map = new google.maps.Map(document.getElementById('map'),{
      zoom: 10,
      center: coord
    });
    var marker = new google.maps.Marker({
      position: coord,
      map: map
    });
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('mapa')) {
        const map = L.map('mapa').setView([20, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker = L.marker([20, 0], { draggable: true }).addTo(map)
            .bindPopup('Arrastrame o haz click en el mapa')
            .openPopup();

        function actualizarUbicacion(lat, lon) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                .then(response => response.json())
                .then(data => {
                    const address = data.address;
                    const pais = address.country || '';
                    const ciudad = address.city || address.town || address.village || '';
                    document.getElementById('pais').value = pais;
                    document.getElementById('ciudad').value = ciudad;

                    const textoUbicacion = document.getElementById('ubicacion-seleccionada');
                    if (textoUbicacion) {
                        if (pais) {
                            textoUbicacion.innerText = `Ubicación: ${ciudad}, ${pais}`;
                        } else {
                            textoUbicacion.innerText = 'Ubicación no encontrada. Intenta de nuevo.';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al obtener la ubicación:', error);
                    const textoUbicacion = document.getElementById('ubicacion-seleccionada');
                    if (textoUbicacion) {
                        textoUbicacion.innerText = 'No se pudo obtener la ubicación.';
                    }
                });
        }

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            actualizarUbicacion(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function(e) {
            const latlng = e.target.getLatLng();
            actualizarUbicacion(latlng.lat, latlng.lng);
        });
    }
});
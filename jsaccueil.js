var linkToDepart = true;
var directionsService;
var directionsRenderer;

function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

$(document).ready(function () {

    $('#depart').on('click', function () {
        initAutocomplete1(); // Initialiser l'autocomplétion pour l'entrée de départ
    });

    $('#arrivee').on('click', function () {
        initAutocomplete2(); // Initialiser l'autocomplétion pour l'entrée d'arrivée
    });

    $('.dateInput').on('focus', function () {
        $(this).prop('type', 'date'); // Afficher le sélecteur de date au focus
    });

    $('.dateInput').on('blur', function () {
        if ($(this).val() === '') {
            $(this).prop('type', 'text'); // Revenir au type texte si l'entrée est vide
        }
    });

    $('#icone').on('click', function () {
        $('#menu1').css('transform', 'translateX(0)'); // Ouvrir le menu1
        $('#menu2').css('transform', 'translateX(100%)'); // Fermer le menu2 si ouvert
    });

    $('#close').on('click', function (e) {
        e.stopPropagation();
        $('#menu1').css('transform', 'translateX(-100%)'); // Fermer le menu1
    });

    $('#profil').on('click', function () {
        $('#menu2').css('transform', 'translateX(0)'); // Ouvrir le menu2
        $('#menu1').css('transform', 'translateX(-100%)'); // Fermer le menu1 si ouvert
    });

    $('#close2').on('click', function (e) {
        e.stopPropagation();
        $('#menu2').css('transform', 'translateX(100%)'); // Fermer le menu2
    });

    $('.swap-button').on('click', function () {
        var depart = $('input[name="depart"]').val();
        var arrivee = $('input[name="arrivee"]').val();
        $('input[name="depart"]').val(arrivee);
        $('input[name="arrivee"]').val(depart);

        linkToDepart = !linkToDepart;

        $('input[name="depart"]').prop('readonly', linkToDepart);
        $('input[name="arrivee"]').prop('readonly', !linkToDepart);
        updateLabelPosition($('input[name="depart"]'));
        updateLabelPosition($('input[name="arrivee"]'));
        updateRoute(); // Calculer et afficher l'itinéraire
    });

    $('input[name="nouvel_input"]').on('input', function () {
        var campusInput = $(this).val();
        if (linkToDepart) {
            $('input[name="depart"]').val(campusInput).trigger('input');
        } else {
            $('input[name="arrivee"]').val(campusInput).trigger('input');
        }
        updateRoute(); // Calculer et afficher l'itinéraire
    });

    $('input').on('input', function () {
        updateLabelPosition($(this)); // Mettre à jour la position du label
    });

    $('#act').on('click', function (e) {
        e.stopPropagation();
        $('#map').toggleClass('hidden'); // Afficher/masquer la carte
        var element = $('.fondjaune');
        if ($('#map').hasClass('hidden')) {
            element.css('height', '145vw');
        } else {
            element.css('height', '190vw');
        }
    });

    $('select[name="nouvel_input"]').on('change', function () {
        var campusInput = $(this);
        var depart = $('input[name="depart"]');
        var arrivee = $('input[name="arrivee"]');

        if (linkToDepart) {
            depart.val(campusInput.val());
        } else {
            arrivee.val(campusInput.val());
        }

        updateLabelPosition(campusInput); // Mettre à jour la position du label
        updateLabelPosition(depart);
        updateLabelPosition(arrivee);
        handleCampusSelection(); // Gérer la sélection du campus
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#depart').length) {
            $('#depart').siblings('label').removeClass('hidden'); // Cacher le label de départ
        }
        if (!$(e.target).closest('#arrivee').length) {
            $('#arrivee').siblings('label').removeClass('hidden'); // Cacher le label d'arrivée
        }
    });

    // Ajouter des styles pour l'autocomplétion de Google Maps
    var css = `
        .pac-container {
            font-size: 20px;
            max-width: 600px;
            z-index: 1050;
        }
        .pac-item {
            padding: 10px;
        }
        .pac-item-query {
            font-size: 20px;
        }
    `;
    var style = document.createElement('style');
    style.type = 'text/css';
    if (style.styleSheet) {
        style.styleSheet.cssText = css;
    } else {
        style.appendChild(document.createTextNode(css));
    }
    document.getElementsByTagName('head')[0].appendChild(style);

    $('select[name="nouvel_input"]').on('change', handleCampusSelection);

});

function updateLabelPosition(element) {
    var value = element.val();
    var label = element.siblings('.label');
    var underline = element.siblings('.underline');

    if (value) {
        label.css({
            'top': '-20px',
            'font-size': '25px',
            'color': '#333'
        });
        underline.css('transform', 'scaleX(1)');
    } else {
        label.css({
            'top': '0',
            'font-size': '42px',
            'color': 'black',
            'opacity': '0.3'
        });
        underline.css('transform', 'scaleX(0)');
    }
}

function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 45.75, lng: 4.85 },
        zoom: 8
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    var geocoder = new google.maps.Geocoder();
    var marker;

    map.addListener('click', function (event) {
        geocodeLatLng(geocoder, event.latLng, function(latLng, address) {
            if (marker) {
                marker.setPosition(latLng);
            } else {
                marker = new google.maps.Marker({
                    position: latLng,
                    map: map
                });
            }
            updateRoute();
        });
    });
}

function geocodeLatLng(geocoder, latLng, callback) {
    geocoder.geocode({ 'location': latLng }, function (results, status) {
        if (status === 'OK') {
            if (results[0]) {
                var address = results[0].formatted_address;
                if ($('input[name="depart"]').prop('readonly')) {
                    $('input[name="arrivee"]').val(address);
                    updateLabelPosition($('input[name="arrivee"]'));
                } else {
                    $('input[name="depart"]').val(address);
                    updateLabelPosition($('input[name="depart"]'));
                }
                displayLatLng('Carte', latLng);
                callback(latLng, address);
            } else {
                window.alert('No results found');
            }
        } else {
            window.alert('Geocoder failed due to: ' + status);
        }
    });
}

function geocodeAddress(geocoder, address, callback) {
    geocoder.geocode({ 'address': address }, function (results, status) {
        if (status === 'OK') {
            var latLng = results[0].geometry.location;
            callback(latLng, results[0].formatted_address); // Passer latLng et adresse formatée au callback
        } else {
            console.log('Geocode failed: ' + status);
        }
    });
}

function displayLatLng(input, latLng) {
    var lat = latLng.lat();
    var lng = latLng.lng();
    console.log(input + " - Latitude: " + lat + ", Longitude: " + lng);
}

function handlePlaceChanged(autocomplete, inputId) {
    var place = autocomplete.getPlace();
    if (!place.geometry) {
        console.log("Lieu non trouvé pour l'entrée: '" + place.name + "'");
        return;
    }
    displayLatLng(inputId, place.geometry.location);
    updateLabelPosition($(inputId));
    updateRoute();
}

function initAutocomplete1() {
    var departInput = document.getElementById('depart');
    var departAutocomplete = new google.maps.places.Autocomplete(departInput);

    departAutocomplete.addListener('place_changed', function () {
        handlePlaceChanged(departAutocomplete, '#depart'); // Gérer le changement de lieu
    });
}

function initAutocomplete2() {
    var arriveeInput = document.getElementById('arrivee');
    var arriveeAutocomplete = new google.maps.places.Autocomplete(arriveeInput);

    arriveeAutocomplete.addListener('place_changed', function () {
        handlePlaceChanged(arriveeAutocomplete, '#arrivee'); // Gérer le changement de lieu
    });
}

function handleCampusSelection() {
    var campusInput = $('select[name="nouvel_input"]').val();
    var address = campusAdresses[campusInput];
    var geocoder = new google.maps.Geocoder();

    if (address) {
        geocodeAddress(geocoder, address, function (latLng, formattedAddress) {
            var inputId = linkToDepart ? '#depart' : '#arrivee';
            $(inputId).val(formattedAddress); // Utiliser l'adresse formatée pour l'affichage
            displayLatLng(inputId, latLng);
            updateLabelPosition($(inputId));
            updateRoute(); // Calculer et afficher l'itinéraire
        });
    }
}

function updateRoute() {
    var depart = $('input[name="depart"]').val();
    var arrivee = $('input[name="arrivee"]').val();

    if (depart && arrivee) {
        directionsService.route({
            origin: depart,
            destination: arrivee,
            travelMode: google.maps.TravelMode.DRIVING
        }, function (response, status) {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsRenderer.setDirections(response); // Afficher les directions sur la carte
            } else {
                window.alert('Directions request failed due to ' + status);
            }
        });
    }
}

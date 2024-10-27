var linkToDepart = true;
var directionsService;
var directionsRenderer;

$(document).ready(function () {
    // Gestion du changement de type pour les champs date et heure
    $('.dateInput').on('focus', function () {
        $(this).prop('type', 'date'); // Afficher le sélecteur de date au focus
    });

    $('.dateInput').on('blur', function () {
        if ($(this).val() === '') {
            $(this).prop('type', 'text'); // Revenir au type texte si l'entrée est vide
        }
    });

    $('.timeInput').on('focus', function () {
        $(this).prop('type', 'time'); // Afficher le sélecteur de temps au focus
    });

    $('.timeInput').on('blur', function () {
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

    $('select[name="nouvel_input"]').on('change', function () {
        var campusInput = $(this);
        var depart = $('input[name="depart"]');
        var arrivee = $('input[name="arrivee"]');

        if (linkToDepart) {
            depart.val(campusInput.val());
        } else {
            arrivee.val(campusInput.val());
        }

        updateLabelPosition(campusInput);
        updateLabelPosition(depart);
        updateLabelPosition(arrivee);
        handleCampusSelection(); // Gérer la sélection du campus
    });

    // Échange des valeurs des champs de départ et d'arrivée
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

    // Pré-remplissage du champ de départ ou d'arrivée en fonction de la saisie dans le champ de campus
    $('input[name="nouvel_input"]').on('input', function () {
        var campusInput = $(this).val();
        if (linkToDepart) {
            $('input[name="depart"]').val(campusInput).trigger('input');
        } else {
            $('input[name="arrivee"]').val(campusInput).trigger('input');
        }
        updateRoute(); // Calculer et afficher l'itinéraire
    });

    // Mise à jour de la position du label et de la barre de soulignement pour les champs de saisie
    $('input').on('input', function () {
        updateLabelPosition($(this));
    });

    // Validation pour autoriser uniquement les chiffres dans la rubrique prix
    $('input[name="prix"]').on('input', function () {
        $(this).val($(this).val().replace(/\D/g, '')); // Supprimer tout caractère non numérique
        updateLabelPosition($(this));
    });

    // Gestion de la soumission du formulaire
    $('#commentForm').on('submit', function (e) {
        e.preventDefault();
        const commentaire = $('#commentaire').val();
        const prix = $('input[name="prix"]').val();
        if (commentaire) {
            alert('Merci pour votre commentaire: ' + commentaire + '\nPrix: ' + prix);
            // Ajoutez ici le code pour envoyer le commentaire et le prix au serveur
        } else {
            alert('Veuillez écrire un commentaire.');
        }
    });

    // Affichage et masquage de la carte
    $('#act').on('click', function (e) {
        e.stopPropagation();
        $('#map').toggleClass('hidden'); // Afficher/masquer la carte
        var element = $('.container');
        if ($('#map').hasClass('hidden')) {
            element.css('height', '145vw');
        } else {
            element.css('height', '190vw');
        }
    });

    $('#depart').on('click', function () {
        initAutocomplete1(); // Initialiser l'autocomplétion pour l'entrée de départ
    });

    $('#arrivee').on('click', function () {
        initAutocomplete2(); // Initialiser l'autocomplétion pour l'entrée d'arrivée
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

// Fonction pour mettre à jour la position du label et de la barre de soulignement
function updateLabelPosition(input) {
    if (input.val()) {
        input.siblings('.label').css({
            'top': '-20px',
            'font-size': '25px',
            'color': '#333'
        });
        input.siblings('.underline').css('transform', 'scaleX(1)');
    } else {
        input.siblings('.label').css({
            'top': '0',
            'font-size': '42px',
            'color': 'black',
            'opacity': '0.3'
        });
        input.siblings('.underline').css('transform', 'scaleX(0)');
    }

    // Ajustement de la taille du label lorsque le champ est en focus
    input.on('focus', function () {
        if (!input.val()) {
            input.siblings('.label').css({
                'font-size': '25px'
            });
        }
    });

    // Restauration de la taille du label lorsque le champ perd le focus
    input.on('blur', function () {
        if (!input.val()) {
            input.siblings('.label').css({
                'font-size': '42px'
            });
        }
    });
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

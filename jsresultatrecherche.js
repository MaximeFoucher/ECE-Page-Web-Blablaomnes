function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden'); // Ouvrir le modal
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden'); // Fermer le modal
}

$(document).ready(function () {
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
});

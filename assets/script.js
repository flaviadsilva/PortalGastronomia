// Portal Gastronomia - JavaScript

document.addEventListener('DOMContentLoaded', function() {

    // Menu mobile
    var menuBtn = document.getElementById('mobileMenuBtn');
    var mainNav = document.getElementById('mainNav');

    if (menuBtn && mainNav) {
        mainNav.classList.add('hidden');

        menuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('hidden');
            var icon = menuBtn.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });

        // desktop sempre visivel
        function checkWidth() {
            if (window.innerWidth > 768) {
                mainNav.classList.remove('hidden');
            } else {
                mainNav.classList.add('hidden');
            }
        }
        window.addEventListener('resize', checkWidth);
        checkWidth();
    }

    // auto fechar alertas
    var alertas = document.querySelectorAll('.alert');
    for (var i = 0; i < alertas.length; i++) {
        (function(alerta) {
            setTimeout(function() {
                alerta.style.display = 'none';
            }, 5000);
        })(alertas[i]);
    }

    // preview imagem upload
    var fileInputs = document.querySelectorAll('input[type="file"]');
    for (var j = 0; j < fileInputs.length; j++) {
        fileInputs[j].addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;
            var preview = document.getElementById('imagePreview');
            if (preview && file.type.indexOf('image') === 0) {
                var reader = new FileReader();
                reader.onload = function(ev) {
                    preview.innerHTML = '<img src="' + ev.target.result + '" style="max-width:100%;border-radius:6px;margin-top:8px;">';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // confirmacao de exclusao
    var deletes = document.querySelectorAll('.confirm-delete');
    for (var k = 0; k < deletes.length; k++) {
        deletes[k].addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja excluir?')) {
                e.preventDefault();
            }
        });
    }
});

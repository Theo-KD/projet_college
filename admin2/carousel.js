const radios = document.querySelectorAll('.edit-radio');
const editor = document.getElementById('editor');
const form = document.getElementById('editForm');
const successMsg = document.getElementById('successMsg');
const currentImage = document.getElementById('currentImage');
const existingSelect = document.getElementById('existingImage');

function updateEditor(){
    const selected = document.querySelector('.edit-radio:checked');
    if(selected){
        editor.classList.remove('hidden');
        document.getElementById('slide_id').value = selected.dataset.id;
        document.getElementById('title').value = selected.dataset.title;
        document.getElementById('subtitle').value = selected.dataset.subtitle;
        document.getElementById('text').value = selected.dataset.text;

        // Afficher image actuelle
        currentImage.src = '../col6/img/carousel/' + selected.dataset.image;
        existingSelect.value = ''; // reset choix existant

        radios.forEach(r => { if(r !== selected) r.checked = false; });
    } else {
        editor.classList.add('hidden');
        document.getElementById('slide_id').value = '';
        document.getElementById('title').value = '';
        document.getElementById('subtitle').value = '';
        document.getElementById('text').value = '';
        currentImage.src = '';
        existingSelect.value = '';
    }
}

radios.forEach(radio => radio.addEventListener('change', updateEditor));
updateEditor();

// Gestion activation / désactivation
document.querySelectorAll('.active-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const id = this.closest('.slide').querySelector('.edit-radio').dataset.id;
        const active = this.checked ? 1 : 0;

        fetch('toggle_active.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}&active=${active}`
        })
        .then(res => res.text())
        .then(data => console.log(data))
        .catch(err => console.error(err));
    });
});

// AJAX pour enregistrer modification
if(form){
    form.addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(form);
        fetch('update_carousel.php', { method:'POST', body: formData })
        .then(r => r.text())
        .then(data => {
            successMsg.style.display = 'block';
            setTimeout(() => { successMsg.style.display = 'none'; }, 3000);
        })
        .catch(err => console.error(err));
    });
}
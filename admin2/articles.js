// Sélectionner / désélectionner tout
document.getElementById('selectAll').addEventListener('change', function () {
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => {
        cb.checked = this.checked;
    });
});

// Supprimer les articles sélectionnés
document.getElementById('deleteSelected').addEventListener('click', function () {

    let ids = Array.from(
        document.querySelectorAll('input[name="ids[]"]:checked')
    ).map(cb => cb.value);

    if (ids.length === 0) {
        alert("Aucun article sélectionné");
        return;
    }

    if (!confirm("Supprimer les articles sélectionnés ?")) return;

    fetch('articles_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: ids.map(id => 'ids[]=' + encodeURIComponent(id)).join('&')
    })
    .then(res => res.text())
    .then(res => {
    if (res.trim() === 'success') {
        location.reload();
    } else {
        alert("Erreur lors de la suppression");
        console.error(res);
    }
});
});
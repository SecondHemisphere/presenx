document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('alertConfirmBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            document.getElementById('customAlert').style.display = 'none';
        });
    }
    
    // Cerrar al hacer clic fuera del modal
    const customAlert = document.getElementById('customAlert');
    if (customAlert) {
        customAlert.addEventListener('click', function(e) {
            if (e.target === customAlert) {
                customAlert.style.display = 'none';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-eliminar');
    const confirmModal = document.getElementById('confirmModal');
    const confirmDelete = document.getElementById('confirmDelete');
    const confirmCancel = document.getElementById('confirmCancel');
    
    let currentForm = null;
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentForm = this.closest('form');
            confirmModal.style.display = 'flex';
        });
    });
    
    confirmDelete.addEventListener('click', function() {
        if(currentForm) {
            currentForm.submit();
        }
        confirmModal.style.display = 'none';
    });
    
    confirmCancel.addEventListener('click', function() {
        confirmModal.style.display = 'none';
    });
    
    // Cerrar al hacer clic fuera del modal
    confirmModal.addEventListener('click', function(e) {
        if(e.target === confirmModal) {
            confirmModal.style.display = 'none';
        }
    });
});
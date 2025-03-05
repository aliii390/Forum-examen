document.addEventListener('DOMContentLoaded', () => {
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const profileModal = document.getElementById('profile-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const cancelEditBtn = document.getElementById('cancel-edit');
    const profileEditForm = document.getElementById('profile-edit-form');

    // Open Modal
    editProfileBtn.addEventListener('click', () => {
        profileModal.classList.remove('hidden');
    });

    // Close Modal Functions
    const closeModal = () => {
        profileModal.classList.add('hidden');
    };

    closeModalBtn.addEventListener('click', closeModal);
    cancelEditBtn.addEventListener('click', closeModal);

    // Close modal when clicking outside the modal content
    profileModal.addEventListener('click', (event) => {
        if (event.target === profileModal) {
            closeModal();
        }
    });

    // Form Submission
    profileEditForm.addEventListener('submit', (event) => {
        event.preventDefault();
        
        // Collect form data
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            address: document.getElementById('address').value,
            bio: document.getElementById('bio').value
        };

        // Here you would typically send the data to the server
        console.log('Profile Update Data:', formData);
        
        // For demonstration, update the page with new data
        document.querySelector('.text-2xl.font-bold.text-gray-800').textContent = formData.name;
        
        // Close the modal
        closeModal();

        // Optional: Show a success toast or notification
        alert('Profil mis à jour avec succès!');
    });
});
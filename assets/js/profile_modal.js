// Modal functionality for Edit Profile
document.getElementById('editProfileBtn').addEventListener('click', function() {
    document.getElementById('editProfileModal').style.display = 'flex'; // Use 'flex' to activate centering
});

document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('editProfileModal').style.display = 'none'; // Hide the modal
});

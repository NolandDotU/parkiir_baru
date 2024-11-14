// Custom JavaScript

// Confirm before deleting a parking slot
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll("form[action='delete_slot.php'] button");

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            const confirmed = confirm("Are you sure you want to delete this parking slot?");
            if (!confirmed) {
                event.preventDefault();  // Prevent the form from submitting if not confirmed
            }
        });
    });
});

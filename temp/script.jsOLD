document.addEventListener('DOMContentLoaded', function() {

    // Main event delegation function
    document.addEventListener('input', async function(event) {
        const input = event.target;
 
        if (input.type !== 'password') {
            return;
        }

        const listItem = input.parentNode;
        try {
            handlePasswordInput(listItem);
        } catch (error) {
            console.error('There was a problem with the handling the password input:', error);
        }
    });

    // Handle button clicks.
    document.addEventListener('click', async function(event) {
        const element = event.target;

        if (element.classList.contains('save')) {
            const listItem = element.parentNode;
            try {
                handleSaveButtonClick(listItem);
            } catch (error) {
                console.error('There was a problem with the save button click:', error);
            }
        } else if (element.classList.contains('delete')) {
            try {
                handleDeleteButtonClick(listItem);
            } catch (error) {
                console.error('There was a problem with the delete button click:', error);
            }
        }
    });
});
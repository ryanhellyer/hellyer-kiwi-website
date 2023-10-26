document.addEventListener('DOMContentLoaded', function() {

    // Main event delegation function
    document.addEventListener('input', async function(event) {
        const input = event.target as HTMLInputElement;
        if (input && input.type !== 'password') {
            return;
        }

        const listItem = input.parentNode as HTMLElement;
        try {
            handlePasswordInput(listItem);
        } catch (error) {
            console.error('There was a problem with the handling the password input:', error);
        }
    });

    // Handle button clicks.
    document.addEventListener('click', async function(event) {
        const element = event.target as HTMLElement;

        if (element.classList.contains('save')) {
            const listItem = element.parentNode as HTMLElement;
            try {
                handleSaveButtonClick(listItem);
            } catch (error) {
                console.error('There was a problem with the save button click:', error);
            }
        }
         else if (element.classList.contains('delete')) {
            try {
                alert('add delete button click later');
//                handleDeleteButtonClick(listItem);
            } catch (error) {
                console.error('There was a problem with the delete button click:', error);
            }
        }

    });
});
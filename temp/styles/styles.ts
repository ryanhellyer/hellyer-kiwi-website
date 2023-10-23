/**
 * Handles password input and associated actions.
 * @param listItem - The HTMLInputElement.
 */
const handlePasswordInput = async (listItem: HTMLInputElement): Promise<void> => {
    const password = getPassword(listItem);

    if (password.length === 0) {
        //removeDecryptedClass(listItem);
        return;
    }

    try {
        console.log('bla');
        //await decryptAndPopulateTextarea(listItem);
    } catch (error) {
        console.log(error);
        //removeDecryptedClass(listItem);
    }
};

document.addEventListener('DOMContentLoaded', function(): void {
    // Main event delegation function
    document.addEventListener('input', async function(event: Event): Promise<void> {
        const input = event.target as HTMLInputElement;

        if (input.type !== 'password') {
            return;
        }

        try {
            handlePasswordInput(input);
        } catch (error) {
            console.error('There was a problem with handling the password input:', error);
        }
    });
});



/*
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
    */

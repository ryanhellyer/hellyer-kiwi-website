/**
 * Handles password input and associated actions.
 * @param listItem - The HTMLInputElement.
*/
const getPassword = (inputElement: HTMLInputElement): string => {
    return inputElement.value;
};
const removeDecryptedClass = (listItem: HTMLInputElement): void => {
    listItem.classList.remove('decrypted');
};

const decryptAndPopulateTextarea = async (listItem: HTMLInputElement): Promise<void> => {
    // Your decryption logic here
};

/*
const handlePasswordInput = async (listItem: HTMLInputElement): Promise<void> => {
    const password = getPassword(listItem);

    if (password.length === 0) {
        removeDecryptedClass(listItem);
        return;
    }

    try {
        console.log('bla');
        await decryptAndPopulateTextarea(listItem);
    } catch (error) {
        console.log(error);
        removeDecryptedClass(listItem);
    }
};
*/
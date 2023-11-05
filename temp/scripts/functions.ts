

/**
 * Escapes HTML entities in a string.
 * @param {string} str - The string to escape.
 * @returns {string} - The escaped string.
 */
const escHtml = (str: string) => {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML as string;
}

/**
 * Removes the 'decrypted' class from the list item.
 * @param {HTMLElement} listItem - The parent list item.
 */
const addDecryptedClass = (listItem: HTMLElement) => {
    listItem.classList.add('decrypted');
}

/**
 * Removes the 'decrypted' class from the list item.
 * @param {HTMLElement} listItem - The parent list item.
 */
const removeDecryptedClass = (listItem: HTMLElement) => {
    listItem.classList.remove('decrypted');
}

/**
 * Starts the save animation on a list item.
 * 
 * @param {HTMLElement} listItem - The list item on which to start the save animation.
 * 
 * This function adds the 'savingStart' class to the list item, triggering any associated CSS animations or transitions.
 */
const startSaveAnimation = (listItem: HTMLElement) => {
    listItem.classList.add('savingStart');
}

/**
 * Ends the save animation on a list item.
 * 
 * @param {HTMLElement} listItem - The list item on which to end the save animation.
 * 
 * This function performs the following operations:
 * 1. Removes the 'savingStart' class from the list item.
 * 2. Adds the 'savingEnd' class to trigger the end of the animation.
 * 3. Removes the 'savingEnd' class after 1 second to reset the animation state.
 */
const endSaveAnimation = (listItem: HTMLElement) => {
    listItem.classList.remove('savingStart');
    listItem.classList.add('savingEnd');
    setTimeout(() => {
        listItem.classList.remove('savingEnd');
    }, 1000);
}

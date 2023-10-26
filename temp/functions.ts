// Constants and variables
const encryptionConfirmationKey = 'encryptionConfirmationKey|';

/**
 * Encrypts a given content using AES-GCM algorithm.
 * @param {string} content - The content to encrypt.
 * @param {string} password - The password for encryption.
 * @returns {Promise<string>} - The encrypted content.
 */
const encrypt = async (content: string, password: string): Promise<string> => {
    const encoder = new TextEncoder();
    const keyMaterial = await window.crypto.subtle.importKey(
        "raw",
        encoder.encode(password),
        { name: "PBKDF2" },
        false,
        ["deriveBits", "deriveKey"]
    );
    const key = await window.crypto.subtle.deriveKey(
        {
            "name": "PBKDF2",
            salt: encoder.encode("salt"),
            iterations: 1000,
            hash: "SHA-256"
        },
        keyMaterial,
        { "name": "AES-GCM", "length": 256 },
        true,
        [ "encrypt" ]
    );
    const iv = window.crypto.getRandomValues(new Uint8Array(12));
    const encryptedContent = await window.crypto.subtle.encrypt(
        {
            name: "AES-GCM",
            iv: iv
        },
        key,
        encoder.encode(content)
    );
    const encryptedBase64 = btoa(
        String.fromCharCode.apply(
            null,
            Array.from(new Uint8Array(encryptedContent))
        )
    );
    const ivBase64 = btoa(String.fromCharCode.apply(null, Array.from(iv)));

    return `${encryptedBase64}:${ivBase64}`;
}

/**
 * Decrypts a given encrypted string using AES-GCM algorithm.
 * @param {string} encryptedString - The encrypted string to decrypt.
 * @param {string} password - The password for decryption.
 * @returns {Promise<string>} - The decrypted content.
 */
const decrypt = async (encryptedString: string, password: string): Promise<string> => {
    const [encryptedBase64, ivBase64] = encryptedString.split(':');
    const encryptedContent = Uint8Array.from(atob(encryptedBase64), c => c.charCodeAt(0));
    const iv = Uint8Array.from(atob(ivBase64), c => c.charCodeAt(0));
    const encoder = new TextEncoder();
    const keyMaterial = await window.crypto.subtle.importKey(
        "raw",
        encoder.encode(password),
        { name: "PBKDF2" },
        false,
        ["deriveBits", "deriveKey"]
    );
    const key = await window.crypto.subtle.deriveKey(
        {
            "name": "PBKDF2",
            salt: encoder.encode("salt"),
            iterations: 1000,
            hash: "SHA-256"
        },
        keyMaterial,
        { "name": "AES-GCM", "length": 256 },
        true,
        [ "decrypt" ]
    );
    const decryptedContent = await window.crypto.subtle.decrypt(
        {
            name: "AES-GCM",
            iv: iv
        },
        key,
        encryptedContent
    );
    return new TextDecoder().decode(new Uint8Array(decryptedContent));
}

/**
 * Sends POST data to a specified URL.
 * @param {Object} data - The data to send.
 * @param {string} type - The type of request.
 * @returns {Promise<Object>} - The JSON response from the server.
 */
const postData = async (data: Record<string, any> = {}, type: string): Promise<any> => {
    const urlEncodedData = new URLSearchParams(data).toString();
    let response;

    try {
        response = await fetch('./?' + type, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: urlEncodedData
        });
    } catch (error) {
        console.error('Fetch failed:', error);
    }

    if (!response) {
        throw new Error('No response found!');
    }

    if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }

    const jsonResponse = await response.json();
    if (!jsonResponse) {
        throw new Error(`HTTP error! status: ${response.status}`);
    }
    return jsonResponse;
}

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
 * Hashes a password using SHA-256.
 * @param {string} password - The password to hash.
 * @returns {Promise<string>} - The hashed password.
 */
const hashPassword = async (password: string) => {
    const msgBuffer = new TextEncoder().encode(password);

    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashString = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    const hashBase64 = btoa(hashString);
    return hashBase64 as string;
}

/**
 * Gets the value of the input element from its parent node.
 * @param {HTMLElement} listItem - The parent node of the input element.
 * @returns {string} - The value of the input element.
 */
const getPassword = (listItem: HTMLElement) => {
    const inputElement = listItem.querySelector('input[type="password"]') as HTMLInputElement;
    const value = inputElement.value;
    return value as string;
}

/**
 * Gets the save button within a list item.
 * @param {HTMLElement} listItem - The list item element.
 * @returns {HTMLButtonElement} - The save button element.
 */
const getSaveButton = (listItem: HTMLElement) => {
    return listItem.querySelector('.save') as HTMLButtonElement;
}

/**
 * Gets the textarea element within a list item.
 * @param {HTMLElement} listItem - The list item element.
 * @returns {HTMLTextAreaElement} - The textarea element.
 */
const getTextArea = (listItem: HTMLElement) => {
    return listItem.querySelector('textarea') as HTMLTextAreaElement;
}

/**
 * Gets the delete button within a list item.
 * @param {HTMLElement} listItem - The list item element.
 * @returns {HTMLButtonElement} - The delete button element.
 */
const getDeleteButton = (listItem: HTMLElement) => {
    return listItem.querySelector('.delete') as HTMLInputElement;
}

/**
 * Gets the message paragraph within a list item.
 * @param {HTMLElement} listItem - The list item element.
 * @returns {HTMLParagraphElement} - The message paragraph element.
 */
const getMessage = (listItem: HTMLElement) => {
    return listItem.querySelector('p') as HTMLParagraphElement;
}

/**
 * Hashes a password using the hashPassword function, password is fetched from parent node.
 * @param {HTMLElement} listItem - The parent node of the input element.
 * @returns {Promise<string>} - The hashed password.
 */
const getHash = async (listItem: HTMLElement) => {
	const inputElement = listItem.querySelector('input[type="password"]') as HTMLInputElement;
    const password = inputElement.value as string;
    return await hashPassword(password) as string;
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

/**
 * Handles the click event for the save button within a list item.
 * 
 * @param {HTMLElement} listItem - The parent list item containing the save button.
 * 
 * This function performs the following operations:
 * 1. Retrieves necessary elements and values from the list item.
 * 2. Encrypts the content of the textarea.
 * 3. Sends a POST request to save the encrypted content.
 * 4. Updates the UI based on the server response.
 */
const handleSaveButtonClick = async (listItem: HTMLElement) => {
	const saveButton: HTMLButtonElement | null = getSaveButton(listItem);
    const message: HTMLParagraphElement | null = getMessage(listItem);
    const password: string = getPassword(listItem);

    const textarea: HTMLTextAreaElement | null = getTextArea(listItem);
	const textContent: string | null = textarea ? textarea.value : null;

    const hash: string = await getHash(listItem);

    const contentForEncryption = encryptionConfirmationKey + textContent;
    const encryptedContent = await encrypt(contentForEncryption, password);

    const inputTextElement = listItem.querySelector('input[type=text]') as HTMLInputElement | null;
    const title = inputTextElement ? inputTextElement.value : null;

    const inputHiddenElement = listItem.querySelector('input[type=hidden]') as HTMLInputElement | null;
    const originalTitle = inputHiddenElement ? inputHiddenElement.value : null;

    startSaveAnimation(listItem);

    try {
        const response = await postData({title: title, originalTitle: originalTitle, encryptedContent: encryptedContent, hash: hash}, 'save');
        endSaveAnimation(listItem);
    
        if (response.error) {
            message.innerHTML = escHtml(response.error);
        } else if (inputHiddenElement && title !== null) {
            inputHiddenElement.value = title;
            listItem.classList.remove('new');
            message.innerHTML = escHtml('Much success ma bro!');
        }

    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
    }
}

/**
 * Handles password input and associated actions.
 * @param {HTMLInputElement} listItem - The list item element.
 */
const handlePasswordInput = async (listItem: HTMLElement): Promise<void> => {
    const password = getPassword(listItem);

    if (password.length === 0) {
        removeDecryptedClass(listItem);
        return;
    }

    try {
        await decryptAndPopulateTextarea(listItem);
    } catch (error) {
        console.log(error);
        removeDecryptedClass(listItem);
    }
}

/**
 * Decrypts content and populates the textarea.
 * @param {HTMLElement} listItem - The parent list item.
 */
const decryptAndPopulateTextarea = async (listItem: HTMLElement) => {
    let decryptedContent = '';
    listItem.classList.add('decrypted');

    const password = getPassword(listItem);
    const textarea = getTextArea(listItem);

    // Don't decrypt try to decrypt if this is a new item.
    if (! listItem.classList.contains('new')) {
        decryptedContent = await decrypt(textarea.innerHTML, password);

        // Check if encryption confirmation key was found in the result.
        if (decryptedContent.substring(0, encryptionConfirmationKey.length) !== encryptionConfirmationKey) {
            throw new Error('Confirmation of data decryption failed');
        }

        decryptedContent = decryptedContent.replace(encryptionConfirmationKey, '');
    }

    textarea.innerHTML = escHtml(decryptedContent);

    listItem.classList.add('decrypted');
}

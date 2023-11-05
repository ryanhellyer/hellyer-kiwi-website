"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
class En2EndEncryption {
    constructor() {
        this.salt = "salt";
        this.encoder = new TextEncoder();
    }
    /**
     * Encrypts a given content using AES-GCM algorithm.
     * @param content - The content to encrypt.
     * @param password - The password for encryption.
     * @returns - The encrypted content.
     */
    encrypt(content, password) {
        return __awaiter(this, void 0, void 0, function* () {
            const keyMaterial = yield window.crypto.subtle.importKey("raw", this.encoder.encode(password), { name: "PBKDF2" }, false, ["deriveBits", "deriveKey"]);
            const key = yield window.crypto.subtle.deriveKey({
                "name": "PBKDF2",
                salt: this.encoder.encode(this.salt),
                iterations: 1000,
                hash: "SHA-256"
            }, keyMaterial, { "name": "AES-GCM", "length": 256 }, true, ["encrypt"]);
            const iv = window.crypto.getRandomValues(new Uint8Array(12));
            const encryptedContent = yield window.crypto.subtle.encrypt({
                name: "AES-GCM",
                iv: iv
            }, key, this.encoder.encode(content));
            const encryptedBase64 = btoa(String.fromCharCode.apply(null, Array.from(new Uint8Array(encryptedContent))));
            const ivBase64 = btoa(String.fromCharCode.apply(null, Array.from(iv)));
            return `${encryptedBase64}:${ivBase64}`;
        });
    }
    /**
     * Decrypts a given encrypted string using AES-GCM algorithm.
     * @param encryptedString - The encrypted string to decrypt.
     * @param password - The password for decryption.
     * @returns - The decrypted content.
     */
    decrypt(encryptedString, password) {
        return __awaiter(this, void 0, void 0, function* () {
            const [encryptedBase64, ivBase64] = encryptedString.split(':');
            const encryptedContent = Uint8Array.from(atob(encryptedBase64), c => c.charCodeAt(0));
            const iv = Uint8Array.from(atob(ivBase64), c => c.charCodeAt(0));
            const keyMaterial = yield window.crypto.subtle.importKey("raw", this.encoder.encode(password), { name: "PBKDF2" }, false, ["deriveBits", "deriveKey"]);
            const key = yield window.crypto.subtle.deriveKey({
                "name": "PBKDF2",
                salt: this.encoder.encode(this.salt),
                iterations: 1000,
                hash: "SHA-256"
            }, keyMaterial, { "name": "AES-GCM", "length": 256 }, true, ["decrypt"]);
            const decryptedContent = yield window.crypto.subtle.decrypt({
                name: "AES-GCM",
                iv: iv
            }, key, encryptedContent);
            return new TextDecoder().decode(new Uint8Array(decryptedContent));
        });
    }
}
class En2EndItemHandler {
    /**
     * Gets the value of the input element from its parent node.
     * @param listItem - The parent node of the input element.
     * @returns - The value of the input element.
     */
    getPassword(listItem) {
        const inputElement = listItem.querySelector('input[type="password"]');
        return inputElement.value;
    }
    getSaveButton(listItem) {
        return listItem.querySelector('.save');
    }
    getTextArea(listItem) {
        return listItem.querySelector('textarea');
    }
    getDiv(listItem) {
        return listItem.querySelector('div');
    }
    getDeleteButton(listItem) {
        return listItem.querySelector('.delete');
    }
    getMessage(listItem) {
        return listItem.querySelector('p');
    }
    /**
     * Hashes a password using the hashPassword function, password is fetched from parent node.
     * @param listItem - The parent node of the input element.
     * @returns - The hashed password.
     */
    getHash(listItem) {
        return __awaiter(this, void 0, void 0, function* () {
            const password = this.getPassword(listItem);
            return yield this.hashPassword(password);
        });
    }
    /**
     * Hashes a password using SHA-256.
     * @param password - The password to hash.
     * @returns - The hashed password.
     */
    hashPassword(password) {
        return __awaiter(this, void 0, void 0, function* () {
            const msgBuffer = new TextEncoder().encode(password);
            const hashBuffer = yield crypto.subtle.digest('SHA-256', msgBuffer);
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            const hashString = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            return btoa(hashString);
        });
    }
}
class e2eAjax {
    /**
     * Sends POST data to a specified URL.
     * @param data - The data to send.
     * @param type - The type of request.
     * @returns - The JSON response from the server.
     */
    postData(data = {}, type) {
        return __awaiter(this, void 0, void 0, function* () {
            const urlEncodedData = new URLSearchParams(data).toString();
            let response;
            try {
                response = yield fetch('./?' + type, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: urlEncodedData
                });
            }
            catch (error) {
                console.error('Fetch failed:', error);
                throw error;
            }
            if (!response) {
                throw new Error('No response found!');
            }
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const jsonResponse = yield response.json();
            if (!jsonResponse) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return jsonResponse;
        });
    }
}
/**
 * Escapes HTML entities in a string.
 * @param {string} str - The string to escape.
 * @returns {string} - The escaped string.
 */
const escHtml = (str) => {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
};
/**
 * Removes the 'decrypted' class from the list item.
 * @param {HTMLElement} listItem - The parent list item.
 */
const addDecryptedClass = (listItem) => {
    listItem.classList.add('decrypted');
};
/**
 * Removes the 'decrypted' class from the list item.
 * @param {HTMLElement} listItem - The parent list item.
 */
const removeDecryptedClass = (listItem) => {
    listItem.classList.remove('decrypted');
};
/**
 * Starts the save animation on a list item.
 *
 * @param {HTMLElement} listItem - The list item on which to start the save animation.
 *
 * This function adds the 'savingStart' class to the list item, triggering any associated CSS animations or transitions.
 */
const startSaveAnimation = (listItem) => {
    listItem.classList.add('savingStart');
};
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
const endSaveAnimation = (listItem) => {
    listItem.classList.remove('savingStart');
    listItem.classList.add('savingEnd');
    setTimeout(() => {
        listItem.classList.remove('savingEnd');
    }, 1000);
};

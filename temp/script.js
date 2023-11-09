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
class Ajax {
    getItems() {
        return __awaiter(this, void 0, void 0, function* () {
            try {
                const randomInt = Math.floor(Math.random() * 10000);
                const url = './?data=' + randomInt;
                const response = yield fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const data = response.json();
                return data;
            }
            catch (error) {
                console.error('There was a problem fetching the data:', error);
                throw error;
            }
        });
    }
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
class Encryption {
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
class EventHandlers {
    constructor(inputs) {
        this.inputs = inputs;
        document.addEventListener('input', this.handleInputEvent.bind(this));
        document.addEventListener('click', this.handleClickEvent.bind(this));
    }
    handleInputEvent(event) {
        return __awaiter(this, void 0, void 0, function* () {
            console.log('_');
            const input = event.target;
            if (input && input.type !== 'password') {
                return;
            }
            console.log('xxx');
            const listItem = input.parentNode;
            try {
                yield this.inputs.handlePasswordInput(listItem);
            }
            catch (error) {
                console.error('There was a problem with handling the password input:', error);
            }
        });
    }
    handleClickEvent(event) {
        return __awaiter(this, void 0, void 0, function* () {
            const element = event.target;
            if (element.classList.contains('save')) {
                const listItem = element.parentNode;
                try {
                    this.inputs.handleSaveButtonClick(listItem);
                }
                catch (error) {
                    console.error('There was a problem with the save button click:', error);
                }
            }
            else if (element.classList.contains('delete')) {
                try {
                    alert('add delete button click later');
                    // this.inputs.handleDeleteButtonClick(listItem);
                }
                catch (error) {
                    console.error('There was a problem with the delete button click:', error);
                }
            }
        });
    }
}
class Inputs {
    constructor(inputHelper, animations, ajax, encryption) {
        this.inputHelper = inputHelper;
        this.animations = animations;
        this.ajax = ajax;
        this.encryption = encryption;
    }
    /**
     * Handles the click event for the save button within a list item.
     *
     * @param listItem - The parent list item containing the save button.
     */
    handleSaveButtonClick(listItem) {
        return __awaiter(this, void 0, void 0, function* () {
            const saveButton = this.inputHelper.getSaveButton(listItem);
            const message = this.inputHelper.getMessage(listItem);
            const password = this.inputHelper.getPassword(listItem);
            const textarea = this.inputHelper.getTextArea(listItem);
            const textContent = textarea ? textarea.innerHTML : null;
            const hash = yield this.inputHelper.getHash(listItem);
            const newHash = yield this.inputHelper.getNewHash(listItem);
            const contentForEncryption = config.encryptionConfirmationKey + textContent;
            const encryptedContent = yield this.encryption.encrypt(contentForEncryption, password);
            const inputTextElement = listItem.querySelector('input[type=text]');
            const title = inputTextElement ? inputTextElement.value : null;
            const inputHiddenElement = listItem.querySelector('input[type=hidden]');
            const originalTitle = inputHiddenElement ? inputHiddenElement.value : null;
            this.animations.startSaveAnimation(listItem);
            try {
                const response = yield this.ajax.postData({ title: title, originalTitle: originalTitle, encryptedContent: encryptedContent, hash: hash, textContent: textContent /*@todo remove textContent, just here for testing*/ }, 'save');
                this.animations.endSaveAnimation(listItem);
                if (response.error) {
                    message.innerHTML = this.escHtml(response.error);
                }
                else if (inputHiddenElement && title !== null) {
                    inputHiddenElement.value = title;
                    listItem.classList.remove('new');
                    message.innerHTML = this.escHtml('Much success ma bro!');
                }
            }
            catch (error) {
                console.error('There was a problem with the fetch operation:', error);
            }
        });
    }
    /**
     * Handles password input and associated actions.
     *
     * @param listItem - The list item element.
     */
    handlePasswordInput(listItem) {
        return __awaiter(this, void 0, void 0, function* () {
            const password = this.inputHelper.getPassword(listItem);
            if (password.length === 0) {
                this.inputHelper.unsetDecrypted(listItem);
                return;
            }
            try {
                yield this.decryptAndPopulateTextarea(listItem);
            }
            catch (error) {
                console.log(error);
                this.inputHelper.unsetDecrypted(listItem);
            }
        });
    }
    /**
     * Decrypts content and populates the textarea.
     *
     * @param listItem - The parent list item.
     */
    decryptAndPopulateTextarea(listItem) {
        return __awaiter(this, void 0, void 0, function* () {
            let decryptedContent = '';
            const password = this.inputHelper.getPassword(listItem);
            const textarea = this.inputHelper.getTextArea(listItem);
            const div = this.inputHelper.getDiv(listItem);
            if (!listItem.classList.contains('new')) {
                decryptedContent = yield this.encryption.decrypt(div.innerHTML, password);
                if (decryptedContent.substring(0, config.encryptionConfirmationKey.length) !== config.encryptionConfirmationKey) {
                    throw new Error('Confirmation of data decryption failed');
                }
                decryptedContent = decryptedContent.replace(config.encryptionConfirmationKey, '');
                this.inputHelper.setDecrypted(listItem);
            }
            textarea.innerHTML = this.escHtml(decryptedContent);
            listItem.classList.add('decrypted');
        });
    }
    /**
     * Escapes HTML entities in a string.
     * @param {string} str - The string to escape.
     * @returns {string} - The escaped string.
     */
    escHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        const escapedString = div.innerHTML;
        // We need to manually-deescape <b>, <i> and <u> tags.
        const deEscapedString = escapedString.replace(/&lt;b&gt;/g, '<b>')
            .replace(/&lt;\/b&gt;/g, '</b>')
            .replace(/&lt;i&gt;/g, '<i>')
            .replace(/&lt;\/i&gt;/g, '</i>')
            .replace(/&lt;u&gt;/g, '<u>')
            .replace(/&lt;\/u&gt;/g, '</u>')
            .replace(/&lt;\/br&gt;/g, '<br>');
        return deEscapedString;
    }
}
class InputHelper {
    /**
     * Retrieves the password value from an input element inside the given list item.
     *
     * @param listItem - The parent node containing the input element.
     * @returns - The value of the password input element.
     */
    getPassword(listItem) {
        const inputElement = listItem.querySelector('.password');
        return inputElement.value;
    }
    /**
     * Retrieves the new password value from an input element inside the given list item.
     *
     * @param listItem - The parent node containing the input element.
     * @returns - The value of the new password input element.
     */
    getNewPassword(listItem) {
        const inputElement = listItem.querySelector('.new-password');
        return inputElement.value;
    }
    /**
     * Retrieves the save button element from the given list item.
     *
     * @param listItem - The parent node containing the save button.
     * @returns - The save button element.
     */
    getSaveButton(listItem) {
        return listItem.querySelector('.save');
    }
    /**
     * Retrieves the text area (editable content) element from the given list item.
     *
     * @param listItem - The parent node containing the text area.
     * @returns - The text area element.
     */
    getTextArea(listItem) {
        return listItem.querySelector('.editableContent');
    }
    /**
     * Retrieves the div (encrypted content) element from the given list item.
     *
     * @param listItem - The parent node containing the div.
     * @returns - The div element.
     */
    getDiv(listItem) {
        return listItem.querySelector('.encryptedContent');
    }
    /**
     * Retrieves the delete button element from the given list item.
     *
     * @param listItem - The parent node containing the delete button.
     * @returns - The delete button element.
     */
    getDeleteButton(listItem) {
        return listItem.querySelector('.delete');
    }
    /**
     * Retrieves the message paragraph element from the given list item.
     *
     * @param listItem - The parent node containing the paragraph.
     * @returns - The paragraph element.
     */
    getMessage(listItem) {
        return listItem.querySelector('p');
    }
    /**
     * Adds the 'decrypted' class to the list item to indicate it has been decrypted.
     *
     * @param listItem - The list item to be marked as decrypted.
     */
    setDecrypted(listItem) {
        listItem.classList.add('decrypted');
    }
    /**
     * Removes the 'decrypted' class from the list item to indicate it is no longer decrypted.
     *
     * @param listItem - The list item to be unmarked as decrypted.
     */
    unsetDecrypted(listItem) {
        listItem.classList.remove('decrypted');
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
     * Hashes a new password using the hashPassword function, new password is fetched from parent node.
     * @param listItem - The parent node of the input element.
     * @returns - The hashed password.
     */
    getNewHash(listItem) {
        return __awaiter(this, void 0, void 0, function* () {
            const password = this.getNewPassword(listItem);
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
class Animations {
    /**
     * Starts the save animation on a list item.
     *
     * @param listItem - The list item on which to start the save animation.
     *
     * This method adds the 'savingStart' class to the list item, triggering any associated CSS animations or transitions.
     */
    startSaveAnimation(listItem) {
        listItem.classList.add('savingStart');
    }
    /**
     * Ends the save animation on a list item.
     *
     * @param listItem - The list item on which to end the save animation.
     *
     * This method performs the following operations:
     * 1. Removes the 'savingStart' class from the list item.
     * 2. Adds the 'savingEnd' class to trigger the end of the animation.
     * 3. Removes the 'savingEnd' class after 1 second to reset the animation state.
     */
    endSaveAnimation(listItem) {
        listItem.classList.remove('savingStart');
        listItem.classList.add('savingEnd');
        setTimeout(() => {
            listItem.classList.remove('savingEnd');
        }, 1000);
    }
}
const config = {
    'encryptionConfirmationKey': 'encryptionConfirmationKey|',
    'blockTemplate': `
<li>
    <input type="text" value="{{name}}">
    <input type="hidden" value="{{name}}">
    <input class="password" type="password" value="" placeholder="Enter password">
    <div class="editableContent" contenteditable="true"></div>
    <div class="encryptedContent">{{encryptedContent}}</div>
    <button class="save">Save</button>
    <button class="delete">Delete</button>
    <input class="new-password" type="password" value="" placeholder="Enter new password">
    <p></p>
</li>`
};
document.addEventListener('DOMContentLoaded', function () {
    return __awaiter(this, void 0, void 0, function* () {
        const ajax = new Ajax();
        // Populate the blocks.
        let items = yield ajax.getItems();
        const blocks = document.getElementById('blocks');
        if (blocks) {
            for (const item of items) {
                blocks.innerHTML += getBlock(item);
            }
        }
        const inputHelper = new InputHelper();
        const animations = new Animations();
        const encryption = new Encryption();
        const inputs = new Inputs(inputHelper, animations, ajax, encryption);
        const eventHandlers = new EventHandlers(inputs);
        function getBlock(item) {
            let HTML = config.blockTemplate;
            HTML = HTML.replaceAll('{{encryptedContent}}', item.encryptedContent);
            HTML = HTML.replaceAll('{{name}}', item.name);
            return HTML;
        }
    });
});

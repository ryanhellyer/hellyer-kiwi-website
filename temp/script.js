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
class LoadItems {
    getItems() {
        return __awaiter(this, void 0, void 0, function* () {
            const randomInt = Math.floor(Math.random() * 10000);
            const data = yield this.fetchJSON('/temp/?data=' + randomInt);
            return data;
        });
    }
    fetchJSON(url) {
        return __awaiter(this, void 0, void 0, function* () {
            try {
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
const config = {
    'encryptionConfirmationKey': 'encryptionConfirmationKey|',
    'blockTemplate': `
<li>
    <input type="text" value="{{name}}">
    <input type="hidden" value="{{name}}">
    <input type="password" value="" placeholder="Enter password">
    <div class="editableContent" contenteditable="true"></div>
    <div class="encryptedContent">{{encryptedContent}}</div>
    <button class="save">Save</button>
    <button class="delete">Delete</button>
<p></p>
</li>`
};
document.addEventListener('DOMContentLoaded', function () {
    return __awaiter(this, void 0, void 0, function* () {
        const loadItems = new LoadItems();
        const encryption = new Encryption();
        let items = yield loadItems.getItems();
        const blocks = document.getElementById('blocks');
        if (blocks) {
            for (const item of items) {
                blocks.innerHTML += getBlock(item);
            }
        }
        function getBlock(item) {
            let HTML = config.blockTemplate;
            HTML = HTML.replace('{{encryptedContent}}', item.encryptedContent);
            HTML = HTML.replace('{{name}}', item.name);
            return HTML;
        }
    });
});

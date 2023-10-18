document.addEventListener('DOMContentLoaded', function() {
    const encryptionConfirmationKey = 'encryptionConfirmationKey|';
    const passwordInputs = document.querySelectorAll('input[type="password"]');

    async function encrypt(content, password) {
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
        const encryptedBase64 = btoa(String.fromCharCode.apply(null, new Uint8Array(encryptedContent)));
        const ivBase64 = btoa(String.fromCharCode.apply(null, iv));
        return `${encryptedBase64}:${ivBase64}`;
    }

    async function decrypt(encryptedString, password) {
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

    async function postData(data = {}, type) {
        const urlEncodedData = new URLSearchParams(data).toString();

        const response = await fetch('./?' + type, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: urlEncodedData
        });
    
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
    
        return await response.json();
    }    

    function escHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    async function hashPassword(password) {
        const msgBuffer = new TextEncoder().encode(password);
        const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashString = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        const hashBase64 = btoa(hashString);
        return hashBase64;
    }

    passwordInputs.forEach(input => {
        input.addEventListener('input', async function() {
            const password = this.value;
            const listItem = this.parentNode;
            const textarea = listItem.querySelector('textarea');
            const saveButton = listItem.querySelector('.save');
            const deleteButton = listItem.querySelector('.delete');
            const message = listItem.querySelector('p');
            const element = document.querySelector('.some-element');
            const hash = await hashPassword(password);

            try {
                if (password.length === 0) {
                    throw new Error('No password entered');
                }

                let decryptedContent = '';

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

                // Delete the item.
                deleteButton.addEventListener('click', async function() {
                    const title = listItem.querySelector('input[type=text]').value;

                    postData({hash: hash, title: title}, 'delete')
                    .then(data => {
                        console.log('Item has been deleted');
                        // DLETE THE ITEM @todo
                    })
                    .catch(error => {
                        console.error('There was a problem deleting the item:', error);
                    });

                });

                // Save the result.
                saveButton.addEventListener('click', async function() {
                    const textContent = textarea.value;
                    const contentForEncryption = encryptionConfirmationKey + textContent;
                    const encryptedContent = await encrypt(contentForEncryption, password);

                    const title = listItem.querySelector('input[type=text]').value;
                    const originalTitle = listItem.querySelector('input[type=hidden]').value;

                    // Start save animation
                    listItem.classList.add('savingStart');

                    console.log(hash);

                    postData({title: title, originalTitle: originalTitle, encryptedContent: encryptedContent, hash: hash}, 'save')
                    .then(data => {

                        // End save animation
                        listItem.classList.remove('savingStart');
                        listItem.classList.add('savingEnd');
                        setTimeout(() => {
                            listItem.classList.remove('savingEnd');
                        }, 1000);

                        if(data.error) {
                            message.innerHTML = escHtml(data.error);
                        } else {
                            listItem.querySelector('input[type=hidden]').value = title;
                            listItem.classList.remove('new');

                            message.innerHTML = escHtml('Much success ma bro!');
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with the fetch operation:', error);
                    });


                });

            } catch (error) {
                console.log(error);
                listItem.classList.remove('decrypted');
            }
        });
    });
});
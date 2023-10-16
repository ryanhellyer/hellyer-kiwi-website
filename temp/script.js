document.addEventListener('DOMContentLoaded', function() {
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

    passwordInputs.forEach(input => {
        input.addEventListener('input', async function() {
            const encryptedDiv = this.nextElementSibling;
            const decryptedDiv = encryptedDiv.nextElementSibling;
            const password = this.value;

            const encryptedContent = await encrypt(encryptedDiv.innerHTML, password);
            console.log(encryptedContent);

            const decryptedContent = await decrypt(encryptedContent, password);
            console.log(decryptedContent);

            if (password.length > 0) {
                const encryptedString = await encrypt(encryptedContent, password);
                const decryptedContent = await decrypt(encryptedString, password);
                
                if (decryptedContent.substring(0, 5) === "ryan|") {
                    encryptedDiv.classList.add('passwordEntered');
                    decryptedDiv.classList.add('passwordEntered');
                    decryptedDiv.innerHTML = decryptedContent;
                } else {
                    encryptedDiv.classList.remove('passwordEntered');
                    decryptedDiv.classList.remove('passwordEntered');
                }
            } else {
                encryptedDiv.classList.remove('passwordEntered');
                decryptedDiv.classList.remove('passwordEntered');
            }
        });
    });
});
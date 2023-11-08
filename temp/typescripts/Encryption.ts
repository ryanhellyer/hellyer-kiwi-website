class e2eEncryption {
    private salt = "salt";
    private encoder: TextEncoder;

    constructor() {
        this.encoder = new TextEncoder();
    }

    /**
     * Encrypts a given content using AES-GCM algorithm.
     * @param content - The content to encrypt.
     * @param password - The password for encryption.
     * @returns - The encrypted content.
     */
    public async encrypt(content: string, password: string): Promise<string> {
        const keyMaterial = await window.crypto.subtle.importKey(
            "raw",
            this.encoder.encode(password),
            { name: "PBKDF2" },
            false,
            ["deriveBits", "deriveKey"]
        );
        const key = await window.crypto.subtle.deriveKey(
            {
                "name": "PBKDF2",
                salt: this.encoder.encode(this.salt),
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
            this.encoder.encode(content)
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
     * @param encryptedString - The encrypted string to decrypt.
     * @param password - The password for decryption.
     * @returns - The decrypted content.
     */
    public async decrypt(encryptedString: string, password: string): Promise<string> {
        const [encryptedBase64, ivBase64] = encryptedString.split(':');
        const encryptedContent = Uint8Array.from(atob(encryptedBase64), c => c.charCodeAt(0));
        const iv = Uint8Array.from(atob(ivBase64), c => c.charCodeAt(0));

        const keyMaterial = await window.crypto.subtle.importKey(
            "raw",
            this.encoder.encode(password),
            { name: "PBKDF2" },
            false,
            ["deriveBits", "deriveKey"]
        );
        const key = await window.crypto.subtle.deriveKey(
            {
                "name": "PBKDF2",
                salt: this.encoder.encode(this.salt),
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
}
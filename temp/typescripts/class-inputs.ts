class e2eInputs {
    private itemHandler: e2eItemHandler;
    private animations: e2eAnimations;
    private ajax: e2eAjax;
    private encryption: e2eEncryption;

    constructor(itemHandler: e2eItemHandler, animations: e2eAnimations, ajax: e2eAjax, encryption: e2eEncryption) {
        this.itemHandler = itemHandler;
        this.animations = animations;
        this.ajax = ajax;
        this.encryption = encryption;
    }

    /**
     * Handles the click event for the save button within a list item.
     * 
     * @param listItem - The parent list item containing the save button.
     */
    public async handleSaveButtonClick(listItem: HTMLElement): Promise<void> {
        const saveButton: HTMLButtonElement | null = this.itemHandler.getSaveButton(listItem);
        const message: HTMLParagraphElement | null = this.itemHandler.getMessage(listItem);
        const password: string = this.itemHandler.getPassword(listItem);

        const textarea: HTMLDivElement | null = this.itemHandler.getTextArea(listItem);
        const textContent: string | null = textarea ? textarea.innerHTML : null;

        const hash: string = await this.itemHandler.getHash(listItem);
        const contentForEncryption = encryptionConfirmationKey + textContent;

        const encryptedContent = await this.encryption.encrypt(contentForEncryption, password);

        const inputTextElement = listItem.querySelector('input[type=text]') as HTMLInputElement | null;
        const title = inputTextElement ? inputTextElement.value : null;

        const inputHiddenElement = listItem.querySelector('input[type=hidden]') as HTMLInputElement | null;
        const originalTitle = inputHiddenElement ? inputHiddenElement.value : null;

        this.animations.startSaveAnimation(listItem);
        try {
            const response = await this.ajax.postData({ title: title, originalTitle: originalTitle, encryptedContent: encryptedContent, hash: hash, textContent: textContent /*@todo remove textContent, just here for testing*/}, 'save');
            this.animations.endSaveAnimation(listItem);

            if (response.error) {
                message.innerHTML = this.escHtml(response.error);
            } else if (inputHiddenElement && title !== null) {
                inputHiddenElement.value = title;
                listItem.classList.remove('new');
                message.innerHTML = this.escHtml('Much success ma bro!');
            }
        } catch (error) {
            console.error('There was a problem with the fetch operation:', error);
        }
    }

    /**
     * Handles password input and associated actions.
     *
     * @param listItem - The list item element.
     */
    public async handlePasswordInput(listItem: HTMLElement): Promise<void> {
        const password = this.itemHandler.getPassword(listItem);

        if (password.length === 0) {
            this.itemHandler.unsetDecrypted(listItem);
            return;
        }

        try {
            await this.decryptAndPopulateTextarea(listItem);
        } catch (error) {
            console.log(error);
            this.itemHandler.unsetDecrypted(listItem);
        }
    }

    /**
     * Decrypts content and populates the textarea.
     * 
     * @param listItem - The parent list item.
     */
    private async decryptAndPopulateTextarea(listItem: HTMLElement): Promise<void> {
        let decryptedContent = '';

        const password = this.itemHandler.getPassword(listItem);
        const textarea = this.itemHandler.getTextArea(listItem);
        const div = this.itemHandler.getDiv(listItem);

        if (!listItem.classList.contains('new')) {
            decryptedContent = await this.encryption.decrypt(div.innerHTML, password);

            if (decryptedContent.substring(0, encryptionConfirmationKey.length) !== encryptionConfirmationKey) {
                throw new Error('Confirmation of data decryption failed');
            }

            decryptedContent = decryptedContent.replace(encryptionConfirmationKey, '');
            this.itemHandler.setDecrypted(listItem);
        }

        textarea.innerHTML = this.escHtml(decryptedContent);
        listItem.classList.add('decrypted');
    }

    /**
     * Escapes HTML entities in a string.
     * @param {string} str - The string to escape.
     * @returns {string} - The escaped string.
     */
    private escHtml(str: string): string {
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

        return deEscapedString as string;
    }
}

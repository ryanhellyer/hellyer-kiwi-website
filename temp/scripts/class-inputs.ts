class e2eInputs {
    /**
     * Handles the click event for the save button within a list item.
     * 
     * @param listItem - The parent list item containing the save button.
     */
    public async handleSaveButtonClick(listItem: HTMLElement): Promise<void> {
        const saveButton: HTMLButtonElement | null = itemHandler.getSaveButton(listItem);
        const message: HTMLParagraphElement | null = itemHandler.getMessage(listItem);
        const password: string = itemHandler.getPassword(listItem);

        const textarea: HTMLTextAreaElement | null = itemHandler.getTextArea(listItem);
        const textContent: string | null = textarea ? textarea.value : null;

        const hash: string = await itemHandler.getHash(listItem);

        const contentForEncryption = encryptionConfirmationKey + textContent;

        const encryptedContent = await encryption.encrypt(contentForEncryption, password);

        const inputTextElement = listItem.querySelector('input[type=text]') as HTMLInputElement | null;
        const title = inputTextElement ? inputTextElement.value : null;

        const inputHiddenElement = listItem.querySelector('input[type=hidden]') as HTMLInputElement | null;
        const originalTitle = inputHiddenElement ? inputHiddenElement.value : null;

        this.startSaveAnimation(listItem);

        try {
            const response = await ajax.postData({ title: title, originalTitle: originalTitle, encryptedContent: encryptedContent, hash: hash }, 'save');
            this.endSaveAnimation(listItem);

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
     * 
     * @param listItem - The list item element.
     */
    public async handlePasswordInput(listItem: HTMLElement): Promise<void> {
        const password = itemHandler.getPassword(listItem);

        if (password.length === 0) {
            this.removeDecryptedClass(listItem);
            return;
        }

        try {
            await this.decryptAndPopulateTextarea(listItem);
        } catch (error) {
            console.log(error);
            this.removeDecryptedClass(listItem);
        }
    }

    /**
     * Decrypts content and populates the textarea.
     * 
     * @param listItem - The parent list item.
     */
    private async decryptAndPopulateTextarea(listItem: HTMLElement): Promise<void> {
        let decryptedContent = '';

        const password = itemHandler.getPassword(listItem);
        const textarea = itemHandler.getTextArea(listItem);
        const div = itemHandler.getDiv(listItem);

        if (!listItem.classList.contains('new')) {
            decryptedContent = await encryption.decrypt(div.innerHTML, password);

            if (decryptedContent.substring(0, encryptionConfirmationKey.length) !== encryptionConfirmationKey) {
                throw new Error('Confirmation of data decryption failed');
            }

            decryptedContent = decryptedContent.replace(encryptionConfirmationKey, '');
            this.addDecryptedClass(listItem);
        }

        textarea.innerHTML = escHtml(decryptedContent);
        listItem.classList.add('decrypted');
    }

    private startSaveAnimation(listItem: HTMLElement): void {
        // Add logic to start the save animation
    }

    private endSaveAnimation(listItem: HTMLElement): void {
        // Add logic to end the save animation
    }

    private addDecryptedClass(listItem: HTMLElement): void {
        listItem.classList.add('decrypted');
    }

    private removeDecryptedClass(listItem: HTMLElement): void {
        listItem.classList.remove('decrypted');
    }
}

class InputHelper {

    /**
     * Retrieves the password value from an input element inside the given list item.
     * 
     * @param listItem - The parent node containing the input element.
     * @returns - The value of the password input element.
     */
    public getPassword(listItem: HTMLElement): string {
        const inputElement = listItem.querySelector('.password') as HTMLInputElement;
        return inputElement.value;
    }

    /**
     * Retrieves the new password value from an input element inside the given list item.
     * 
     * @param listItem - The parent node containing the input element.
     * @returns - The value of the new password input element.
     */
    public getNewPassword(listItem: HTMLElement): string {
        const inputElement = listItem.querySelector('.new-password') as HTMLInputElement;
        return inputElement.value;
    }

    /**
     * Retrieves the save button element from the given list item.
     * 
     * @param listItem - The parent node containing the save button.
     * @returns - The save button element.
     */
    public getSaveButton(listItem: HTMLElement): HTMLButtonElement {
        return listItem.querySelector('.save') as HTMLButtonElement;
    }

    /**
     * Retrieves the text area (editable content) element from the given list item.
     * 
     * @param listItem - The parent node containing the text area.
     * @returns - The text area element.
     */
    public getTextArea(listItem: HTMLElement): HTMLDivElement {
        return listItem.querySelector('.editableContent') as HTMLDivElement;
    }

    /**
     * Retrieves the div (encrypted content) element from the given list item.
     * 
     * @param listItem - The parent node containing the div.
     * @returns - The div element.
     */
    public getDiv(listItem: HTMLElement): HTMLDivElement {
        return listItem.querySelector('.encryptedContent') as HTMLDivElement;
    }

    /**
     * Retrieves the delete button element from the given list item.
     * 
     * @param listItem - The parent node containing the delete button.
     * @returns - The delete button element.
     */
    public getDeleteButton(listItem: HTMLElement): HTMLButtonElement {
        return listItem.querySelector('.delete') as HTMLButtonElement;
    }

    /**
     * Retrieves the message paragraph element from the given list item.
     * 
     * @param listItem - The parent node containing the paragraph.
     * @returns - The paragraph element.
     */
    public getMessage(listItem: HTMLElement): HTMLParagraphElement {
        return listItem.querySelector('p') as HTMLParagraphElement;
    }

    /**
     * Adds the 'decrypted' class to the list item to indicate it has been decrypted.
     * 
     * @param listItem - The list item to be marked as decrypted.
     */
    public setDecrypted(listItem: HTMLElement): void {
        listItem.classList.add('decrypted');
    }

    /**
     * Removes the 'decrypted' class from the list item to indicate it is no longer decrypted.
     * 
     * @param listItem - The list item to be unmarked as decrypted.
     */
    public unsetDecrypted(listItem: HTMLElement): void {
        listItem.classList.remove('decrypted');
    }

    /**
     * Hashes a password using the hashPassword function, password is fetched from parent node.
     * @param listItem - The parent node of the input element.
     * @returns - The hashed password.
     */
    public async getHash(listItem: HTMLElement): Promise<string> {
        const password = this.getPassword(listItem);
        return await this.hashPassword(password);
    }

    /**
     * Hashes a new password using the hashPassword function, new password is fetched from parent node.
     * @param listItem - The parent node of the input element.
     * @returns - The hashed password.
     */
    public async getNewHash(listItem: HTMLElement): Promise<string> {
        const password = this.getNewPassword(listItem);
        return await this.hashPassword(password);
    }

    /**
     * Hashes a password using SHA-256.
     * @param password - The password to hash.
     * @returns - The hashed password.
     */
    private async hashPassword(password: string): Promise<string> {
        const msgBuffer = new TextEncoder().encode(password);

        const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashString = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        return btoa(hashString);
    }
}
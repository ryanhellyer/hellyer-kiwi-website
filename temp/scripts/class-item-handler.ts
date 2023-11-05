class En2EndItemHandler {

    /**
     * Gets the value of the input element from its parent node.
     * @param listItem - The parent node of the input element.
     * @returns - The value of the input element.
     */
    public getPassword(listItem: HTMLElement): string {
        const inputElement = listItem.querySelector('input[type="password"]') as HTMLInputElement;
        return inputElement.value;
    }

    public getSaveButton(listItem: HTMLElement): HTMLButtonElement {
        return listItem.querySelector('.save') as HTMLButtonElement;
    }

    public getTextArea(listItem: HTMLElement): HTMLTextAreaElement {
        return listItem.querySelector('textarea') as HTMLTextAreaElement;
    }

    public getDiv(listItem: HTMLElement): HTMLDivElement {
        return listItem.querySelector('div') as HTMLDivElement;
    }

    public getDeleteButton(listItem: HTMLElement): HTMLButtonElement {
        return listItem.querySelector('.delete') as HTMLButtonElement;
    }

    public getMessage(listItem: HTMLElement): HTMLParagraphElement {
        return listItem.querySelector('p') as HTMLParagraphElement;
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
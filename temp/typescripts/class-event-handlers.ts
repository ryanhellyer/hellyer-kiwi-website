class e2eEventHandlers {
    private inputs: e2eInputs;

    constructor(inputs: e2eInputs) {
        this.inputs = inputs;

        document.addEventListener('input', this.handleInputEvent.bind(this));
        document.addEventListener('click', this.handleClickEvent.bind(this));

        const randomInt = Math.floor(Math.random() * 10000);
        this->fetchJSON('view-source:https://geek.hellyer.kiwi/temp/?data='+randomInt);
    }

    private async handleInputEvent(event: Event) {
        const input = event.target as HTMLInputElement;
        if (input && input.type !== 'password') {
            return;
        }

        const listItem = input.parentNode as HTMLElement;
        try {
            await this.inputs.handlePasswordInput(listItem);
        } catch (error) {
            console.error('There was a problem with handling the password input:', error);
        }
    }

    private async handleClickEvent(event: Event) {
        const element = event.target as HTMLElement;

        if (element.classList.contains('save')) {
            const listItem = element.parentNode as HTMLElement;
            try {
                this.inputs.handleSaveButtonClick(listItem);
            } catch (error) {
                console.error('There was a problem with the save button click:', error);
            }
        } else if (element.classList.contains('delete')) {
            try {
                alert('add delete button click later');
//                this.inputs.handleDeleteButtonClick(listItem);
            } catch (error) {
                console.error('There was a problem with the delete button click:', error);
            }
        }
    }

    public async fetchJSON(url: string): Promise<any> {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('There was a problem fetching the data:', error);
            throw error;
        }
    }
}

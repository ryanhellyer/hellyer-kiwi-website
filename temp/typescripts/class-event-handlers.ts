class e2eEventHandlers {
    private inputs: e2eInputs;

    constructor(inputs: e2eInputs) {
        this.inputs = inputs;

        document.addEventListener('input', this.handleInputEvent.bind(this));
        document.addEventListener('click', this.handleClickEvent.bind(this));
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
}

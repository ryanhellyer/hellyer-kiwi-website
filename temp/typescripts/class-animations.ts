class e2eAnimations {
    /**
     * Starts the save animation on a list item.
     * 
     * @param listItem - The list item on which to start the save animation.
     * 
     * This method adds the 'savingStart' class to the list item, triggering any associated CSS animations or transitions.
     */
    public startSaveAnimation(listItem: HTMLElement): void {
        listItem.classList.add('savingStart');
    }

    /**
     * Ends the save animation on a list item.
     * 
     * @param listItem - The list item on which to end the save animation.
     * 
     * This method performs the following operations:
     * 1. Removes the 'savingStart' class from the list item.
     * 2. Adds the 'savingEnd' class to trigger the end of the animation.
     * 3. Removes the 'savingEnd' class after 1 second to reset the animation state.
     */
    public endSaveAnimation(listItem: HTMLElement): void {
        listItem.classList.remove('savingStart');
        listItem.classList.add('savingEnd');
        setTimeout(() => {
            listItem.classList.remove('savingEnd');
        }, 1000);
    }
}

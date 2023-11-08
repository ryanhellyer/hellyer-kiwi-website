class LoadItems {

    public async getItems(): Promise<any[]> {
        const randomInt = Math.floor(Math.random() * 10000);
        const data = await this.fetchJSON('/temp/?data='+randomInt);
        return data;
    }

    public async fetchJSON(url: string): Promise<any> {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = response.json();
            return data;
        } catch (error) {
            console.error('There was a problem fetching the data:', error);
            throw error;
        }
    }
}

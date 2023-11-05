class e2eAjax {
    /**
     * Sends POST data to a specified URL.
     * @param data - The data to send.
     * @param type - The type of request.
     * @returns - The JSON response from the server.
     */
    public async postData(data: Record<string, any> = {}, type: string): Promise<any> {
        const urlEncodedData = new URLSearchParams(data).toString();
        let response: Response;

        try {
            response = await fetch('./?' + type, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: urlEncodedData
            });
        } catch (error) {
            console.error('Fetch failed:', error);
            throw error;
        }

        if (!response) {
            throw new Error('No response found!');
        }

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const jsonResponse = await response.json();
        if (!jsonResponse) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return jsonResponse;
    }
}

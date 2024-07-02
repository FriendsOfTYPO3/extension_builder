import axios from "axios";

export async function listAvailableExtensions() {
    const payload = {
        id: 1,
        method: "listWirings",
        params: {
            language: "extbaseModeling",
        },
        version: "json-rpc-2.0"
    };

    try {
        const response = await axios.post(TYPO3.settings.ajaxUrls.eb_dispatchRpcAction, JSON.stringify(payload), {
            headers: {
                'Content-Type': 'application/json',
                "X-Requested-With": "XMLHttpRequest"
            }
        });

        const success = response && response.data && response.data.success;
        const error = response && response.data && response.data.error;

        if(!success || error || !response) {
            return createErrorMessage("Something went wrong on server side. Success: " + (error ? JSON.stringify(error) : ''));
        }

        return response.data;

    } catch (error) {
        console.error(error.message); //for error logging in production, it's better to use console.error instead of console.log
        return createErrorMessage(error.message);
    }
}

function createErrorMessage(msg) {
    return {
        success: false,
        error: true,
        message: msg
    };
}

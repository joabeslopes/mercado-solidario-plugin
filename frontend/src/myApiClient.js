const apiNamespace = '/wp-json/mercado-solidario/v1';

export async function post(path, requestBody) {

    const apiResponse = await fetch(apiNamespace + path,
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: requestBody
        }
        ).then(response => response.json()
        ).catch(error => null);

    return apiResponse;
};

export async function get(path) {

    const apiResponse = await fetch(apiNamespace + path,
        ).then(response => response.json()
        ).catch(error => null);

    return apiResponse;
};
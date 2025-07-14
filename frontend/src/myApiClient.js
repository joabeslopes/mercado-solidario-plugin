const apiNamespace = '/wp-json/mercado-solidario/v1';
const nonce = mercadoSolidarioSettings.nonce;

export async function post(path, requestObj) {

    const apiResponse = await fetch(apiNamespace + path,
        {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": nonce
            },
            body: JSON.stringify(requestObj)
        }
        ).then(response => response.json()
        ).catch(error => null);

    return apiResponse;
};

export async function get(path) {

    const apiResponse = await fetch(apiNamespace + path,
        {
            method: "GET",
            headers: {
                "X-WP-Nonce": nonce
            },
        }
        ).then(response => response.json()
        ).catch(error => null);

    return apiResponse;
};
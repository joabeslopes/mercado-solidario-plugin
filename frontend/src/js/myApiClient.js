const apiNamespace = mercadoSolidarioSettings.namespace;
const nonce = mercadoSolidarioSettings.nonce;

async function createMyResponse(response) {

    const myResponse = {
        ok: response.ok,
        status: response.status,
        message: '',
        data: []
    };

    try {
        const responseJson = await response.json();

        myResponse.data = responseJson.data;

        if (!myResponse.ok){
            myResponse.message = myResponseErrorString(responseJson);
        };

    } catch (error) {
        console.error(error);
    };

    return myResponse;
};

export function myResponseErrorString(response) {

    let messageString = '';

    if ( response.message && response.message != '' ){
        messageString = response.message;
    } else {
        if (response.data){
            if ( Array.isArray(response.data) ){
                response.data.forEach( msg => messageString += String(msg) + '\n' );
            } else {
                if ( typeof response.data === "string" ){
                    messageString = response.data;
                };
            };
        };
    };

    return messageString;
};

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
    );

    const response = await createMyResponse(apiResponse);

    return response;
};

export async function get(path) {

    const apiResponse = await fetch(apiNamespace + path,
        {
            method: "GET",
            headers: {
                "X-WP-Nonce": nonce
            },
        }
    );

    const response = await createMyResponse(apiResponse);

    return response;
};

export async function del(path, requestObj) {

    const apiResponse = await fetch(apiNamespace + path,
        {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-WP-Nonce": nonce
            },
            body: JSON.stringify(requestObj)
        }
    );

    const response = await createMyResponse(apiResponse);

    return response;
};
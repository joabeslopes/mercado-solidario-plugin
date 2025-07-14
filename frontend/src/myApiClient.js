const apiNamespace = '/wp-json/mercado-solidario/v1';
const nonce = mercadoSolidarioSettings.nonce;

async function create_my_response(response) {

    let myResponse = {};

    try {
        const responseJson = await response.json();
        if (!response.ok) {
            // em caso de erro a API Wordpress ja vai retornar as propriedades 'message' e 'data'
            myResponse = responseJson;
        } else {
            myResponse.data = responseJson;
        };

        myResponse.status = response.status;

    } catch (error) {
        myResponse.message = await error.text();
        myResponse.status = 0;
    };

    return myResponse;
};

export function response_error_string(myResponse) {

    if( myResponse.message ){
      return myResponse.message;
    } else {
      let messageString = '';
      myResponse.data.forEach( msg => messageString += msg + '\n' );
      return messageString;
    };
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

    const response = await create_my_response(apiResponse);

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

    const response = await create_my_response(apiResponse);

    return response;
};
const apiNamespace = '/wp-json/mercado-solidario/v1';
const nonce = mercadoSolidarioSettings.nonce;

async function createMyResponse(response) {

    let myResponse = {};

    try {
        // sempre vai vir da API o atributo 'data'
        myResponse = await response.json();

        myResponse.status = response.status;
        myResponse.ok = response.ok;

        if (!response.ok){
            myResponse.message = myResponseErrorString(myResponse);
        };

    } catch (error) {
        myResponse.message = await error.text();
        myResponse.status = 0;
    };

    return myResponse;
};

export function myResponseErrorString(myResponse) {

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
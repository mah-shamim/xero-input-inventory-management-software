export class Request {

    get(url, apps, func) {

        return axios.get(url)
            .then(response => {
                return response.data
            }).then(data => {
                func(data)

            }).catch(error => {
                if(error.response){
                    if (error.response.status === 401) {
                        window.location = '/login'
                    } else if (error.response.status === 403) {
                        window.location = '/access-forbidden'
                    } else {
                        console.log(error.response);
                    }
                }

        })
    }
}

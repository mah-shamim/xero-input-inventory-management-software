import {Errors} from './Errors';

export class Forms {
    constructor(data) {
        this.originalData = data;
        for (let field in data) {
            this[field] = data;
        }
        // calling error class
        this.errors = new Errors();
    }

    // data should be dynamic, we can not call the static
    data() {
        let data = Object.assign({}, this);
        delete data.originalData; //delete original data object
        delete data.errors; //delete original data object
        return data;
    }

    post(data){
        let type =['post', 'patch', 'delete', 'get'];
        return type.includes(data.toLowerCase())?data.toLowerCase():'';
    }

    submit(requestType, url, reset = true, apps = null) {

        if (apps) apps.showSpinner();
        return new Promise((resolve, reject)=>{
            axios[this.post(requestType)](url, this.data())
                .then(response=>{
                    this.onSuccess(response.data, reset, apps);
                    resolve(response.data);
                })
                .catch(error =>{
                    this.onError(error.response.data);
                    reject(error.response.data);
                });
                // .catch(this.onError.bind(this));
        });
    }

    onSuccess(data, reset, apps = null) {
        if(reset){
            this.reset();
        }
        this.errors.errors = {};
        if (apps) apps.hideSpinner();
    }

    onError(error){
        this.errors.record(error);

        // this.errors.record(error.response.data);
    }

    reset(){
        // return null;
        for (let field in this.data()){
            this[field] =''
        }
    }

    fieldReset() {
        for (let field in this.data()) {
            delete this[field]
        }
    }

}

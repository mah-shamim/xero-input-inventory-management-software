export class Errors {

    constructor() {
        this.errors = {};
    }

    get(field) {
        if (this.errors.errors) {
            let err = this.errors.errors;
            if (err[field]) {
                return err[field][0];
            }
        }
    }

    record(errors) {
        this.errors = errors;
    }

    clear(field) {
        if (this.errors.errors) {
            delete this.errors.errors[field];
        }
    }

    any(){
        return Object.keys(this.errors).length>0;
    }
}


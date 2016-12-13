import Backbone from 'backbone';

export default class BaseModel extends Backbone.Model {

    parse (response, options) {
        if (options.xhr.status === 200) {
            return this.onParse(response);
        }
    }

    onParse (response) {
        return response;
    }

    fetch (options) {

        options = options || {};

        if (this.nocache === true) {
            options.cache = false;
        }

        return super.fetch(options);

    }

}


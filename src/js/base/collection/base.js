import Backbone from 'backbone';
import BaseModel from '../model/base';

export default class BaseCollection extends Backbone.Collection {

    constructor (models, options) {
        super(models, options);

        this.options = options;
        this.model = BaseModel;
    }

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


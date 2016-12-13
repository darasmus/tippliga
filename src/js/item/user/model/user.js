import _ from 'underscore';
import BaseModel from '../../../base/model/base';

export default class UserModel extends BaseModel {

    constructor (options) {
        super(options);
    }

    onParse (response) {
        return response;
    }

    url () {
        return _.getApiUrl('user');
    }
}
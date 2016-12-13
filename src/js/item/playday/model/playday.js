import _ from 'underscore';
import BaseModel from '../../../base/model/base';

export default class PlaydayModel extends BaseModel {

    constructor (options) {
        super(options);
    }

    url () {
        return _.getApiUrl('playday');
    }

}
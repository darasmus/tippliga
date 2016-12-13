import _ from 'underscore';
import BaseModel from '../../../base/model/base';

export default class ResultModel extends BaseModel {
    url () {
        return _.getApiUrl('table');
    }
}
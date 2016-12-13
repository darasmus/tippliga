import _ from 'underscore';
import BaseModel from '../../../base/model/base';

export default class LinkModel extends BaseModel {

    defaults () {

        return {

            route: null,

            title: () => {
                return _.getTitle(this.get('route'));
            },

            url: () => {
                return _.getPath(this.get('route'));
            }

        };

    }

}


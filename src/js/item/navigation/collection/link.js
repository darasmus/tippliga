import _ from 'underscore';
import BaseCollection from '../../../base/collection/base';
import LinkModel from '../model/link';

export default class LinkCollection extends BaseCollection {

    constructor (models, options) {

        options = options || {};

        super(models, options);

        this.model = LinkModel;

        _.each(_.getLinks(options.route), (item) => {
            if (_.isObject(item)) {
                this.add(item);
            } else {
                this.add({
                    route: item
                });
            }
        });
    }

}


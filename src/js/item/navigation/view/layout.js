import app from '../../../app';
import ItemView from '../../../base/view/item';
import LinkCollection from '../collection/link';

export default class NavigationView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/navigation/layout'),

            className: 'navigation-wrapper',

            collection: new LinkCollection([], options)

        }, options));
    }

}


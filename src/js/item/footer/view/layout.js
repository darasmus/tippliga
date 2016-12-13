import ItemView from '../../../base/view/item';
import app from '../../../app';

export default class FooterView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/footer/layout'),

            className: 'container-wrapper'

        }, options));
    }

}


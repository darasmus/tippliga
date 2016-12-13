import app from '../../../app';
import ItemView from '../../../base/view/item';

export default class SpinnerView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/spinner/layout'),

            className: 'spinner-container is-hidden',

        }, options));

        this.listenTo(app, 'ajax:start', this.showSpinner);
        this.listenTo(app, 'ajax:stop', this.hideSpinner);

    }

    showSpinner () {
        this.$el.removeClass('is-hidden');
    }

    hideSpinner () {
        this.$el.addClass('is-hidden');
    }

}


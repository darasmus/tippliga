import app from '../../../app';
import ItemView from '../../../base/view/item';
import AlertModel from '../model/alert';

export default class AlertView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/alert/layout'),

            className: 'alert-container is-hidden',

            events: {
                'click .js-close'   : 'hideAlert',
                'click .js-confirm' : 'confirmAlert',
                'click'             : 'clickOnBackground'
            }

        }, options));

        this.listenTo(app, 'alert:start', this.init);
    }

    init (data) {
        this.model = new AlertModel(data);
        
        this.render();
        
        if (this.model.get('confirmCallback')) {
            // show confirm-button
            this.$('.js-confirm').removeClass('is-hidden');
        } else {
            this.$('.js-confirm').addClass('is-hidden');
        }

        this.showAlert();
    }

    showAlert () {
        this.$el.removeClass('is-hidden');
    }

    hideAlert () {
        this.$el.addClass('is-hidden');
    }

    confirmAlert () {
        this.hideAlert();

        this.model.get('confirmCallback')();
    }

    clickOnBackground (e) {
        if (e.target === this.el) {
            this.hideAlert();
        }
    }
    
}


import app from '../../../app';
import ItemView from '../../../base/view/page';
import LoginForm from '../../login-form/view/item';

export default class LoginView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/login'),

            className: 'login-container is-hidden',

            regions: {
                form: '.login-form'
            },

            events: {
                'click .js-close'   : 'hideLogin'
            }

        }, options));

        this.listenTo(app, 'ajax:authError', this.showLogin);
        this.listenTo(app, 'login:error', this.onRender);
        this.listenTo(app, 'login:success', this.hideLogin);
    }

    onRender () {
        super.onRender();

        this.getRegion('form').show(new LoginForm());
    }

    showLogin () {
        this.$el.removeClass('is-hidden');
    }

    hideLogin () {
        this.$el.addClass('is-hidden');
    }

}




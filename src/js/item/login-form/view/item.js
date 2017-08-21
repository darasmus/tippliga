import $ from 'jquery';
import app from '../../../app';
import ItemView from '../../../base/view/item';
import LoginModel from '../model/login';

export default class LoginForm extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/login-form'),

            events: {
                'click button'  : 'submit',
                'submit'        : 'submit'
            },

            model: new LoginModel()

        }, options));

        this.listenTo(app.user, 'sync', this.handleResponse);
    }

    submit (e) {
        e.preventDefault();

        //let errors = this.validate();
        let errors = false;
        if (!errors) {
            this.model.set('user', $('#user').val());
            this.model.set('pwd', $('#pwd').val());
            this.model.sync();
        }
    }

    handleResponse (response) {
        if (response.get('errors')) {
            app.trigger('login:error', response);
        } else {
            app.trigger('login:success');
        }
    }

}


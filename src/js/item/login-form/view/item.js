//import _ from 'underscore';
import app from '../../../app';
import BaseForm from '../../../base/view/form';
import LoginModel from '../model/login';

export default class LoginForm extends BaseForm {

    constructor (options) {
        options = options || {};

        super(Object.assign({}, {

            events: {
                'click button'  : 'submit',
                'submit'        : 'submit'
            },

            model: new LoginModel(options.data)

        }, options));

        this.listenTo(app.user, 'sync', this.handleResponse);
    }

    submit (e) {
        e.preventDefault();

        let errors = this.validate();
        if (!errors) {
            this.model.set('user', this.getValue('user'));
            this.model.set('pwd', this.getValue('pwd'));
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


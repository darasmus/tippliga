import app from '../../../app';
import BaseForm from '../../../base/view/form';
import MessageModel from '../model/message';

export default class ForumForm extends BaseForm {

    constructor (options) {
        options = options || {};

        super(Object.assign({}, {

            events: {
                'click button'  : 'submit',
                'submit'        : 'submit'
            },

            model: new MessageModel(options.data)

        }, options));

        //this.listenTo(app.user, 'sync', this.handleResponse);
    }

    submit (e) {
        e.preventDefault();

        let errors = this.validate();
        if (!errors) {

            this.model.set('title', this.getValue('title'));
            this.model.set('message', this.getValue('message'));
            //this.model.set('link', this.getValue('link'));
            //this.model.set('linktext', this.getValue('linktext'));

            this.model.sync();
        }
    }

    handleResponse (response) {
        if (response.get('errors')) {
            app.trigger('savepost:error', response);
        } else {
            app.trigger('savepost:success');
        }
    }

}


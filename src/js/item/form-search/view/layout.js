import _ from 'underscore';
import BaseForm from '../../../base/view/form';
import SearchModel from '../model/search';

export default class SearchForm extends BaseForm {

    constructor (options) {
        super(Object.assign({

            events: {
                'click button'  : 'submit',
                'submit'        : 'submit'
            },

            model: new SearchModel(options.data)

        }, options));
    }

    submit (e) {
        e.preventDefault();

        let errors = this.validate();
        if (!errors) {
            _.redirect(_.getPath('user') + '/' + this.getValue('email'));
        }
    }

}


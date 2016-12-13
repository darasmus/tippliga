import app from '../../../app';
import PageView from '../../../base/view/page';

export default class RulesView extends PageView {

    constructor (options) {
        super(Object.assign({

            template    : app.template('page/rules/layout'),

            className   : 'rules-wrapper container-wrapper'

        }, options));
    }

}


import app from '../../../app';
import PageView from '../../../base/view/page';
import NavigationView from '../../../item/navigation/view/layout';

export default class ErrorView extends PageView {

    constructor (options) {
        super(Object.assign({}, {

            template: app.template('page/error/layout'),

            className: 'error-wrapper',

            regions: {
                'navigation': 'nav'
            }

        }, options));
    }

    onRender () {
        super.onRender();

        this.getRegion('navigation').show(new NavigationView());
    }

}


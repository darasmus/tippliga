import app from '../../../app';
import ItemView from '../../../base/view/item';
import SpinnerView from '../../spinner/view/layout';
import AlertView from '../../alert/view/layout';
import LoginView from '../../login/view/item';
import PostView from '../../post/view/layout';
import AlltippsView from '../../tipps/view/layout';

export default class ModalsView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/modals/layout'),

            regions: {
                'spinner'   : '.spinner-wrapper',
                'alert'     : '.alert-wrapper',
                'login'     : '.login-wrapper',
                'post'      : '.post-wrapper',
                'alltipps'  : '.alltipps-wrapper'
            }

        }, options));

    }

    onRender () {
        super.onRender();

        this.getRegion('spinner').show(new SpinnerView());
        this.getRegion('alert').show(new AlertView());
        this.getRegion('login').show(new LoginView());
        this.getRegion('post').show(new PostView());
        this.getRegion('alltipps').show(new AlltippsView());
    }

}


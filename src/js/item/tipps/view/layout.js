import _ from 'underscore';
import app from '../../../app';
import ItemView from '../../../base/view/item';
import AlltippsModel from '../model/alltipps';

export default class AlltippsView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/tipps/layout'),
            className: 'alltipps-container is-hidden',
            events: {
                'click .js-close'   : 'hideTipps'
            }

        }, options));

        this.model = new AlltippsModel();

        this.listenTo(app, 'alltipps:open', this.showTipps);
        this.listenTo(app, 'alltipps:close', this.hideTipps);

    }

    showTipps (spiel) {
        this.model.url = _.getApiUrl('alltipps').replace('{spiel}', spiel);
        this.synchronize(this.model);
        this.$el.removeClass('is-hidden');
    }

    hideTipps () {
        this.$el.addClass('is-hidden');
    }

}
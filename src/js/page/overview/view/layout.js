import _ from 'underscore';
import $ from 'jquery';
import app from '../../../app';
import PageView from '../../../base/view/page';
import OverviewModel from '../model/overview';

export default class OverviewView extends PageView {

    constructor (options) {
        super(Object.assign({

            template    : app.template('page/overview/layout'),

            className   : 'overview-wrapper container-wrapper',

            events      : {
                'change .gameday-selector': 'reload',
                'click .print-page': 'print',
                'click .open-alltipps': 'openAlltipps'
            }

        }, options));

        this.model = new OverviewModel();

        this.model.url = _.getApiUrl('overview').replace('{spieltag}', 1).replace('{spieler}', 1);

        //load on redirect...
        if(app.user.get('user')) {
            this.load();
        }

        this.listenTo(app, 'login:success', this.load);
    }

    onRender () {
        this.$('.gameday-selector').val(this.spieltag);
    }

    load () {
        this.user = app.user.get('user');

        this.model.set('user', this.user);

        this.spieltag = app.playday.get('playday');

        this.model.set('spieltag', this.spieltag);

        this.model.url = _.getApiUrl('overview').replace('{spieltag}', this.spieltag).replace('{spieler}', this.user.id);

        this.synchronize(this.model);

    }

    reload () {

        this.spieltag = this.$('.gameday-selector').val();

        this.model.url = _.getApiUrl('overview').replace('{spieltag}', this.spieltag).replace('{spieler}', this.user.id);

        this.synchronize(this.model);
    }

    print () {
        window.print();
    }

    openAlltipps (ev) {
        let spiel = $(ev.currentTarget).data('game');
        app.trigger('alltipps:open', spiel);
    }

}


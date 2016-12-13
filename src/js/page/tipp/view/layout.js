import _ from 'underscore';
import $ from 'jquery';
import app from '../../../app';
import PageView from '../../../base/view/page';
import UserModel from '../../user/model/user';
import TippsModel from '../model/tipps';

export default class UserView extends PageView {

    constructor (options) {
        super(Object.assign({

            template    : app.template('page/savetipp/layout'),

            className   : 'tipp-wrapper container-wrapper',

            events      : {
                'change .gameday-selector'  : 'reload',
                'click button.submit'       : 'submit'
            }

        }, options));

        this.model = new UserModel();

        //load on redirect...
        if(app.user.get('user')) {
            this.load();
        }

        this.listenTo(app, 'login:success', this.load);
        this.listenTo(app, 'saveTipp:done', this.sync);
    }

    onSync () {
        //this.collection = new BaseCollection(this.originalCollection.where({vorname: 'Jan'}));
        super.onSync();
    }

    onRender () {
        this.$('.gameday-selector').val(this.spieltag);
    }

    reload () {
        this.spieltag = this.$('.gameday-selector').val();

        this.model.url = _.getApiUrl('usertable')
            .replace('{spieler}', this.spieler)
            .replace('{spieltag}', this.spieltag)
            .replace('{liga}', this.liga);

        this.synchronize(this.model);
    }

    sync () {
        this.synchronize(this.model);
    }

    load () {
        this.liga = app.user.get('user').liga;

        this.spieltag = parseInt(app.playday.get('playday'));

        this.spieler = app.user.get('user').id;

        this.model.url = _.getApiUrl('usertable')
            .replace('{spieler}', this.spieler)
            .replace('{spieltag}', this.spieltag)
            .replace('{liga}', this.liga);

        this.synchronize(this.model);
    }

    submit () {

        let tippsModel = new TippsModel();
        let tipps = [];

        $('.tipp').each((index, value) => {

            let valA = parseInt($(value).find('.tipp-a').val());
            let valB = parseInt($(value).find('.tipp-b').val());

            if(_.isInteger(valA) && _.isInteger(valB)) {
                tipps[$(value).data('game')] = { a: valA, b: valB };
            }

        });

        tippsModel.set('tipps', tipps);
        tippsModel.save();
    }



}


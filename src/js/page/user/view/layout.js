import _ from 'underscore';
import app from '../../../app';
import PageView from '../../../base/view/page';
import UserModel from '../model/user';

export default class UserView extends PageView {

    constructor (options) {
        super(Object.assign({

            template    : app.template('page/user/layout'),

            className   : 'home-wrapper container-wrapper',

            events      : {
                'change .gameday-selector': 'reload'
            }

        }, options));

        this.liga = 1;

        this.model = new UserModel();

        //load on redirect...
        if(app.user.get('user')) {
            this.load();
        }

        this.listenTo(app, 'login:success', this.load);
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

    load () {
        this.liga = app.user.get('user').liga;

        this.spieltag = app.playday.get('playday');

        this.spieler = app.user.get('user').id;

        this.model.url = _.getApiUrl('usertable')
            .replace('{spieler}', this.spieler)
            .replace('{spieltag}', this.spieltag)
            .replace('{liga}', this.liga);

        this.synchronize(this.model);
    }



}


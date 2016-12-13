import _ from 'underscore';
import app from '../../../app';
import PageView from '../../../base/view/page';
import ResultModel from '../model/result';

export default class ResultView extends PageView {

    constructor (options) {
        super(Object.assign({

            template    : app.template('page/result/layout'),

            className   : 'result-wrapper container-wrapper',

            events      : {
                'change .gameday-selector': 'reload',
                'change .liga-selector': 'reload',
            }

        }, options));


        this.model = new ResultModel();

        this.model.url = _.getApiUrl('result').replace('{spieltag}', 1).replace('{liga}', 1);

        //load on redirect...
        if(app.user.get('user')) {
            this.load();
        }

        this.listenTo(app, 'login:success', this.load);
    }

    onRender () {
        this.$('.gameday-selector').val(this.spieltag);
        this.$('.liga-selector').val(this.liga);
    }

    load () {
        this.liga = app.user.get('user').liga;

        if(app.playday.get('playday') > 34) {
            this.spieltag = 1;
        } else {
            this.spieltag = app.playday.get('playday');
        }

        this.model.url = _.getApiUrl('result').replace('{spieltag}', this.spieltag).replace('{liga}', this.liga);

        this.synchronize(this.model);
    }

    reload () {

        this.spieltag = this.$('.gameday-selector').val();
        this.liga = this.$('.liga-selector').val();

        this.model.url = _.getApiUrl('result').replace('{spieltag}', this.spieltag).replace('{liga}', this.liga);

        this.synchronize(this.model);
    }

}


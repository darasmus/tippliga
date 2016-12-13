import _ from 'underscore';
import app from '../../../app';
import PageView from '../../../base/view/page';
import ForumModel from '../model/forum';

export default class ForumView extends PageView {

    constructor (options) {
        super(Object.assign({

            template    : app.template('page/forum/layout'),

            className   : 'forum-wrapper container-wrapper',

            events      : {
                'click .new-post': 'openpost'
            }

        }, options));

        this.model = new ForumModel();
        this.model.url = _.getApiUrl('forum').replace('{id}', this.options.id);

        //load on redirect...
        if(app.user.get('user')) {
            this.load();
        }

        this.listenTo(app, 'login:success', this.load);
    }

    load () {
        this.synchronize(this.model);
    }

    reload () {
        this.synchronize(this.model);
    }

    openpost () {
        app.trigger('post:open', this.options.id);
    }

}


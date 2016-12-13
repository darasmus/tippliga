import _ from 'underscore';
import $ from 'jquery';
import app from '../../../app';
import PageView from '../../../base/view/page';
import ForumOverviewModel from '../model/forumoverview';


export default class ForumOverviewView extends PageView {

    constructor (options) {
        super(Object.assign({

            template    : app.template('page/forumoverview/layout'),

            className   : 'forum-wrapper container-wrapper',

            events      : {
                'click .new-post': 'openpost',
                'mouseover .overview-list': 'hoverlistOn',
                'mouseout .overview-list': 'hoverlistOff'
            }

        }, options));

        this.model = new ForumOverviewModel();
        this.model.url = _.getApiUrl('forumoverview');

        //load on redirect...
        if(app.user.get('user')) {
            this.load();
        }

        this.listenTo(app, 'login:success', this.load);
    }

    hoverlistOn (e) {
        $(e.currentTarget).removeClass('off').addClass('on');
    }

    hoverlistOff (e) {
        $(e.currentTarget).removeClass('on').addClass('off');
    }

    load () {
        this.synchronize(this.model);
    }

    reload () {
        this.synchronize(this.model);
    }

    openpost () {
        app.trigger('post:open', 0);
    }

}


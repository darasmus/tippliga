import _ from 'underscore';
import $ from 'jquery';
import app from '../../../app';
import ItemView from '../../../base/view/page';

export default class PostView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/post'),

            className: 'forum-container is-hidden',

            regions: {
                post: '.forum-form'
            },

            events: {
                'click .js-close'   : 'hidePost',
                'click .submit'     : 'validate'
            }

        }, options));

        this.listenTo(app, 'post:open', this.showPost);
        this.listenTo(app, 'post:close', this.hidePost);
        this.listenTo(app, 'post:done', this.hidePost);

    }

    onRender () {
        super.onRender();
    }

    showPost (id) {
        this.id = id;
        this.$el.removeClass('is-hidden');
    }

    hidePost () {
        this.$el.addClass('is-hidden');
        this.reset();
    }

    reset () {
        this.$('.error-msg').hide();
        this.$('#postTitle').removeClass('error');
        this.$('#postTitle').val('');
        this.$('#postPost').removeClass('error');
        this.$('#postPost').val('');
        this.$('#postLink').val('');
        this.$('#postLinktext').val('');
    }

    validate () {
        let valid = true;

        let title = this.$('#postTitle').val();
        let message = this.$('#postPost').val();
        let link = this.$('#postLink').val();
        let linktext = this.$('#postLinktext').val();

        if(title === '') {
            this.$('#postTitle').addClass('error');
            valid = false;
        } else {
            this.$('#postTitle').removeClass('error');
        }

        if(message === '') {
            this.$('#postPost').addClass('error');
            valid = false;
        } else {
            this.$('#postPost').removeClass('error');
        }

        //submit if valid
        if(valid) {
            let post = {
                title: title,
                message: message,
                link: link,
                linktext: linktext,
                child: this.id
            };
            this.submit(post);
        } else {
            this.$('.error-msg').show();
        }
    }

    submit (post) {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: _.getApiUrl('savepost'),
            data: {
                dta: JSON.stringify(post)
            },
            success: function () {
                app.trigger('post:done');
                app.user.fetch();
            }
        });

        this.reset();

    }

}




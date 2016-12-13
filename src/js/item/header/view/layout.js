import _ from 'underscore';
import Backbone from 'backbone';
import app from '../../../app';
import ItemView from '../../../base/view/item';
import BaseModel from '../../../base/model/base';

export default class HeaderView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/header/layout'),

            className: 'container-wrapper',

            events: {
                'click .nav-home'           : 'gotoHome',
                'click .nav-result'         : 'gotoResult',
                'click .nav-tipp'           : 'gotoTipp',
                'click .nav-table'          : 'gotoTable',
                'click .nav-overview'       : 'gotoOverview',
                'click .nav-forum'          : 'gotoForum',
                'click .nav-logout'         : 'logout',
                'click .nav-rules'          : 'gotoRules',
                'click .mobile-menu-btn'    : 'toggleMenu',
                'click .mobile-menu .item'  : 'toggleMenu'
            },

            model: new BaseModel()

        }, options));

        //load on redirect...
        if(app.user.get('user')) {
            this.load();
        }

        this.menuOpen = false;

        this.listenTo(this.model, 'change', this.render);

        this.listenTo(app, 'login:success', this.load);
    }

    load () {
        this.model.set('user', app.user.get('user'));
        this.model.set('gegner', app.user.get('gegner'));

        //tippabgabe bis...
        this.model.set('tipptime', app.playday.get('tipptime')*1000);

        let current = Backbone.history.fragment;

        if( current.indexOf('forum/') === 0 ) {
            current = 'forum';
        }

        this.$('.item').removeClass('active');

        switch(current) {
            case 'tipp':
                this.gotoTipp();
                break;
            case 'result':
                this.gotoResult();
                break;
            case 'table':
                this.gotoTable();
                break;
            case 'overview':
                this.gotoOverview();
                break;
            case 'rules':
                this.gotoRules();
                break;
            case 'forumoverview':
                this.gotoForum();
                break;
            case 'forum':
                this.changeNavi('nav-forum', 799, 10);
                break;
            default:
                return false;
        }
    }

    toggleMenu () {

        if(this.menuOpen) {
            //this.$('.mobile-menu').removeClass('menu-open').addClass('menu-closed');
            this.$('.mobile-menu').slideUp(250);
            this.menuOpen = false;
        } else {
            //this.$('.mobile-menu').removeClass('menu-closed').addClass('menu-open');
            this.$('.mobile-menu').slideDown(250);
            this.menuOpen = true;
        }

    }

    gotoHome () {
        _.redirect('home');
        this.$('.item').removeClass('active');
        this.$('.ball').css({'left':'27px', 'bottom':'3px'});
    }

    gotoOverview () {
        this.navigateTo ('overview', 'nav-overview', 182, 10);
    }

    gotoResult () {
        this.navigateTo ('result', 'nav-result', 342, 10);
    }

    gotoTipp () {
        this.navigateTo ('tipp', 'nav-tipp', 511, 10);
    }

    gotoTable () {
        this.navigateTo ('table', 'nav-table', 669, 10);
    }

    gotoForum () {
        this.navigateTo ('forumoverview', 'nav-forum', 799, 10);
    }

    gotoRules () {
        this.$('.item').removeClass('active');
        this.$('.nav-rules').addClass('active');
        this.$('.ball').css({'left':'27px', 'bottom':'3px'});
    }

    logout () {
        this.logoutModel = new BaseModel();
        this.logoutModel.url = _.getApiUrl('logout');

        this.logoutModel.fetch({
            success: () => {
                window.location.href = '/';
            },
            error: () => {
                console.log('error logout');
            }
        });


    }

    navigateTo (route, navitem, left, bottom) {
        _.redirect(route);
        this.$('.item').removeClass('active');
        this.$('.' + navitem).addClass('active');
        this.$('.ball').css({'left': left + 'px', 'bottom': bottom + 'px'});
    }

    changeNavi (navitem, left, bottom) {
        this.$('.item').removeClass('active');
        this.$('.' + navitem).addClass('active');
        this.$('.ball').css({'left': left + 'px', 'bottom': bottom + 'px'});
    }


}


import _ from 'underscore';
import Backbone from 'backbone';
import app from './app';

import HeaderView from './item/header/view/layout';
import FooterView from './item/footer/view/layout';

import ModalsView from './item/modals/view/layout';
import ErrorView from './page/error/view/layout';
import HomeView from './page/index/view/layout';
import UserView from './page/user/view/layout';
import ResultView from './page/result/view/layout';
import OverviewView from './page/overview/view/layout';
import TippView from './page/tipp/view/layout';
import TableView from './page/table/view/layout';
import RulesView from './page/rules/view/layout';
import ForumOverviewView from './page/forumoverview/view/layout';
import ForumView from './page/forum/view/layout';

export default class Router extends Backbone.Router {

    constructor (options) {
        super(options);

        app.addRegions({
            'header'    : 'header',
            'main'      : 'main',
            'footer'    : 'footer',
            'modals'    : '.modal-wrapper'
        });

        this.listenTo(app, 'start', this.renderRegions);
        this.listenTo(Backbone.history, 'route', this.pagetrack);
    }

    routes () {
        let routes = {};

        // add 404/catchall route as very last route
        _.each(app.config.routes, (routeItem) => {
            if (routeItem.routes) {
                _.each(routeItem.routes, (route) => {
                    routes[route] = this[routeItem.key] || this.error;
                });
            } else {
                routes[routeItem.key] = this[routeItem.key] || this.error;
            }

            if (routeItem.redirects) {
                _.each(routeItem.redirects, (path) => {
                    routes[path] = () => {
                        this.redirect(routeItem.routes[0]);
                    };
                });
            }
        });

        return routes;
    }

    pagetrack () {
        //console.log('track', Backbone.history.fragment);
        let options = {
            hitType: 'pageview',
            page: Backbone.history.fragment
        };
        window.ga('send', options);
    }

    redirect (path, trigger, replace) {
        this.navigate(path, {
            // explicit false value required
            trigger: trigger !== false,
            // explicit true value required
            replace: replace === true
        });
    }

    renderRegions () {
        app.getRegion('header').show(new HeaderView());
        app.getRegion('footer').show(new FooterView());
        app.getRegion('modals').show(new ModalsView());
    }

    error () {
        app.getRegion('main').show(new ErrorView());
    }

    index () {
        app.getRegion('main').show(new HomeView());
    }

    user () {
        app.getRegion('main').show(new UserView());
    }

    result () {
        app.getRegion('main').show(new ResultView());
    }

    overview () {
        app.getRegion('main').show(new OverviewView());
    }

    tipp () {
        app.getRegion('main').show(new TippView());
    }

    table () {
        app.getRegion('main').show(new TableView());
    }

    rules() {
        app.getRegion('main').show(new RulesView());
    }

    forumoverview() {
        app.getRegion('main').show(new ForumOverviewView());
    }

    forum(id) {
        app.getRegion('main').show(new ForumView({'id': id}));
    }

}


import $ from 'jquery';
import Backbone from 'backbone';
import Marionette from 'backbone.marionette';
import _ from './util/mixins';
import Handlebars from './util/handlebars';

import config from './config';
import templates from '../../.tmp/tpl';

import UserModel from './item/user/model/user';
import PlaydayModel from './item/playday/model/playday';

// clean up global namespace
//
$.noConflict(true);
_.noConflict();
Backbone.noConflict();

var app = new Marionette.Application();

// aggregated config files
app.config = config;

//user model
app.user = new UserModel();

//playday model
app.playday = new PlaydayModel();

// handlebars precomiled templates
app.template = (key) => {
    return templates(Handlebars.default)[key];
};

// defines app start behaviour
app.start = (options) => {
    app.playday.fetch().then( () => {

        app.trigger('start', options || {}, app);

        Backbone.history.start({
            pushState   : app.config.pushState,
            // don't use hashes on activated push state
            hashChange  : !app.config.pushState,
            root        : app.config.root
        });

        app.user.fetch();
    });

    return app;
};

//
// export app object
//
export default app;


import _ from 'underscore';
import app from '../../app';

_.mixin({

    getApiUrl: (key) => {
        return app.config.api ? (app.config.api.basePath + app.config.api[key]) : '';
    },

    getPath: (key) => {
        let result = _.findWhere(app.config.routes, {
            key: key
        });

        if (!_.isEmpty(result)) {
            return app.config.root + (result.routes ? result.routes[0] : key);
        }

        return app.config.root;
    },

    getTitle: (key) => {
        let result = _.findWhere(app.config.routes, {
            key: key
        });

        return result ? result.title || key : key;
    },

    getLinks: (key) => {
        let result = _.findWhere(app.config.routes, {
            key: key
        });

        return result ? result.links || [] : [];
    },

    redirect: (path) => {
        if (_.isEmpty(path)) {
            // do nothing for empty paths
        } else if ((app.config.pushState === true) && (/^#.+/.test(path))) {
            // change hash only if push state is used
            window.location.hash = path;
        } else if (path === app.config.root) {
            // hard reload for basePath
            window.location.href = path;
        } else if (/^https?:\/\/.+/.test(path)) {
            // new window for full paths
            window.open(path);
        } else {
            // navigate
            app.router.redirect(path);
        }
    },

    goBack: () => {
        window.history.back();
    },

    gotoErrorPage: () => {
        if (window.location.pathname !== _.getPath('error')) {
            _.redirect(_.getPath('error'));
        }
    }
    
});

export default _;


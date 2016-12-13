import _ from 'underscore';
import app from '../../config/app.json';
import api from '../../config/api.json';
import substitutes from '../../config/substitutes.json';
import routes from '../../config/routes.json';

let config = _.extend(app, {
    api,
    substitutes,
    routes
});

export default config;


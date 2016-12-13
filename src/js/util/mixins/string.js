import _ from 'underscore';
import app from '../../app';
import moment from 'moment';
import moment_de from '../../../../node_modules/moment/locale/de';

//set moment to german
moment.locale('de', moment_de);

_.mixin({

    getSubstitute: (group, key) => {
        if (group === 'boolean') {
            key = String(!!key);
        }

        if (app.config.substitutes[group]) {
            return app.config.substitutes[group][key] || key;
        }

        return key;
    },

    formatDate: (date, format) => {
        if (date) {
            return moment(date).format(format);
        }
    },

    formatPrice: (price) => {
        if (_.isNumber(price)) {
            price = price.toFixed(2).replace('.', ',');

            let commaPos = price.indexOf(',');
            let beforeComma = price.substring(0, commaPos);

            price = _.formatNumber(beforeComma) + price.substring(commaPos);

            return price + ' €';
        }
    },

    formatNumber: (num) => {
        return String(num).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    },

    capitalize: function(string) {
        return string.charAt(0).toUpperCase() + string.substring(1).toLowerCase();
    }

});

export default _;


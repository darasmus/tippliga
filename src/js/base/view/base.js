import _ from 'underscore';
import Marionette from 'backbone.marionette';

export default class BaseView extends Marionette.LayoutView {

    constructor (options) {

        options = options || {};

        // careful, order changed: overrides options with defaults
        super(Object.assign({}, options, {

            events: _.extend({
                'click a'           : 'captureClick',
                'click .js-toggle'  : 'toggle'
            }, options.events || {})

        }));

    }

    onRender () {}

    synchronize (item) {
        this.listenTo(item, 'sync', this.onSync);
        this.listenTo(item, 'error', this.onError);
        item.fetch();
    }

    onSync () {
        this.render();
    }

    onError () {
        console.log('sync error');
    }

    captureClick (e) {
        e.preventDefault();
        e.stopPropagation();

        _.redirect(this.$(e.currentTarget).attr('href'));
    }

    toggle (e) {
        e.preventDefault();
        e.stopPropagation();

        let item = this.$(e.currentTarget);
        let target = this.$('.' + item.attr('data-target'));
        item.toggleClass('is-on');
        target.toggleClass('is-hidden');
    }

}


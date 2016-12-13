import _ from 'underscore';
import $ from 'jquery';
import app from '../../../app';
import PageView from '../../../base/view/page';
import HomeModel from '../model/home';
import Chartist from '../../../../js/lib/chartist';

export default class HomeView extends PageView {

    constructor (options) {
        super(Object.assign({
            
            template    : app.template('page/home/layout'),
            
            className   : 'home-wrapper container-wrapper',

            events      : {
                'mouseenter .ct-point' : 'showTooltip',
                'mouseleave .ct-point' : 'hideTooltip',
                'mousemove .ct-point'  : 'moveTooltip'
            }
            
        }, options));

        this.model = new HomeModel();

        //load on redirect...
        if(app.user.get('user')) {
            this.load();
        }

        this.displayed = false;

        this.listenTo(app, 'login:success', this.load);
    }

    onRender () {

        this.$('#standing').on('scroll', () => {
           this.$('.scroll-hint').fadeOut(400);
        });

        if(this.displayed) {
            this.loadChart();
        }
    }

    onShow () {
        this.displayed = true;
    }

    showTooltip (e) {
        let point = $(e.target),
            value = (point.attr('ct:value') || 0),
            seriesName = point.parent().attr('ct:series-name');
        this.toolTip.html(seriesName + '<br>' + value).show();
    }

    hideTooltip () {
        this.toolTip.hide();
    }

    moveTooltip (event) {
        this.toolTip.css({
            left: (event.originalEvent.layerX) - this.toolTip.width() / 2 - 10,
            top:  (event.originalEvent.layerY) - this.toolTip.height() - 40
        });
    }

    loadChart () {
        //chart data
        let places = _.pluck(this.model.get('summery'), 'platz');
        let points = _.pluck(this.model.get('summery'), 'punkte');

        let length = Math.min(points.length + 1, 34);

        let labels = [];
        for (let i=1; i < length; i++) {
            labels.push(i);
        }

        let data = {
            labels: labels,
            series: [
                {
                    name: 'Punkte',
                    data: points
                },
                {
                    name: 'Platz',
                    data: places
                }
            ]
        };

        //chart options
        let options = {
            lineSmooth: false,
            width: 960,
            height: 450,
            axisY: {
                onlyInteger: true,
                low: 0,
                high: 18
            }
        };

        //build chart
        new Chartist.Line('.ct-chart', data, options);

        //chart tooltips
        let linechart = this.$('.ct-chart');

        //build tooltip
        this.toolTip = linechart
            .append('<div class="tooltip"></div>')
            .find('.tooltip')
            .hide();

    }

    load () {

        this.liga = app.user.get('user').liga;

        this.spieltag = app.playday.get('playday');

        this.model.url = _.getApiUrl('home').replace('{spieltag}', this.spieltag).replace('{liga}', this.liga);

        this.synchronize(this.model);
    }

}


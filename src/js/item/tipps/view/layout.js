import _ from 'underscore';
import app from '../../../app';
import ItemView from '../../../base/view/item';
import AlltippsModel from '../model/alltipps';
import Chartist from '../../../../js/lib/chartist';

export default class AlltippsView extends ItemView {

    constructor (options) {
        super(Object.assign({

            template: app.template('item/tipps/layout'),
            className: 'alltipps-container is-hidden',
            events: {
                'click .js-close'   : 'hideTipps'
            }

        }, options));

        this.model = new AlltippsModel();

        this.listenTo(app, 'alltipps:open', this.showTipps);
        this.listenTo(app, 'alltipps:close', this.hideTipps);

    }

    loadData (spiel) {
        this.model.url = _.getApiUrl('alltipps').replace('{spiel}', spiel);
        this.synchronize(this.model);
    }

    showTipps (spiel) {
        this.loadData(spiel);        
        this.$el.removeClass('is-hidden');

        setTimeout(()=>{

            let stats = this.model.get('stats');
            let data = {
                series: [stats.heimsiege,stats.auswaertssiege,stats.unentschieden]
            };

            let options = {
                donut: true,
                donutWidth: 15,
                showLabel: false
            };
            
            let chart = new Chartist.Pie('.chart', data, options);
            console.log(chart);

            //animate
            // See http://gionkunz.github.io/chartist-js/api-documentation.html#chartistsvg-function-animate
            chart.on('draw', function(data) {
                if(data.type === 'slice') {
                    let pathLength = data.element._node.getTotalLength();
                    data.element.attr({
                        'stroke-dasharray': pathLength + 'px ' + pathLength + 'px'
                    });

                    let animationDefinition = {
                        'stroke-dashoffset': {
                            id: 'anim' + data.index,
                            dur: 800,
                            from: -pathLength + 'px',
                            to:  '0px',
                            easing: Chartist.Svg.Easing.easeOutQuint,
                            fill: 'freeze'
                        }
                    };

                    if(data.index !== 0) {
                        animationDefinition['stroke-dashoffset'].begin = 'anim' + (data.index - 1) + '.end';
                    }

                    data.element.attr({
                        'stroke-dashoffset': -pathLength + 'px'
                    });

                    data.element.animate(animationDefinition, false);
                }
            });

            // For the sake of the example we update the chart every time it's created with a delay of 8 seconds
            chart.on('created', function() {
                if(window.__anim21278907124) {
                    clearTimeout(window.__anim21278907124);
                    window.__anim21278907124 = null;
                }
                window.__anim21278907124 = setTimeout(chart.update.bind(chart), 10000);
            });

        }, 250);
    }

    hideTipps () {
        this.$el.addClass('is-hidden');
    }

}
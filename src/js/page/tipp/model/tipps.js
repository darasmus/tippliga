import _ from 'underscore';
import $ from 'jquery';
import app from '../../../app';
import BaseModel from '../../../base/model/base';

export default class TippsModel extends BaseModel {

    sync () {
        let sendDta = [];

        _.each(this.get('tipps'), (tipp, index) => {
            if(tipp) {
                sendDta.push( {
                    game : index,
                    user : app.user.get('user').id,
                    tipps: tipp
                } );
            }
        });

        return $.ajax({
            type: 'post',
            dataType: 'json',
            url: _.getApiUrl('savetipp'),
            data: {
                tipps: JSON.stringify(sendDta)
            },
            success: function () {
                app.trigger('saveTipp:done');
            }
        });

    }

}



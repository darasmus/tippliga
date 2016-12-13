import _ from 'underscore';
import $ from 'jquery';
import app from '../../../app';
import BaseModel from '../../../base/model/base';

export default class LoginModel extends BaseModel {

    constructor (options) {
        super(options);

        this.schema = {
            title: {
                type        : 'Text',
                title       : 'Titel',
                validators  : [
                    {
                        type: 'required',
                        message: ''
                    }
                ]
            },
            message: {
                type        : 'TextArea',
                title       : 'Beitrag',
                validators  : [
                    {
                        type: 'required',
                        message: ''
                    }
                ]
            }
            //,
            //link: {
            //    type        : 'Text',
            //    title       : 'Link'
            //},
            //linktext: {
            //    type        : 'Text',
            //    title       : 'Titel'
            //}
        };
    }

    sync () {
        let dta = {
            title: this.get('title'),
            message: this.get('message')
            //link: this.get('link'),
            //linktext: this.get('linktext')
        };

        return $.ajax({
            type: 'post',
            dataType: 'json',
            url: _.getApiUrl('savepost'),
            data: {
                dta: JSON.stringify(dta)
            },
            success: function () {
                app.trigger('post:done');
                app.user.fetch();
            }
        });

    }

}


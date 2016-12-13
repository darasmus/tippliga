import _ from 'underscore';
import $ from 'jquery';
import app from '../../../app';
import BaseModel from '../../../base/model/base';

export default class LoginModel extends BaseModel {

    constructor (options) {
        super(options);

        this.schema = {
            user: {
                type        : 'Text',
                title       : 'Benutzername',
                validators  : [
                    {
                        type: 'required',
                        message: ''
                    }
                ]
            },
            pwd: {
                type        : 'Password',
                title       : 'Passwort',
                validators  : [
                    {
                        type: 'required',
                        message: ''
                    }
                ]
            }
        };
    }

    sync () {
        let dta = {
            usr: this.get('user'),
            pwd: this.get('pwd')
        };

        return $.ajax({
            type: 'post',
            dataType: 'json',
            url: _.getApiUrl('login'),
            data: {
                dta: JSON.stringify(dta)
            },
            success: function () {
                app.trigger(':done');
                app.user.fetch();
            }
        });

    }

}


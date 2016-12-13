import BaseModel from '../../../base/model/base';

export default class SearchModel extends BaseModel {

    constructor (options) {
        super(options);

        this.schema = {
            email: {
                type        : 'Text',
                title       : 'E-MailAdresse',
                validators  : [
                    {
                        type: 'email',
                        message: 'Ungültige E-Mail-Adresse'
                    },
                    {
                        type: 'required',
                        message: ''
                    }
                ]
            }
        };
    }

}


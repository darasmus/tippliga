import BaseModel from '../../../base/model/base';

export default class AlertModel extends BaseModel {
    
    defaults () {
        return {
            confirmCallback : '',
            message         : '',
            close           : 'Close',
            confirm         : 'OK'
        };
    }
    
}


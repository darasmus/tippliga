import app from '../../../app';
import PageView from '../../../base/view/page';
import TableModel from '../model/table';

export default class TableView extends PageView {

    constructor (options) {
        super(Object.assign({

            template    : app.template('page/table/layout'),

            className   : 'table-wrapper container-wrapper',

            events      : {
                'change .gameday-selector': 'reload'
            }

        }, options));


        this.model = new TableModel();

        this.synchronize(this.model);
    }

}


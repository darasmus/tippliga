import _ from 'underscore';
import Backbone from 'backbone';
import 'backbone-forms';
import app from '../../app';

let BaseForm = Backbone.Form;

BaseForm.template = app.template('base/form/layout');

// use pre-rendered handlebars template
BaseForm.Field.template = _.template(app.template('base/form/form-field')());

export default BaseForm;


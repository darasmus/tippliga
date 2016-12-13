import _ from 'underscore';
import wrapper from 'handlebars/runtime';

let Handlebars = wrapper.default;

// find a path by route
Handlebars.registerHelper('getPath', function (key) {
    return _.getPath(key);
});

// find a title by route
Handlebars.registerHelper('getTitle', function (key) {
    return _.getTitle(key);
});

// find a substitute value
Handlebars.registerHelper('getSubstitute', function (group, item) {
    return _.getSubstitute(group, item);
});

// format a given date
Handlebars.registerHelper('formatDate', function (date, format) {
    return _.formatDate(date, _.isObject(format) ? 'Do MMMM YYYY, HH:mm:ss' : format);
});

// url encoding of a string
Handlebars.registerHelper('urlencode', function (string) {
    return encodeURIComponent(string);
});

// format html
Handlebars.registerHelper('formathtml', function (nonhtml) {
    let html = nonhtml.replace(/&lt;/gi, '<');
    let data = html.replace(/&gt;/gi, '>');
    return data;
});


// stripes
Handlebars.registerHelper('stripes', function(index) {
    return (index % 2 === 0 ? 'even' : 'odd');
});

// for
Handlebars.registerHelper('times', function(n, block) {
    var accum = '';
    for(var i = 1; i <= n; ++i) {
        accum += block.fn(i);
    }
    return accum;
});

// check for -
Handlebars.registerHelper('compare', function(lvalue, rvalue, options) {

    if (arguments.length < 3) {
        throw new Error('Handlerbars Helper compare needs 2 parameters');
    }

    var operator = options.hash.operator || '==';

    var operators = {
        '===':      function(l,r) { return l === r; },
        '!==':       function(l,r) { return l !== r; },
        '<':        function(l,r) { return l < r; },
        '>':        function(l,r) { return l > r; },
        '<=':       function(l,r) { return l <= r; },
        '>=':       function(l,r) { return l >= r; },
        'typeof':   function(l,r) { return typeof l === r; }
    };

    var result = operators[operator](lvalue,rvalue);

    if( result ) {
        return options.fn(this);
    } else {
        return options.inverse(this);
    }

});

export default Handlebars;


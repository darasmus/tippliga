import _ from 'underscore';
import math from './mixins/math';
import string from './mixins/string';
import navigation from './mixins/navigation';
import screen from './mixins/screen';

_.mixin(math);
_.mixin(string);
_.mixin(navigation);
_.mixin(screen);
_.mixin({

    startInterval: (callback, duration) => {
        callback();
        return setInterval(callback, duration);
    }

});

export default _;


import _ from 'underscore';

_.mixin({

    isInteger: (n) => {
        return parseInt(n, 10) === n;
    },

    getRandom: (min, max) => {
        if (min > max) {
            let temp = min;
            min = max;
            max = temp;
        }
        if (min === max) {
            return(min);
        }
        return (min + parseInt(Math.random() * (max - min + 1), 10));
    }

});

export default _;


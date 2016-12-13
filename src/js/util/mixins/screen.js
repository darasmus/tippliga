import _ from 'underscore';

_.mixin({

    getRandomColor: (transparency) => {
        let rgbList = _.getRandom(0, 255) + ', ' + _.getRandom(0, 255) + ', ' + _.getRandom(0, 255);

        if (transparency === false) {
            return 'rgb(' + rgbList + ')';
        } else {
            let transparencyItems = [
                'transparent',
                'rgba(' + rgbList + ', ' + (_.getRandom(0, 10) / 10) + ')'
            ];
            return transparencyItems[_.getRandom(0, transparencyItems.length - 1)];
        }
    },

    getWindowSize () {
        let de = document.documentElement;
        let myWidth = window.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
        let myHeight = window.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
        return [myWidth, myHeight];
    }

});

export default _;


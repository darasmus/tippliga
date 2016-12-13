var Readable = require('stream').Readable;
var exorcist = require('exorcist');
var shelljs = require('shelljs');

module.exports = function (grunt) {

    require('load-grunt-tasks')(grunt);
    require('time-grunt')(grunt);
    //
    // Grunt config
    //
    grunt.initConfig({

        srcFolder: 'src',
        outFolder: 'public',
        tmpFolder: '.tmp',
        bldFolder: 'build',

        pkg: grunt.file.readJSON('package.json'),

        rev: shelljs.exec(
            'bash -c \'echo -n `git rev-parse --short HEAD || date +%s`\'',
            {silent: true}
        ).output,

        tag: shelljs.exec(
            'bash -c \'echo -n $BUILD_NUMBER_LONG\'',
            {silent: true}
        ).output,

        env: {
            development: {
                NODE_ENV: 'development'
            },
            staging: {
                NODE_ENV: 'staging'
            },
            production: {
                NODE_ENV: 'production'
            }
        },

        //
        // template processing
        //
        handlebars: {
            all: {
                options: {
                    namespace: '<%= pkg.name %>',
                    commonjs: true,
                    partialsUseNamespace: true,
                    partialRegex: /.*/,
                    processName: function (filePath) {
                        return filePath.replace(/src\/tpl\/(.*)\.hbs/, '$1');
                    },
                    processPartialName: function (filePath) {
                        return filePath.replace(/src\/tpl\/(.*)\.hbs/, '$1');
                    }
                },
                files: {
                    '<%= tmpFolder %>/tpl.js': '<%= srcFolder %>/tpl/**/*.hbs'
                }
            }
        },

        //
        // {java,ecma}script processing
        //
        browserify: {
            options: {
                watch: true,
                browserifyOptions: {
                    debug: true
                },
                postBundleCB: function createSourceMap (err, src, next) {
                    var mapFile = grunt.template.process('<%= outFolder %>/js/<%= pkg.name %>.js.map');
                    var stream = new Readable();
                    var newSrc = '';
                    stream.push(src);
                    stream.push(null);
                    function data (chunk) {
                        newSrc += chunk.toString('utf8');
                    }

                    function end (chunk) {
                        if (chunk) {
                            data(chunk);
                        }
                        next(err, newSrc);
                    }

                    stream.pipe(exorcist(mapFile)).on('data', data).on('end', end);
                }
            },
            dev: {
                files: {
                    '<%= outFolder %>/js/<%= pkg.name %>.js': '<%= srcFolder %>/js/main.js'
                }
            },
            prod: {
                options: {
                    watch: false,
                    browserifyOptions: {
                        debug: true,
                        fullPaths: false
                    }
                },
                files: {
                    '<%= outFolder %>/js/<%= pkg.name %>.js': '<%= srcFolder %>/js/main.js'
                }
            }
        },

        jshint: {
            options: {
                jshintrc: true,
                ignores: '<%= srcFolder %>/js/lib/**/*.js'

            },
            app: {
                src: '<%= srcFolder %>/js/**/*.js'
            },
            self: {
                src: 'Gruntfile.js'
            }
        },

        uglify: {
            dest: {
                options: {
                    sourceMap: true,
                    sourceMapIn: '<%= outFolder %>/js/<%= pkg.name %>.js.map'
                },
                files: {
                    '<%= outFolder %>/js/<%= pkg.name %>.js': '<%= outFolder %>/js/<%= pkg.name %>.js'
                }
            }
        },

        //
        // (s)css handling/processing
        //
        sass: {
            options: {
                sourceMap: true
            },
            dist: {
                files: {
                    '<%= outFolder %>/css/<%= pkg.name %>.css': '<%= srcFolder %>/sass/main.scss'
                }
            }
        },

        scsslint: {
            allFiles: [
                'src/sass/**/*.scss'
            ],
            options: {
                gemVersion: '0.38.0',
                config: '.scss-lint.yml',
                colorizeOutput: true,
                compact: false,
                reporterOutput: '<%= bldFolder %>/reports/scss-lint-report.xml'
            }
        },

        postcss: {
            options: {
                map: true,
                processors: [
                    require('autoprefixer-core')({browsers: ['last 3 versions', 'ie 9']})
                ]
            },
            dist: {
                files: {
                    '<%= outFolder %>/css/<%= pkg.name %>.css': '<%= outFolder %>/css/<%= pkg.name %>.css'
                }
            }
        },

        cssshrink: {
            dist: {
                files: {
                    '<%= outFolder %>/css/<%= pkg.name %>.css': '<%= outFolder %>/css/<%= pkg.name %>.css'
                }
            }
        },

        //
        // svg handling/processing
        //
        svgmin: {
            options: {
                plugins: [
                    {
                        removeViewBox: false
                    },
                    {
                        removeUselessStrokeAndFill: false
                    },
                    {
                        cleanupIDs: false
                    }
                ]
            },
            multiple: {
                files: [{
                    cwd: 'src/',
                    expand: true,
                    src: ['svg/*.svg'],
                    dest: '.tmp/'
                }]
            }
        },

        svgstore: {
            options: {
                prefix: 'icon-', // This will prefix each <symbol> ID
                svg: { // will add and overide the the default xmlns="http://www.w3.org/2000/svg" attribute to the resulting SVG
                    class: 'is-hidden',
                    hidden: '',
                    xmlns: 'http://www.w3.org/2000/svg'
                }
            },
            includedemo: {
                options: {
                    includedemo: true
                }
            },
            withCustomTemplate: {
                options: {
                    includedemo: grunt.file.read('src/svg/icon-demo.hbs')
                },
                files: {
                    'public/svg/customTemplate.svg': ['.tmp/svg/*.svg']
                }
            },
            default: {
                files: {
                    'public/sprite.svg': ['.tmp/svg/*.svg']
                }
            }
        },

        inline: {
            dist: {
                src: 'public/index.html'
            }
        },

        //
        // Utilities
        //
        clean: {
            dest: ['<%= outFolder %>/**/*'],
            tmp: ['<%= tmpFolder %>/**/*'],
            build: ['<%= bldFolder %>/**/*.tar.gz']
        },

        copy: {
            /*
             normalizecss: {
             expand: true,
             cwd: 'node_modules/normalize.css/',
             src: ['normalize.css'],
             dest: '<%= outFolder %>/css'
             },
             */
            asset: {
                expand: true,
                cwd: '<%= srcFolder %>/assets/',
                src: [ '**/*.*'],
                dest: '<%= outFolder %>/'
            },
            api: {
                expand: true,
                cwd: '<%= srcFolder %>/api/',
                src: [ '**/*.*'],
                dest: '<%= outFolder %>/api/'
            },
            admin: {
                expand: true,
                cwd: '<%= srcFolder %>/admin/',
                src: [ '**/*.*'],
                dest: '<%= outFolder %>/admin/'
            },
            adminlib: {
                expand: true,
                cwd: '<%= srcFolder %>/lib/',
                src: [ '**/*.*'],
                dest: '<%= outFolder %>/lib/'
            },
            html: {
                expand: true,
                cwd: '<%= srcFolder %>/',
                src: ['*.html', '*.txt'],
                dest: '<%= outFolder %>/',
                options: {
                    process: function (content) {
                        return grunt.template.process(content);
                    }
                }
            },
            php: {
                expand: true,
                cwd: '<%= srcFolder %>/',
                src: ['*.php'],
                dest: '<%= outFolder %>/'
            }
        },

        compress: {
            live: {
                options: {
                    archive: './<%= bldFolder %>/<%= pkg.name %>.tar.gz'
                },
                files: [
                    {
                        expand: true,
                        cwd: '<%= outFolder %>/',
                        src: ['**'],
                        dest: '<%= outFolder %>/'
                    }
                ]
            }
        },

        watch: {
            html: {
                files: ['<%= srcFolder %>/*.html'],
                tasks: ['copy:html', 'inline']
            },
            php: {
                files: ['<%= srcFolder %>/*.php'],
                tasks: ['copy:php', 'inline']
            },
            js: {
                files: ['<%= srcFolder %>/js/**/*.js'],
                tasks: [
                    'jshint:app',
                    //'browserify:dev'
                ]
            },
            tpl: {
                files: ['<%= srcFolder %>/tpl/**/*.hbs'],
                tasks: [
                    'handlebars:all',
                    //'browserify:dev'
                ]
            },
            config: {
                files: ['config/**/*.*'],
                tasks: [
                    //'browserify:dev'
                ]
            },
            sass: {
                files: ['<%= srcFolder %>/sass/**/*.scss'],
                tasks: ['scsslint', 'sass', 'postcss']
            },
            svg: {
                files: ['<%= srcFolder %>/svg/**/*.svg'],
                tasks: ['svgmin', 'svgstore', 'inline']
            },
            img: {
                files: ['<%= srcFolder %>img/**/*.{gif,jpg,png}'],
                tasks: ['copy:asset']
            },
            api: {
                files: ['<%= srcFolder %>/api/*.php'],
                tasks: ['copy:api']
            },
            admin: {
                files: ['<%= srcFolder %>/admin/*.php'],
                tasks: ['copy:admin']
            }
        }

    });

    //
    // composite tasks
    //
    grunt.registerTask('setenv', function () {
        grunt.task.run('env:' + (process.env.NODE_ENV || 'development'));
    });

    // build-task
    grunt.registerTask('build', [
        'clean',
        'setenv',
        'jshint',
        'scsslint',
        'handlebars',
        'svgmin',
        'svgstore',
        'sass',
        'postcss',
        'copy',
        'inline'
    ]);

    // development-build-task
    grunt.registerTask('build-dev', [
        'build',
        'browserify:dev'
    ]);

    // production-build-task
    grunt.registerTask('build-release', [
        'build',
        'browserify:prod',
        'uglify',
        'cssshrink',
        'compress:live'
    ]);

    grunt.registerTask('validate', [
        'scsslint'
    ]);
    
    grunt.registerTask('default', [
        'build-dev',
        'watch'
    ]);

};


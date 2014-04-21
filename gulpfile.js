// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var concat = require('gulp-concat');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');
var exec = require('child_process').exec;
var Q = require('q');

// Install Autobahn
gulp.task('autobahn_install', function(cb) {
    exec('make install -C bower_components/autobahnjs/package/', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        cb(err);
    });
});

// Make directory Autobahn
gulp.task('autobahn_mkdir', function(cb) {
    exec('mkdir bower_components/autobahnjs/package/build/', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        cb(err);
    });
});

// Install Autobahn
gulp.task('autobahn_build', function(cb) {
    var deferred = Q.defer();

    exec('make -C bower_components/autobahnjs/package/', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        deferred.resolve(err);
    });

    return deferred.promise;
});

// Lint Task
gulp.task('lint', function() {
    return gulp.src('webroot/js/cake-wamp.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

// Concatenate & Minify JS
gulp.task('scripts', ['autobahn_build'], function() {
    return gulp.src([
            'bower_components/autobahnjs/package/build/autobahn.js',
            'webroot/js/cake-wamp.js'
        ])
        .pipe(concat('ratchet.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('webroot/js'));
});

gulp.task('swf', function() {
    return gulp.src([
            'bower_components/web-socket-js/WebSocketMain.swf'
        ])
        .pipe(gulp.dest('webroot/swf'));
});

// Default Task
gulp.task('default', ['lint', 'scripts', 'swf']);
gulp.task('autobahn_prepare', ['autobahn_install', 'autobahn_mkdir']);
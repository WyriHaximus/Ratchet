// Include gulp
var gulp = require('gulp');

// Include Our Plugins
var concat = require('gulp-concat');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');

// Lint Task
gulp.task('lint', function() {
    return gulp.src('webroot/js/cake-wamp.js')
        .pipe(jshint())
        .pipe(jshint.reporter('default'));
});

// Concatenate & Minify JS
gulp.task('scripts', function() {
    return gulp.src([
            'bower_components/web-socket-js/swfobject.js',
            'bower_components/web-socket-js/web_socket.js',
            'bower_components/autobahnjs/autobahn/license.js',
            'bower_components/when/when.js',
            'bower_components/autobahnjs/cryptojs/rollups/crypto-sha256-hmac.js',
            'bower_components/autobahnjs/autobahn/autobahn.js',
            'bower_components/autobahnjs/autobahn/useragent.js',
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
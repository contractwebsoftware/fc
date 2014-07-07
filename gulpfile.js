// Include gulp
var gulp = require('gulp');

// Include plugin
var sass   = require('gulp-ruby-sass'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    jshint = require('gulp-jshint');

// Setting Up Task for sass
var cssDir = 'app/assets/sass/**/*.sass';
gulp.task('sass',function(){
	return gulp.src(cssDir)
		.pipe(sass({style:'extended'}))
		.pipe(gulp.dest('public/css'));
});

// Setting up Scrips task (concatenate, combine javascript)
var jsDir = 'app/assets/js/**/*.js';
gulp.task('scripts', function() {
    return gulp.src(jsDir)
        .pipe(concat('merchant.js'))
        .pipe(gulp.dest('public/js/min'))
        .pipe(rename('merchant.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('dist'));
});
// Setting Up Watch task
gulp.task('watch',function(){
	gulp.watch(cssDir,['sass']);
    gulp.watch(jsDir,['scripts']);
})

// Default Task
gulp.task('default', ['sass', 'watch']);
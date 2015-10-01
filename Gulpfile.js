// This 'Gulpfile' configured how to compile Sass files

'use strict';

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    minifyCss = require('gulp-minify-css');

gulp.task('sass', function () {
    gulp.src('./app/assets/css/style.scss')
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(minifyCss())
        .pipe(gulp.dest('./public/css'));
});

gulp.task('sass:watch', function () {
    gulp.watch('./app/assets/css/**/*.scss', ['sass']);
});
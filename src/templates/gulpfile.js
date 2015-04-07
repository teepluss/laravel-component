var gulp = require('gulp');

gulp.task('default', function() {
    gulp.src('**/assets/**')
        .pipe(gulp.dest('../../public/components/'));
});

gulp.task('watch', function() {
    gulp.watch('**/assets/**', ['default']);

});
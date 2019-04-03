/* 
 * This file is part of the php-magic package
 * 
 *  (c) Cory Laughlin <corylcomposinger@gmail.com>
 * 
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

var gulp = require('gulp');
var exec = require('child_process').exec;

function fix_in_dir(dir) {
    exec('php-cs-fixer fix "./' + dir + '" --rules=@PSR2', function (error, stdout) {
        console.log(stdout);
    });
}

function csfixtests(cb) {
    fix_in_dir('tests');
    cb();
}

function csfixsrc(cb) {
    fix_in_dir('src');
    cb();
}

function phpunit(cb) {
    exec('phpunit --colors=always', function (error, stdout) {
        console.log(stdout);
    });
    cb();
}

function watcher(cb) {
    gulp.watch([
        'src/**/*.php',
        'src/*.php',
        'tests/**/*.php',
        'tests/*.php',
    ], {delay: 6000, ignoreInitial: false}, gulp.series(gulp.parallel(csfixtests, csfixsrc)));
    cb();
}

exports.default = gulp.series(watcher);

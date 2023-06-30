const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

//**************** CSS ******************** 
//css
mix.copy('node_modules/@coreui/chartjs/dist/css/coreui-chartjs.css', 'public/css');
mix.copy('node_modules/cropperjs/dist/cropper.css', 'public/css');
//main css
mix.sass('resources/sass/style.scss', 'public/css');

//************** SCRIPTS ****************** 
// general scripts
mix.copy('node_modules/@coreui/utils/dist/coreui-utils.js', 'public/js');
mix.copy('node_modules/axios/dist/axios.min.js', 'public/js'); 
mix.copy('node_modules/@coreui/coreui/dist/js/coreui.bundle.min.js', 'public/js');
// views scripts
mix.copy('node_modules/chart.js/dist/Chart.min.js', 'public/js'); 
mix.copy('node_modules/@coreui/chartjs/dist/js/coreui-chartjs.bundle.js', 'public/js');
mix.copy('node_modules/cropperjs/dist/cropper.js', 'public/js');
// vue app script
mix.js('resources/js/app.js', 'public/js').vue()
// details scripts
mix.copy('resources/js/coreui/main.js', 'public/js');
mix.copy('resources/js/coreui/colors.js', 'public/js');
mix.copy('resources/js/coreui/charts.js', 'public/js');
mix.copy('resources/js/coreui/widgets.js', 'public/js');
mix.copy('resources/js/coreui/popovers.js', 'public/js');
mix.copy('resources/js/coreui/tooltips.js', 'public/js');
// details scripts admin-panel
mix.js('resources/js/coreui/menu-create.js', 'public/js');
mix.js('resources/js/coreui/menu-edit.js', 'public/js');
mix.js('resources/js/coreui/media.js', 'public/js');
mix.js('resources/js/coreui/media-cropp.js', 'public/js');
// registration
mix.js('resources/js/registration.js', 'public/js');
// bundy
mix.js('resources/js/bundy.js', 'public/js');
// leave request
mix.js('resources/js/leave-request.js', 'public/js');
// admin
mix.js('resources/js/admin.js', 'public/js');
mix.js('resources/js/admin/timesheet_adjustments/index.js', 'public/js/admin/timesheet_adjustments/');
// table to csv
mix.js('resources/js/tableToCsv.js', 'public/js');
// supervisor
mix.js('resources/js/supervisor.js', 'public/js'); // @remind - currently being used by department attendance - can be cleared soon
// face recognition
mix.js('resources/js/face-recognition/face-register.js', 'public/js');
mix.js('resources/js/face-recognition/face-clock.js', 'public/js');
// advance user
mix.js('resources/js/advance/base.js', 'public/js/advance');
mix.js('resources/js/advance/supervisor/department-leave.js', 'public/js/advance/supervisor/');
mix.js('resources/js/advance/supervisor/department-timesheet-adjustments.js', 'public/js/advance/supervisor/');
//*************** OTHER ****************** 
//fonts
mix.copy('node_modules/@coreui/icons/fonts', 'public/fonts');
//icons
mix.copy('node_modules/@coreui/icons/css/free.min.css', 'public/css');
mix.copy('node_modules/@coreui/icons/css/brand.min.css', 'public/css');
mix.copy('node_modules/@coreui/icons/css/flag.min.css', 'public/css');
mix.copy('node_modules/@coreui/icons/svg/flag', 'public/svg/flag');
mix.copy('node_modules/@coreui/icons/sprites/', 'public/icons/sprites');
// OrgChart
mix.copy('node_modules/orgchart/dist/css', 'public/css/');
mix.copy('node_modules/orgchart/dist/js', 'public/js/');
//images
mix.copy('resources/assets', 'public/assets');

mix.postCss("resources/css/app.css", "public/css", [
        require("tailwindcss"),
    ])
    .sass('resources/sass/app.scss', 'public/css/admin')
    .sass('resources/sass/vue.scss', 'public/css/vue')
    .copy('resources/css/leave-request.css', 'public/css/')
    .copy('resources/css/timesheet-records.css', 'public/css/')
    .copy('resources/css/bundy.css', 'public/css/')
    .copy('resources/css/admin.css', 'public/css/')
    .copy('resources/css/face-recognition.css', 'public/css/')
    .copy('resources/css/advance/base.css', 'public/css/advance');

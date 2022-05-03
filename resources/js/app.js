import Alpine from 'alpinejs'

require('./components/turbo');
require('./components/conditional');
require('./components/confirm');
require('./components/datepicker');
require('./components/dirty');
require('./components/dismiss');
require('./components/dropdown');
require('./components/htmlPreview');
require('./components/modal');
require('./components/nav');
require('./components/poll');
require('./components/segments');
require('./components/tags');
require('./components/charts/emailListStatistics');
require('./components/charts/campaignStatistics');

window.Alpine = Alpine;

Alpine.start();

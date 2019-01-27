require('../scss/custom.scss');

const $ = jQuery = window.jQuery = window.$ = require('jquery');
require('bootstrap');
const questions = require('./questions');
questions();
const global = require('./global');
global();
require('datepicker-bootstrap');

// bootstrap.js
import 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min';

import EasyMDE from 'easymde';
import 'easymde/dist/easymde.min.css';
window.EasyMDE = EasyMDE;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction'; // For event clicking and dragging

window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;
window.interactionPlugin = interactionPlugin;

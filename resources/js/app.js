import.meta.glob([
    '../images/**',
]);

import {Livewire, Alpine} from '../../vendor/livewire/livewire/dist/livewire.esm'
import Datepicker from './components/datepicker-component'
import SeatingAnimation from './components/seating-animation-component'

Alpine.data('datepicker', Datepicker)
Alpine.data('seatingAnimation', SeatingAnimation)

Livewire.start()
